<?php
session_start();

//membuat koneksi
$konek = mysqli_connect('localhost','root','','stokbarang');


//cek login

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $cekdatabase = mysqli_query($konek, "SELECT * FROM login WHERE email='$email' and password='$password'");
    $hitung = mysqli_num_rows($cekdatabase);

        if($hitung>0){
            $_SESSION['login'] = 'True';
            header('location:index.php');

        }else {
            echo '
            <script>alert("username atau passwor salah");
            window.location.href="login.php"
            </script>
            ';
        }
    }

    //tambahkan barang
    if(isset($_POST['addnewbarang'])){
        $namabarang = $_POST['namabarang'];
        $deskripsi = $_POST['deskripsi'];
        $stock = $_POST['stock'];

        //addgambar
        $allowed_extension = array('png','jpg');
        $nama = $_FILES['file']['name'];
        $dot = explode('.',$nama);
        $ekstensi = strtolower(end($dot));
        $ukuran = $_FILES['file']['size'];
        $file_tmp = $_FILES['file']['tmp_name'];

        $gambar = md5(uniqid($nama,true) . time()).'.'.$ekstensi;

        $cek = mysqli_query($konek,"SELECT * FROM stock WHERE namabarang='$namabarang'");
        $hitung = mysqli_num_rows($cek);

        if($hitung<1){
            if(in_array($ekstensi, $allowed_extension) === true){
                if($ukuran < 15000000){
                    move_uploaded_file($file_tmp, 'gambar/'.$gambar);
        
                    $addtotable = mysqli_query($konek,"insert into stock (namabarang, deskripsi, stock, gambar) values('$namabarang','$deskripsi','$stock','$gambar')");
                    if($addtotable){
                        header('location:index.php');
                    }else {
                        echo 'Gagal';
                        header('location:index.php');
                    }
                }else {
                    echo '
                    <script>alert("Ukuran terlalu besar");
                    window.location.href="index.php"
                    </script>
                    ';
                }
            } else {
                echo '
                    <script>
                    alert("File harus png/jpg");
                    window.location.href="index.php"
                    </script>
                    ';
                }
            } else {
                echo '
                    <script>
                    alert("Nama barang sudah terdaftar");
                    window.location.href="index.php"
                    </script>
                    ';
            }
        };


    //barang masuk
    if(isset($_POST['barangmasuk'])){
        $barangnya = $_POST['barangnya'];
        $pengirim = $_POST['pengirim'];
        $kuantitas = $_POST['kuantitas'];

        $cekstocksekarang = mysqli_query($konek,"SELECT * FROM stock where idbarang='$barangnya'");
        $ambildatanya = mysqli_fetch_array($cekstocksekarang);

        $stocksekarang = $ambildatanya['stock'];
        $tambahkanstockdengankuantitas = $stocksekarang+$kuantitas;

        $addtomasuk = mysqli_query($konek,"insert into masuk (idbarang, keterangan, kuantitas) values('$barangnya','$pengirim','$kuantitas')");
        $updatestokmasuk = mysqli_query($konek,"UPDATE stock set stock='$tambahkanstockdengankuantitas' WHERE idbarang='$barangnya'");

        if($addtomasuk&&$updatestokmasuk){
            header('location:masuk.php');
        }else {
            echo 'Gagal';
            header('location:masuk.php');
        }
    }

    if(isset($_POST['addbarangkeluar'])){
        $barangnya = $_POST['barangnya'];
        $penerima = $_POST['penerima'];
        $kuantitas = $_POST['kuantitas'];

        $cekstocksekarang = mysqli_query($konek,"SELECT * FROM stock where idbarang='$barangnya'");
        $ambildatanya = mysqli_fetch_array($cekstocksekarang);

        $stocksekarang = $ambildatanya['stock'];
        $tambahkanstockdengankuantitas = $stocksekarang-$kuantitas;

        $addtokeluar = mysqli_query($konek,"insert into keluar (idbarang, penerima, kuantitas) values('$barangnya','$penerima','$kuantitas')");
        $updatestokmasuk = mysqli_query($konek,"UPDATE stock set stock='$tambahkanstockdengankuantitas' WHERE idbarang='$barangnya'");

        if($addtokeluar&&$updatestokmasuk){
            header('location:keluar.php');
        }else {
            echo 'Gagal';
            header('location:keluar.php');
        }
    }

    //update barang
    if(isset($_POST['updatebarang'])){
        $idb = $_POST['idb'];
        $namabarang = $_POST['namabarang'];
        $deskripsi = $_POST['deskripsi'];

        //addgambar
        $allowed_extension = array('png','jpg');
        $nama = $_FILES['file']['name'];
        $dot = explode('.',$nama);
        $ekstensi = strtolower(end($dot));
        $ukuran = $_FILES['file']['size'];
        $file_tmp = $_FILES['file']['tmp_name'];

        $gambar = md5(uniqid($nama,true) . time()).'.'.$ekstensi;

        if($ukuran==0){
            $update = mysqli_query($konek,"update stock set namabarang='$namabarang', deskripsi='$deskripsi', gambar='$gambar' where idbarang ='$idb'");
            if($update){
                header('location:index.php');
            }else {
                echo 'Gagal';
                header('location:index.php');
            }

        }else{
            move_uploaded_file($file_tmp, 'gambar/'.$gambar);
            $update = mysqli_query($konek,"update stock set namabarang='$namabarang', deskripsi='$deskripsi', gambar='$gambar' where idbarang ='$idb'");
            if($update){
                header('location:index.php');
            }else {
                echo 'Gagal';
                header('location:index.php');
            }
        }

    }

    //hapus barang
    if(isset($_POST['deletbarang'])){
        $idb = $_POST['idb'];

        $gambar = mysqli_query($konek,"SELECT * FROM stock WHERE idbarang='$idb'");
        $get = mysqli_fetch_array($gambar);
        $img = 'images/'.$get['image'];
        unlink($img);

        $delete = mysqli_query($konek,"delete from stock where idbarang='$idb'");
        if($delete){
            header('location:index.php');
        }else {
            echo 'Gagal';
            header('location:index.php');
        }
    }

    //update barang masuk
    if(isset($_POST['updatebarangmasuk'])){
        $idb = $_POST['idb'];
        $idm = $_POST['idm'];
        $deskripsi = $_POST['keterangan'];
        $kuantitas = $_POST['kuantitas'];

        $lihatstock = mysqli_query($konek,"SELECT * FROM stock WHERE idbarang='$idb'");
        $stocknya = mysqli_fetch_array($lihatstock);
        $stocksekarang = $stocknya['stock'];

        $kuantitassekarang = mysqli_query($konek,"SELECT * FROM masuk WHERE idmasuk='$idm'");
        $kuantitasnya = mysqli_fetch_array($kuantitassekarang);
        $kuantitassekarang = $kuantitasnya['kuantitas'];

        if($kuantitas>$kuantitassekarang){
            $selisih = $kuantitas-$kuantitassekarang;
            $kurangin = $stocksekarang + $selisih;
            $kurangistocknya = mysqli_query($konek,"UPDATE stock set stock='$kurangin' WHERE idbarang='$idb'");
            $updatenya = mysqli_query($konek,"UPDATE masuk SET kuantitas='$kuantitas', keterangan='$deskripsi' WHERE idmasuk='$idm'");

            if($kurangistocknya&&$updatenya){
                header('location:masuk.php');
            }else {
                echo 'Gagal';
                header('location:masuk.php');
            }
        }else {
            $selisih = $kuantitassekarang-$kuantitas;
            $kurangin = $stocksekarang - $selisih;
            $kurangistocknya = mysqli_query($konek,"UPDATE stock set stock='$kurangin' WHERE idbarang='$idb'");
            $updatenya = mysqli_query($konek,"UPDATE masuk SET kuantitas='$kuantitas', keterangan='$deskripsi' WHERE idmasuk='$idm'");

            if($kurangistocknya&&$updatenya){
                header('location:masuk.php');
            }else {
                echo 'Gagal';
                header('location:masuk.php');
            }
        }
        
    }

    //hapus barang masuk
    if(isset($_POST['deletbarangmasuk'])){
        $idb = $_POST['idb'];
        $idm = $_POST['idm'];
        $kuantitas = $_POST['kuantitas'];

        $getstock = mysqli_query($konek,"SELECT * FROM stock WHERE idbarang='$idb'");
        $data = mysqli_fetch_array($getstock);
        $stock = $data['stock'];

        $selisih = $stock-$kuantitas;

        $update = mysqli_query($konek,"UPDATE stock SET stock='$selisih' where idbarang='$idb'");
        $hapusdata= mysqli_query($konek,"DELETE FROM masuk WHERE idmasuk='$idm'");

        if($update&&$hapusdata){
            header('location:masuk.php');
        }else {
            echo 'Gagal';
            header('location:masuk.php');
        }
    
    }

    //update barang keluar
    if(isset($_POST['updatebarangkeluar'])){
        $idb = $_POST['idb'];
        $idk = $_POST['idk'];
        $penerima = $_POST['penerima'];
        $kuantitas = $_POST['kuantitas'];

        $lihatstock = mysqli_query($konek,"SELECT * FROM stock WHERE idbarang='$idb'");
        $stocknya = mysqli_fetch_array($lihatstock);
        $stocksekarang = $stocknya['stock'];

        $kuantitassekarang = mysqli_query($konek,"SELECT * FROM keluar WHERE idkeluar='$idk'");
        $kuantitasnya = mysqli_fetch_array($kuantitassekarang);
        $kuantitassekarang = $kuantitasnya['kuantitas'];

        if($kuantitas>$kuantitassekarang){
            $selisih = $kuantitas-$kuantitassekarang;
            $kurangin = $stocksekarang - $selisih;
            $kurangistocknya = mysqli_query($konek,"UPDATE stock set stock='$kurangin' WHERE idbarang='$idb'");
            $updatenya = mysqli_query($konek,"UPDATE keluar SET kuantitas='$kuantitas', penerima='$penerima' WHERE idkeluar='$idk'");

            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            }else {
                echo 'Gagal';
                header('location:keluar.php');
            }
        }else {
            $selisih = $kuantitassekarang-$kuantitas;
            $kurangin = $stocksekarang + $selisih;
            $kurangistocknya = mysqli_query($konek,"UPDATE stock set stock='$kurangin' WHERE idbarang='$idb'");
            $updatenya = mysqli_query($konek,"UPDATE keluar SET kuantitas='$kuantitas', penerima='$penerima' WHERE idkeluar='$idk'");

            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            }else {
                echo 'Gagal';
                header('location:keluar.php');
            }
        }
        
    }

    //hapus barang masuk
    if(isset($_POST['deletbarangkeluar'])){
        $idb = $_POST['idb'];
        $idk = $_POST['idk'];
        $kuantitas = $_POST['kuantitas'];

        $getstock = mysqli_query($konek,"SELECT * FROM stock WHERE idbarang='$idb'");
        $data = mysqli_fetch_array($getstock);
        $stock = $data['stock'];

        $selisih = $stock+$kuantitas;

        $update = mysqli_query($konek,"UPDATE stock SET stock='$selisih' where idbarang='$idb'");
        $hapusdata= mysqli_query($konek,"DELETE FROM keluar WHERE idkeluar='$idk'");

        if($update&&$hapusdata){
            header('location:keluar.php');
        }else {
            echo 'Gagal';
            header('location:keluar.php');
        }
    
    }
    

?>