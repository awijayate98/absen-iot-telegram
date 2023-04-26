<?php
     include "koneksi.php";


     //baca id yang akan dihapus
     $id = $_GET['id'];

     //hapus data
     $hapus = mysqli_query($konek, "delete from mahasiswa where id='$id'");

     // jika berhhasil terhapus tampilkan pesan terhapus
     //kemudian kembali ke data karyawan
     if ($hapus) 
     {
     	echo "
     	       <script>
     	          alert('terhapus');
     	          location.replace('datakaryawan.php');
     	       </script>
     	    ";
     }
     else
     {
     	echo "
     	       <script>
     	          alert('Gagal terhapus');
     	          location.replace('datakaryawan.php');
     	       </script>
     	    ";
     }



?>