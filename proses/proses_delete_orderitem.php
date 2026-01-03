<?php
include "connect.php";
    $id =  (isset ($_POST['id'])) ? htmlentities($_POST['id']) : "";
    $kode_order =  (isset ($_POST['kode_order'])) ? htmlentities($_POST['kode_order']) : "";
    $meja =  (isset ($_POST['meja'])) ? htmlentities($_POST['meja']) : "";
    $pelanggan =  (isset ($_POST['pelanggan'])) ? htmlentities($_POST['pelanggan']) : "";

    if(!empty($_POST['delete_orderitem_validate'])){
        // Ambil data order item yang akan dihapus
        $query_item = mysqli_query($conn, "SELECT menu, jumlah FROM tb_list_order WHERE id_list_order='$id'");
        $data_item = mysqli_fetch_array($query_item);
        $menu_id = $data_item['menu'];
        $jumlah = $data_item['jumlah'];
        
        // Hapus order item
        $query = mysqli_query($conn, "DELETE FROM tb_list_order WHERE id_list_order='$id'");
        
        if(!$query){
            $message = '<script>alert("Data gagal dihapus");
            window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';           
        }else{
            // Kembalikan stok menu
            $update_stok = mysqli_query($conn, "UPDATE tb_daftar_menu SET stok = stok + $jumlah WHERE id='$menu_id'");
            
            if(!$update_stok){
                $message = '<script>alert("Data berhasil dihapus, tetapi gagal mengembalikan stok");
                window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
            } else {
                $message = '<script>alert("Data berhasil dihapus");
                window.location="../?x=orderitem&order='.$kode_order.'&meja='.$meja.'&pelanggan='.$pelanggan.'"</script>';
            }
        }
    }
    echo $message;
?>