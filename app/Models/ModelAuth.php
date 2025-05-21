<?php 

namespace App\Models;

use CodeIgniter\Model;

class ModelAuth extends Model
{
    protected $table = 'tbl_user';  // Menggunakan nama tabel tbl_user
    protected $primaryKey = 'id_user';  // Primary key tabel

    // Field yang diperbolehkan untuk diinsert/update
    protected $allowedFields = ['nama_user', 'username', 'password', 'role'];

    // Metode untuk mendapatkan semua pengguna
    public function getAllUsers()
    {
        return $this->findAll();
    }

    // Metode untuk mendapatkan data pengguna berdasarkan ID
    public function getUserById($id)
    {
        return $this->find($id);
    }

    // Metode untuk menyimpan pengguna baru
    public function createUser($data)
    {
        return $this->insert($data);
    }

    // Metode untuk mengupdate data pengguna
    public function updateUser($id, $data)
    {
        return $this->update($id, $data);
    }

    // Metode untuk menghapus pengguna
    public function deleteUser($id)
    {
        return $this->delete($id);
    }

    // Metode untuk mengecek login berdasarkan username
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
}
