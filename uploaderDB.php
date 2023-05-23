<?php
$who_id = $_POST['who_id'];
$id = $_POST['id'];
$hobbit = $_POST['hobbit'];
echo $who_id ;
echo '<br>';
echo $id;
echo '<br>';
echo $hobbit;
echo '<br>';

$link = mysqli_connect("localhost", "iva", "12345", "cw");
$stmt = mysqli_prepare($link, "UPDATE clean_up SET who_id = ? WHERE id = ?");
$who_idSend = mysqli_real_escape_string($link, $who_id);
$row_id = $id;
mysqli_stmt_bind_param($stmt, "ii", $who_idSend, $row_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);
?>

