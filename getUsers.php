<?php
/* Попытка подключения к серверу MySQL. Предполагая, что вы используете MySQL
 сервер с настройкой по умолчанию (пользователь root без пароля) */
 
$link = mysqli_connect("localhost", "iva", "12345","cw");
 
// Проверка подключения
if($link === false){
    die("ОШИБКА: не удалось подключиться. " . mysqli_connect_error());
}
  $query ="SELECT user_id, name FROM dle_users";

$res = mysqli_query($link, $query);
$part1 = '';
$part2 = '';
while($row = mysqli_fetch_assoc($res)){ 
    $part1 = $part1 . ',' . $row["name"];
	$part2 = $part2 . ',' . $row["user_id"];
    

}
echo $part1 . '&' . $part2;

mysqli_close($link);
?>