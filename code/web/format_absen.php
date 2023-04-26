<?php
include "koneksi.php";
//delete all records
$query = "TRUNCATE table absensi";


if (mysqli_multi_query($konek, $query)) {
    
    echo "
    <script>
       alert('TERFORMAT');
       location.replace('absensi.php');
    </script>
 ";
} else {
  echo "Error:" . mysqli_error($konek);
}

mysqli_close($konek);
?>