<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Vat;
use App\Models\VatSetting;
use App\Models\Wastage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class SalesController extends Controller
{
    /**
     * Load the Point of Sale (POS) interface with products, categories, VAT, and discounts.
     */
    public function index()
    {
        // Fetch products along with their ingredient and category relationships
        $products = Product::with(['ingredients', 'category'])->get()->map(function ($product) {
            // Explicitly append calculated portion count for frontend JS access
            $product->calculated_stock = $product->available_stock;
            // Attach category_name directly so the POS product card data-category attribute works seamlessly
            if ($product->relationLoaded('category') && $product->category) {
                $product->category_name = $product->category->category_name;
            }
            return $product;
        });

        // Fetch categories from database to generate dynamic filter pills in POS
        $categories = class_exists(Category::class) 
            ? Category::all() 
            : collect([]);

        // Fetch VAT configuration safely
        $rawVat = null;
        if (class_exists(Vat::class)) {
            $rawVat = Vat::first();
        } 
        if (!$rawVat && class_exists(VatSetting::class)) {
            $rawVat = VatSetting::first();
        }

        $vat = (object) [
            'rate'         => (float) ($rawVat->rate ?? $rawVat->vat_rate ?? 12.00),
            'is_inclusive' => (bool) ($rawVat->is_inclusive ?? $rawVat->vat_inclusive ?? true),
            'is_enabled'   => (bool) ($rawVat->is_enabled ?? $rawVat->is_active ?? true),
            'is_active'    => (bool) ($rawVat->is_enabled ?? $rawVat->is_active ?? true),
        ];

        $discounts = class_exists(Discount::class) 
            ? Discount::where('is_active', true)->get() 
            : collect([]);

        $viewName = view()->exists('pos') ? 'pos' : 'pointofsale';

        return view($viewName, compact('products', 'categories', 'vat', 'discounts'));
    }

    /**
     * Process POS checkout, deduct ingredient inventory, and print the receipt on POS58.
     */
    public function store(Request $request)
    {
        $request->validate([
            'total_amount'     => 'required|numeric|min:0',
            'subtotal'         => 'nullable|numeric|min:0',
            'vat_amount'       => 'nullable|numeric|min:0',
            'discount_amount'  => 'nullable|numeric|min:0',
            'discount_type'    => 'nullable|string',
            'channel'          => 'nullable|string',
            'amount_tendered'  => 'nullable|numeric|min:0',
            'change_amount'    => 'nullable|numeric|min:0',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.name'     => 'nullable|string', // Allows custom Milk Tea options (e.g. "Okinawa (22oz, 50% Sugar)")
            'items.*.price'    => 'nullable|numeric|min:0', // Allows size-adjusted prices if needed
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $subtotal = $request->subtotal ?? $request->total_amount;
                
                $vatAmount = $request->vat_amount;
                if ($vatAmount === null || $vatAmount == 0) {
                    $vatAmount = $subtotal - ($subtotal / 1.12);
                }

                // Generate Order Number
                $saleDate = now();
                $monthlyCount = Sale::whereYear('sale_date', $saleDate->year)
                                    ->whereMonth('sale_date', $saleDate->month)
                                    ->count() + 1;
                $orderNumber = 'ORD-' . $saleDate->format('Ym') . '-' . str_pad($monthlyCount, 4, '0', STR_PAD_LEFT);

                // Create Sale Record
                $sale = Sale::create([
                    'order_number'    => $orderNumber,
                    'sale_date'       => $saleDate,
                    'subtotal'        => $subtotal,
                    'vat_amount'      => round($vatAmount, 2),
                    'discount_type'   => $request->discount_type,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'total_amount'    => $request->total_amount,
                    'amount_tendered' => $request->amount_tendered ?? 0,
                    'change_amount'   => $request->change_amount ?? 0,
                    'order_channel'   => $request->channel ?? 'Walk-in',
                    'payment_method'  => 'Cash',
                ]);

                // Array to hold formatted items for the POS58 thermal printer
                $printableItems = [];

                foreach ($request->items as $item) {
                    $product = Product::with('ingredients')
                                       ->where('product_id', $item['id'])
                                       ->lockForUpdate()
                                       ->firstOrFail();

                    if ($product->available_stock < $item['quantity']) {
                        throw new \Exception("Insufficient ingredient stock for: {$product->product_name}. Max available portions: {$product->available_stock}");
                    }

                    // Use custom price if passed (e.g. for 22oz upgrade), otherwise default to product price
                    $unitPrice = isset($item['price']) ? (float) $item['price'] : (float) $product->price;

                    // Create Sale Detail
                    SaleDetail::create([
                        'sale_id'    => $sale->sale_id ?? $sale->id,
                        'product_id' => $product->product_id,
                        'quantity'   => $item['quantity'],
                        'subtotal'   => $unitPrice * $item['quantity'],
                    ]);

                    // Deduct raw ingredients using the auto-unboxing method
                    foreach ($product->ingredients as $ingredient) {
                        $qtyNeeded = $ingredient->pivot->quantity_needed 
                                  ?? $ingredient->pivot->quantity_required 
                                  ?? $ingredient->pivot->quantity 
                                  ?? 1;

                        $deductAmount = $qtyNeeded * $item['quantity'];

                        // Deduct loose pieces and unbox sealed boxes as necessary
                        $success = $ingredient->deductPieces($deductAmount);

                        if (!$success) {
                            throw new \Exception("Insufficient stock for ingredient: {$ingredient->ingredient_name}");
                        }
                    }

                    // Add item to the thermal printer queue (uses custom name if options were selected)
                    $printableItems[] = [
                        'name'     => !empty($item['name']) ? $item['name'] : $product->product_name,
                        'quantity' => (int) $item['quantity'],
                        'price'    => $unitPrice,
                    ];
                }

                // Automatically print the receipt on the POS58 thermal printer!
                $this->printThermalReceipt($sale, $printableItems);

                return response()->json([
                    'success' => true,
                    'sale_id' => $sale->sale_id ?? $sale->id, 
                    'message' => 'Transaction complete and sent to POS58 printer!'
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Checkout Failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Transaction failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display today's sales history.
     */
    public function history()
    {
        $todaySalesList = Sale::with('details.product')
                              ->whereDate('sale_date', Carbon::today())
                              ->orderBy('sale_date', 'desc')
                              ->get();

        return view('sales_history', compact('todaySalesList'));
    }

    /**
     * Generate monthly sales, best sellers, payment methods, and wastage reports.
     */
    public function reports(Request $request)
    {
        $activeTab = $request->input('tab', 'summary');

        $currentYear  = (int) Carbon::now()->year;
        $currentMonth = (int) Carbon::now()->month;

        // Read GET inputs
        $selectedYear  = $request->filled('year')  ? (int) $request->input('year')  : $currentYear;
        $selectedMonth = $request->filled('month') ? (int) $request->input('month') : $currentMonth;

        // Redirect future date requests to the current month/year to fix the URL
        if ($selectedYear > $currentYear || ($selectedYear === $currentYear && $selectedMonth > $currentMonth)) {
            return redirect()->route('reports.index', [
                'tab'   => $activeTab,
                'month' => $currentMonth,
                'year'  => $currentYear,
            ]);
        }

        // Define exact start and end boundaries for the selected month
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->toDateTimeString();
        $endDate   = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->toDateTimeString();

        // 1. Sales Summary Metrics
        $salesQuery = Sale::whereBetween('sale_date', [$startDate, $endDate]);

        $monthlySalesList = (clone $salesQuery)->with('details.product')
                                               ->orderBy('sale_date', 'desc')
                                               ->get();

        $totalSales    = (clone $salesQuery)->sum('total_amount');
        $totalSubtotal = (clone $salesQuery)->sum('subtotal');
        $totalVat      = (clone $salesQuery)->sum('vat_amount');
        $totalDiscount = (clone $salesQuery)->sum('discount_amount');
        $totalOrders   = (clone $salesQuery)->count();

        // 2. Best Selling Products
        $bestSellers = SaleDetail::select(
                'products.product_name', 
                DB::raw('SUM(sale_details.quantity) as total_qty'), 
                DB::raw('SUM(sale_details.subtotal) as total_revenue')
            )
            ->join('products', 'sale_details.product_id', '=', 'products.product_id')
            ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sale_date', [$startDate, $endDate]);
            })
            ->groupBy('products.product_id', 'products.product_name')
            ->orderByDesc('total_qty')
            ->get();

        // 3. Payment Method Breakdown
        $paymentMethods = (clone $salesQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // 4. Wastage Costs
        $totalWastageCost = 0;
        if (class_exists(Wastage::class)) {
            $wastageQuery = Wastage::whereBetween('created_at', [$startDate, $endDate]);

            if (Schema::hasColumn('wastages', 'total_cost')) {
                $totalWastageCost = $wastageQuery->sum('total_cost');
            } elseif (Schema::hasColumn('wastages', 'cost')) {
                $totalWastageCost = $wastageQuery->sum('cost');
            } elseif (Schema::hasColumn('wastages', 'amount')) {
                $totalWastageCost = $wastageQuery->sum('amount');
            } elseif (Schema::hasColumn('wastages', 'total_amount')) {
                $totalWastageCost = $wastageQuery->sum('total_amount');
            }
        }

        $reportDateTitle = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        return view('sales_reports', compact(
            'monthlySalesList',
            'totalSales',
            'totalSubtotal',
            'totalVat',
            'totalDiscount',
            'totalOrders',
            'bestSellers',
            'paymentMethods',
            'totalWastageCost',
            'selectedMonth',
            'selectedYear',
            'reportDateTitle',
            'reportDateTitle',
            'activeTab'
        ));
    }

    /**
     * Prints a sharp 58mm receipt directly to the POS58 thermal printer via USB.
     * Supports both direct array calls from store() and AJAX reprint calls by sale_id.
     * 
     * @param  mixed       $saleOrId  The saved Sale model instance OR a sale_id integer
     * @param  array|null  $items     Optional array of items purchased in this transaction
     */
    public function printThermalReceipt($saleOrId, $items = null)
    {
        try {
            // 1. Resolve Sale model and items if called via route with just a $sale_id
            if (is_numeric($saleOrId)) {
                $sale = Sale::with('details.product')->findOrFail($saleOrId);
                $items = [];
                foreach ($sale->details as $detail) {
                    $qty = $detail->quantity ?? 1;
                    $unitPrice = $qty > 0 ? (($detail->subtotal ?? 0) / $qty) : 0;
                    $items[] = [
                        'name'     => $detail->product->product_name ?? 'Item',
                        'quantity' => $qty,
                        'price'    => $unitPrice,
                    ];
                }
            } else {
                $sale = $saleOrId;
            }

            // 2. Connect to your shared Windows USB printer.
            // ADJUSTABLE PARAMETER: Change 'POS58' if your Windows Printer Share Name changes
            $connector = new WindowsPrintConnector("POS58");
            $printer = new Printer($connector);

            // 3. Print the Receipt Header (Centered)
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("PBWSIS POS\n");
            $printer->setEmphasis(false);
            $printer->text("Official Receipt\n");

            if (!empty($sale->order_number)) {
                $printer->text("Order: " . $sale->order_number . "\n");
            }
            if (!empty($sale->order_channel)) {
                $printer->text("Type: " . strtoupper($sale->order_channel) . "\n");
            }

            $printer->text(Carbon::parse($sale->sale_date ?? now())->format('d/m/Y, H:i:s') . "\n");
            $printer->text("--------------------------------\n"); // 32 characters fits 58mm perfectly

            // 4. Print the Items List (Left Aligned with automatic word-wrapping)
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $itemName  = $item['quantity'] . "x " . $item['name'];
                    $itemPrice = "P" . number_format($item['price'] * $item['quantity'], 2);
                    
                    $printer->text($this->formatReceiptRow($itemName, $itemPrice));
                }
            }

            $printer->text("--------------------------------\n");

            // 5. Print Subtotal, Discount, and VAT using your exact Sale column names
            $subtotal = number_format($sale->subtotal ?? $sale->total_amount ?? 0, 2);
            $discount = number_format($sale->discount_amount ?? 0, 2);
            $vat      = number_format($sale->vat_amount ?? 0, 2);
            $total    = number_format($sale->total_amount ?? 0, 2);

            $printer->text($this->formatReceiptRow("Subtotal:", "P" . $subtotal));
            $printer->text($this->formatReceiptRow("Discount:", "-P" . $discount));
            $printer->text($this->formatReceiptRow("VAT (12% Incl.):", "P" . $vat));
            $printer->text("--------------------------------\n");

            // 6. Print Grand Total (Bold)
            $printer->setEmphasis(true);
            $printer->text($this->formatReceiptRow("TOTAL:", "P" . $total));
            $printer->setEmphasis(false);

            // Print Cash Tendered and Change if provided
            if (!empty($sale->amount_tendered) && $sale->amount_tendered > 0) {
                $tendered = number_format($sale->amount_tendered, 2);
                $change   = number_format($sale->change_amount ?? 0, 2);
                $printer->text($this->formatReceiptRow("Cash:", "P" . $tendered));
                $printer->text($this->formatReceiptRow("Change:", "P" . $change));
            }

            $printer->text("--------------------------------\n");

            // 7. Print Footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for your purchase!\n");

            // 8. Feed 4 blank lines so the paper clears the tear bar, then close connection
            $printer->feed(4);
            $printer->close();

            if (request()->wantsJson() && is_numeric($saleOrId)) {
                return response()->json(['success' => true, 'message' => 'Receipt printed!']);
            }

        } catch (\Exception $e) {
            // If the printer is turned off or unplugged, log the error so the POS checkout still succeeds!
            Log::error("Thermal Printer Error: " . $e->getMessage());

            if (request()->wantsJson() && is_numeric($saleOrId)) {
                return response()->json(['error' => 'Printer error: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * Helper function to align left text and right price on 58mm paper (32 characters wide)
     * with automatic word-wrapping so long product names never collide with prices.
     */
    private function formatReceiptRow($name, $price)
    {
        $maxWidth = 32; // Standard character limit for 58mm thermal paper
        $priceStr = " " . $price;
        $maxNameWidth = $maxWidth - strlen($priceStr);

        // Wrap long product names onto multiple lines
        $wrappedLines = explode("\n", wordwrap($name, $maxNameWidth, "\n", true));

        $firstLine = $wrappedLines[0];
        $spaces = $maxWidth - strlen($firstLine) - strlen($price);
        $output = $firstLine . str_repeat(" ", max(1, $spaces)) . $price . "\n";

        // Print any remaining wrapped words cleanly on subsequent lines
        for ($i = 1; $i < count($wrappedLines); $i++) {
            $output .= $wrappedLines[$i] . "\n";
        }

        return $output;
    }
}