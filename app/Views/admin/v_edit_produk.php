<div class="container-fluid">
    <h3 class="mt-3">Edit Produk</h3>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('Admin/updateProduk'.$produk['id_produk']) ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" 
                           value="<?= old('nama_produk', $produk['nama_produk']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga" name="harga" 
                               value="<?= old('harga', $produk['harga']) ?>" required min="0" step="100">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" 
                           value="<?= old('stok', $produk['stok']) ?>" required min="0" step="1">
                    <div class="form-text">Jumlah stok saat ini</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Produk</button>
                    <a href="<?= base_url('Admin/Produk') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div> 