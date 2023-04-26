<?php 
    include "koneksi.php";

    //baca isi tabel tmprfid
    $sql = mysqli_query($konek, "select * from t_scanr");
    $data = mysqli_fetch_array($sql);
    //baca nokartu
    $nokartu = $data ['no_kartu'];
?>

<div class="form-group">
    <label>No.kartu</label>
    <input type="text" name="nokartu" id="nokartu" placeholder="tempelkan kartu rfid anda" class="form-control" style="width: 200px" value="<?php echo $nokartu; ?>"> 
</div>