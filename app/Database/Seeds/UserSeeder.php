<?php 

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nama_user' => 'User Ayek',
            'username'  => 'ayek',
            'password'  => password_hash('ayek123', PASSWORD_DEFAULT), // Hash password
            'role'      => 'user'
        ];

        // Insert data ke tbl_user
        $this->db->table('tbl_user')->insert($data);
    }
}
