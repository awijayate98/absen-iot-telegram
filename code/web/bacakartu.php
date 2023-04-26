<?php
	include "koneksi.php";
	//baca tabel status untuk mode absensi
	function sendMessage($messaggio) {
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

	$sql = mysqli_query($konek, "select * from status");
	$data = mysqli_fetch_array($sql);
	$mode_absen = $data['mode'];
	//uji mode absen
	$mode = "";
	if($mode_absen==1)
		$mode = "ABSEN MATKUL 1";
	else if($mode_absen==2)
		$mode = "ABSEN MATKUL 2";


	//baca tabel tmprfid
	$baca_data = mysqli_query($konek, "select * from t_scanabsen");
	$data_log = mysqli_fetch_array($baca_data);
	$nokartu    = $data_log['nokartu'];
	$temp		= $data_log['temperature'];
	$statusabsen		= $data_log['statusabsen'];
?>


<div class="container-fluid" style="text-align: center;">
	<?php if($nokartu=="") { ?>

	<h3>Absen : <?php echo $mode; ?> </h3>
	<h3>Silahkan Tempelkan Kartu RFID Anda</h3>
	<img src="images/rfid.png" style="width: 200px"> <br>
	<img src="images/animasi2.gif">

	<?php } else {
		//cek nomor kartu RFID tersebut apakah terdaftar di tabel karyawan
		$cari_mahasiswa = mysqli_query($konek, "select * from mahasiswa where nokartu='$nokartu'");
		$jumlah_data = mysqli_num_rows($cari_mahasiswa);

		if($jumlah_data==0)
		{
			echo "<h1>Maaf! Kartu Tidak Dikenali</h1>";
			sleep(5);
		}else
		{
			//ambil nama karyawan
			$data_karyawan = mysqli_fetch_array($cari_mahasiswa);
			$nama = $data_karyawan['nama'];

			//tanggal dan jam hari ini
			date_default_timezone_set('Asia/Jakarta') ;
			$tanggal = date('Y-m-d');
			$jam     = date('H:i:s');

			//cek di tabel absensi, apakah nomor kartu tersebut sudah ada sesuai tanggal saat ini. Apabila belum ada, maka dianggap absen masuk, tapi kalau sudah ada, maka update data sesuai mode absensi
			$cari_absen = mysqli_query($konek, "select * from absensi where nokartu='$nokartu' and tanggal='$tanggal'");
			//hitung jumlah datanya
			$jumlah_absen = mysqli_num_rows($cari_absen);
			if($jumlah_absen == 0)
			{
				echo "<h1>ABSEN MATA KULIAH 1 <br> $nama</h1>";
				$kalimat = "<b>SUKSES ABSEN PADA MATA KULIAH $mode_absen</b>
						NAMA : $nama
						TEMPERATURE : $temp
						NO KARTU : $nokartu
						TANGGAL : $tanggal
						WAKTU : $jam";
				sendMessage($kalimat);
				$simpan = mysqli_query($konek, "INSERT IGNORE INTO `absensi`(nokartu,nama,temperature , tanggal, matkul1,statusabsen)VALUES('$nokartu','$nama','$temp', '$tanggal', '$jam','$statusabsen')");
				echo ($simpan);
				sleep(5);
			}
			else
			{
				//update sesuai pilihan mode absen
				if($mode_absen == 2)
				{
					$kalimat = "<b>SUKSES ABSEN PADA MATA KULIAH $mode_absen</b>
					NAMA : $nama
					TEMPERATURE : $temp
					NO KARTU : $nokartu
					TANGGAL : $tanggal
					WAKTU : $jam";
					sendMessage($kalimat);	
					echo "<h1>ABSEN MATA KULIAH 2 <br> $nama</h1>";
					mysqli_query($konek, "update absensi set matkul2='$jam' where nokartu='$nokartu' and tanggal='$tanggal'");
					sleep(5);
				}
			}
		}

		//kosongkan tabel tmprfid
		mysqli_query($konek, "delete from t_scanabsen");
	} ?>

</div>