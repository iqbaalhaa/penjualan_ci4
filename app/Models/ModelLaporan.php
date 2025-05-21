<?php 

namespace App\Models;

use CodeIgniter\Model;

class ModelLaporan extends Model
{
    protected $table = 'tbl_penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $allowedFields = [
        'tanggal',
        'id_produk',
        'jumlah',
        'total_harga',
        'id_user'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Mendapatkan semua data penjualan dengan detail produk dan user
    public function getAllPenjualan()
    {
        return $this->select('tbl_penjualan.*, tbl_produk.nama_produk, tbl_user.nama_user')
                    ->join('tbl_produk', 'tbl_produk.id_produk = tbl_penjualan.id_produk')
                    ->join('tbl_user', 'tbl_user.id_user = tbl_penjualan.id_user')
                    ->orderBy('tbl_penjualan.created_at', 'DESC')
                    ->findAll();
    }

    // Mendapatkan detail penjualan berdasarkan ID
    public function getPenjualanById($id)
    {
        return $this->select('tbl_penjualan.*, tbl_produk.nama_produk, tbl_produk.harga, tbl_user.nama_user')
                    ->join('tbl_produk', 'tbl_produk.id_produk = tbl_penjualan.id_produk')
                    ->join('tbl_user', 'tbl_user.id_user = tbl_penjualan.id_user')
                    ->where('tbl_penjualan.id_penjualan', $id)
                    ->first();
    }

    // Filter penjualan berdasarkan parameter
    public function filterPenjualan($tanggal_awal = null, $tanggal_akhir = null, $id_produk = null, $id_user = null)
    {
        $builder = $this->select('tbl_penjualan.*, tbl_produk.nama_produk, tbl_user.nama_user')
                       ->join('tbl_produk', 'tbl_produk.id_produk = tbl_penjualan.id_produk')
                       ->join('tbl_user', 'tbl_user.id_user = tbl_penjualan.id_user');

        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('tanggal >=', $tanggal_awal)
                   ->where('tanggal <=', $tanggal_akhir);
        }

        if ($id_produk) {
            $builder->where('tbl_penjualan.id_produk', $id_produk);
        }

        if ($id_user) {
            $builder->where('tbl_penjualan.id_user', $id_user);
        }

        return $builder->orderBy('tbl_penjualan.created_at', 'DESC')->findAll();
    }

    // Mendapatkan total penjualan per hari
    public function getTotalPenjualanHarian($tanggal)
    {
        return $this->selectSum('total_harga')
                    ->where('DATE(tanggal)', $tanggal)
                    ->first();
    }

    // Mendapatkan produk terlaris
    public function getProdukTerlaris($limit = 5)
    {
        return $this->select('
                tbl_produk.nama_produk, 
                SUM(tbl_penjualan.jumlah) as total_terjual,
                SUM(tbl_penjualan.total_harga) as total_penjualan
            ')
            ->join('tbl_produk', 'tbl_produk.id_produk = tbl_penjualan.id_produk')
            ->groupBy('tbl_penjualan.id_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->find();
    }
} 