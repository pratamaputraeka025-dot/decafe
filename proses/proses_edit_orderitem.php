<?php
session_start();
include "connect.php";
    $id =  (isset ($_POST['id'])) ? htmlentities($_POST['id']) : "";
    $kode_order =  (isset ($_POST['kode_order'])) ? htmlentities($_POST['kode_order']) : "";
    $meja =  (isset ($_POST['meja'])) ? htmlentities($_POST['meja']) : "";
    $pelanggan =  (isset ($_POST['pelanggan'])) ? htmlentities($_POST['pelanggan']) : "";
    $catatan =  (isset ($_POST['catatan'])) ? htmlentities($_POST['catatan']) : "";
    $menu =  (isset ($_POST['menu'])) ? htmlentities($_POST['menu']) : "";
    $jumlah =  (isset ($_POST['jumlah'])) ? htmlentities($_POST['jumlah']) : "";

    if(!empty($_POST['edit_orderitem_validate'])){
        // Ambil data order item lama
        $query_old = mysqli_query($conn, "SELECT menu, jumlah FROM tb_list_order WHERE id_list_order='$id'");
        $data_old = mysqli_fetch_array($query_old);
        $menu_lama = $data_old['menu'];
        $jumlah_lama = $data_old['jumlah'];
        
        // Cek apakah menu berubah atau jumlah berubah
        if($menu_lama != $menu){
            // Jika menu berubah, kembalikan stok menu lama dan kurangi stok menu baru
            
            // Cek stok menu baru
            $cek_stok_baru = mysqli_query($conn, "SELECT stok, nama_menu FROM tb_daftar_menu WHERE id='$menu'");
            $data_stok_baru = mysqli_fetch_array($cek_stok_baru);
            
            if($data_stok_baru['stok'] < $jumlah){
                $message = '<script>alert("Stok '.$data_stok_baru['nama_menu'].' tidak mencukupi! Stok tersedia: '.$data_stok_baru['stok'].'");
                window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
                echo $message;
                exit;
            }
            
            // Kembalikan stok menu lama
            mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok + $jumlah_lama WHERE id='$menu_lama'");
            
            // Kurangi stok menu baru
            mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok - $jumlah WHERE id='$menu'");
            
        } else {
            // Jika menu sama, hanya jumlah yang berubah
            $selisih = $jumlah - $jumlah_lama;
            
            if($selisih > 0){
                // Jika jumlah bertambah, cek stok
                $cek_stok = mysqli_query($conn, "SELECT stok, nama_menu FROM tb_daftar_menu WHERE id='$menu'");
                $data_stok = mysqli_fetch_array($cek_stok);
                
                if($data_stok['stok'] < $selisih){
                    $message = '<script>alert("Stok '.$data_stok['nama_menu'].' tidak mencukupi! Stok tersedia: '.$data_stok['stok'].'");
                    window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
                    echo $message;
                    exit;
                }
                
                // Kurangi stok sesuai selisih
                mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok - $selisih WHERE id='$menu'");
            } else if($selisih < 0){
                // Jika jumlah berkurang, kembalikan stok
                $selisih_positif = abs($selisih);
                mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok + $selisih_positif WHERE id='$menu'");
            }
        }
        
        // Cek apakah menu sudah ada di order yang sama (kecuali item yang sedang diedit)
        $select = mysqli_query($conn, "SELECT * FROM tb_list_order WHERE menu ='$menu' && kode_order='$kode_order' && id_list_order !='$id'");
        if(mysqli_num_rows($select) > 0){
            // Kembalikan stok jika item sudah ada
            if($menu_lama != $menu){
                mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok - $jumlah_lama WHERE id='$menu_lama'");
                mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok + $jumlah WHERE id='$menu'");
            } else {
                $selisih = $jumlah - $jumlah_lama;
                if($selisih > 0){
                    mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok + $selisih WHERE id='$menu'");
                } else if($selisih < 0){
                    $selisih_positif = abs($selisih);
                    mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok - $selisih_positif WHERE id='$menu'");
                }
            }
            
            $message = '<script>alert("Item yang dimasukan telah ada");
            window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
        }else{
            // Update order item
            $query = mysqli_query($conn, "UPDATE tb_list_order SET menu='$menu', jumlah='$jumlah', catatan='$catatan' WHERE id_list_order='$id'");
            if(!$query){
                $message = '<script>alert("Data gagal diupdate");
                window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
            }else{
                $message = '<script>alert("Data berhasil diupdate");
                window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
            }
        }
    }
    echo $message;
?>