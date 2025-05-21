<?php 

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nama_user' => 'Admin',
            'username'  => 'admin',
            'password'  => password_hash('admin123', PASSWORD_DEFAULT), // Hash password
            'role'      => 'admin'
        ];

        // Insert data ke tbl_user
        $this->db->table('tbl_user')->insert($data);
    }
}
