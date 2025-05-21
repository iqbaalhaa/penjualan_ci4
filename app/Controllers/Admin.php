<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\ModelAuth;
use App\Models\ModelProduk;
use App\Models\ModelLaporan;

class Admin extends BaseController
{
    protected $ModelLaporan;
    protected $ModelProduk;

    public function __construct()
    {
        $this->ModelLaporan = new ModelLaporan();
        $this->ModelProduk = new ModelProduk();

        // Pastikan user sudah login dan memiliki role admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            header('Location: ' . base_url('Auth'));
            exit;
        }
    }

    public function index()
    {
        // Ambil data untuk dashboard
        $today = date('Y-m-d');
        $month = date('Y-m');

        // Total penjualan hari ini
        $total_hari_ini = $this->ModelLaporan->select('COALESCE(SUM(total_harga), 0) as total')
                                            ->where('DATE(tanggal)', $today)
                                            ->first()['total'];

        // Total penjualan bulan ini
        $total_bulan_ini = $this->ModelLaporan->select('COALESCE(SUM(total_harga), 0) as total')
                                             ->where('DATE_FORMAT(tanggal, "%Y-%m")', $month)
                                             ->first()['total'];

        // Total produk
        $total_produk = $this->ModelProduk->countAllResults();

        // Jumlah transaksi hari ini
        $transaksi_hari_ini = $this->ModelLaporan->where('DATE(tanggal)', $today)
                                                ->countAllResults();

        // Produk terlaris (5 teratas)
        $produk_terlaris = $this->ModelLaporan->getProdukTerlaris(5);

        // Stok menipis (kurang dari 10)
        $stok_menipis = $this->ModelProduk->where('stok <', 20)
                                         ->orderBy('stok', 'ASC')
                                         ->findAll();

        // Data grafik penjualan (7 hari terakhir)
        $grafik_penjualan = $this->getDataGrafikPenjualan('harian');

        $data = [
            'judul' => 'Dashboard Admin',
            'page' => 'admin/v_dashboard',
            'total_hari_ini' => $total_hari_ini,
            'total_bulan_ini' => $total_bulan_ini,
            'total_produk' => $total_produk,
            'transaksi_hari_ini' => $transaksi_hari_ini,
            'produk_terlaris' => $produk_terlaris,
            'stok_menipis' => $stok_menipis,
            'grafik_penjualan' => $grafik_penjualan
        ];
        
        return view('admin/v_template_admin', $data);
    }

    public function getGrafikPenjualan($periode = 'harian')
    {
        $data = $this->getDataGrafikPenjualan($periode);
        return $this->response->setJSON($data);
    }

    private function getDataGrafikPenjualan($periode = 'harian')
    {
        $labels = [];
        $data = [];

        if ($periode === 'harian') {
            // Data 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($date));

                $result = $this->ModelLaporan->select('COALESCE(SUM(total_harga), 0) as total')
                                           ->where('DATE(tanggal)', $date)
                                           ->first();
                $data[] = (float)$result['total'];
            }
        } else {
            // Data 6 bulan terakhir
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $labels[] = date('M Y', strtotime($month));

                $result = $this->ModelLaporan->select('COALESCE(SUM(total_harga), 0) as total')
                                           ->where('DATE_FORMAT(tanggal, "%Y-%m")', $month)
                                           ->first();
                $data[] = (float)$result['total'];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function Produk()
    {
        $ModelProduk = new ModelProduk();

        $data = [
            'judul' => 'Produk',
            'page' => 'admin/v_produk',
            'produk' => $ModelProduk->findAll() 
        ];
        
        return view('admin/v_template_admin', $data);
    }

    public function tambahProduk()
    {
        // Generate kode produk otomatis
        $kode_produk = $this->generateKodeProduk();
        
        $data = [
            'judul' => 'Tambah Produk',
            'page' => 'admin/v_tambah_produk',
            'kode_produk' => $kode_produk
        ];
        
        return view('admin/v_template_admin', $data);
    }

    private function generateKodeProduk()
    {
        // Ambil kode produk terakhir
        $lastProduct = $this->ModelProduk->orderBy('kode_produk', 'DESC')->first();
        
        if ($lastProduct) {
            // Ambil angka dari kode terakhir
            $lastNumber = (int) substr($lastProduct['kode_produk'], 2);
            $nextNumber = $lastNumber + 1;
        } else {
            // Jika belum ada produk, mulai dari 1
            $nextNumber = 1;
        }
        
        // Format kode baru dengan prefix MS dan padding angka dengan 0
        return 'MS' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function simpanProduk()
    {
        // Debug
        log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));

        // Validasi input
        if (!$this->validate([
            'kode_produk' => 'required',
            'nama_produk' => 'required|min_length[3]',
            'harga' => 'required|numeric|greater_than[0]',
            'stok' => 'required|numeric|greater_than_equal_to[0]'
        ])) {
            return redirect()->back()
                           ->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()))
                           ->withInput();
        }

        try {
            // Siapkan data
            $data = [
                'kode_produk' => $this->request->getPost('kode_produk'),
                'nama_produk' => $this->request->getPost('nama_produk'),
                'harga' => (float)$this->request->getPost('harga'),
                'stok' => (int)$this->request->getPost('stok'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Debug
            log_message('debug', 'Data yang akan disimpan: ' . json_encode($data));

            // Simpan produk menggunakan model
            if (!$this->ModelProduk->insert($data)) {
                throw new \Exception('Gagal menyimpan data produk');
            }

            return redirect()->to('Admin/Produk')
                           ->with('success', 'Produk berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', 'Error saat menyimpan produk: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menyimpan produk: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function editProduk($id)
    {
        // Ambil data produk
        $produk = $this->ModelProduk->find($id);
        
        if (!$produk) {
            return redirect()->to('Admin/Produk')
                           ->with('error', 'Produk tidak ditemukan');
        }

        $data = [
            'judul' => 'Edit Produk',
            'page' => 'admin/v_edit_produk',
            'produk' => $produk
        ];
        
        return view('admin/v_template_admin', $data);
    }

    public function updateProduk($id)
    {
        // Debug
        log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'ID Produk: ' . $id);

        // Validasi input
        if (!$this->validate([
            'nama_produk' => 'required|min_length[3]',
            'harga' => 'required|numeric|greater_than[0]',
            'stok' => 'required|numeric|greater_than_equal_to[0]'
        ])) {
            return redirect()->back()
                           ->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()))
                           ->withInput();
        }

        try {
            // Siapkan data
            $data = [
                'nama_produk' => $this->request->getPost('nama_produk'),
                'harga' => (float)$this->request->getPost('harga'),
                'stok' => (int)$this->request->getPost('stok'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Debug
            log_message('debug', 'Data yang akan diupdate: ' . json_encode($data));

            // Update produk menggunakan query builder
            $db = \Config\Database::connect();
            $result = $db->table('tbl_produk')
                        ->where('id_produk', $id)
                        ->update($data);

            if ($result === false) {
                throw new \Exception('Gagal mengupdate data produk');
            }

            return redirect()->to('Admin/Produk')
                           ->with('success', 'Produk berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', 'Error saat update produk: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal mengupdate produk: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function hapusProduk($id)
    {
        // Cek apakah produk masih digunakan di tabel penjualan
        $penjualan = $this->ModelLaporan->where('id_produk', $id)->first();
        if ($penjualan) {
            return redirect()->to('Admin/Produk')
                           ->with('error', 'Produk tidak dapat dihapus karena masih memiliki data penjualan');
        }

        // Hapus produk
        if ($this->ModelProduk->delete($id)) {
            return redirect()->to('Admin/Produk')
                           ->with('success', 'Produk berhasil dihapus');
        }

        return redirect()->to('Admin/Produk')
                       ->with('error', 'Gagal menghapus produk');
    }

    public function Laporan()
    {
        $data = [
            'judul' => 'Laporan Penjualan',
            'page' => 'admin/v_laporan',
            'produk' => $this->ModelProduk->findAll(),
            'penjualan' => $this->ModelLaporan->getAllPenjualan()
        ];

        return view('admin/v_template_admin', $data);
    }

    public function simpanPenjualan()
    {
        // Debug: tampilkan data POST
        // log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));

        // Validasi input
        if (!$this->validate([
            'id_produk' => 'required|numeric',
            'jumlah' => 'required|numeric|greater_than[0]',
            'total_harga' => 'required|numeric|greater_than[0]'
        ])) {
            return redirect()->back()
                           ->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()))
                           ->withInput();
        }

        // Ambil data dari form
        $id_produk = $this->request->getPost('id_produk');
        $jumlah = (int)$this->request->getPost('jumlah');
        $total_harga = (float)$this->request->getPost('total_harga');

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Cek stok produk
            $produk = $this->ModelProduk->find($id_produk);
            if (!$produk) {
                throw new \Exception('Produk tidak ditemukan');
            }

            // Cek stok mencukupi
            if ($produk['stok'] < $jumlah) {
                throw new \Exception('Stok tidak mencukupi. Stok tersedia: ' . $produk['stok']);
            }

            // Siapkan data untuk disimpan
            $data = [
                'tanggal' => date('Y-m-d'),
                'id_produk' => $id_produk,
                'jumlah' => $jumlah,
                'total_harga' => $total_harga,
                'id_user' => session()->get('id_user')
            ];

            // Debug data yang akan disimpan
            log_message('debug', 'Data yang akan disimpan: ' . json_encode($data));

            // Simpan transaksi menggunakan query builder
            $insert_result = $db->table('tbl_penjualan')->insert($data);
            if (!$insert_result) {
                throw new \Exception('Gagal menyimpan data penjualan');
            }

            // Update stok
            $stok_baru = $produk['stok'] - $jumlah;
            $update_result = $db->table('tbl_produk')
                              ->set('stok', $stok_baru)
                              ->where('id_produk', $id_produk)
                              ->update();

            if (!$update_result) {
                throw new \Exception('Gagal memperbarui stok produk');
            }

            // Commit transaksi
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan transaksi: ' . json_encode($db->error()));
            }

            return redirect()->to('Admin/Laporan')
                           ->with('success', 'Transaksi berhasil disimpan');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $db->transRollback();
            
            log_message('error', 'Error saat menyimpan transaksi: ' . $e->getMessage());
            log_message('error', 'Stack Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                           ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function detailPenjualan($id)
    {
        $penjualan = $this->ModelLaporan->getPenjualanById($id);
        
        return view('admin/v_detail_penjualan', ['penjualan' => $penjualan]);
    }

    public function hapusPenjualan($id = null)
    {
        // Validasi ID
        if ($id === null) {
            return redirect()->to('Admin/Laporan')
                           ->with('error', 'ID Penjualan tidak valid');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Debug: Cek ID yang diterima
            log_message('debug', 'ID Penjualan yang akan dihapus: ' . $id);

            // Ambil data penjualan
            $penjualan = $this->ModelLaporan->find($id);
            
            // Debug: Cek data penjualan
            log_message('debug', 'Data Penjualan: ' . json_encode($penjualan));
            
            if (!$penjualan) {
                throw new \Exception('Data penjualan tidak ditemukan');
            }

            // Ambil data produk
            $produk = $this->ModelProduk->find($penjualan['id_produk']);
            
            // Debug: Cek data produk
            log_message('debug', 'Data Produk: ' . json_encode($produk));
            
            if (!$produk) {
                throw new \Exception('Data produk tidak ditemukan');
            }

            // Debug: Cek perhitungan stok
            $stok_baru = $produk['stok'] + $penjualan['jumlah'];
            log_message('debug', 'Stok Lama: ' . $produk['stok'] . ', Jumlah: ' . $penjualan['jumlah'] . ', Stok Baru: ' . $stok_baru);
            
            // Update stok produk
            $update_result = $db->table('tbl_produk')
                              ->set('stok', $stok_baru)
                              ->where('id_produk', $penjualan['id_produk'])
                              ->update();
            
            // Debug: Cek hasil update
            log_message('debug', 'Hasil Update Stok: ' . json_encode($update_result));
            
            if ($update_result === false) {
                throw new \Exception('Gagal memperbarui stok produk');
            }
            
            // Hapus penjualan
            $delete_result = $db->table('tbl_penjualan')
                              ->where('id_penjualan', $id)
                              ->delete();
            
            // Debug: Cek hasil delete
            log_message('debug', 'Hasil Delete: ' . json_encode($delete_result));
                               
            if ($delete_result === false) {
                throw new \Exception('Gagal menghapus data penjualan');
            }

            // Commit transaksi
            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Database Error: ' . json_encode($error));
                throw new \Exception('Gagal menghapus data: ' . json_encode($error));
            }

            return redirect()->to('Admin/Laporan')
                           ->with('success', 'Data penjualan berhasil dihapus');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $db->transRollback();
            
            log_message('error', 'Error saat menghapus penjualan: ' . $e->getMessage());
            log_message('error', 'Stack Trace: ' . $e->getTraceAsString());
            
            return redirect()->to('Admin/Laporan')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function filterPenjualan()
    {
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $id_produk = $this->request->getGet('id_produk');
        
        $data = [
            'judul' => 'Laporan Penjualan',
            'page' => 'admin/v_laporan',
            'produk' => $this->ModelProduk->findAll(),
            'penjualan' => $this->ModelLaporan->filterPenjualan($tanggal_awal, $tanggal_akhir, $id_produk)
        ];

        return view('admin/v_template_admin', $data);
    }

    public function cetakStruk($id)
    {
        $penjualan = $this->ModelLaporan->getPenjualanById($id);
        
        $data = [
            'penjualan' => $penjualan
        ];

        return view('admin/v_cetak_struk', $data);
    }

    public function Pengguna()
    {
        $ModelAuth = new ModelAuth();

        $data = [
            'judul' => 'Manajemen Pengguna',
            'page' => 'admin/v_Pengguna',
            'pengguna' => $ModelAuth->findAll()
        ];

        return view('admin/v_template_admin', $data);
    }
}
