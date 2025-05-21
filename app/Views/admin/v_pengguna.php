<div class="container-fluid">
    <h2>Manajemen User</h2>
    <a href="<?= base_url('admin/users/add') ?>" class="btn btn-primary mb-3">Tambah User</a>
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($pengguna as $p): ?>
            <tr>
                <td><?= $p['id_user'] ?></td>
                <td><?= $p['nama_user'] ?></td>
                <td><?= $p['username'] ?></td>
                <td><?= $p['role'] ?></td>
                <td>
                    <a href="<?= base_url('admin/pengguna/edit/'.$p['id_user']) ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="<?= base_url('admin/pengguna/delete/'.$p['id_user']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
