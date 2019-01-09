<?php 
	include "../inc/funcoesConecta.php"; //carrega a conexão com o banco de dados
	include "../inc/funcoesGerais.php"; //carrega as funções gerais
	
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
		date_default_timezone_set('America/Sao_Paulo');
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




	$y = json_encode($rele);
	echo $y;