<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\ModelAuth;
use App\Models\ModelProduk;
use App\Models\ModelLaporan;

class User extends BaseController
{
    protected $ModelLaporan;
    protected $ModelProduk;

    public function __construct()
    {
        $this->ModelLaporan = new ModelLaporan();
        $this->ModelProduk = new ModelProduk();

        // Pastikan user sudah login dan memiliki role admin
        if (!session()->get('logged_in') || session()->get('role') !== 'user') {
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
            'judul' => 'Dashboard User',
            'page' => 'user/v_dashboard',
            'total_hari_ini' => $total_hari_ini,
            'total_bulan_ini' => $total_bulan_ini,
            'total_produk' => $total_produk,
            'transaksi_hari_ini' => $transaksi_hari_ini,
            'produk_terlaris' => $produk_terlaris,
            'stok_menipis' => $stok_menipis,
            'grafik_penjualan' => $grafik_penjualan
        ];
        
        return view('user/v_template_user', $data);
    }

    public function Produk()
    {
        $ModelProduk = new ModelProduk();

        $data = [
            'judul' => 'Produk',
            'page' => 'user/v_produk',
            'produk' => $ModelProduk->findAll() 
        ];
        
        return view('user/v_template_user', $data);
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

    public function Transaksi()
    {
        $data = [
            'judul' => 'Transaksi',
            'page' => 'user/v_transaksi',
            'produk' => $this->ModelProduk->findAll(),
            'penjualan' => $this->ModelLaporan->getAllPenjualan()
        ];

        return view('user/v_template_user', $data);
    }

    public function simpanPenjualan()
    {
        // Debug: tampilkan data POST
        log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));

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
                'tanggal' => date('Y-m-d H:i:s'),
                'id_produk' => $id_produk,
                'jumlah' => $jumlah,
                'total_harga' => $total_harga,
                'id_user' => session()->get('id_user')
            ];

            // Debug data yang akan disimpan
            log_message('debug', 'Data yang akan disimpan: ' . json_encode($data));

            // Simpan transaksi menggunakan model
            if (!$this->ModelLaporan->insert($data)) {
                throw new \Exception('Gagal menyimpan data penjualan');
            }

            // Update stok menggunakan model
            $stok_baru = $produk['stok'] - $jumlah;
            $update_data = [
                'stok' => $stok_baru,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->ModelProduk->update($id_produk, $update_data)) {
                throw new \Exception('Gagal memperbarui stok produk');
            }

            // Commit transaksi
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan transaksi: ' . json_encode($db->error()));
            }

            return redirect()->to('User/Transaksi')
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

    public function hapusPenjualan($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Ambil data penjualan
            $penjualan = $this->ModelLaporan->find($id);
            if (!$penjualan) {
                throw new \Exception('Data penjualan tidak ditemukan');
            }

            // Kembalikan stok produk
            $produk = $this->ModelProduk->find($penjualan['id_produk']);
            if ($produk) {
                $stok_baru = $produk['stok'] + $penjualan['jumlah'];
                $this->ModelProduk->update($penjualan['id_produk'], ['stok' => $stok_baru]);
            }

            // Hapus data penjualan
            if (!$this->ModelLaporan->delete($id)) {
                throw new \Exception('Gagal menghapus data penjualan');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus transaksi');
            }

            return redirect()->to('User/Transaksi')
                           ->with('success', 'Data penjualan berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('User/Transaksi')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function cetakStruk($id)
    {
        $penjualan = $this->ModelLaporan->getPenjualanById($id);
        return view('user/v_struk', ['penjualan' => $penjualan]);
    }

    public function filterPenjualan()
    {
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $id_produk = $this->request->getGet('id_produk');

        $data = [
            'judul' => 'Transaksi',
            'page' => 'user/v_transaksi',
            'produk' => $this->ModelProduk->findAll(),
            'penjualan' => $this->ModelLaporan->getFilteredPenjualan($tanggal_awal, $tanggal_akhir, $id_produk)
        ];

        return view('user/v_template_user', $data);
    }
}
