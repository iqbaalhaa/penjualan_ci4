<div class="container-fluid">
    <h3 class="mt-3">Data Produk</h3>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <button class="btn btn-primary mb-3" onclick="location.href='<?= base_url('Admin/tambahProduk') ?>'">Tambah Produk</button>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tabelProduk">
                    <thead>
                        <tr>
                            <th style="width : 5%">No</th>
                            <th style="width : 10%">Kode Produk</th>
                            <th style="width : 35%">Nama Produk</th>
                            <th style="width : 20%">Harga</th>
                            <th style="width : 10%">Stok</th>
                            <th style="width : 250%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; foreach($produk as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $p['kode_produk'] ?></td>
                            <td><?= $p['nama_produk'] ?></td>
                            <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                            <td><?= $p['stok'] ?></td>
                            <td>
                                <a href="<?= base_url('Admin/editProduk'.$p['id_produk']) ?>" class="btn btn-warning btn-sm">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </a>
                                <a href="<?= base_url('Admin/hapusProduk'.$p['id_produk']) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    <i class="mdi mdi-delete"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi DataTables
    $('#tabelProduk').DataTable();
});
</script>
