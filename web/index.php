<?php 

include "inc/funcoesConecta.php";
include "inc/funcoesGerais.php";

if(isset($_POST['usuario'])){

	$usuario = $_POST['usuario'];
	$senha = $_POST['senha'];
	autenticaUsuario($usuario,$senha);	

}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Estufana - v.01 - 2019</title>
    <link href="visual/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="visual/css/style.css" rel="stylesheet" media="screen">
	<link href="visual/color/default.css" rel="stylesheet" media="screen">
	<script src="visual/js/modernizr.custom.js"></script>
</head>


<body>
	  <section id="contact" class="home-section bg-white">
	  	<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="text-hide">
					 <h2>Estufana</h2>
					 </div>
				  </div>
			  </div>

	  		<div class="row">
	  			<div class="col-md-offset-1 col-md-10">

				<form method="POST" action="index.php"class="form-horizontal" role="form">
				  <div class="form-group">
					<div class="col-md-offset-2 col-md-6">
					  <input type="text" name="usuario" class="form-control" id="inputName" placeholder="Usuário">
					</div>
				  
					<div class=" col-md-6">
					  <input type="password" name="senha" class="form-control" id="inputEmail" placeholder="Senha">
					</div>
				  </div>

				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
					 <button type="submit" class="btn btn-theme btn-lg btn-block">Entrar</button>
					</div>
				  </div>
				</form>
				<br />
                <br />
				<br />
                

				  <div class="form-group">
					<div class="col-md-offset-2 col-md-8">
					<br />
                                      
					</div>
				  </div>




	
	  			</div>
			
				
	  		</div>
			

	  	</div>
	  </section>  

    


</body>
</html>
