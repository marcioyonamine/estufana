<?php

include "globals.php";

// Conexão de Banco MySQLi
// Cria conexao ao banco. Substitui o include "conecta_mysql.php" .
function bancoMysqli(){ 
	$con = mysqli_connect($GLOBALS["servidor"],$GLOBALS["usuario"],$GLOBALS["senha"],$GLOBALS["banco"]); 
	mysqli_set_charset($con,"utf8");
	return $con;
}





?>
