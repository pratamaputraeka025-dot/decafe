<?php
include "connect.php";
    $kode_order =  (isset ($_POST['kode_order'])) ? htmlentities($_POST['kode_order']) : "";

    if(!empty($_POST['delete_order_validate'])){
        // Ambil semua item order untuk mengembalikan stok
        $query_items = mysqli_query($conn, "SELECT menu, jumlah FROM tb_list_order WHERE kode_order='$kode_order'");
        
        if(mysqli_num_rows($query_items) > 0){
            // Ada item order, kembalikan stok untuk setiap item
            while($item = mysqli_fetch_array($query_items)){
                mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok + ".$item['jumlah']." WHERE id='".$item['menu']."'");
            }
        }
        
        // Hapus data pembayaran jika ada
        mysqli_query($conn, "DELETE FROM tb_bayar WHERE id_bayar='$kode_order'");
        
        // Hapus semua item order
        mysqli_query($conn, "DELETE FROM tb_list_order WHERE kode_order='$kode_order'");
        
        // Hapus order
        $query = mysqli_query($conn, "DELETE FROM tb_order WHERE id_order='$kode_order'");
        
        if(!$query){
            $message = '<script>alert("Data gagal dihapus");
            window.location="../order"</script>';
        }else{
            $message = '<script>alert("Order dan semua history berhasil dihapus");
            window.location="../order"</script>';
        }
    }
    echo $message;
?>