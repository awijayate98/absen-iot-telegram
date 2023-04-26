<?php
	include "koneksi.php";
	$sql = mysqli_query($konek, "select * from status");
	$data = mysqli_fetch_array($sql);
	$mode_absen = $data['mode'];
	if($mode_absen < 3)
	{
		$nokartu = $_GET['nokartu'];
		$temp = $_GET['temperature'];
		$varstatusabsen = mysqli_real_escape_string($konek,$_GET['statusabsen']);
		mysqli_query($konek, "DELETE from t_scanabsen");
		$simpan = mysqli_query($konek, "INSERT INTO t_scanabsen(nokartu, temperature, statusabsen)values('$nokartu', '$temp', '$varstatusabsen')");

	}else{
		$nokartu = $_GET['nokartu'];
		mysqli_query($konek, "DELETE from t_scanr");
		$simpan = mysqli_query($konek, "INSERT INTO t_scanr(no_kartu)values('$nokartu')");

	}
	//baca nomor kartu dari NodeMCU
	if($simpan)
		echo "Berhasil";
	else
		echo "Gagal";
?>