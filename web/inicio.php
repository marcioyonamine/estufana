<?php 
if(isset($_POST['status'])){
	changeStatus();
}


$status = getStatus();
if($status == 1){
	$botao = "Mudar modo para Programado";
}else{
	$botao = "Mudar modo para Manual";
}
?>

<section id="services" class="home-section bg-white">
	<div class="container">
		<div class="row">
			 <div class="col-md-offset-2 col-md-8">
				<div class="section-heading">
					 <h3>Bem-vindo(a)!</h3>
                    <p>O sistema está em modo: <strong><?php echo ($status == 1) ? 'Manual' : 'Programado'; ?></strong></p>
					<?php 
					$rele_status = releApi("array");
					$leitura = ultimaLeitura();
					?>
					<p>Rel01: <?php if($rele_status[1] == 1){ echo "LIGADO"; }else{echo "DESLIGADO";} ?> / Rele02: <?php if($rele_status[1] == 1){ echo "LIGADO"; }else{echo "DESLIGADO";} ?> / Rele03: <?php if($rele_status[1] == 1){ echo "LIGADO"; }else{echo "DESLIGADO";} ?> / Rele04: <?php if($rele_status[1] == 1){ echo "LIGADO"; }else{echo "DESLIGADO";} ?></p>
					<p> A última leitura foi feita em <?php echo exibirDataBr($leitura['datetime'])?> às <?php echo exibirHora($leitura['datetime'])?></p>
                    <p>Temperatura está em <?php echo $leitura['temp'] ?>ºC, a Umidade em <?php echo $leitura['temp'] ?>% e o pH em <?php echo $leitura['ph'] ?></p>
					
					
					
				</div>
			</div>
			<div class="form-group">
						<div class="col-md-offset-2 col-md-8">
						<form method="POST" action="?" class="form-horizontal" role="form">	
						<input type="hidden" name="status" value="<?php echo $status; ?>" />
						<input type="submit" class="btn btn-theme btn-lg btn-block" value="<?php echo $botao ?>">
						</form>
						</div>
			</div>

			<div class="form-group">
				<div class="col-md-offset-2 col-md-8">
			<br /><br />
			</div>
			</div>
