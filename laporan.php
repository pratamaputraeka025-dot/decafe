<?php
include 'proses/connect.php';
date_default_timezone_set('Asia/Jakarta');

// Query untuk laporan per tahun
$query_tahun = mysqli_query($conn, "SELECT 
    YEAR(tb_bayar.waktu_bayar) as tahun,
    COUNT(DISTINCT tb_order.id_order) as total_transaksi,
    SUM(harga*jumlah) AS total_penjualan
    FROM tb_order
    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
    LEFT JOIN tb_daftar_menu ON tb_daftar_menu.id = tb_list_order.menu
    JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
    GROUP BY YEAR(tb_bayar.waktu_bayar)
    ORDER BY tahun DESC");

$result_tahun = [];
while ($record = mysqli_fetch_array($query_tahun)) {
    $result_tahun[] = $record;
}

// Query untuk laporan per bulan (tahun ini)
$tahun_sekarang = date('Y');
$query_bulan = mysqli_query($conn, "SELECT 
    MONTH(tb_bayar.waktu_bayar) as bulan,
    YEAR(tb_bayar.waktu_bayar) as tahun,
    COUNT(DISTINCT tb_order.id_order) as total_transaksi,
    SUM(harga*jumlah) AS total_penjualan
    FROM tb_order
    LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
    LEFT JOIN tb_daftar_menu ON tb_daftar_menu.id = tb_list_order.menu
    JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
    WHERE YEAR(tb_bayar.waktu_bayar) = '$tahun_sekarang'
    GROUP BY MONTH(tb_bayar.waktu_bayar), YEAR(tb_bayar.waktu_bayar)
    ORDER BY bulan ASC");

$result_bulan = [];
while ($record = mysqli_fetch_array($query_bulan)) {
    $result_bulan[] = $record;
}

// Array nama bulan
$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Siapkan data untuk grafik per tahun
$data_tahun = [];
$data_penjualan_tahun = [];
foreach ($result_tahun as $row) {
    $data_tahun[] = "'" . $row['tahun'] . "'";
    $data_penjualan_tahun[] = $row['total_penjualan'];
}

// Siapkan data untuk grafik per bulan
$data_bulan_label = [];
$data_penjualan_bulan = [];
foreach ($result_bulan as $row) {
    $data_bulan_label[] = "'" . $nama_bulan[$row['bulan']] . "'";
    $data_penjualan_bulan[] = $row['total_penjualan'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Laporan Penjualan</h5>
        </div>
        <div class="card-body">
            
            <!-- Laporan Per Tahun -->
            <div class="mb-5">
                <h5 class="mb-3"><i class="bi bi-calendar"></i> Laporan Penjualan Per Tahun</h5>
                
                <?php if (empty($result_tahun)) { ?>
                    <div class="alert alert-info">Belum ada data penjualan</div>
                <?php } else { ?>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tahun</th>
                                    <th scope="col">Total Transaksi</th>
                                    <th scope="col">Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $grand_total_tahun = 0;
                                foreach ($result_tahun as $row) {
                                    $grand_total_tahun += $row['total_penjualan'];
                                ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td><strong><?php echo $row['tahun'] ?></strong></td>
                                        <td><?php echo $row['total_transaksi'] ?> transaksi</td>
                                        <td class="text-success fw-bold">Rp <?php echo number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="table-secondary">
                                    <td colspan="3" class="text-end fw-bold">GRAND TOTAL:</td>
                                    <td class="text-success fw-bold">Rp <?php echo number_format($grand_total_tahun, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Grafik Per Tahun -->
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-bar-chart-fill"></i> Grafik Penjualan Per Tahun
                        </div>
                        <div class="card-body">
                            <canvas id="chartTahun"></canvas>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <hr class="my-5">

            <!-- Laporan Per Bulan -->
            <div>
                <h5 class="mb-3"><i class="bi bi-calendar3"></i> Laporan Penjualan Per Bulan (Tahun <?php echo $tahun_sekarang ?>)</h5>
                
                <?php if (empty($result_bulan)) { ?>
                    <div class="alert alert-info">Belum ada data penjualan untuk tahun <?php echo $tahun_sekarang ?></div>
                <?php } else { ?>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Bulan</th>
                                    <th scope="col">Total Transaksi</th>
                                    <th scope="col">Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $grand_total_bulan = 0;
                                foreach ($result_bulan as $row) {
                                    $grand_total_bulan += $row['total_penjualan'];
                                ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td><strong><?php echo $nama_bulan[$row['bulan']] ?> <?php echo $row['tahun'] ?></strong></td>
                                        <td><?php echo $row['total_transaksi'] ?> transaksi</td>
                                        <td class="text-success fw-bold">Rp <?php echo number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="table-secondary">
                                    <td colspan="3" class="text-end fw-bold">TOTAL TAHUN INI:</td>
                                    <td class="text-success fw-bold">Rp <?php echo number_format($grand_total_bulan, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Grafik Per Bulan -->
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <i class="bi bi-graph-up"></i> Grafik Penjualan Per Bulan (<?php echo $tahun_sekarang ?>)
                        </div>
                        <div class="card-body">
                            <canvas id="chartBulan"></canvas>
                        </div>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<script>
// Grafik Per Tahun
<?php if (!empty($result_tahun)) { ?>
const ctxTahun = document.getElementById('chartTahun');
new Chart(ctxTahun, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', $data_tahun); ?>],
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: [<?php echo implode(',', $data_penjualan_tahun); ?>],
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
<?php } ?>

// Grafik Per Bulan
<?php if (!empty($result_bulan)) { ?>
const ctxBulan = document.getElementById('chartBulan');
new Chart(ctxBulan, {
    type: 'line',
    data: {
        labels: [<?php echo implode(',', $data_bulan_label); ?>],
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: [<?php echo implode(',', $data_penjualan_bulan); ?>],
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
<?php } ?>
</script>