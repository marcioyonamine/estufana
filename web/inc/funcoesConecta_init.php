<?php

// Conexão de Banco MySQLi

// Cria conexao ao banco. Substitui o include "conecta_mysql.php" .
function bancoMysqli(){ 
	$servidor = '';
	$usuario = '';
	$senha = '';
	$banco = '';
	$con = mysqli_connect($servidor,$usuario,$senha,$banco); 
	mysqli_set_charset($con,"utf8");
	return $con;
}





?>
