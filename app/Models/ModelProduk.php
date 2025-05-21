<?php 

namespace App\Models;

use CodeIgniter\Model;

class ModelProduk extends Model
{
    protected $table = 'tbl_produk';  // Menggunakan nama tabel tbl_produk
    protected $primaryKey = 'id_produk';  // Primary key tabel

    // Field yang diperbolehkan untuk diinsert/update
    protected $allowedFields = ['kode_produk', 'nama_produk', 'harga', 'stok', 'created_at', 'updated_at'];

    // Metode untuk mendapatkan semua produk
    public function getAllProduk()
    {
        return $this->findAll();
    }

    // Metode untuk mendapatkan data produk berdasarkan ID
    public function getProdukById($id)
    {
        return $this->find($id);
    }

    // Metode untuk menyimpan produk baru
    public function createProduk($data)
    {
        return $this->insert($data);
    }

    // Metode untuk mengupdate data produk
    public function updateProduk($id, $data)
    {
        return $this->update($id, $data);
    }

    // Metode untuk menghapus produk
    public function deleteProduk($id)
    {
        return $this->delete($id);
    }

    // Metode untuk mengecek login berdasarkan username
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
}
