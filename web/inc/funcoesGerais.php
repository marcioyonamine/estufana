<?php 

/*
estufana v0.1 - 2019
Esta é a página para as funções gerais do sistema.
*/

date_default_timezone_set('America/Sao_Paulo');





function var_sistema(){
	echo "<strong>SESSION</strong><pre>", var_dump($_SESSION), "</pre>";
	echo "<strong>POST</strong><pre>", var_dump($_POST), "</pre>";
	echo "<strong>GET</strong><pre>", var_dump($_GET), "</pre>";
	echo "<strong>SERVER</strong><pre>", var_dump($_SERVER), "</pre>";
	echo ini_get('session.gc_maxlifetime')/60; // em minutos
}


function habilitarErro(){
   @ini_set('display_errors', '1');
	error_reporting(E_ALL); 	
}


function verificaSessao($idUsuario){
	$con = bancoMysqli();
	$time = date('Y-m-d H:i:s');
	$ip = $_SERVER["REMOTE_ADDR"];

	//Verifica se o usuário está no banco
	$sql_busca = "SELECT * FROM igsis_time WHERE idUsuario = '$idUsuario' AND ip = '$ip'";
	$query_busca = mysqli_query($con,$sql_busca);
	$numero_busca = mysqli_num_rows($query_busca);
	if($numero_busca > 0){
		$sql_atualiza = "UPDATE igsis_time SET time = '$time', ip = '$ip' WHERE idUsuario = '$idUsuario'";
		mysqli_query($con,$sql_atualiza); 
	}else{
		$sql_insere = "INSERT INTO igsis_time (`id`, `idUsuario`, `time`, `ip`) VALUES (NULL, '$idUsuario', '$time', '$ip')";
		mysqli_query($con,$sql_insere);
	}
}


// Framework

//autentica usuario e cria inicia uma session
function autenticaUsuario($usuario, $senha){ 
//
	
	$sql = "SELECT * FROM user WHERE username = '$usuario' LIMIT 0,1";
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	 //query que seleciona os campos que voltarão para na matriz
	if($query){ //verifica erro no banco de dados
		if(mysqli_num_rows($query) > 0){ // verifica se retorna usuário válido
			$user = mysqli_fetch_array($query);
				if($user['pword'] == md5($_POST['senha'])){ // compara as senhas
					session_start();
					$_SESSION['usuario'] = $user['username'];
					header("Location: visual/index.php"); 

				}else{

			echo "A senha está incorreta.";
			}
		}else{
			echo "O usuário não existe.";
		}
	}else{
		echo "Erro no banco de dados";
	}	
}

//saudacao inicial
function saudacao(){ 
	$hora = date('H');
	if(($hora > 12) AND ($hora <= 18)){
		return "Boa tarde";	
	}else if(($hora > 18) AND ($hora <= 23)){
		return "Boa noite";	
	}else if(($hora >= 0) AND ($hora <= 4)){
		return "Boa noite";	
	}else if(($hora > 4) AND ($hora <=12)){
		return "Bom dia";
	}
}

// Formatação de datas, valores

