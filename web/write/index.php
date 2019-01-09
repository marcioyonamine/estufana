<?php 
// grava no banco os dados de umidade, temperatura e ph

if(isset($_GET['umid']) && isset($_GET['temp']) && isset($_GET['ph'])){ //verifica se todos os dados foram enviados

	include "../inc/funcoesConecta.php"; //carrega a conexão com o banco de dados
	include "../inc/funcoesGerais.php"; //carrega as funções gerais
	$umid = $_GET['umid'];
	$temp = $_GET['temp'];
	$ph = $_GET['ph'];
	$datahora = date('Y-m-d H:i:s');
	
	$con = bancoMysqli();
	$sql_ins = "INSERT INTO `history` (`id`, `temp`, `umid`, `ph`, `datetime`) VALUES (NULL, '$temp', '$umid', '$ph', '$datahora')";
	$query = mysqli_query($con,$sql_ins);

	if($query){
		echo "Inserido com sucesso";
	}else{
		echo "Erro ao inserir";
	}

}else{

	echo "Não foram enviados todos dados necessários.";
}




?>