<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$stats = [
    [
        'label' => 'Harian',
        'value' => $hari,
        'selisih' => $selisihHari,
        'note' => 'kemarin',
        'icon' => 'fa-calendar-day',
        'color' => 'primary'
    ],
    [
        'label' => 'Mingguan',
        'value' => $minggu,
        'selisih' => $selisihMinggu,
        'note' => 'minggu lalu',
        'icon' => 'fa-calendar-week',
        'color' => 'info'
    ],
    [
        'label' => 'Bulanan',
        'value' => $bulan,
        'selisih' => $selisihBulan,
        'note' => 'bulan lalu',
        'icon' => 'fa-calendar-alt',
        'color' => 'warning'
    ],
    [
        'label' => 'Tahunan',
        'value' => $tahun,
        'selisih' => $selisihTahun,
        'note' => 'tahun lalu',
        'icon' => 'fa-calendar',
        'color' => 'danger'
    ],
];
?>

<div class="dashboard-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1">Welcome Back, <strong><?= esc($nama_user) ?></strong> 👋</h3>
            <p class="text-muted mb-0">Dashboard monitoring kerusakan kontainer</p>
        </div>
        <div class="date-display bg-light p-2 rounded">
            <i class="fas fa-clock me-2"></i>
            <span id="current-date-time"><?= date('l, d F Y H:i') ?></span>
        </div>
    </div>
</div>

<!-- Statistik Card -->
<div class="row g-3 mb-4">
    <?php foreach ($stats as $stat): ?>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2"><?= $stat['label'] ?></h6>
                            <h2 class="mb-0"><?= $stat['value'] ?></h2>
                        </div>
                        <div class="icon-circle bg-<?= $stat['color'] ?>-soft">
                            <i class="fas <?= $stat['icon'] ?> text-<?= $stat['color'] ?>"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-<?= $stat['selisih'] > 0 ? 'danger' : 'success' ?>-soft text-<?= $stat['selisih'] > 0 ? 'danger' : 'success' ?>">
                            <i class="fas fa-arrow-<?= $stat['selisih'] >= 0 ? 'up' : 'down' ?> me-1"></i>
                            <?= $stat['selisih'] >= 0 ? '+' : '' ?><?= $stat['selisih'] ?>
                        </span>
                        <span class="text-muted small ms-2">dari <?= $stat['note'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Chart Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Grafik Jenis Kerusakan Kontainer</h5>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chartFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Tahun <?= date('Y') ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="chartFilterDropdown">
                    <li><a class="dropdown-item" href="#">Tahun 2023</a></li>
                    <li><a class="dropdown-item" href="#">Tahun 2022</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body pt-0">
        <!-- Loading -->
        <div id="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data grafik...</p>
        </div>

        <!-- Chart -->
        <div class="chart-container" style="height: 400px; display: none;" id="chartContainer">
            <canvas id="damageChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0">Aktivitas Terkini</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Laporan</th>
                        <th>Jenis Kerusakan</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#CTR-2023-056</td>
                        <td>Penyok</td>
                        <td>Area Parkir A</td>
                        <td>15 Jun 2023</td>
                        <td><span class="badge bg-warning">Dalam Proses</span></td>
                    </tr>
                    <tr>
                        <td>#CTR-2023-055</td>
                        <td>Sobek</td>
                        <td>Dermaga 2</td>
                        <td>14 Jun 2023</td>
                        <td><span class="badge bg-success">Selesai</span></td>
                    </tr>
                    <tr>
                        <td>#CTR-2023-054</td>
                        <td>Lubang</td>
                        <td>Gudang B</td>
                        <td>13 Jun 2023</td>
                        <td><span class="badge bg-success">Selesai</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #164ec0;
        --primary-soft: rgba(22, 78, 192, 0.1);
        --info: #17a2b8;
        --info-soft: rgba(23, 162, 184, 0.1);
        --warning: #ffc107;
        --warning-soft: rgba(255, 193, 7, 0.1);
        --danger: #f25a5a;
        --danger-soft: rgba(242, 90, 90, 0.1);
        --success: #28a745;
        --success-soft: rgba(40, 167, 69, 0.1);
    }

    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 12px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .bg-primary-soft {
        background-color: var(--primary-soft);
    }

    .bg-info-soft {
        background-color: var(--info-soft);
    }

    .bg-warning-soft {
        background-color: var(--warning-soft);
    }

    .bg-danger-soft {
        background-color: var(--danger-soft);
    }

    .bg-success-soft {
        background-color: var(--success-soft);
    }

    .shadow-hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .dashboard-header {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
    }

    .date-display {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .chart-container {
        position: relative;
    }
</style>

<script>
    // Update current date and time
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        document.getElementById('current-date-time').textContent = now.toLocaleDateString('id-ID', options);
    }

    setInterval(updateDateTime, 60000);
    updateDateTime();

    // Chart initialization
    let myChart;

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('damageChart').getContext('2d');

        // Simulate loading
        setTimeout(() => {
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Damage',
                        data: <?= json_encode($grafikData['Damage']) ?>,
                        borderColor: 'rgba(22, 78, 192, 0.8)',
                        backgroundColor: 'rgba(22, 78, 192, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: 'rgba(22, 78, 192, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.8)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: <?= $maxValue ?>,
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });

            document.getElementById('loading').style.display = 'none';
            document.getElementById('chartContainer').style.display = 'block';
        }, 800);
    });
</script>

<?= $this->endSection() ?>