<?php
include "connect.php";
    $id =  (isset ($_POST['id'])) ? htmlentities($_POST['id']) : "";
    $nama_menu =  (isset ($_POST['nama_menu'])) ? htmlentities($_POST['nama_menu']) : "";
    $keterangan =  (isset ($_POST['keterangan'])) ? htmlentities($_POST['keterangan']) : "";
    $kat_menu =  (isset ($_POST['kat_menu'])) ? htmlentities($_POST['kat_menu']) : "";
    $harga =  (isset ($_POST['harga'])) ? htmlentities($_POST['harga']) : "";
    $stok =  (isset ($_POST['stok'])) ? htmlentities($_POST['stok']) : "";

    if(!empty($_POST['input_menu_validate'])){
        // Cek apakah ada file foto yang diupload
        if(!empty($_FILES['foto']['name'])){
            // Jika ada foto baru diupload
            $kode_rand = rand(10000,99999)."_";
            $target_dir = "../assets/img/".$kode_rand;
            $target_file = $target_dir.basename($_FILES['foto']['name']);
            $imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            //apakah gambar atau bukan
            $cek = getimagesize($_FILES['foto']['tmp_name']);
            if($cek == false){
                $message = "Ini bukan file gambar.";
                $statusUpload = 0;
            }else{
                $statusUpload = 1;
                if(file_exists($target_file)){
                    $message = "Maaf, File yang dimasukkan Telah ada.";
                    $statusUpload = 0;
                }else{
                    if($_FILES['foto']['size'] > 500000){
                        $message = "Maaf, Ukuran file terlalu besar.";
                        $statusUpload = 0;
                    }else{
                        if($imageType != "jpg" && $imageType != "png" && $imageType != "jpeg" && $imageType != "gif"){
                            $message = "Maaf, Hanya diperbolehkan gambar yang memiliki format JPG, JPEG, PNG, dan GIF.";
                            $statusUpload = 0;
                        }
                    }
                }
            }

            if($statusUpload == 0){
                $message = '<script>alert("'.$message.', Gambar tidak dapat diupload");
                            window.location="../menu"</script>';
            }else{ 
                // Cek nama menu (kecuali nama menu sendiri)
                $select = mysqli_query($conn, "SELECT * FROM tb_daftar_menu WHERE nama_menu ='$nama_menu' AND id != '$id'");
                if(mysqli_num_rows($select) > 0){
                    $message = '<script>alert("Nama Menu yang dimasukan telah ada, silahkan gunakan nama lain");
                    window.location="../menu"</script>';
                }else{
                    // Ambil foto lama untuk dihapus
                    $query_old_foto = mysqli_query($conn, "SELECT foto FROM tb_daftar_menu WHERE id='$id'");
                    $old_foto = mysqli_fetch_array($query_old_foto);
                    
                    if(move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)){
                        // Update dengan foto baru
                        $query = mysqli_query($conn, "UPDATE tb_daftar_menu SET foto='".$kode_rand.$_FILES['foto']['name']."', nama_menu='$nama_menu', keterangan='$keterangan', kategori='$kat_menu', harga='$harga', stok='$stok' WHERE id='$id'");
                        if(!$query){
                            $message = '<script>alert("Data gagal diupdate")
                            window.location="../menu"</script>';
                        }else{
                            // Hapus foto lama jika ada
                            if(file_exists("../assets/img/".$old_foto['foto'])){
                                unlink("../assets/img/".$old_foto['foto']);
                            }
                            $message = '<script>alert("Data berhasil diupdate")
                            window.location="../menu"</script>';
                        }
                    }else{
                        $message = '<script>alert("Maaf, Gambar gagal diupload")
                        window.location="../menu"</script>';
                    }
                }
            }
        }else{
            // Jika tidak ada foto baru, update tanpa mengubah foto
            // Cek nama menu (kecuali nama menu sendiri)
            $select = mysqli_query($conn, "SELECT * FROM tb_daftar_menu WHERE nama_menu ='$nama_menu' AND id != '$id'");
            if(mysqli_num_rows($select) > 0){
                $message = '<script>alert("Nama Menu yang dimasukan telah ada, silahkan gunakan nama lain");
                window.location="../menu"</script>';
            }else{
                // Update tanpa foto
                $query = mysqli_query($conn, "UPDATE tb_daftar_menu SET nama_menu='$nama_menu', keterangan='$keterangan', kategori='$kat_menu', harga='$harga', stok='$stok' WHERE id='$id'");
                if(!$query){
                    $message = '<script>alert("Data gagal diupdate")
                    window.location="../menu"</script>';
                }else{
                    $message = '<script>alert("Data berhasil diupdate")
                    window.location="../menu"</script>';
                }
            }
        }
    }
    echo $message;

?>