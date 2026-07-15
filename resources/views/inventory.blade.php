<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBWSIS | Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { cranberry: '#6b1225', } } } }
    </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen">

    <aside class="w-64 bg-black text-white flex flex-col">
        <div class="p-6 text-center border-b border-gray-800">
            <h1 class="text-2xl font-bold text-cranberry tracking-wider">PBWSIS</h1>
            <p class="text-xs text-gray-400 mt-1">Owner Panel</p>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/" class="block px-4 py-2 hover:bg-gray-800 rounded text-gray-300">Dashboard</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-800 rounded text-gray-300">Point of Sale</a>
            <a href="/inventory" class="block px-4 py-2 bg-cranberry rounded text-white font-medium">Inventory</a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col">
        <header class="h-16 bg-cranberry text-white flex items-center px-8 shadow-md">
            <h2 class="text-xl font-semibold">Product Inventory</h2>
        </header>

        <div class="p-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm">
                            <th class="py-3 px-6 border-b">ID</th>
                            <th class="py-3 px-6 border-b">Product Name</th>
                            <th class="py-3 px-6 border-b">Price</th>
                            <th class="py-3 px-6 border-b">Stock</th>
                            <th class="py-3 px-6 border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50 border-b border-gray-100">
                            <td class="py-3 px-6 text-gray-500">{{ $product->product_id }}</td>
                            <td class="py-3 px-6 font-medium">{{ $product->product_name }}</td>
                            <td class="py-3 px-6">₱{{ number_format($product->price, 2) }}</td>
                            <td class="py-3 px-6">{{ $product->stock_quantity }}</td>
                            <td class="py-3 px-6">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">{{ $product->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>