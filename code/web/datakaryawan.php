<?php
      include "koneksi.php";
	  if (isset($_POST["btntambah"])) 
      {
		mysqli_query($konek, "update status set mode='3'");
		echo "udahh";
		header("Location: tambah.php");
	  }
?>

<!DOCTYPE html>
<html>
<head>
	<?php include "header.php"; ?> 
	<title>Data karyawan</title>
</head>
<body>

	<?php include "menu.php"; ?>

	<!----- isi------>
	<div class="container-fluid">
		<h3>Data Mahasiswa</h3>
		<table class="table table-bordered">
			<thead>
				<tr style="background-color: black; color: orange;">
					<th style="width: 10px; text-align: center;">No.</th>
					<th style="width: 300px; text-align: center;">Nama</th>
					<th style="width: 200px; text-align: center;">No. kartu</th>
					<th style="width: 200px; text-align: center;">Nim</th>
					<th style="width: 400px; text-align: center;">Email</th>
					<th style="width: 100px; text-align: center;">Sunting</th>
				</tr>
			</thead>
			<tbody>

				<?php 
				     //koneksi database
				     include "koneksi.php";

				     //baca data karyawan
				     $sql = mysqli_query($konek, "select * from mahasiswa");
				     $no = 0;
				     while ($data = mysqli_fetch_array($sql)) 
				     {
				     	$no++;

				?>
				<tr>
					<td> <?php echo $no; ?> </td>
					<td> <?php echo $data['nama']; ?></td>
					<td> <?php echo $data['nokartu']; ?></td>
					<td> <?php echo $data['nim']; ?></td>
					<td> <?php echo $data['email']; ?></td>
					<td>
						<a href="edit.php?id=<?php echo $data['id']; ?>"> Edit </a> | <a href="hapus.php?id=<?php echo $data ['id']; ?>"> hapus </a>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<!----- tambah data ----->
		<a href="tambah.php"> <button class="btn btn-primary">Tambah Data Mahasiswa</button></a>
		<a href="export.php?data=mahasiswa"> <button class="btn btn-primary">Download</button></a>
		
	</div>

</body>
</html>