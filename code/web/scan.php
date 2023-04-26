<!DOCTYPE html>
<html>
<head>
	<?php include "header.php"; ?>
	<title>Scan kartu</title>

	<!----- scan kartu rfid ---->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
           setInterval(function(){
               $("#cekkartu").load("bacakartu.php");
           }, 0);
		});
	</script>
</head>
<body>
	<?php include "menu.php"; ?>

	<!----- isi ----> 
	<div class="container-fluid">
		<div id="cekkartu"></div>
	</div>
</body>
</html>