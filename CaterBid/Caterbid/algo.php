<?php
$password = "12345678";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>