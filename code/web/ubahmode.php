<?php
	include "koneksi.php";
    $vmode = $_GET['mode'];
	$simpan = mysqli_query($konek, "update status set mode='$vmode'");	//baca nomor kartu dari NodeMCU
    
	//baca nomor kartu dari NodeMCU
	if($simpan)
		echo "Berhasil";
	else
		echo "Gagal";
?>