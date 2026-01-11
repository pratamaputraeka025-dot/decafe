<?php
include "connect.php";
    $id =  (isset ($_POST['id'])) ? htmlentities($_POST['id']) : "";
    $password_baru =  (isset ($_POST['password_baru'])) ? htmlentities($_POST['password_baru']) : "";
    $repassword_baru =  (isset ($_POST['repassword_baru'])) ? htmlentities($_POST['repassword_baru']) : "";

    if(!empty($_POST['edit_password_user_validate'])){
        // Validasi password tidak boleh kosong
        if(empty($password_baru)){
            $message = '<script>alert("Password baru harus diisi");
            window.location="../user";</script>';
        }
        // Validasi password minimal 6 karakter
        else if(strlen($password_baru) < 6){
            $message = '<script>alert("Password minimal 6 karakter");
            window.location="../user";</script>';
        }
        // Validasi password dan re-password harus sama
        else if($password_baru != $repassword_baru){
            $message = '<script>alert("Password baru dan Ulangi Password tidak sama");
            window.location="../user";</script>';
        }
        else {
            // Hash password dengan md5
            $password_hash = md5($password_baru);
            
            $query = mysqli_query($conn, "UPDATE tb_user SET password='$password_hash' WHERE id='$id'");
            
            if(!$query){
                $message = '<script>alert("Password gagal diupdate");
                window.location="../user";</script>';
            }else{
                $message = '<script>alert("Password berhasil diupdate");
                window.location="../user";</script>';
            }
        }
    }
    echo $message;
?>