<?php 
include "koneksi.php";
include_once("parsedown.php");
$parsedown = new Parsedown();
$text = "
Judul Artikel
====================
Sub Judul Artikel
--------------------
![Alt text](img/img.jpg)
Deskripsi tentang artikel disini.";
//$md = $parsedown->text($text);
$md = "<b>This</b> <i>is some Text</i>";
echo $md;
$kalimat = urlencode("<b>DATA MAHASISWA DITAMBAHKAN</b>
NAMA : dddd
NO KARTU : dddd
NIM : dddd
EMAIL : ddd");
//file_get_contents("https://api.telegram.org/bot$tokenbot/sendMessage?" .http_build_query($data));
file_get_contents("https://api.telegram.org/bot$tokenbot/sendMessage?chat_id=$idgroup&text=$kalimat&parse_mode=html");
?>