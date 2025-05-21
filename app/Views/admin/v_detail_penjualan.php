<div class="table-responsive">
    <table class="table">
        <tr>
            <th width="30%">ID Transaksi</th>
            <td><?= $penjualan['id_penjualan'] ?></td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td><?= date('d/m/Y H:i:s', strtotime($penjualan['created_at'])) ?></td>
        </tr>
        <tr>
            <th>Nama Produk</th>
            <td><?= $penjualan['nama_produk'] ?></td>
        </tr>
        <tr>
            <th>Harga Satuan</th>
            <td>Rp <?= number_format($penjualan['harga'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td><?= $penjualan['jumlah'] ?></td>
        </tr>
        <tr>
            <th>Total Harga</th>
            <td>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th>Kasir</th>
            <td><?= $penjualan['nama_user'] ?></td>
        </tr>
    </table>
</div>

<div class="text-center mt-3">
    <button class="btn btn-success" onclick="window.print()">
        <i class="mdi mdi-printer"></i> Cetak
    </button>
</div> 