<?php

// Conex�o de Banco MySQLi

// Cria conexao ao banco. Substitui o include "conecta_mysql.php" .
function bancoMysqli(){ 
	$servidor = 'localhost';
	$usuario = 'root';
	$senha = '';
	$banco = 'estufana';
	$con = mysqli_connect($servidor,$usuario,$senha,$banco); 
	mysqli_set_charset($con,"utf8");
	return $con;
}





?>
