<?php
include 'proses/connect.php';

$query = mysqli_query($conn, "SELECT *, SUM(harga*jumlah) AS harganya, tb_order.waktu_order FROM tb_list_order
LEFT JOIN tb_order ON tb_order.id_order = tb_list_order.kode_order
LEFT JOIN tb_daftar_menu ON tb_daftar_menu.id = tb_list_order.menu
LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
GROUP BY id_list_order
HAVING tb_list_order.kode_order = $_GET[order]");

$kode = $_GET['order'];
$meja = $_GET['meja'];
$pelanggan = $_GET['pelanggan'];
while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
    // $kode = $record['id_order'];
    // $meja = $record['meja'];
    // $pelanggan = $record['pelanggan'];
}
$select_menu = mysqli_query($conn, "SELECT id,nama_menu FROM tb_daftar_menu");
?>
<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Order Item
        </div>
        <div class="card-body">
            <a href="order" class="btn btn-info mb-3"><i class="bi bi-box-arrow-in-left"></i></a>
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-floating mb-3">
                        <input disabled type="text" class="form-control " id="kodeorder" value="<?php echo $kode; ?>">
                        <label for="uploadFoto">Kode Order</label>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-floating mb-3">
                        <input disabled type="text" class="form-control " id="meja" value="<?php echo $meja; ?>">
                        <label for="uploadFoto">Meja</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-floating mb-3">
                        <input disabled type="text" class="form-control " id="pelanggan" value="<?php echo $pelanggan; ?>">
                        <label for="uploadFoto">Pelanggan</label>
                    </div>
                </div>
            </div>

            <!-- Ganti bagian Modal Tambah item Baru dengan kode berikut -->
<!-- Modal Tambah item Baru -->
<div class="modal fade" id="tambahItem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscren-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Menu makanan dan minuman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate action="proses/proses_input_orderitem.php" method="POST">
                    <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                    <input type="hidden" name="meja" value="<?php echo $meja ?>">
                    <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-floating mb-3">
                                <select class="form-select" name="menu" id="menuSelect" required onchange="updateStokInfo()">
                                    <option selected hidden value="">Pilih Menu</option>
                                    <?php
                                    // Update query untuk mendapatkan stok
                                    $select_menu_with_stok = mysqli_query($conn, "SELECT id, nama_menu, stok FROM tb_daftar_menu");
                                    foreach ($select_menu_with_stok as $value) {
                                        echo "<option value='$value[id]' data-stok='$value[stok]'>$value[nama_menu] (Stok: $value[stok])</option>";
                                    }
                                    ?>
                                </select>
                                <label for="menu">Menu Makanan/Minuman</label>
                                <div class="invalid-feedback">
                                    Pilih Menu.
                                </div>
                            </div>
                            <div id="stokInfo" class="text-muted small mb-2" style="display:none;">
                                <i class="bi bi-info-circle"></i> Stok tersedia: <span id="stokValue">0</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="jumlahInput" placeholder="Jumlah Porsi" name="jumlah" required min="1" max="999">
                                <label for="floatingInput">Jumlah Porsi</label>
                                <div class="invalid-feedback">
                                    Masukan Jumlah Porsi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput" placeholder="Catatan" name="catatan">
                                <label for="floatingInput">Catatan</label>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" name="input_orderitem_validate" value="12345">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- akhir Modal Tambah Item Baru -->