<?php 
if($status == 1){ // manual

if(isset($_POST['rele01'])){
	mudaRele($_POST['rele01'],"01");
}
if(isset($_POST['rele02'])){
	mudaRele($_POST['rele02'],"02");
}
if(isset($_POST['rele03'])){
	mudaRele($_POST['rele03'],"03");
}
if(isset($_POST['rele04'])){
	mudaRele($_POST['rele04'],"04");
}





$rele = getRele(1);


?>

			<div class="form-group">
			
				<div class="col-md-offset-2 col-md-8">
					<table border = '1' width="100%">
					<tr>

					<td width='25%'>Relê 01</td><td width='25%'>Relê 02</td><td width='25%'>Relê 03</td><td>Relê 04</td>
					</tr>
					<tr>
					<td>
					<form method="POST" action="?" class="form-horizontal" role="form">	
						<input type="hidden" name="rele01" value="<?php echo $rele['rele01']; ?>" />
						<input type="submit" class="btn btn-theme btn-lg btn-block" value="<?php if($rele['rele01'] == 1) {echo "Desligar";}else{echo "Ligar";} ?>">
						</form>
					</td>
					<td>
					<form method="POST" action="?" class="form-horizontal" role="form">	
						<input type="hidden" name="rele02" value="<?php echo $rele['rele02']; ?>" />
						<input type="submit" class="btn btn-theme btn-lg btn-block" value="<?php if($rele['rele02'] == 1) {echo "Desligar";}else{echo "Ligar";} ?>">
						</form>
					</td>
					<td>

					<form method="POST" action="?" class="form-horizontal" role="form">	
						<input type="hidden" name="rele03" value="<?php echo $rele['rele03']; ?>" />
						<input type="submit" class="btn btn-theme btn-lg btn-block" value="<?php if($rele['rele03'] == 1) {echo "Desligar";}else{echo "Ligar";} ?>">
						</form>
					</td>
					<td>
					<form method="POST" action="?" class="form-horizontal" role="form">	
						<input type="hidden" name="rele04" value="<?php echo $rele['rele04']; ?>" />
						<input type="submit" class="btn btn-theme btn-lg btn-block" value="<?php if($rele['rele04'] == 1) {echo "Desligar";}else{echo "Ligar";} ?>">
						</form>
					</td>
					
					</tr>
					</table>


				</div>
			</div>
<?php 
}else{

	if(isset($_GET['p'])){
		$p = $_GET['p'];
	}else{
		$p = 'lista';	
	}

	switch($p){
	
	case "lista":	
	
	$con = bancoMysqli();
	
	
	if(isset($_POST['online'])){
		$id = $_POST['online'];
		$sql_zera = "UPDATE setup SET online = 0";
		mysqli_query($con,$sql_zera);
		
		$sql_online = "UPDATE setup set online = 1 WHERE id = '$id'";
		mysqli_query($con,$sql_online);
		
		
	}
	
	
	$sql = "SELECT * FROM setup ORDER BY online DESC";
	$query = mysqli_query($con,$sql);
	
	
	
?>
			<div class="form-group">
				<div class="col-md-offset-2 col-md-8">
	<h2>Lista Programado</h2>	
	
				</div>
			</div>	
			<div class="form-group">
				<div class="col-md-offset-2 col-md-8">
<a href='?p=insere' class="btn btn-theme btn-lg btn-block">Inserir nova programação</a>
	<br /><br />
				</div>
			</div>				
			<div class="form-group">
							<div class="col-md-offset-2 col-md-8">
			
			<table border = '1' width="100%">
					<tr>
					<td width="20%">Título</td>
					<td width="15%">Temperatura</td>
					<td width="15%">Umidade</td>
					<td width="15%">pH</td>
					<td width="15%">Iluminação</td>
					<td></td>
					<td></td>
					</tr>
					<?php 
					while($lista = mysqli_fetch_array($query)){
					?>
					
					
					<tr>
					<td><?php echo $lista['titulo']; ?></td>
					<td><?php echo $lista['temperatura']; ?>º / rele <?php echo $lista['rele_temp']; ?></td>
					<td><?php echo $lista['umidade']; ?>% / rele <?php echo $lista['rele_umidade']; ?></td>
					<td>entre <?php echo $lista['ph_min']; ?> e <?php echo $lista['ph_max']; ?></td>
					<td>entre <?php echo substr($lista['lampada_liga'],0,5); ?> e <?php echo substr($lista['lampada_desliga'],0,5); ?> / Rele <?php echo $lista['rele_lampada']; ?></td>
					<td><form method="POST" action="?p=edita" class="form-horizontal" role="form">	
						<input type="hidden" name="editar" value="<?php echo $lista['id']; ?>" />
						<input type="submit" class="btn btn-theme btn-lg btn-block" value="Editar">
						</form></td>
						<td>
						<?php if($lista['online'] != 1){?>
						<form method="POST" action="?p=lista" class="form-horizontal" role="form">	

					<input type="hidden" name="online" value="<?php echo $lista['id']; ?>" />
						<input type="submit" class="btn btn-theme btn-lg btn-block" value="Offline">
						</form>
						<?php }else{ 
						
						?>
						<a href='#' class="btn btn-theme btn-lg btn-block">online</a>
						<?php } ?>						
						
	</td>
					
					
					</tr>
					
					<?php } ?>
					</table>


				</div>
			</div>

<?php 
	break;
	case "insere":

?>			
 <script>
$(function() {
    $( ".calendario" ).datepicker();
	$( ".hora" ).mask("99:99");
	$( ".min" ).mask("999");
	$( ".ph" ).mask("99");
	$( ".valor" ).maskMoney({prefix:'', thousands:'.', decimal:',', affixesStay: true});
});
</script>
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="form-group">
					
               </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">
				<h1>Insere nova programação</h1>
				<form class="form-horizontal" role="form" action="?p=edita" method="post">
                <div class="form-group">
				<div class="col-md-offset-2 col-md-8"><strong>Título da programação</strong><br/>
					  <input type="text" class="form-control" id="titulo" name="titulo"  value="" >
                      </div>
				  </div>	
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>Temperatura mínima:</strong><br/>
					  <input type="text" class="form-control" id="temperatura" name="temperatura"  value="" >

					</div>				  
					<div class=" col-md-6"><strong>Rele:</strong><br/>
                	  <select class="form-control" id="rele_temp" name="rele_temp" >
						<option value='1'>rele01</option>
						<option value='2'>rele02</option>
						<option value='3'>rele03</option>
						<option value='4'>rele04</option>
					</select>
					</div>
				  </div>
				   <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>Umidade mínima:</strong><br/>
					  <input type="text" class="form-control" id="umidade" name="umidade"  value="" >

					</div>				  
					<div class=" col-md-6"><strong>Rele:</strong><br/>
                	  <select class="form-control" id="rele_umidade" name="rele_umidade" >
						<option value='1'>rele01</option>
						<option value='2'>rele02</option>
						<option value='3'>rele03</option>
						<option value='4'>rele04</option>
					</select>
					</div>
				  </div>
				  
				   <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>Horário Liga Lâmpada:</strong><br/>
					  <input type="text" class="form-control hora" id="lamp_liga" name="lamp_liga"  value="" >

					</div>				  
					<div class=" col-md-6"><strong>Horário Desliga Lâmpada:</strong><br/>
					  <input type="text" class="form-control hora" id="lamp_desliga" name="lamp_desliga"  value="" >
					</div>
				  </div>
                  <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Rele</strong><br/>
                	  <select class="form-control" id="rele_lampada" name="rele_lampada" >
						<option value='1'>rele01</option>
						<option value='2'>rele02</option>
						<option value='3'>rele03</option>
						<option value='4'>rele04</option>
					  </select>
                      </div>
				  </div>
				   <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>pH minimo:</strong><br/>
					  <input type="text" class="form-control ph" id="ph_min" name="ph_min"  value="" >

					</div>				  
					<div class=" col-md-6"><strong>ph máximo:</strong><br/>
					  <input type="text" class="form-control ph" id="ph_max" name="ph_max"  value="" >
					</div>
				  </div>
                  <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Email de alerta</strong><br/>
						<input type="text" class="form-control" id="email" name="email"  value="" >
                      </div>
				  </div>
				  
				  
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
                    <input type="hidden" name="cadastraProgramacao" value="1" />
 					 <input type="submit" value="INSERIR" class="btn btn-theme btn-lg btn-block">
					</div>
				  </div>
				</form>

				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
 					 <a href='?p=lista' class="btn btn-theme btn-lg btn-block">Listar Programas</a>
					</div>
				  </div>

				</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  
	  
	  
<?php 
	break;
	case "edita":
	
	if(isset($_POST['editar'])){
		$id_prog = $_POST['editar'];
	}
	
	if(isset($_POST['cadastraProgramacao']) OR isset ($_POST['editaProgramacao'])){
		$con = bancoMysqli();
		
		// trata as variáveis via post
		$titulo = addslashes($_POST["titulo"]);
		$temperatura = $_POST["temperatura"];
		$rele_temp = $_POST["rele_temp"];
		$umidade = $_POST["umidade"];
		$rele_umidade = $_POST["rele_umidade"];
		$lamp_liga = $_POST["lamp_liga"].":00";
		$lamp_desliga = $_POST["lamp_desliga"].":00";
		$rele_lampada = $_POST["rele_lampada"];
		$ph_min = $_POST["ph_min"];
		if($ph_min < 0){
			$ph_min = 0;
		}
		$ph_max = $_POST["ph_max"];
		if($ph_max > 14){
			$ph_max = 14;
		}	
		if($ph_min > $ph_max){
			$ph_min = $ph_temp_min;
			$ph_max = $ph_temp_max;
			$ph_min = $ph_temp_max;
			$ph_max =$ph_temp_min;
		}
		$email = $_POST["email"];

		if(isset($_POST['cadastraProgramacao'])){
			$sql = "INSERT INTO `setup` (`id`, `titulo`, `temperatura`, `rele_temp`, `umidade`, `rele_umidade`, `lampada_liga`, `lampada_desliga`, `rele_lampada`, `ph_min`, `ph_max`, `email`, `online`, `publicado`) 
			VALUES (NULL, '$titulo','$temperatura' ,'$rele_temp', '$umidade', '$rele_umidade', '$lamp_liga', '$lamp_desliga', '$rele_lampada', '$ph_min', '$ph_max', '$email', '',  '1')";
			$query = mysqli_query($con,$sql);
			if($query){
				$mensagem = "Programa inserido com sucesso";
				$id_prog = mysqli_insert_id($con);
			}else{
				$mensagem = "Erro ao inserir o programa. Tente novamente.";
			}
			
			
			
		}
		
		if(isset($_POST['editaProgramacao'])){
			$id_prog = $_POST['editaProgramacao'];
			$sql = "UPDATE setup SET
			`titulo` = '$titulo',
			`temperatura` = '$temperatura',
			`rele_temp` = '$rele_temp',
			`umidade` = '$umidade',
			`rele_umidade` = '$rele_umidade',
			`lampada_liga` = '$lamp_liga',
			`lampada_desliga` = '$lamp_desliga',
			`rele_lampada` = '$rele_lampada',
			`ph_min` = '$ph_min',
			`ph_max` = '$ph_max',
			`email` = '$email'
			WHERE id = '$id_prog'";
			
			$query = mysqli_query($con,$sql);
			if($query){
				$mensagem = "Atualizado com sucesso";
			}else{
				$mensagem = "Erro ao atualizar. Tente novamente. $sql";
			}
		}
		
	
}

$prog = recuperaDados("setup",$id_prog,"id");

?>
<script>
$(function() {
    $( ".calendario" ).datepicker();
	$( ".hora" ).mask("99:99");
	$( ".min" ).mask("999");
	$( ".ph" ).mask("99");
	$( ".valor" ).maskMoney({prefix:'', thousands:'.', decimal:',', affixesStay: true});
});
</script>
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="form-group">
					
               </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">
				<h1>Edita programação</h1>
				<?php if(isset($mensagem)){echo $mensagem;}?>
				<form class="form-horizontal" role="form" action="?p=edita" method="post">
                <div class="form-group">
				<div class="col-md-offset-2 col-md-8"><strong>Título da programação</strong><br/>
					  <input type="text" class="form-control" id="titulo" name="titulo"  value="<?php echo $prog['titulo'] ?>" >
                      </div>
				  </div>	
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>Temperatura mínima:</strong><br/>
					  <input type="text" class="form-control" id="temperatura" name="temperatura"  value="<?php echo $prog['temperatura'] ?>" >

					</div>				  
					<div class=" col-md-6"><strong>Rele:</strong><br/>
                	  <select class="form-control" id="rele_temp" name="rele_temp" >
						<option value='1' <?php if($prog['rele_temp'] == 1){ echo " selected";} ?> >rele01</option>
						<option value='2' <?php if($prog['rele_temp'] == 2){ echo " selected";} ?>>rele02</option>
						<option value='3' <?php if($prog['rele_temp'] == 3){ echo " selected";} ?>>rele03</option>
						<option value='4' <?php if($prog['rele_temp'] == 4){ echo " selected";} ?>>rele04</option>
					</select>
					</div>
				  </div>
				   <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>Umidade mínima:</strong><br/>
					  <input type="text" class="form-control" id="umidade" name="umidade"  value="<?php echo $prog['umidade'] ?>" >

					</div>				  
					<div class=" col-md-6"><strong>Rele:</strong><br/>
                	  <select class="form-control" id="rele_umidade" name="rele_umidade" >
						<option value='1' <?php if($prog['rele_umidade'] == 1){ echo " selected";} ?> >rele01</option>
						<option value='2' <?php if($prog['rele_umidade'] == 2){ echo " selected";} ?>>rele02</option>
						<option value='3' <?php if($prog['rele_umidade'] == 3){ echo " selected";} ?>>rele03</option>
						<option value='4' <?php if($prog['rele_umidade'] == 4){ echo " selected";} ?>>rele04</option>
					</select>
					</div>
				  </div>
				  
				   <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>Horário Liga Lâmpada:</strong><br/>
					  <input type="text" class="form-control hora" id="lamp_liga" name="lamp_liga"  value="<?php echo substr($prog['lampada_liga'],0,6) ?>" >

					</div>				  
					<div class=" col-md-6"><strong>Horário Desliga Lâmpada:</strong><br/>
					  <input type="text" class="form-control hora" id="lamp_desliga" name="lamp_desliga"  value="<?php echo substr($prog['lampada_desliga'],0,6) ?>" >
					</div>
				  </div>
                  <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Rele</strong><br/>
                	  <select class="form-control" id="rele_lampada" name="rele_lampada" >
						<option value='1' <?php if($prog['rele_lampada'] == 1){ echo " selected";} ?> >rele01</option>
						<option value='2' <?php if($prog['rele_lampada'] == 2){ echo " selected";} ?>>rele02</option>
						<option value='3' <?php if($prog['rele_lampada'] == 3){ echo " selected";} ?>>rele03</option>
						<option value='4' <?php if($prog['rele_lampada'] == 4){ echo " selected";} ?>>rele04</option>
					  </select>
                      </div>
				  </div>
				   <div class="form-group">
					<div class="col-md-offset-2 col-md-6"><strong>pH minimo:</strong><br/>
					  <input type="text" class="form-control ph" id="ph_min" name="ph_min"  value="<?php echo $prog['ph_min'] ?>" >

					</div>				  
					<div class=" col-md-6"><strong>ph máximo:</strong><br/>
					  <input type="text" class="form-control ph" id="ph_max" name="ph_max"  value="<?php echo $prog['ph_max'] ?>" >
					</div>
				  </div>
                  <div class="form-group">
					<div class="col-md-offset-2 col-md-8"><strong>Email de alerta</strong><br/>
						<input type="text" class="form-control" id="email" name="email"  value="<?php echo $prog['email'] ?>" >
                      </div>
				  </div>
				  
				  
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
                    <input type="hidden" name="editaProgramacao" value="<?php echo $id_prog; ?>" />
 					 <input type="submit" value="Atualizar" class="btn btn-theme btn-lg btn-block">
					</div>
				  </div>
				</form>
				  				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
 					 <a href='?p=lista' class="btn btn-theme btn-lg btn-block">Listar Programas</a>
					</div>
				  </div>
                

    
	  			</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  
	  
	  

<?php 
	var_sistema();

	break;

	
	} //fim da switchpage


} // fim do else
?>


			</div>


		</div>

</section>