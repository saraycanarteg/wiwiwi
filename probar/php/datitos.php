<?php
$nombre = $_POST["nombre"];
$apellido = $_POST["apellido"];
echo("El nombre es: " .$nombre." " .$apellido);

$cedula = $_POST["cedula"];
echo("<br> La cédula del cliente es: " .$cedula);

$email = $_POST["email"];
echo("<br> El correo electrónico es: " .$email);

$estadoCivil = $_POST["estadoCivil"];
echo("<br> El estado civil del usuario es: " .$estadoCivil);

$seguroMedico = $_POST["seguroMedico"];
echo("<br> El seguro medico del usuario es: " .$seguroMedico);


?>