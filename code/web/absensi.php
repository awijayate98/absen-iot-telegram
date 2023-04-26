<!DOCTYPE html>
<html>
<head>
	<?php include "header.php"; ?>
	<title>Rekap Absen</title>
</head>
<body>

	<?php include "menu.php"; ?>

	<!-- isi --->
	<div class="container-fluid">
		<h3>Rekap Absen</h3>
		<table class="table table-bordered">
			<thead>
				<tr style="background-color: black; color:orange">
					<th style="width: 10px; text-align: center">No.</th>
					<th style="width: 10px; text-align: center">Nama</th>
					<th style="width: 10px; text-align: center">NO Kartu</th>
					<th style="width: 10px; text-align: center">Suhu</th>
					<th style="width: 10px; text-align: center">Tanggal</th>
					<th style="width: 10px; text-align: center">Absen Matkul 1</th>
					<th style="width: 10px; text-align: center">Absen Matkul 2</th>
					
					<th style="width: 10px; text-align: center">Status</th>
				</tr>
			</thead>
			 <tbody>
			 	<?php 

			 	error_reporting(0);
			 	     include "koneksi.php";

			 	     // baca tabel absensi

                     // baca tanggal saat ini
                     date_default_timezone_set('Asia/Jakarta');
                     $tanggal = date('Y-m-d');

                     // filter absensi berdasarkan tanggal saat ini
					 //$sql = mysqli_query($konek, "select * b.nama, b.temperature, a.tanggal, a.matkul1, a.matkul2 from absensi a, mahasiswa b where a.no_kartu=b.nokartu and a.tanggal='$tanggal'");
					 $sql = mysqli_query($konek, "SELECT * FROM absensi");

                     $no = 0;
                     while ($data = mysqli_fetch_array($sql)) 
                     {
                     	$no++;

			 	?>
			 	<tr>
			 		<td> <?php echo $no; ?></td>
			 		<td> <?php echo $data ['nama']; ?> </td>
					 <td> <?php echo $data ['nokartu']; ?> </td>
			 		<td> <?php echo $data ['temperature']; ?> </td>
					 <td> <?php echo $data ['tanggal']; ?> </td>
			 		<td> <?php echo $data ['matkul1']; ?> </td>
					<td> <?php echo $data ['matkul2']; ?> </td>
					<td> <?php echo $data ['statusabsen']; ?> </td>
			 		</tr>
			 	<?php } ?>
			 </tbody>
		</table>
		<a href="export.php?data=absen"> <button class="btn btn-primary">Download</button></a>
		<a href="format_absen.php"> <button class="btn btn-primary">FORMAT</button></a>

	</div>



</body>
</html>