<script>
function updateStokInfo() {
    var select = document.getElementById('menuSelect');
    var selectedOption = select.options[select.selectedIndex];
    var stok = selectedOption.getAttribute('data-stok');
    var jumlahInput = document.getElementById('jumlahInput');
    var stokInfo = document.getElementById('stokInfo');
    var stokValue = document.getElementById('stokValue');
    
    if(stok && stok > 0) {
        stokValue.textContent = stok;
        stokInfo.style.display = 'block';
        jumlahInput.setAttribute('max', stok);
        
        // Ubah warna jika stok menipis
        if(stok < 5) {
            stokInfo.className = 'text-danger small mb-2';
        } else if(stok < 10) {
            stokInfo.className = 'text-warning small mb-2';
        } else {
            stokInfo.className = 'text-muted small mb-2';
        }
    } else {
        stokInfo.style.display = 'none';
        jumlahInput.setAttribute('max', '999');
    }
}
</script>

        <?php
        if (empty($result)) {
            echo "Data menu makanan atau minuman tidak ada";
        } else {
            foreach ($result as $row) {
        ?>
                <!-- Modal Edit -->
                <div class="modal fade" id="ModalEdit<?php echo $row['id_list_order'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-fullscren-md-down">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Menu makanan dan minuman</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" novalidate action="proses/proses_edit_orderitem.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id_list_order'] ?>">
                                    <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                                    <input type="hidden" name="meja" value="<?php echo $meja ?>">
                                    <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="form-floating mb-3">
                                                <select class="form-select" name="menu" id="">
                                                    <option selected hidden value="">Pilih Menu</option>
                                                    <?php
                                                    foreach ($select_menu as $value) {
                                                        if ($row['menu'] == $value['id']) {
                                                            echo "<option selected value= $value[id]>$value[nama_menu]</option>";
                                                            continue;
                                                        } else {
                                                            echo "<option value= $value[id]>$value[nama_menu]</option>";
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                                <label for="menu">Menu Makanan/Minuman</label>
                                                <div class="invalid-feedback">
                                                    Pilih Menu.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-floating mb-3">
                                                <input type="number" class="form-control" id="floatingInput" placeholder="Jumlah Porsi" name="jumlah" required value="<?php echo $row['jumlah'] ?>">
                                                <label for="floatingInput">Jumlah Porsi</label>
                                                <div class="invalid-feedback">
                                                    Masukan Jumlah Porsi.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingInput" placeholder="Catatan" name="catatan" value="<?php echo $row['catatan'] ?>">
                                                <label for="floatingInput">Catatan</label>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="edit_orderitem_validate" value="12345">Save changes</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- akhir Modal Edit -->

                <!-- Modal Delete -->
                <div class="modal fade" id="ModalDelete<?php echo $row['id_list_order'] ?>" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-fullscren-md-down">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Data Menu</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" novalidate action="proses/proses_delete_orderitem.php" method="POST">
                                    <input type="hidden" value="<?php echo $row['id_list_order'] ?>" name="id">
                                    <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                                    <input type="hidden" name="meja" value="<?php echo $meja ?>">
                                    <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                                    <div class="col-lg-12">
                                        Apakah anda yakin ingin menghapus menu <b><?php echo $row['nama_menu'] ?></b>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-danger" name="delete_orderitem_validate" value="1234">Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- akhir Modal Delete -->

            <?php
            }

            ?>

            <!-- Modal Bayar -->
            <div class="modal fade" id="Bayar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-fullscren-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Pembayaran</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr class="text-nowrap">
                                            <th scope="col">Menu</th>
                                            <th scope="col">Harga</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Catatan</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach ($result as $row) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['nama_menu'] ?></td>
                                                <td><?php echo number_format($row['harga'], 0, ',', '.') ?></td>
                                                <td><?php echo $row['jumlah'] ?></td>
                                                <td><?php echo $row['status'] ?></td>
                                                <td><?php echo $row['catatan'] ?></td>
                                                <td><?php echo number_format($row['harganya'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php
                                            $total += $row['harganya'];
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="5" class="fw-bold">
                                                Total Harga
                                            </td>
                                            <td class="fw-bold">
                                                <?php echo number_format($total, 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <span class="text-danger fs-5 fw-semibold">Apakah Anda Yakin Ingin Melakukan Pembayaran?</span>

                            <form class="needs-validation" novalidate action="proses/proses_bayar.php" method="POST">
                                <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                                <input type="hidden" name="meja" value="<?php echo $meja ?>">
                                <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                                <input type="hidden" name="total" value="<?php echo $total ?>">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="floatingInput" placeholder="Nominal Uang" name="uang" required>
                                            <label for="floatingInput">Nominal Uang</label>
                                            <div class="invalid-feedback">
                                                Masukan Nominal Uang.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="bayar_validate" value="12345">Bayar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- akhir Modal Bayar -->

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="text-nowrap">
                            <th scope="col">Menu</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Status</th>
                            <th scope="col">Catatan</th>
                            <th scope="col">Total</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($result as $row) {
                        ?>
                            <tr>
                                <td><?php echo $row['nama_menu'] ?></td>
                                <td><?php echo number_format($row['harga'], 0, ',', '.') ?></td>
                                <td><?php echo $row['jumlah'] ?></td>
                                <td><?php
                                    if ($row['status'] == 1) {
                                        echo "<span class='badge text-bg-warning'>Masuk ke dapur</span>";
                                    } elseif ($row['status'] == 2) {
                                        echo "<span class='badge text-bg-primary'>Siap Saji</span>";
                                    }
                                    ?></td>
                                <td><?php echo $row['catatan'] ?></td>
                                <td><?php echo number_format($row['harganya'], 0, ',', '.') ?></td>
                                <td class="d-flex">
                                    <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-1 disabled" : "btn btn-warning btn-sm me-1"; ?>" data-bs-toggle="modal"
                                        data-bs-target="#ModalEdit<?php echo $row['id_list_order'] ?>"><i class="bi bi-pencil-square"></i></i></button>
                                    <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-1 disabled" : "btn btn-danger btn-sm me-1"; ?>" data-bs-toggle="modal"
                                        data-bs-target="#ModalDelete<?php echo $row['id_list_order'] ?>"><i class="bi bi-trash"></i></i></button>
                                </td>
                            </tr>
                        <?php
                            $total += $row['harganya'];
                        }
                        ?>
                        <tr>
                            <td colspan="5" class="fw-bold">
                                Total Harga
                            </td>
                            <td class="fw-bold">
                                <?php echo number_format($total, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php
        }
        ?>
        <div>
            <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary disabled" : "btn btn-success"; ?>" data-bs-toggle="modal" data-bs-target="#tambahItem"><i class="bi bi-plus-circle-fill"></i> Item</button>
            <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary disabled" : "btn btn-primary"; ?>" data-bs-toggle="modal" data-bs-target="#bayar"><i class="bi bi-cash-coin"></i> Bayar</button>
            <button onclick="printStruk()" class="btn btn-info">Cetak Struk</button>
        </div>
    </div>
</div>
</div>

<div id="strukContent" class="d-none">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #struk, #struk * {
                visibility: visible;
            }
            #struk {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
        
        #struk {
            font-family: "Courier New", monospace;
            font-size: 12px;
            max-width: 300px;
            margin: 0 auto;
            padding: 15px;
            background: white;
            color: black;
        }
        
        #struk .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
        }
        
        #struk .header h2 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        #struk .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }
        
        #struk .info-section {
            margin: 10px 0;
            font-size: 11px;
        }
        
        #struk .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        #struk .info-label {
            font-weight: bold;
            width: 100px;
        }
        
        #struk .divider {
            border-top: 1px dashed #333;
            margin: 10px 0;
        }
        
        #struk .divider-double {
            border-top: 2px solid #333;
            margin: 10px 0;
        }
        
        #struk table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }
        
        #struk th {
            text-align: left;
            padding: 5px 0;
            border-bottom: 1px solid #333;
            font-weight: bold;
        }
        
        #struk td {
            padding: 5px 0;
            vertical-align: top;
        }
        
        #struk .item-name {
            width: 50%;
        }
        
        #struk .item-qty {
            width: 15%;
            text-align: center;
        }
        
        #struk .item-price {
            width: 35%;
            text-align: right;
        }
        
        #struk .subtotal-row {
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        
        #struk .total-section {
            margin-top: 10px;
            font-size: 12px;
        }
        
        #struk .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-weight: bold;
        }
        
        #struk .grand-total {
            font-size: 14px;
            padding: 8px 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        
        #struk .payment-section {
            margin-top: 10px;
            font-size: 11px;
        }
        
        #struk .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #333;
            font-size: 10px;
        }
        
        #struk .footer p {
            margin: 3px 0;
        }
        
        #struk .thank-you {
            font-weight: bold;
            font-size: 12px;
            margin: 10px 0 5px 0;
        }
    </style>
    <div id="struk">
        <!-- Header -->
        <div class="header">
            <h2>☕ DeCAFE ☕</h2>
            <p>Aplikasi Pemesanan Cafe</p>
            <p>Jl. HAJI No. 123, Jakarta</p>
            <p>Telp: (021) 1234-5678</p>
        </div>

        <!-- Info Transaksi -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No. Order</span>
                <span>: <?php echo $kode ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span>: <?php echo date('d/m/Y H:i:s', strtotime($result[0]['waktu_order'])) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Pelanggan</span>
                <span>: <?php echo $pelanggan ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Meja</span>
                <span>: <?php echo $meja ?></span>
            </div>
        </div>

        <div class="divider-double"></div>

        <!-- Item Pesanan -->
        <table>
            <thead>
                <tr>
                    <th class="item-name">Item</th>
                    <th class="item-qty">Qty</th>
                    <th class="item-price">Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($result as $row) { 
                    $subtotal = $row['harganya'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td class="item-name"><?php echo $row['nama_menu'] ?></td>
                        <td class="item-qty"><?php echo $row['jumlah'] ?>x</td>
                        <td class="item-price">Rp <?php echo number_format($row['harga'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="item-price" style="font-size: 10px; color: #666;">
                            Rp <?php echo number_format($subtotal, 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span>Rp <?php echo number_format($total, 0, ',', '.') ?></span>
            </div>
        </div>

        <?php 
        // Ambil data pembayaran jika sudah bayar
        if(!empty($result[0]['id_bayar'])) {
            $query_bayar = mysqli_query($conn, "SELECT * FROM tb_bayar WHERE id_bayar='$kode'");
            $data_bayar = mysqli_fetch_array($query_bayar);
            
            if($data_bayar) {
                $uang_bayar = $data_bayar['nominal_uang'];
                $kembalian = $uang_bayar - $total;
        ?>
        <!-- Payment Section -->
        <div class="payment-section">
            <div class="divider"></div>
            <div class="info-row">
                <span class="info-label">Tunai</span>
                <span>: Rp <?php echo number_format($uang_bayar, 0, ',', '.') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Kembalian</span>
                <span>: Rp <?php echo number_format($kembalian, 0, ',', '.') ?></span>
            </div>
        </div>
        <?php 
            }
        } 
        ?>

        <!-- Footer -->
        <div class="footer">
            <p class="thank-you">Terima Kasih</p>
            <p>Atas Kunjungan Anda</p>
            <p>Selamat Menikmati!</p>
            <div class="divider"></div>
            <p><?php echo date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</div>

<script>
    function printStruk() {
        var strukContent = document.getElementById("strukContent").innerHTML;
        
        var printWindow = window.open('', '_blank', 'width=400,height=600');
        printWindow.document.write('<html><head><title>Cetak Struk</title>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(strukContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        
        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 250);
    }
</script>