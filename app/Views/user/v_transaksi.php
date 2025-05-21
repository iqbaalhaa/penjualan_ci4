<?php
$session = session();
?>
<div class="container-fluid">
    <div class="row">
        <!-- Form Transaksi Baru -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Transaksi Baru</h4>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('User/simpanPenjualan') ?>" method="POST" id="formTransaksi">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="id_produk" class="form-label">Pilih Produk</label>
                            <select class="form-select" id="id_produk" name="id_produk" required>
                                <option value="">Pilih Produk...</option>
                                <?php foreach($produk as $p): ?>
                                    <option value="<?= $p['id_produk'] ?>" data-harga="<?= $p['harga'] ?>">
                                        <?= $p['nama_produk'] ?> - Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga Satuan</label>
                            <input type="text" class="form-control" id="harga" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="total" class="form-label">Total Harga</label>
                            <input type="text" class="form-control" id="total" readonly>
                            <input type="hidden" id="total_harga" name="total_harga">
                        </div>

                        <input type="hidden" name="id_user" value="<?= $session->get('id_user') ?>">
                        <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Penjualan -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Daftar Penjualan</h4>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir">
                        </div>
                        <div class="col-md-4">
                            <label for="filter_produk" class="form-label">Produk</label>
                            <select class="form-select" id="filter_produk">
                                <option value="">Semua Produk</option>
                                <?php foreach($produk as $p): ?>
                                    <option value="<?= $p['id_produk'] ?>"><?= $p['nama_produk'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Tabel Penjualan -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabelPenjualan">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                    <th>Kasir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($penjualan as $p): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                                    <td><?= $p['nama_produk'] ?></td>
                                    <td><?= $p['jumlah'] ?></td>
                                    <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                                    <td><?= $p['nama_user'] ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="lihatDetail(<?= $p['id_penjualan'] ?>)">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="hapusPenjualan(<?= $p['id_penjualan'] ?>)">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm" onclick="cetakStruk(<?= $p['id_penjualan'] ?>)">
                                            <i class="mdi mdi-printer"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Penjualan -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Isi detail akan dimuat di sini -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formTransaksi = document.getElementById('formTransaksi');
    const selectProduk = document.getElementById('id_produk');
    const inputJumlah = document.getElementById('jumlah');
    const inputHarga = document.getElementById('harga');
    const inputTotal = document.getElementById('total');
    const inputTotalHarga = document.getElementById('total_harga');

    // Hitung total otomatis
    function hitungTotal() {
        const selectedOption = selectProduk.options[selectProduk.selectedIndex];
        const harga = selectedOption ? parseFloat(selectedOption.dataset.harga) : 0;
        const jumlah = parseFloat(inputJumlah.value) || 0;
        const total = harga * jumlah;

        inputHarga.value = harga.toLocaleString('id-ID', {style: 'currency', currency: 'IDR'});
        inputTotal.value = total.toLocaleString('id-ID', {style: 'currency', currency: 'IDR'});
        // Simpan nilai numerik untuk dikirim ke server
        inputTotalHarga.value = total;
    }

    selectProduk.addEventListener('change', hitungTotal);
    inputJumlah.addEventListener('input', hitungTotal);

    // Validasi form
    formTransaksi.addEventListener('submit', function(e) {
        const selectedOption = selectProduk.options[selectProduk.selectedIndex];
        if (!selectedOption.value) {
            e.preventDefault();
            alert('Silakan pilih produk terlebih dahulu!');
            return;
        }

        const jumlah = parseInt(inputJumlah.value);
        if (jumlah <= 0) {
            e.preventDefault();
            alert('Jumlah harus lebih dari 0!');
            return;
        }
    });

    // Inisialisasi DataTables
    $('#tabelPenjualan').DataTable();
});

// Fungsi untuk melihat detail penjualan
function lihatDetail(id) {
    fetch(`<?= base_url('User/laporan/detail/') ?>${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('detailContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        });
}

// Fungsi untuk menghapus penjualan
function hapusPenjualan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = `<?= base_url('User/hapusPenjualan') ?>${id}`;
    }
}

// Fungsi untuk mencetak struk
function cetakStruk(id) {
    window.open(`<?= base_url('User/laporan/cetak/') ?>${id}`, '_blank');
}

// Filter penjualan
document.getElementById('tanggal_awal').addEventListener('change', filterPenjualan);
document.getElementById('tanggal_akhir').addEventListener('change', filterPenjualan);
document.getElementById('filter_produk').addEventListener('change', filterPenjualan);

function filterPenjualan() {
    const tanggal_awal = document.getElementById('tanggal_awal').value;
    const tanggal_akhir = document.getElementById('tanggal_akhir').value;
    const id_produk = document.getElementById('filter_produk').value;

    window.location.href = `<?= base_url('User/laporan/filter') ?>?tanggal_awal=${tanggal_awal}&tanggal_akhir=${tanggal_akhir}&id_produk=${id_produk}`;
}
</script>