// Retira acentos das strings
function semAcento($string){
	$newstring = preg_replace("/[^a-zA-Z0-9_.]/", "", strtr($string, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
	return $newstring;
}

//retorna data d/m/y de mysql/date(a-m-d)
function exibirDataBr($data){ 
	$timestamp = strtotime($data); 
	return date('d/m/Y', $timestamp);	
}

// retorna datatime sem hora
function retornaDataSemHora($data){
	$semhora = substr($data, 0, 10);
	return $semhora;
}
	
//retorna data d/m/y de mysql/datetime(a-m-d H:i:s)	
function exibirDataHoraBr($data){ 
	$timestamp = strtotime($data); 
	return date('d/m/y - H:i:s', $timestamp);	
}

//retorna hora H:i de um datetime
function exibirHora($data){
	$timestamp = strtotime($data); 
	return date('H:i', $timestamp);	
}
//retorna data mysql/date (a-m-d) de data/br (d/m/a)
function exibirDataMysql($data){ 
	list ($dia, $mes, $ano) = explode ('/', $data);
	$data_mysql = $ano.'-'.$mes.'-'.$dia;
	return $data_mysql;
}

//retorna o endereço da página atual
function urlAtual(){ 
	$dominio= $_SERVER['HTTP_HOST'];
	$url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
	return $url;
}

//retorna valor xxx,xx para xxx.xx
function dinheiroDeBr($valor) { 
	$valor = str_ireplace(".","",$valor);
    $valor = str_ireplace(",",".",$valor);
    return $valor;
}

//retorna valor xxx.xx para xxx,xx
function dinheiroParaBr($valor) { 
    	$valor = number_format($valor, 2, ',', '.');
    	return $valor;
}
//use em problemas de codificacao utf-8
function _utf8_decode($string){ 
	$tmp = $string;
	$count = 0;
	while (mb_detect_encoding($tmp)=="UTF-8"){
    	$tmp = utf8_decode($tmp);
    	$count++;
  	}
	for ($i = 0; $i < $count-1 ; $i++){
	    $string = utf8_decode($string);
	}
	return $string;
}

//retorna o dia da semana segundo um date(a-m-d)
function diasemana($data) { 
	$ano =  substr("$data", 0, 4);
	$mes =  substr("$data", 5, -3);
	$dia =  substr("$data", 8, 9);

	$diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );

	switch($diasemana) {
		case"0": $diasemana = "Domingo";       break;
		case"1": $diasemana = "Segunda-Feira"; break;
		case"2": $diasemana = "Terça-Feira";   break;
		case"3": $diasemana = "Quarta-Feira";  break;
		case"4": $diasemana = "Quinta-Feira";  break;
		case"5": $diasemana = "Sexta-Feira";   break;
		case"6": $diasemana = "Sábado";        break;
	}
	return "$diasemana";
}

//soma(+) ou substrai(-) dias de um date(a-m-d)
function somarDatas($data,$dias){ 
	$data_final = date('Y-m-d', strtotime("$dias days",strtotime($data)));	
	return $data_final;
}

//retorna a diferença de dias entre duas datas
function diferencaDatas($data_inicial,$data_final){
	// Define os valores a serem usados
	
	// Usa a função strtotime() e pega o timestamp das duas datas:
	$time_inicial = strtotime($data_inicial);
	$time_final = strtotime($data_final);
	// Calcula a diferença de segundos entre as duas datas:
	$diferenca = $time_final - $time_inicial; // 19522800 segundos
	// Calcula a diferença de dias
	$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias

	return $dias;

}

function recuperaDados($tabela,$idEvento,$campo){ //retorna uma array com os dados de qualquer tabela. serve apenas para 1 registro.
	$con = bancoMysqli();
	$sql = "SELECT * FROM $tabela WHERE ".$campo." = '$idEvento' LIMIT 0,1";
	$query = mysqli_query($con,$sql);
	$campo = mysqli_fetch_array($query);
	return $campo;		
}

function getStatus(){
	$con = bancoMysqli();
	$sql = "SELECT status from status WHERE id = '1'";
	$query = mysqli_query($con,$sql);
	$s = mysqli_fetch_array($query);
	return $s['status'];
}

function changeStatus(){
	$con = bancoMysqli();
	$s = getStatus();
	if($s == 1){
		$ns = 0;	
	}else{
		$ns = 1;
	}

	$sql = "UPDATE `status` SET `status` = '$ns' WHERE `status`.`id` = 1";
	mysqli_query($con,$sql);	
	
}

function getRele($status){
	$con = bancoMysqli();
	if($status == 1){	
		$sql = "SELECT * FROM manual LIMIT 0,1";
		$query = mysqli_query($con,$sql);
		$x = mysqli_fetch_array($query);
		return $x;
	}	
	
}

function mudaRele($status,$rele){
	$con = bancoMysqli();
	if($status == 1){$x = 0;}else{$x = 1;}
	$sql = "UPDATE manual SET rele".$rele." = '$x' WHERE id = 1";
	 if(!mysqli_query($con,$sql)){
		 echo $sql;
	 }	
	
}

function releApi($format){
	$status = getStatus();
	$rele = array();
	$con = bancoMysqli();
		
	if($status == 1){ // manual

		$sql = "SELECT * FROM manual LIMIT 0,1";
		$query = mysqli_query($con,$sql);
		$x = mysqli_fetch_array($query);
		$rele[1] = $x['rele01'];
		$rele[2] = $x['rele02'];
		$rele[3] = $x['rele04'];
		$rele[4] = $x['rele04'];
	}else{ // programado
		// carrega a última leitura
		$sql_leitura = "SELECT * FROM history ORDER BY datetime DESC LIMIT 0,1";
		$query_leitura = mysqli_query($con,$sql_leitura);
		$leitura = mysqli_fetch_array($query_leitura);
		
		// carrega a programação online
		$sql_programacao = "SELECT * FROM setup WHERE online = 1";
		$query_programacao = mysqli_query($con,$sql_programacao);
		$programacao = mysqli_fetch_array($query_programacao);
		
		// compara temperatura
		if($leitura['temp'] > $programacao['temperatura']){
			$rele[$programacao['rele_temp']] = 1;
		}else{
			$rele[$programacao['rele_temp']] = 0;
		}
		
		// compara umidade
		if($leitura['umid'] < $programacao['umidade']){
			$rele[$programacao['rele_umidade']] = 1;
		}else{
			$rele[$programacao['rele_umidade']] = 0;
		}
		
		// compara o tempo

		$horainicial = strtotime($programacao['lampada_liga']);
		$horafinal = strtotime($programacao['lampada_desliga']);
		$horaatual = strtotime('now');
		
		/*
		echo $horainicial."<br />";
		echo $horafinal."<br />";
		echo $horaatual."<br />";

		echo date('H:i:s')."<br />";
		*/
		
		if($horainicial > $horafinal){ // liga a noite e desliga de dia 21:00 22:00 10:00
			if($horainicial < $horaatual AND $horafinal > $horaatual){
				$rele[$programacao['rele_lampada']] = 1;
			}else{
				$rele[$programacao['rele_lampada']] = 0;
			}
		}
		
		if($horainicial < $horafinal){
			if($horainicial < $horaatual AND $horafinal > $horaatual){
				$rele[$programacao['rele_lampada']] = 1;
			}else{
				$rele[$programacao['rele_lampada']] = 0;
			}
			
			
		}
		
		
		
	}
	//var_dump($programacao);
	if($format == 'json'){	
		return json_encode($rele);
	}else{
		return $rele;
	}		
}

function ultimaLeitura(){
	$con = bancoMysqli();
	$sql = "SELECT * FROM history ORDER BY datetime DESC LIMIT 0,1";
	$query = mysqli_query($con,$sql);
	return mysqli_fetch_array($query);
	
}


?>
