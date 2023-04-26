<?php
	include "koneksi.php";
    $ambil = $_GET['data'];
    if($ambil == 'mahasiswa')
    {
        header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data_mahasiswa.csv');
		$output = fopen("php://output", "w");
		fputcsv($output, array('ID','Nama','NO KARTU','NIM','Email'));
		$sql = "SELECT * FROM mahasiswa";
		$query = mysqli_query($konek,$sql) or die (mysqli_error($konek));
		while($data = mysqli_fetch_assoc($query)){
			fputcsv($output, $data);
		}
		fclose($output);
    }elseif($ambil == 'absen')
    {
        header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data_absen.csv');
		$output = fopen("php://output", "w");
		fputcsv($output, array('Nama','NO Kartu','Suhu','Tanggal', 'Absen Matkul 1', 'Absen Matkul 2', 'Status'));
		$sql = "SELECT * FROM absensi";
		$query = mysqli_query($konek,$sql) or die (mysqli_error($konek));
		while($data = mysqli_fetch_assoc($query)){
            unset($data['id']);
			fputcsv($output, $data);
		}
		fclose($output);
    }
?>