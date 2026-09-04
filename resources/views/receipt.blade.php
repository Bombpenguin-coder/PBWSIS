<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Prince Buffalo Wings</title>
    <style>
        /* Base styles for web viewing */
        body {
            font-family: monospace;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #000000;
        }

        .no-print {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn-print {
            background-color: #7f1d1d;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-back {
            color: #2563eb;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14px;
        }

        .receipt {
            width: 260px;
            background: #ffffff;
            padding: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .divider {
            border-bottom: 1px dashed #000000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        td, th {
            padding: 2px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: bold;
            margin: 6px 0;
        }

        .footer-text {
            font-size: 11px;
            margin-top: 12px;
        }

        /* Thermal Printer Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
                width: 100% !important;
            }
            @page {
                size: 58mm auto;
                margin: 0mm;
            }
            .receipt {
                width: 58mm !important;
                max-width: 58mm !important;
                padding: 2mm !important;
                box-shadow: none !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Print Receipt</button>
        <a href="{{ route('sales.store') }}" class="btn-back">Back to POS</a>
    </div>

    <!-- Receipt Content -->
    <div class="text-center mb-4">
        <h1 class="font-bold text-xl">Prince Buffalo Wings</h1>
        <p class="text-xs">123 Flavor Street, Taguig City</p>
        <p class="text-xs">Order #: {{ $sale->order_number }}</p>
    </div>

    <div class="border-t border-b border-black py-2 mb-2 border-dashed">
        <div class="flex justify-between text-xs font-bold mb-1">
            <span>Item</span>
            <span>Total</span>
        </div>
        
        <!-- Loop through the actual items from the database -->
        @foreach($sale->details as $item)
            <div class="flex justify-between text-xs mb-1">
                <span>{{ $item->quantity }}x {{ $item->product->name ?? 'Unknown Item' }}</span>
                <span>₱ {{ number_format($item->subtotal, 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="flex justify-between font-bold text-sm mb-4">
        <span>Total Amount:</span>
        <span>₱ {{ number_format($sale->total_amount, 2) }}</span>
    </div>

    <div class="text-center text-xs">
        <!-- Display the actual cashier name and transaction date -->
        <p>Cashier: {{ $sale->user->username ?? 'Staff' }}</p>
        <p>Date: {{ $sale->created_at->format('Y-m-d h:i A') }}</p>
        <p class="mt-2 font-bold">Thank you for your order!</p>
    </div>

</body>
</html>