<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Prince Buffalo Wings</title>
    <!-- We still load Tailwind for basic styling -->
    @vite(['resources/css/app.css'])
    
    <style>
        /* This CSS only applies when the browser is printing */
        @media print {
            /* Hides the print button on the actual paper */
            .no-print {
                display: none !important;
            }
            /* Removes margins and sets the width to a standard 80mm thermal roll */
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
                font-family: monospace; /* Monospace looks like a real receipt */
            }
        }
    </style>
</head>
<body class="bg-white text-black p-4 max-w-[80mm] mx-auto text-sm">

    <!-- Action Bar (Hidden during printing) -->
    <div class="mb-4 no-print text-center">
        <button onclick="window.print()" class="bg-red-900 hover:bg-red-800 text-white font-bold py-2 px-6 rounded transition">
            Print Receipt
        </button>
        <a href="{{ route('sales.store') }}" class="text-blue-600 ml-4 underline">Back to POS</a>
    </div>

    <!-- Receipt Content -->
    <div class="text-center mb-4">
        <h1 class="font-bold text-xl">Prince Buffalo Wings</h1>
        <p class="text-xs">123 Flavor Street, Taguig City</p>
        <p class="text-xs">Tel: 0912-345-6789</p>
    </div>

    <div class="border-t border-b border-black py-2 mb-2 border-dashed">
        <div class="flex justify-between text-xs font-bold mb-1">
            <span>Item</span>
            <span>Total</span>
        </div>
        
        <!-- Example Loop (You will pass actual cart data here later) -->
        <div class="flex justify-between text-xs mb-1">
            <span>1x Garlic Parmesan (6pcs)</span>
            <span>₱ 180.00</span>
        </div>
        <div class="flex justify-between text-xs mb-1">
            <span>2x Extra Rice</span>
            <span>₱ 50.00</span>
        </div>
    </div>

    <div class="flex justify-between font-bold text-sm mb-4">
        <span>Total Amount:</span>
        <span>₱ 230.00</span>
    </div>

    <div class="text-center text-xs">
        <p>Cashier: Keith</p>
        <p>Date: {{ now()->format('Y-m-d H:i') }}</p>
        <p class="mt-2 font-bold">Thank you for your order!</p>
    </div>

</body>
</html>