<!DOCTYPE html>
<html>
<head>
    <title>Struk Penjualan #<?= $penjualan['id_penjualan'] ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .struk {
            width: 80mm;
            margin: 0 auto;
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .info {
            margin: 5px 0;
        }
        table {
            width: 100%;
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        td {
            padding: 5px 0;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .total {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
        }
        @media print {
            body {
                width: 80mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="struk">
        <div class="header">
            <div class="title">MS Store</div>
            <div class="info">Jl. Contoh No. 123</div>
            <div class="info">Telp: (021) 1234567</div>
        </div>

        <div class="info">
            No: #<?= $penjualan['id_penjualan'] ?><br>
            Tanggal: <?= date('d/m/Y H:i:s', strtotime($penjualan['created_at'])) ?><br>
            Kasir: <?= $penjualan['nama_user'] ?>
        </div>

        <table>
            <tr>
                <td class="text-left"><?= $penjualan['nama_produk'] ?></td>
            </tr>
            <tr>
                <td class="text-left"><?= $penjualan['jumlah'] ?> x Rp <?= number_format($penjualan['harga'], 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($penjualan['total_harga'], 0, ',', '.') ?></td>
            </tr>
        </table>

        <div class="total">
            Total: Rp <?= number_format($penjualan['total_harga'], 0, ',', '.') ?>
        </div>

        <div class="footer">
            Terima kasih atas kunjungan Anda<br>
            Barang yang sudah dibeli tidak dapat dikembalikan
        </div>
    </div>

    <div class="text-center mt-3 no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">
            Cetak Ulang
        </button>
    </div>
</body>
</html> 