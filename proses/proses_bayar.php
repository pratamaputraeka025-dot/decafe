<?php
session_start();
include "connect.php";
    $kode_order =  (isset ($_POST['kode_order'])) ? htmlentities($_POST['kode_order']) : "";
    $meja =  (isset ($_POST['meja'])) ? htmlentities($_POST['meja']) : "";
    $pelanggan =  (isset ($_POST['pelanggan'])) ? htmlentities($_POST['pelanggan']) : "";
    $total =  (isset ($_POST['total'])) ? htmlentities($_POST['total']) : "";
    $uang =  (isset ($_POST['uang'])) ? htmlentities($_POST['uang']) : "";
    $kembalian = $uang - $total;

    if(!empty($_POST['bayar_validate'])){
        if($kembalian < 0){
                $message = '<script>alert("Nominal Uang Tidak Cukup");
                window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
        }else{
                // Proses pembayaran
                $query = mysqli_query($conn, "INSERT INTO tb_bayar (id_bayar, nominal_uang, total_bayar) 
                values ('$kode_order', '$uang', '$total')");
                
                if(!$query){
                    $message = '<script>alert("Pembayaran Gagal");
                    window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
                }else{
                    // Ambil data order dan pelayan
                    $query_order = mysqli_query($conn, "SELECT tb_order.*, tb_user.nama as pelayan_nama 
                        FROM tb_order 
                        LEFT JOIN tb_user ON tb_user.id = tb_order.pelayan 
                        WHERE tb_order.id_order = '$kode_order'");
                    $data_order = mysqli_fetch_array($query_order);
                    
                    // Ambil waktu bayar
                    $query_bayar = mysqli_query($conn, "SELECT waktu_bayar FROM tb_bayar WHERE id_bayar = '$kode_order'");
                    $data_bayar = mysqli_fetch_array($query_bayar);
                    
                    // Simpan ke tabel laporan penjualan
                    $query_laporan = mysqli_query($conn, "INSERT INTO tb_laporan_penjualan 
                        (id_order, tanggal_bayar, total_transaksi, pelanggan, meja, pelayan_id, pelayan_nama) 
                        VALUES ('$kode_order', '".$data_bayar['waktu_bayar']."', '$total', '$pelanggan', '$meja', 
                        '".$data_order['pelayan']."', '".$data_order['pelayan_nama']."')");
                    
                    if($query_laporan){
                        // Ambil ID laporan yang baru disimpan
                        $id_laporan = mysqli_insert_id($conn);
                        
                        // Ambil detail order items
                        $query_items = mysqli_query($conn, "SELECT tb_list_order.*, tb_daftar_menu.nama_menu, 
                            tb_daftar_menu.harga, tb_kategori_menu.kategori_menu,
                            (tb_daftar_menu.harga * tb_list_order.jumlah) as subtotal
                            FROM tb_list_order
                            LEFT JOIN tb_daftar_menu ON tb_daftar_menu.id = tb_list_order.menu
                            LEFT JOIN tb_kategori_menu ON tb_kategori_menu.id_kat_menu = tb_daftar_menu.kategori
                            WHERE tb_list_order.kode_order = '$kode_order'");
                        
                        // Simpan setiap item ke tabel laporan detail
                        while($item = mysqli_fetch_array($query_items)){
                            mysqli_query($conn, "INSERT INTO tb_laporan_detail 
                                (id_laporan, nama_menu, kategori_menu, harga, jumlah, subtotal) 
                                VALUES ('$id_laporan', '".$item['nama_menu']."', '".$item['kategori_menu']."', 
                                '".$item['harga']."', '".$item['jumlah']."', '".$item['subtotal']."')");
                        }
                    }
                    
                    $message = '<script>alert("Pembayaran Berhasil \nUANG KEMBALIAN Rp. '.$kembalian.'");
                    window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
                }
            }
        }
    
    echo $message;
?>