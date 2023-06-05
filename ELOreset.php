<?php
$link = mysqli_connect("localhost", "iva", "12345","cw");
$sql = "UPDATE dle_users SET difficulty_ELO = 0 WHERE 1=1";
mysqli_query($link, $sql);

 mysqli_close($link);
?>