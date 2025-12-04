<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi Anda</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 700px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .header {
            background-color: #222;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: #fafafa;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .footer {
            background-color: #fafafa;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #777;
        }

        .highlight {
            color: #4CAF50;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="email-container">
    <div class="header">
        <h2>Terima Kasih atas Transaksi Anda!</h2>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $transaction->customer_email ?? 'Pelanggan' }}</strong>,</p>
        <p>Berikut adalah detail pembelian Anda (ID: #{{ $transaction->id }}):</p>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $item)
                <tr>
                    <td>{{ $item->product->title ?? 'Produk Dihapus' }}</td>
                    
                    <td>
                        {{ $item->product->category_product->product_category_name ?? '-' }}
                    </td>
                    
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>{{ $item->quantity }}</td>
                    
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">Total Pembayaran: 
            <span class="highlight">
                Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
            </span>
        </p>

        <p>Terima kasih telah berbelanja bersama kami 🙏</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} RAKESHA Store — Email ini dikirim otomatis, mohon tidak membalas langsung.
    </div>
</div>

</body>
</html>