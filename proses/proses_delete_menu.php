<?php
include "connect.php";
    $id =  (isset ($_POST['id'])) ? htmlentities($_POST['id']) : "";
    $foto =  (isset ($_POST['foto'])) ? htmlentities($_POST['foto']) : "";

    if(!empty($_POST['input_menu_validate'])){
        // Cek apakah menu masih terkait dengan list order
        $cek_order = mysqli_query($conn, "SELECT * FROM tb_list_order WHERE menu='$id'");
        
        if(mysqli_num_rows($cek_order) > 0){
            // Jika menu masih terkait dengan orderan
            $message = '<script>alert("Menu masih terikat dengan orderan, menu tidak dapat dihapus");
            window.location="../menu"</script>';
        }else{
            // Jika tidak ada orderan, hapus menu
            $query = mysqli_query($conn, "DELETE FROM tb_daftar_menu WHERE id='$id'");
            if(!$query){
                $message = '<script>alert("Data gagal dihapus");
                window.location="../menu"</script>';
            }else{
                // Hapus file foto jika ada dan file exists
                if(!empty($foto) && file_exists("../assets/img/$foto")){
                    unlink("../assets/img/$foto");
                }
                $message = '<script>alert("Data berhasil dihapus");
                window.location="../menu"</script>';
            }
        }
    }
    echo $message;   
?>