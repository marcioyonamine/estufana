<?php 

/*
igSmc v0.1 - 2015
ccsplab.org - centro cultural são paulo

Esta é a página para as funções gerais do sistema.

> Testes e verificações
> Conexão de Banco MySQLi
> Framework
> Formatação de datas, valores
> Outras bibliotecas: email, pdf, etc
*/

// Testes e verificações



// Conecta-se ao banco de dados MySQL
/*
function verificaMysql($sql_inserir){ 	//Verifica erro na string/query
	$mysqli = new mysqli("localhost", "user", "pass","db");
	if (!$mysqli->query($sql_inserir)) {
    printf("Errormessage: %s\n", $mysqli->error);
	}
}
*/

include "globals.php";

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
	
	$sql = "SELECT * FROM ig_usuario, ig_instituicao, ig_papelusuario WHERE ig_usuario.nomeUsuario = '$usuario' AND ig_instituicao.idInstituicao = ig_usuario.idInstituicao AND ig_papelusuario.idPapelUsuario = ig_usuario.ig_papelusuario_idPapelUsuario AND ig_usuario.publicado = '1' LIMIT 0,1";
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	 //query que seleciona os campos que voltarão para na matriz
	if($query){ //verifica erro no banco de dados
		if(mysqli_num_rows($query) > 0){ // verifica se retorna usuário válido
			$user = mysqli_fetch_array($query);
				if($user['senha'] == md5($_POST['senha'])){ // compara as senhas
					session_start();
					$_SESSION['usuario'] = $user['nomeUsuario'];
					$_SESSION['perfil'] = $user['idPapelUsuario'];
					$_SESSION['instituicao'] = $user['instituicao'];
					$_SESSION['nomeCompleto'] = $user['nomeCompleto'];
					$_SESSION['idUsuario'] = $user['idUsuario'];
					$_SESSION['idInstituicao'] = $user['idInstituicao'];

					$log = "Fez login.";
					gravarLog($log);
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

function geraTipoOpcao($abreviatura,$select = 0){
	$con = bancoMysqli();
	$sql = "SELECT * FROM acervo_tipo WHERE abreviatura = '$abreviatura' ORDER BY tipo ASC";
	$query = mysqli_query($con,$sql);
	while($opcao = mysqli_fetch_array($query)){
		if($opcao['id_tipo'] != $select OR $select == 0){
			echo "<option value=".$opcao['id_tipo'].">".$opcao['tipo']."</option>";
		}else{
			echo "<option value=".$opcao['id_tipo']." selected='selected'>".$opcao['tipo']."</option>";	
		}
	}	
}
function geraTipoOpcaoTermo($abreviatura,$select = 0){
	$con = bancoMysqli();
	$sql = "SELECT * FROM acervo_tipo WHERE id_tipo IN ($abreviatura) ORDER BY tipo ASC";
	$query = mysqli_query($con,$sql);
	while($opcao = mysqli_fetch_array($query)){
		if($opcao['id_tipo'] != $select OR $select == 0){
			echo "<option value=".$opcao['id_tipo'].">".$opcao['tipo']."</option>";
		}else{
			echo "<option value=".$opcao['id_tipo']." selected='selected'>".$opcao['tipo']."</option>";	
		}
	}	
}

//Outras bibliotecas

function verificaOpcao($opcao){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_opcoes WHERE opcao = '$opcao' LIMIT 0,1";
	$query = mysqli_query($con,$sql);
	$valor = mysqli_fetch_array($query);
	return $valor['valor'];	
}

function gravarLog($log){ //grava na tabela ig_log os inserts e updates
	$valor = verificaOpcao("log"); //verifica se a opção de gravação de log está habilitada
	if($valor == 1){
		$logTratado = addslashes($log);
		$idUsuario = $_SESSION['idUsuario'];
		$ip = $_SERVER["REMOTE_ADDR"];
		$data = date('Y-m-d H:i:s');
		$sql = "INSERT INTO `ig_log` (`idLog`, `ig_usuario_idUsuario`, `enderecoIP`, `dataLog`, `descricao`) VALUES (NULL, '$idUsuario', '$ip', '$data', '$logTratado')";
		$mysqli = bancoMysqli();
		$mysqli->query($sql);
	}
}

function geraOpcaoPublicado($tabela,$select,$instituicao){ //gera os options de um select
	if($instituicao != ""){
		$sql = "SELECT * FROM $tabela WHERE publicado = 0 AND idInstituicao = $instituicao OR idInstituicao = 999  ";
	}else{
		$sql = "SELECT * FROM $tabela WHERE publicado = 1";
	}
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	while($option = mysqli_fetch_row($query)){
		if($option[0] == $select){
			echo "<option value='".$option[0]."' selected >".$option[1]."</option>";	
		}else{
			echo "<option value='".$option[0]."'>".$option[1]."</option>";	
		}
	}
}



function geraOpcao($tabela,$select,$instituicao){ //gera os options de um select
	if($instituicao != ""){
		$sql = "SELECT * FROM $tabela WHERE idInstituicao = $instituicao OR idInstituicao = 999 ORDER BY 2 ASC";
	}else{
		$sql = "SELECT * FROM $tabela ORDER BY 2";
	}
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	while($option = mysqli_fetch_row($query)){
		if($option[0] == $select){
			echo "<option value='".$option[0]."' selected >".$option[1]."</option>";	
		}else{
			echo "<option value='".$option[0]."'>".$option[1]."</option>";	
		}
	}
}


function geraOpcaoOrder($tabela,$select,$instituicao){ //gera os options de um select
	if($instituicao != ""){
		$sql = "SELECT * FROM $tabela WHERE idInstituicao = $instituicao OR idInstituicao = 999 ORDER BY Verba ASC";
	}else{
		$sql = "SELECT * FROM $tabela ORDER BY Verba ASC";
	}
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	while($option = mysqli_fetch_row($query)){
		if($option[0] == $select){
			echo "<option value='".$option[0]."' selected >".$option[1]."</option>";	
		}else{
			echo "<option value='".$option[0]."'>".$option[1]."</option>";	
		}
	}
}


function geraOpcaoPai($tabela,$select,$instituicao){
	if($instituicao != ""){
		$sql = "SELECT * FROM $tabela WHERE idInstituicao = $instituicao OR idInstituicao = 999";
	}else{
		$sql = "SELECT * FROM $tabela";
	}
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	while($option = mysqli_fetch_array($query)){
		if($option['pai'] != NULL){		
			if($option[0] == $select){
				echo "<option value='".$option[0]."' selected >".$option[1]."</option>";	
			}else{
				echo "<option value='".$option[0]."'>".$option[1]."</option>";	
			}
		}
	}
}

function recuperaModulo($pag){ 
	$sql = "SELECT * FROM ig_modulo WHERE pag = '$pag'";
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	$modulo = mysqli_fetch_array($query);
	return $modulo;
}
	
function listaModulos($perfil){ //gera as tds dos módulos a carregar
	// recupera quais módulos o usuário tem acesso
	$sql = "SELECT * FROM ig_papelusuario WHERE idPapelUsuario = $perfil"; 
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	$campoFetch = mysqli_fetch_array($query);
	
	while($fieldinfo = mysqli_fetch_field($query)){
		if(($campoFetch[$fieldinfo->name] == 1) AND ($fieldinfo->name != 'idPapelUsuario')){
			$descricao = recuperaModulo($fieldinfo->name);
			echo "<tr>";
			echo "<td class='list_description'><b>".$descricao['nome']."</b></td>";
			echo "<td class='list_description'>".$descricao['descricao']."</td>";
			echo "
			<td class='list_description'>
			<form method='POST' action='?perfil=$fieldinfo->name'>
			<input type ='submit' class='btn btn-theme btn-lg btn-block' value='carregar'></td></form>"	;
			echo "</tr>";
		}
	}
}

function listaModulosAlfa($perfil){ //gera as tds dos módulos a carregar
	$con = bancoMysqli();

	// recupera os módulos do sistema
	$sql_modulos = "SELECT pag FROM ig_modulo ORDER BY nome";
	$query_modulos = mysqli_query($con,$sql_modulos);
	while($modulos = mysqli_fetch_array($query_modulos)){
		$sql = "SELECT * FROM ig_papelusuario WHERE idPapelUsuario = $perfil"; 
		$query = mysqli_query($con,$sql);
		$campoFetch = mysqli_fetch_array($query);
		if(($campoFetch[$modulos['pag']] == 1) AND ($campoFetch[$modulos['pag']] != 'idPapelUsuario')){
				$descricao = recuperaModulo($modulos['pag']);
				echo "<tr>";
				echo "<td class='list_description'><b>".$descricao['nome']."</b></td>";
				echo "<td class='list_description'>".$descricao['descricao']."</td>";
				echo "
				<td class='list_description'>
				<form method='POST' action='?perfil=".$modulos['pag']."' >
				<input type ='submit' class='btn btn-theme btn-lg btn-block' value='carregar'></td></form>"	;
				echo "</tr>";
			}
		
	}	
}


function verificaAcesso($usuario,$pagina){ //verifica se o usuário tem permissão de acesso a uma determinada página
	$sql = "SELECT * FROM ig_usuario,ig_papelusuario WHERE ig_usuario.idUsuario = $usuario AND ig_usuario.ig_papelusuario_idPapelUsuario = ig_papelusuario.idPapelUsuario LIMIT 0,1";
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	$verifica = mysqli_fetch_array($query);
	if($verifica["$pagina"] == 1){
		return 1;
	}else{
		return 0;
	}
}


function recuperaDados($tabela,$idEvento,$campo){ //retorna uma array com os dados de qualquer tabela. serve apenas para 1 registro.
	$con = bancoMysqli();
	$sql = "SELECT * FROM $tabela WHERE ".$campo." = '$idEvento' LIMIT 0,1";
	$query = mysqli_query($con,$sql);
	$campo = mysqli_fetch_array($query);
	return $campo;		
}

function opcaoUsuario($idInstituicao,$idUsuario){ //cria as options com usuários de uma instituicao
	$sql = "SELECT DISTINCT * FROM ig_usuario,ig_papelusuario WHERE ig_usuario.ig_papelusuario_idPapelUsuario = ig_papelusuario.idPapelUsuario AND ig_papelusuario.evento = 1 ORDER BY nomeCompleto";
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	while($campo = mysqli_fetch_array($query)){
		if($campo['idUsuario'] == $idUsuario){
			echo "<option value=".$campo['idUsuario']." selected >".$campo['nomeCompleto']."</option>";
		}else{
			echo "<option value=".$campo['idUsuario']." >".$campo['nomeCompleto']."</option>";
			
		}
	}	
}




function recuperaIdDado($tabela,$id){ 
	$con = bancoMysqli();
	//recupera os nomes dos campos
	$sql = "SELECT * FROM $tabela";
	$query = mysqli_query($con,$sql);
	$campo01 = mysqli_field_name($query, 0);
	$campo02 = mysqli_field_name($query, 1);
	
	$sql = "SELECT * FROM $tabela WHERE $campo01 = $id";
	$query = mysql_query($sql);
	$campo = mysql_fetch_array($query);
	return $campo[$campo02];	
}


function retornaInstituicao($local){ 
	$con = bancoMysqli();
	$sql = "SELECT idInstituicao FROM ig_local WHERE idLocal = $local";
	$query = mysqli_query($con,$sql);
	$campo = mysqli_fetch_array($query);
	return $campo['idInstituicao'];
}




function checar($id){ //funcao para imprimir checked do checkbox
	if($id == 1){
		echo "checked";	
	}	
}

function recuperaUsuario($idUsuario){ //retorna dados do usuário
	$recupera = recuperaDados('ig_usuario',$idUsuario,'idUsuario');
	if($recupera){
		return $recupera;
	}else{
		return NULL;
	}
		
}




function retornaEndereco($cep,$numero,$complemento){
	$con = bancoMysqliCEP();
	$cep_index = substr($cep, 0, 5);
	$sql01 = "SELECT * FROM igsis_cep_cep_log_index WHERE cep5 = '$cep_index' LIMIT 0,1";
	$query01 = mysqli_query($con,$sql01);
	$num = mysqli_num_rows($query01);
	if($num > 0){
		$campo01 = mysqli_fetch_array($query01);
		$uf = "igsis_cep_".$campo01['uf'];
	
		$sql02 = "SELECT * FROM $uf WHERE cep = '$cep'";
		$query02 = mysqli_query($con,$sql02);
		$campo02 = mysqli_fetch_array($query02);
		$endereco =  $campo02['tp_logradouro']." ".$campo02['logradouro'].", ".$numero." / ".$complemento." - ".
		$campo02['bairro']." - ".$campo02['cidade']." / ".strtoupper($campo01['uf']);
		return $endereco;
	}else{
		
	}
	
}


function valorPorExtenso($valor=0) { //retorna um valor por extenso
	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
 
	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
 
	$z=0;
 
	$valor = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);
	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];
  	$rt = "";
	// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;) 
	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
	for ($i=0;$i<count($inteiro);$i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
	
		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		$t = count($inteiro)-1-$i;
		$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		if ($valor == "000")$z++; elseif ($z > 0) $z--;
		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
		if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
	}
 
	return($rt ? $rt : "zero");
}

function recuperaModalidade($id){ //imprime a modalidade
	$con = bancoMysqli();
	$sql = "SELECT * FROM ig_modalidade WHERE idModalidade = '$id'";
	$query = mysqli_query($con,$sql);
	$campo = mysqli_fetch_array($query);
	echo $campo['modalidade'];	
}

//Comunicação

function saudacaoCom(){
	return "Olá amigo comunicador!";
	
}


function analisaArray($array){ //imprime o conteúdo de uma array
	echo "<pre>";
   print_r($array);
	echo "</pre>";
}

function retornaPais($id){
	$pais = recuperaDados("ig_pais",$id,"paisId");
	return $pais['paisNome'];	
}


function retornaModulos($perfil){
	// recupera quais módulos o usuário tem acesso
	$sql = "SELECT * FROM ig_papelusuario WHERE idPapelUsuario = $perfil"; 
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	$campoFetch = mysqli_fetch_array($query);
	$nome = "";
	while($fieldinfo = mysqli_fetch_field($query)){
		if(($campoFetch[$fieldinfo->name] == 1) AND ($fieldinfo->name != 'idPapelUsuario')){
			$descricao = recuperaModulo($fieldinfo->name);
			$nome = $nome.";\n + ".$descricao['nome'];
		}
	}
	return substr($nome,1);		
	
	
}

function recuperaUsuarioCompleto($idUsuario){ //retorna dados do usuário
	$recupera = recuperaDados('ig_usuario',$idUsuario,'idUsuario');
	if($recupera){
		$instituicao = recuperaDados("ig_instituicao",$recupera['idInstituicao'],"idInstituicao");
		$perfil = recuperaDados("ig_papelusuario",$recupera['ig_papelusuario_idPapelUsuario'],"idPapelUsuario");
		$modulos = retornaModulos($recupera['ig_papelusuario_idPapelUsuario']);
		if($recupera['receberNotificacao'] == 1){
			$notificacao = "Habilitado";	
		}else{
			$notificacao = "Não habilitado";	
		}
		
		$x = array(
		    "nomeCompleto" => $recupera['nomeCompleto'],
		    "email" => $recupera['email'],
			"nomeUsuario" => $recupera['nomeUsuario'],
		    "perfil" => $perfil['nomePapelUsuario'],
		    "telefone" => $recupera['telefone'],
		    "receberNotificacao" => $recupera['receberNotificacao'],
			"modulos" => $modulos,
			"notificacao" => $notificacao,		
			"instituicao" => $instituicao['instituicao']
		);
		return $x;
		
	}else{
		return NULL;
	}
		
}

function retornaMes($mes){

	switch($mes){
	case "01":
		return "Janeiro";
	break;
	case "02":
		return "Fevereiro";
	break;
	case "03":
		return "Março";
	break;
	case "04":
		return "Abril";
	break;
	case "05":
		return "Maio";
	break;
	case "06":
		return "Junho";
	break;
	case "07":
		return "Julho";
	break;
	case "08":
		return "Agosto";
	break;
	case "09":
		return "Setembro";
	break;
	case "10":
		return "Outubro";
	break;
	case "11":
		return "Novembro";
	break;
	case "12":
		return "Dezembro";
	break;
		
	}	


}


function validaCPF($cpf = null) {
 
    // Verifica se um número foi informado
    if(empty($cpf)) {
        return false;
    }
 
    // Elimina possivel mascara
    $cpf = ereg_replace('[^0-9]', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
     
    // Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequências invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' || 
        $cpf == '11111111111' || 
        $cpf == '22222222222' || 
        $cpf == '33333333333' || 
        $cpf == '44444444444' || 
        $cpf == '55555555555' || 
        $cpf == '66666666666' || 
        $cpf == '77777777777' || 
        $cpf == '88888888888' || 
        $cpf == '99999999999') {
        return false;
     // Calcula os digitos verificadores para verificar se o
     // CPF é válido
     } else {   
         
        for ($t = 9; $t < 11; $t++) {
             
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
 
        return true;
    }
}

function listaArquivosRegistro($id_registro){ //lista arquivos de determinado registro
	$con = bancoMysqli();
	$sql = "SELECT * FROM acervo_arquivos WHERE idReg = '$id_registro' AND publicado = '1'";
	$query = mysqli_query($con,$sql);
	echo "<table class='table table-condensed'>
					<thead>
						<tr class='list_menu'>
							<td>Nome do arquivo</td>
							<td width='10%'></td>
						</tr>
					</thead>
					<tbody>";
	while($campo = mysqli_fetch_array($query)){
			echo "<tr>";
			echo "<td class='list_description'><a href='../uploads/".$campo['nome']."' target='_blank'>".$campo['nome']."</a></td>";
			echo "
			<td class='list_description'>
			<form method='POST' action='?perfil=discoteca&p=frm_arquivos'>
			<input type='hidden' name='apagar' value='".$campo['idArquivo']."' />
			<input type ='submit' class='btn btn-theme  btn-block' value='apagar'></td></form>"	;
			echo "</tr>";		
	}
	echo "</tbody>
		</table>
	
	";
	
}


function enderecoCEP($cep){
	$con = bancoMysqliCEP();
	$cep_index = substr($cep, 0, 5);
	$dados['sucesso'] = 0;
	
	$sql01 = "SELECT * FROM igsis_cep_cep_log_index WHERE cep5 = '$cep_index' LIMIT 0,1";
	$query01 = mysqli_query($con,$sql01);
	$campo01 = mysqli_fetch_array($query01);
	$uf = "igsis_cep_".$campo01['uf'];
	
	$sql02 = "SELECT * FROM $uf WHERE cep = '$cep'";
	$query02 = mysqli_query($con,$sql02);
	$campo02 = mysqli_fetch_array($query02);
	$res = mysqli_num_rows($query02);
	 if($res > 0){
	$dados['sucesso'] = 1;
	 }else{
	$dados['sucesso'] = 0;
	 }
	$dados['rua']     = $campo02['tp_logradouro']." ".$campo02['logradouro'];
	$dados['bairro']  = $campo02['bairro'];
	$dados['cidade']  = $campo02['cidade'];
	$dados['estado']  = strtoupper($campo01['uf']);
 
return $dados; 	

}



//retorna o dia da semana segundo um date(a-m-d)
function diaSemanaBase($data) { 
	$ano =  substr("$data", 0, 4);
	$mes =  substr("$data", 5, -3);
	$dia =  substr("$data", 8, 9);

	$diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );

	switch($diasemana) {
		case"0": $diasemana = "domingo";       break;
		case"1": $diasemana = "segunda"; break;
		case"2": $diasemana = "terca";   break;
		case"3": $diasemana = "quarta";  break;
		case"4": $diasemana = "quinta";  break;
		case"5": $diasemana = "sexta";   break;
		case"6": $diasemana = "sabado";        break;
	}
	return "$diasemana";
}

function geraFrase(){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_frases ORDER BY RAND() LIMIT 1";
	$query = mysqli_query($con,$sql);
	$frase = mysqli_fetch_array($query);
	echo $frase['frase'];
}



function soNumero($str){
	return preg_replace("/[^0-9]/", "", $str);
}



function retornaMesExtenso($data){
	$meses = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
	$data = explode("-", $dataMysql);

	$mes = $data[1];
	
	return $meses[($mes) - 1]; 
	
}

function geraAcervoOpcao($id,$select = NULL){
	$con = bancoMysqli();
	$sql = "SELECT * FROM acervo_acervos WHERE id_acervo = '$id' OR pai = '$id' ORDER BY pai";
	$query = mysqli_query($con,$sql);
	while($opcao = mysqli_fetch_array($query)){
		if($opcao['pai'] == $id){ 
			$ident = " -";
		}else{
			$ident = "";	
		}
		
		if($opcao['id_acervo'] != $select OR $select == NULL){
			echo "<option value='".$opcao['id_acervo']."'>".$ident.$opcao['acervo']."</option>";
		}else{
			echo "<option value='".$opcao['id_acervo']."' selected='selected'>".$ident.$opcao['acervo']."</option>";	
		}
	}	
}

function opcaoTermo($idTipo,$select = NULL){
	$con = bancoMysqli();
	$sql_termo = "SELECT id_termo, termo FROM acervo_termo WHERE tipo = '$idTipo' ORDER BY termo ASC";
	$query_termo = mysqli_query($con,$sql_termo);
	while($termo = mysqli_fetch_array($query_termo)){
		if($termo['id_termo'] == $select){
			echo "<option value='".$termo['id_termo']."' selected='selected'>".$termo['termo']."</option>";
		}else{
			echo "<option value='".$termo['id_termo']."'>".$termo['termo']."</option>";
		
		}
	}
	
}

function opcaoTermoCat($idTipo,$select = NULL){
	$con = bancoMysqli();
	$sql_termo = "SELECT id_termo, termo FROM acervo_termo WHERE tipo = '$idTipo' ORDER BY termo ASC";
	$query_termo = mysqli_query($con,$sql_termo);
	while($termo = mysqli_fetch_array($query_termo)){
		if($termo['id_termo'] == $select){
			echo "<option value='".$termo['id_termo']."' selected='selected'>".$termo['termo']."</option>";
		}else{
			echo "<option value='".$termo['id_termo']."'>".$termo['termo']."</option>";
		
		}
	}
	
}

function listaTermos($idReg,$tipo){
	$con = bancoMysqli();
	$sql = "SELECT * FROM acervo_relacao_termo WHERE idReg = '$idReg' AND idTipo IN($tipo) AND publicado = '1'";
	//echo $sql;
	$query = mysqli_query($con,$sql);
	$num = mysqli_num_rows($query);
	if($num > 0){	
		$y = 0;
		while($x = mysqli_fetch_array($query)){
			
			$termo = recuperaDados("acervo_termo",$x['idTermo'],"id_termo");
			$tipo = recuperaDados("acervo_tipo",$x['idTipo'],"id_tipo");
			$w[$y]['idRel'] = $x['idRel'];
			$w[$y]['termo'] = $termo['termo'];
			$w[$y]['idTermo'] = $x['idTermo'];
			$w[$y]['tipo'] = $tipo['tipo'];
			$w[$y]['idTipo'] = $x['idTipo'];
			$w[$y]['categoria'] = "";
			$w[$y]['idCat'] = $x['idCat'];
			$y++;
			$w['total'] = $y;
		}
	}else{
		$w['total'] = 0;
	}
	$w['sql'] = $sql;
	return $w;
}

function extensaoArquivo($arquivo){

	$extensao = substr($arquivo, -4);
	if($extensao[0] == '.'){
       $extensao = substr($arquivo, -3);
 	}
 
return $extensao;

	
}

function retornaAutoridades($registro,$analitica = NULL){
	$con = bancoMysqli(); //conecta no banco
	// seleciona todos os termos autoridades ligados ao registro
	
	if($registro == 0 OR $registro == NULL){
		$x = array();
		return $x;
	}else{
		$sql_autoridades = "SELECT idTermo,idCat FROM acervo_relacao_termo WHERE idTipo = '1' AND publicado = '1' AND idReg = '$registro' GROUP BY idCat"; 
		$query_autoridades = mysqli_query($con,$sql_autoridades);
		$num = mysqli_num_rows($query_autoridades);
		if($num > 0){
			$i = 0;
			while($termo = mysqli_fetch_array($query_autoridades)){
				$y = recuperaDados("acervo_termo",$termo['idTermo'],"id_termo");
				$w = recuperaDados("acervo_termo",$termo['idCat'],"id_termo");
				$x[$i]['termo'] = $y['termo'];
				$x[$i]['categoria'] = $w['termo'];
				$i++;
				
			}
	
			$str = ",";
			$string = "";
			for($a = 0; $a <= ($num - 1); $a++){
				$str = ", ".$x[$a]['termo']." ( ".$x[$a]['categoria']. " ) ";
				$string = $string.$str;
			} 	
	
		}else{
	
			$string = "";			
		}
		$x['total'] = $num;
		$x['string'] = trim(substr($string, 1));
		if($num == 0){
			$x['string'] = "";
			
		}
		return $x;
	}
	
}

function retornaTermos($registro,$analitica = NULL){
	$con = bancoMysqli(); //conecta no banco
	// seleciona todos os termos autoridades ligados ao registro
	
	if($registro == 0 OR $registro == NULL){
		$x = array();
		return $x;
	}else{
		$sql_autoridades = "SELECT idTermo,idTipo FROM acervo_relacao_termo WHERE idTipo IN(".$GLOBALS['acervo_tipo'].") AND publicado = '1' AND idReg = '$registro'"; 
		$query_autoridades = mysqli_query($con,$sql_autoridades);
		$num = mysqli_num_rows($query_autoridades);
		if($num > 0){
			$i = 0;
			while($termo = mysqli_fetch_array($query_autoridades)){
				$y = recuperaDados("acervo_termo",$termo['idTermo'],"id_termo");
				$w = recuperaDados("acervo_tipo",$termo['idTipo'],"id_tipo");
				$x[$i]['termo'] = $y['termo'];
				$x[$i]['categoria'] = $w['tipo'];
				$i++;
				
			}
	
			$str = ",";
			$string = "";
			for($a = 0; $a <= ($num - 1); $a++){
				$str = ", ".$x[$a]['termo']." ( ".$x[$a]['categoria']. " ) ";
				$string = $string.$str;
			} 	
	
		}else{
	
			$string = "";			
		}
		$x['total'] = $num;
		$x['string'] = substr($string, 1);
		return $x;
	}
	
}

function idMatriz($id){
	$mat = recuperaDados("acervo_discoteca",$id,"idDisco");
	return $mat['matriz']; 	
}

function idReg($id,$tabela){
	$con = bancoMysqli();
	$sql = "SELECT id_registro FROM acervo_registro WHERE id_tabela = '$id' AND tabela = '$tabela' AND publicado = '1' LIMIT 0,1";
	$query = mysqli_query($con,$sql);
	$num = mysqli_num_rows($query);
	$x = mysqli_fetch_array($query);
		return $x['id_registro'];	
}

function recuperaIdTemp($id,$tabela){
	$con = bancoMysqli();
	switch($tabela){
		case 87:
			$sql = "SELECT idDisco FROM acervo_discoteca WHERE idTemp = '$id' LIMIT 0,1";
			
		break;
		case 97:
			$sql = "SELECT idDisco FROM acervo_partitura WHERE idTemp = '$id' LIMIT 0,1";
		break;		
	}
	$query = mysqli_query($con,$sql);
	$x = mysqli_fetch_array($query);
	return $x['idDisco'];
}

function retiraTitulo($string){
	$pontos = array("TÍTULO DO DISCO:", "Título do Disco:","Título Uniforme:","TÍTULO DA FAIXA:","Título da Faixa:","TÍTULO UNIFORME:","TÍTULO DA PARTITURA:","TÍTULO DA OBRA:", "CONTEÚDO:");
	$result = str_replace($pontos, "", $string);
	return $result;

}

function recuperaIdTermo($string,$tipo){
		if($string == "" OR $string == "." OR $string == NULL){
			return NULL;			
		}else{ 
			$con = bancoMysqli();
			$sql = "SELECT id_termo FROM acervo_termo WHERE termo LIKE '$string' AND tipo = '$tipo' LIMIT 0,1";
			$query = mysqli_query($con,$sql);
			$num = mysqli_num_rows($query);
			if($num > 0){
				$x = mysqli_fetch_array($query);
				return $x['id_termo'];
			}else{
				return NULL;
			}
		}
}

function retiraParenteses($string){
	$pontos = array("(", ")","(Conjunto Musical)","[ver notas]");
	$result = str_replace($pontos, "", $string);
	return $result;

}

function retiraTombo($string){
	$pontos = array("D-", "D78-","CD-","F-");
	$result = str_replace($pontos, "", $string);
	return $result;

}

function resumoAutoridades($string){
	$array = explode('/', $string);
	$i = 0;
	foreach($array as $valores){
		preg_match('#\((.*)\)#',$valores, $match);
		$categoria = $match[0];	
		$y = explode("(", $valores);
		$termo = $y[0];
		$retorno[$i]['categoria'] = trim(retiraParenteses($categoria));
		$retorno[$i]['termo'] = trim($termo);
		$i++;
	}
	if($i == 0){
		$retorno['total'] = 0;
	}else{
		$retorno['total'] = $i;
	}
	return $retorno;	
}

function reColecao($id){
	$i= 1;
	$col[$i] = recuperaDados("acervo_acervos",$id,"id_acervo");
	$bread = array(1 => $col[$i]['acervo']);
	while($col[$i]['pai'] != 0){
		$i++;
		$col[$i] = recuperaDados("acervo_acervos",$col[$i-1]['id_acervo'],"id_acervo");
		echo $i;
		echo $col[$i]['acervo'];
	}
	return $bread;	

	
}

function breadCrumb(){
	// tipo > coleção > partitura > analítica
	$reg = recuperaDados("acervo_registro",$_SESSION['idReg'],"id_registro");
	$str = "";
	$colecao = reColecao($reg['id_acervo']);

		
	switch($reg['tabela']){
	
	case 87:
		$disco = recuperaDados("acervo_discoteca",$_SESSION['idDisco'],"idDisco");

		if($_SESSION['idFaixa'] != 0){
			$faixa = recuperaDados("acervo_discoteca",$_SESSION['idFaxia'],"idDisco");	
			
		}else{
			
		}
		
		
	break;
	
	case 97:
		$partitura = recuperaDados("acervo_discoteca",$_SESSION['idDisco'],"idDisco");

	
	break;	
	
	
	}
}


function duplicarReg($id){
	$con = bancoMysqli();
		
	$sql_duplicar = "INSERT INTO `acervo_registro` (`titulo`, `id_acervo`, `id_tabela`, `tabela`) SELECT titulo, id_acervo, id_tabela, tabela FROM `acervo_registro` WHERE id_registro = '$id'";
	$query_duplicar = mysqli_query($con,$sql_duplicar);
	
	if($query_duplicar){ // se duplicar, atualiza com novos dados
	
		$ultimo = mysqli_insert_id($con);
		$reg = recuperaDados("acervo_registro",$ultimo,"id_registro");
		$publicado = 1;
		$hoje = date("Y-m-d H:i:s");
		$idUsuario = $_SESSION['idUsuario']; 
		$titulo_duplicado = $reg['titulo']." (dup)";
		$sql_atualiza = "UPDATE acervo_registro SET publicado = '1', data_catalogacao = '$hoje', idUsuario = '$idUsuario', titulo = '$titulo_duplicado' WHERE id_registro = '$ultimo'";
		$query_atualiza = mysqli_query($con,$sql_atualiza);
		
		if($query_atualiza){ //se atualizar os dados, duplica na tabela
		$reg = recuperaDados("acervo_registro",$ultimo,"id_registro");
		
			switch($reg['tabela']){	
			
				case 87:			
				$sql_discoteca = "INSERT INTO `acervo_discoteca` (`editado`, `fim`, `planilha`, `matriz`, `catalogador`, `tipo_geral`, `tipo_especifico`, `tombo_tipo`, `lado`, `faixa`, `pag_inicial`, `pag_final`, `tombo`, `gravadora`, `registro`, `comp_registro`, `tipo_data`, `data_gravacao`, `local_gravacao`, `estereo`, `descricao_fisica`, `polegadas`, `faixas`, `duracao`, `exemplares`, `titulo_disco`, `titulo_faixa`, `titulo_uniforme`, `conteudo`, `titulo_resumo`, `serie`, `notas`, `obs`, `disponivel`, `idTemp`) SELECT `editado`, `fim`, `planilha`, `matriz`, `catalogador`, `tipo_geral`, `tipo_especifico`, `tombo_tipo`, `lado`, `faixa`, `pag_inicial`, `pag_final`, `tombo`, `gravadora`, `registro`, `comp_registro`, `tipo_data`, `data_gravacao`, `local_gravacao`, `estereo`, `descricao_fisica`, `polegadas`, `faixas`, `duracao`, `exemplares`, `titulo_disco`, `titulo_faixa`, `titulo_uniforme`, `conteudo`, `titulo_resumo`, `serie`, `notas`, `obs`, `disponivel`, `idTemp` FROM `acervo_discoteca` WHERE idDisco = '".$reg['id_tabela']."' ";
				$query_discoteca = mysqli_query($con,$sql_discoteca);
				if($query_discoteca){
					$ultimo_tabela = mysqli_insert_id($con);
						if($query_discoteca){ //se foi duplicado na tabela parittura ou discoteca, tem que atualizar no registro
							$sql_atualiza_id = "UPDATE acervo_registro SET id_tabela = '$ultimo_tabela', publicado = '1' WHERE id_registro = '$ultimo'";
							$query_atualiza_id = mysqli_query($con,$sql_atualiza_id);
							if($query_atualiza_id){ //se atualizou, duplica os termos
								$sql_termos = "SELECT * FROM acervo_relacao_termo WHERE idReg = '$id' and publicado = '1'";
								$query_termos = mysqli_query($con,$sql_termos);
								if($query_termos){
									while($termos = mysqli_fetch_array($query_termos)){
										$idReg = $ultimo;
										$idTermo = $termos['idTermo'];
										$idTipo = $termos['idTipo'];
										$idCat = $termos['idCat'];
										$sql_insert_rel = "INSERT INTO `acervo_relacao_termo` ( `idReg`, `idTermo`, `idTipo`, `idCat`, `publicado`) VALUES ('$idReg', '$idTermo', '$idTipo', '$idCat', '1')";
										$query_insert_rel = mysqli_query($con,$sql_insert_rel);
										if($query_insert_rel){
											$mensagem = "Registro duplicado com sucesso.";	
										}else{
											$mensagem = "Erro ao duplicar (8)";	
										}					
									}
									
								}else{
									$mensagem = "Erro ao duplicar (7)";	
								}
								
							}else{
								$mensagem = "Erro ao duplicar (6)";	
							}	
						}	
				}else{
					$mensagem = "Erro ao duplicar (4)";	
				}
					
				break;
				case 97:
				
				$sql_partituras = "INSERT INTO `acervo_partituras` (`editado`, `fim`, `planilha`, `matriz`, `catalogador`, `tipo_geral`, `tipo_especifico`, `tombo_tipo`, `lado`, `faixa`, `pag_inicial`, `pag_final`, `tombo`, `tombo_antigo`, `editora`, `registro`, `comp_registro`, `tipo_data`, `data_gravacao`, `local_gravacao`, `descricao_fisica`, `medidas`, `faixas`, `paginas`, `exemplares`, `titulo_disco`, `titulo_faixa`, `titulo_uniforme`, `titulo_geral`, `conteudo`, `titulo_obra`, `serie`, `notas`, `obs`, `disponivel`, `idTemp`) SELECT `editado`, `fim`, `planilha`, `matriz`, `catalogador`, `tipo_geral`, `tipo_especifico`, `tombo_tipo`, `lado`, `faixa`, `pag_inicial`, `pag_final`, `tombo`, `tombo_antigo`, `editora`, `registro`, `comp_registro`, `tipo_data`, `data_gravacao`, `local_gravacao`, `descricao_fisica`, `medidas`, `faixas`, `paginas`, `exemplares`, `titulo_disco`, `titulo_faixa`, `titulo_uniforme`, `titulo_geral`, `conteudo`, `titulo_obra`, `serie`, `notas`, `obs`, `disponivel`, `idTemp` FROM `acervo_partituras` WHERE idDisco = '".$reg['id_tabela']."' ";
				$query_partituras = mysqli_query($con,$sql_partituras);
				if($query_partituras){
					$ultimo_tabela = mysqli_insert_id($con);
					if($query_partituras){ //se foi duplicado na tabela parittura ou discoteca, tem que atualizar no registro
						$sql_atualiza_id = "UPDATE acervo_registro SET id_tabela = '$ultimo_tabela', publicado = '1' WHERE id_registro = '$ultimo'";
						$query_atualiza_id = mysqli_query($con,$sql_atualiza_id);
						if($query_atualiza_id){ //se atualizou, duplica os termos
							$sql_termos = "SELECT * FROM acervo_relacao_termo WHERE idReg = '$id' and publicado = '1'";
							$query_termos = mysqli_query($con,$sql_termos);
							if($query_termos){
								while($termos = mysqli_fetch_array($query_termos)){
									$idReg = $ultimo;
									$idTermo = $termos['idTermo'];
									$idTipo = $termos['idTipo'];
									$idCat = $termos['idCat'];
									$sql_insert_rel = "INSERT INTO `acervo_relacao_termo` ( `idReg`, `idTermo`, `idTipo`, `idCat`, `publicado`) VALUES ('$idReg', '$idTermo', '$idTipo', '$idCat', '1')";
									$query_insert_rel = mysqli_query($con,$sql_insert_rel);
									if($query_insert_rel){
										$mensagem = "Registro duplicado com sucesso.";	
									}else{
										$mensagem = "Erro ao duplicar (9)";	
									}					
								}
								
							}else{
								$mensagem = "Erro ao duplicar (10)";	
							}
							
						}else{
							$mensagem = "Erro ao duplicar (11)";	
						}	
					}
					
						
				}else{
					$mensagem = "Erro ao duplicar (5)";	
				}
				
				break;
				default:
				$mensagem = "Erro ao duplicar (3)";			
			}
			

			

		}else{
			$mensagem = "Erro ao duplicar (2) $sql_atualiza";	
		}

		
	}else{ //se não duplicar
		$mensagem = "Erro ao duplicar (1)";
		
	}
	
	$x['mensagem'] = $mensagem;
	$x['id'] =  $ultimo_tabela;
	return $x;
	
}

function reAnaliticas($id){
	$con = bancoMysqli();
	$r = recuperaDados("acervo_registro",$id,"id_registro");
	$i = 0;
	switch($r['tabela']){
		case 87:
			$id_tabela = $r['id_tabela'];
			$sql = "SELECT idDisco FROM acervo_discoteca WHERE matriz = '$id_tabela'";
				$query = mysqli_query($con,$sql);
				
	while($x = mysqli_fetch_array($query)){
		$y[$i]['idDisco'] = $x['idDisco'];
		$y[$i]['idTabela'] = 87;


		$i++;
	}
	return $y;

		break;
		case 97:
			$id_tabela = $r['id_tabela'];
			$sql = "SELECT idDisco FROM acervo_partituras WHERE matriz = '$id_tabela'";
	$query = mysqli_query($con,$sql);
	while($x = mysqli_fetch_array($query)){
		$y[$i]['idDisco'] = $x['idDisco'];
		$y[$i]['idTabela'] = 97;
		
		$i++;
	}
	return $y;
			break;
	

	
	}
	
}

?>
