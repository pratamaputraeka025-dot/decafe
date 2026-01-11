<?php
include "connect.php";
    $name =  (isset ($_POST['nama'])) ? htmlentities($_POST['nama']) : "";
    $username =  (isset ($_POST['username'])) ? htmlentities($_POST['username']) : "";
    $level =  (isset ($_POST['level'])) ? htmlentities($_POST['level']) : "";
    $nohp =  (isset ($_POST['nohp'])) ? htmlentities($_POST['nohp']) : "";
    $alamat =  (isset ($_POST['alamat'])) ? htmlentities($_POST['alamat']) : "";
    $password =  (isset ($_POST['password'])) ? htmlentities($_POST['password']) : "";
    $repassword =  (isset ($_POST['repassword'])) ? htmlentities($_POST['repassword']) : "";

    if(!empty($_POST['input_user_validate'])){
        // Validasi level tidak boleh kosong
        if(empty($level)){
            $message = '<script>alert("Level user harus dipilih");
            window.location="../user";</script>';
        } 
        // Validasi password tidak boleh kosong
        else if(empty($password)){
            $message = '<script>alert("Password harus diisi");
            window.location="../user";</script>';
        }
        // Validasi password minimal 6 karakter
        else if(strlen($password) < 6){
            $message = '<script>alert("Password minimal 6 karakter");
            window.location="../user";</script>';
        }
        // Validasi password dan re-password harus sama
        else if($password != $repassword){
            $message = '<script>alert("Password dan Ulangi Password tidak sama");
            window.location="../user";</script>';
        }
        else {
            // Hash password dengan md5
            $password_hash = md5($password);
            
            $select = mysqli_query($conn, "SELECT * FROM tb_user WHERE username ='$username'");
            if(mysqli_num_rows($select) > 0){
                $message = '<script>alert("Username yang dimasukan telah ada, silahkan gunakan username lain");
                window.location="../user";</script>';
            }else{
                $query = mysqli_query($conn, "INSERT INTO tb_user (nama, username, level, nohp, alamat, password) 
                values ('$name', '$username', '$level', '$nohp', '$alamat', '$password_hash')");
                if(!$query){
                    $message = '<script>alert("Data gagal dimasukkan");
                    window.location="../user";</script>';
                }else{
                    $message = '<script>alert("Data berhasil dimasukkan");
                    window.location="../user";</script>';
                }
            }
        }
    }
    echo $message;
?>