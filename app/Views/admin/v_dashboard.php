<div class="container-fluid">
    <!-- Statistik Cards -->
    <div class="row mb-3">
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Penjualan Hari Ini</h6>
                            <h4 class="mb-0">Rp <?= number_format($total_hari_ini, 0, ',', '.') ?></h4>
                        </div>
                        <i class="mdi mdi-calendar-today h1 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Penjualan Bulan Ini</h6>
                            <h4 class="mb-0">Rp <?= number_format($total_bulan_ini, 0, ',', '.') ?></h4>
                        </div>
                        <i class="mdi mdi-calendar h1 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Produk</h6>
                            <h3 class="mb-0"><?= number_format($total_produk, 0, ',', '.') ?></h3>
                        </div>
                        <i class="mdi mdi-package-variant h1 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Transaksi Hari Ini</h6>
                            <h3 class="mb-0"><?= number_format($transaksi_hari_ini, 0, ',', '.') ?></h3>
                        </div>
                        <i class="mdi mdi-cart h1 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Tabel -->
    <div class="row">
        <!-- Grafik Penjualan -->
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Grafik Penjualan</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-primary" onclick="updateChart('harian')">Harian</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('bulanan')">Bulanan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 300px">
                        <canvas id="penjualanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Informasi -->
        <div class="col-xl-4 col-lg-5">
            <!-- Produk Terlaris -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Produk Terlaris</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($produk_terlaris as $p): ?>
                                <tr>
                                    <td><?= $p['nama_produk'] ?></td>
                                    <td class="text-end"><?= number_format($p['total_terjual'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Stok Menipis -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Stok Menipis</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stok_menipis as $s): ?>
                                <tr>
                                    <td><?= $s['nama_produk'] ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-danger"><?= $s['stok'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data dari PHP
const penjualanData = <?= json_encode($grafik_penjualan) ?>;

// Inisialisasi Chart
let penjualanChart;

function initChart() {
    const ctx = document.getElementById('penjualanChart').getContext('2d');
    penjualanChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: penjualanData.labels,
            datasets: [{
                label: 'Total Penjualan',
                data: penjualanData.data,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function updateChart(periode) {
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('btn-primary', 'btn-outline-primary');
        if (btn.textContent.toLowerCase().includes(periode)) {
            btn.classList.add('btn-primary');
        } else {
            btn.classList.add('btn-outline-primary');
        }
    });

    // Ambil data sesuai periode
    fetch(`<?= base_url('Admin/getGrafikPenjualan/') ?>${periode}`)
        .then(response => response.json())
        .then(data => {
            penjualanChart.data.labels = data.labels;
            penjualanChart.data.datasets[0].data = data.data;
            penjualanChart.update();
        });
}

// Inisialisasi chart saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    initChart();
});
</script>