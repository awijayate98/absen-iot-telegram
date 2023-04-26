<!---- proses simpan ---->
<?php
	include "koneksi.php";
	  function sendMessage($messaggio) {
		include "parsedown.php";
		$parsedown = new Parsedown();
		global $tokenbot,$idgroup;
		$url = "https://api.telegram.org/bot" . $tokenbot . "/sendMessage?chat_id=" . $idgroup;
		$url = $url . "&text=" . urlencode($messaggio);
		$url = $url . "&parse_mode=html";
		$ch = curl_init();
		$optArray = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true
		);
		curl_setopt_array($ch, $optArray);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	

      if (isset($_POST['btnsimpan'])) 
      {
       	$nokartu = $_POST['nokartu'];
       	$nama = $_POST['nama'];
		$nim = $_POST['nim'];
       	$Email = $_POST['email'];
		$kalimat = "<b>DATA MAHASISWA DITAMBAHKAN</b>
						NAMA : $nama
						NO KARTU : $nokartu
						NIM : $nim
						EMAIL : $Email";
		$cari_nokartu = mysqli_query($konek, "select * from mahasiswa where nokartu='$nokartu'");
		$jumlah_data_k = mysqli_num_rows($cari_nokartu);

		$cari_nama = mysqli_query($konek, "select * from mahasiswa where nama='$nama'");
		$jumlah_data_n = mysqli_num_rows($cari_nama);

		$cari_nim = mysqli_query($konek, "select * from mahasiswa where nim='$nim'");
		$jumlah_data_nim = mysqli_num_rows($cari_nim);

		$cari_email = mysqli_query($konek, "select * from mahasiswa where email='$Email'");
		$jumlah_data_e = mysqli_num_rows($cari_email);

		if($jumlah_data_k > 0 || $jumlah_data_n > 0 || $jumlah_data_nim > 0 || $jumlah_data_e > 0)
		{
			echo "
                 <script>
                     alert('data Sudah ada');
                     location.replace('datakaryawan.php');
                 </script>
                 ";
		}else{
			$simpan = mysqli_query ($konek, "insert into mahasiswa (nokartu, nama, nim ,email)values('$nokartu', '$nama', '$nim', '$Email')");
			mysqli_query($konek, "delete from t_scanr");
			// jika berhasil
			if ($simpan) 
			{
				sendMessage($kalimat);
				echo "
					<script>
						alert($kalimat);
						location.replace('datakaryawan.php');
					</script>
					";
			}

			else
			{
				echo "
					<script>
						alert('Gagal Tersimpan');
						location.replace('datakaryawan.php');
					</script>
					";
			}

		}



       }
?>


<!DOCTYPE html>
<html>
<head>
	<?php include "header.php"; ?>
	<title>Tambah Data Mahasiswa</title>

	<!----pembacaan kartu otomatis--->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			setInterval(function(){
				$("#norfid").load("nokartu.php");
			}, 0);
		});
	</script>
</head>
<body>

	<?php include "menu.php"; ?>

	<!----- isi ---->
	<div class="container-fluid">
		<h3>Tambah Data Mahasiswa</h3>

		<!--- form input ---->
		<form method="POST">
			<div id="norfid"></div>

			<div class="form-group">
				<label>Nama Karyawan</label>
				<input type="text" name="nama" id="nama" placeholder="Nama Mahasiswa" class="form-control" style="width: 200px"> 
			</div>
			<div class="form-group">
				<label>Nim Mahasiswa</label>
				<input type="text" name="nim" id="Nim" placeholder="Nim Mahasiswa" class="form-control" style="width: 200px"> 
			</div>
			<div class="form-group">
				<label>Email</label>
				<input type="text" name="email" id="Email" placeholder="Email Mahasiswa" class="form-control" style="width: 200px"> 
			</div>


			<button class="btn btn-primary" name="btnsimpan" id="btnsimpan">Simpan</button>
		</form>


	</div>


</body>
</html>