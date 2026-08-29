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

    <!-- Receipt Printable Area -->
    <div class="receipt">
        <div class="text-center">
            <h2 style="margin: 0; font-size: 16px;">Prince Buffalo Wings</h2>
            <div style="font-size: 11px;">123 Flavor Street, Taguig City</div>
            <div style="font-size: 11px;">Tel: 0912-345-6789</div>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr style="border-bottom: 1px dashed #000;">
                    <th style="text-align: left;">Item</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1x Garlic Parmesan (6pcs)</td>
                    <td class="text-right">₱ 180.00</td>
                </tr>
                <tr>
                    <td>2x Extra Rice</td>
                    <td class="text-right">₱ 50.00</td>
                </tr>
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="total-row">
            <span>Total Amount:</span>
            <span>₱ 230.00</span>
        </div>

        <div class="text-center footer-text">
            <div>Cashier: Keith</div>
            <div>Date: 2026-08-29 08:53</div>
            <div class="font-bold" style="margin-top: 6px;">Thank you for your order!</div>
        </div>
    </div>

</body>
</html>