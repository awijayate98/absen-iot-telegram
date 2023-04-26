<?php
	include "koneksi.php";
	$sql = mysqli_query($konek, "select * from status");
	$data = mysqli_fetch_array($sql);
	$mode_absen = $data['mode'];
    echo $mode_absen;
?>