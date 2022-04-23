<?php
include("php/functions.php");

if(isset($_GET['logoff']) == true){
    session_destroy();
    header('Location: /');
    die();  
}

if(isset($_SESSION['AUTH']) == true && $_SESSION['AUTH'] == true) {
    header('Location: system/');
    die();
}

if(isset($_POST["login"]) == true){
	
	if(isset($_SESSION['USER_CADASTRADO']) == false){
	
	$R = verifyCPF($_POST["CPF"]);
	
	if($R == "FALSO"){
        
		$_SESSION['TASK'] = array("error","CPF Inválido!");
		header('Location: /');
		die();
        
	}else{
        $CPF = RemoveMascaraCPF($_POST["CPF"]);
		$Auth = Valida_User($CPF);
	
		if($Auth == "FALSE"){
			$_SESSION['TASK'] = array("error","Usuário não encontrado!");
			header('Location: /');
			die();
		}else{
			$_SESSION['USER_CADASTRADO'] = true;
			$_SESSION['CPF'] = md5($CPF);
			header('Location: /');
			die();
		}
	}
	}else{
		
		$R = Valida_Senha($_SESSION['CPF'], $_POST["senha"]);
		
		if($R == "TRUE"){
			$_SESSION['AUTH'] = true;
			header('Location: system/');
			die();
		}else{
			$_SESSION['TASK'] = array("error","Senha Inválida!");
			header('Location: /');
			die();
		}
		
		
		
	}
    
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Controle de Acessos - Vitória Supermercados</title>
    <link href="img/Vitoria/vagas.png" rel="icon" type="image/x-icon" />

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
	<!-- icheck bootstrap -->
	<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="plugins/font-awesome-4.7.0/css/font-awesome.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="css/adminlte.min.css">
    <link rel="stylesheet" href="css/customcss.css">

</head>
    
<body class="hold-transition login-page" style="background-image: url('img/backgrounds/image.jpg'); background-size: cover">
<div class="login-box scale-in-center">
<!-- /.login-logo -->
    <div class="card card-outline card-primary" style="border: 1px solid">
        <div class="card-header text-center" style="padding: 15px">
            <div class="logo_sistema">
                <img src="img/backgrounds/asdasda.jpg" width="230">
			</div>
            <h3 for=""> - Controle de Acessos - </h3>
            <a href="/?logoff" class="h4">Vitória Supermercados</a>
        </div>
    <div class="card-body">
		
	<?php
		if(isset($_SESSION['USER_CADASTRADO']) == false && isset($_SESSION['AUTH']) == false) {	
	?>

    <form action="index.php" method="post" onsubmit="buttonlogin.disabled = true; buttonlogin.value = 'Validando CPF...'; return true;">
        <label for="">Informa seu CPF:</label>
		<div class="input-group mb-3 bounce-in-right">
            <input type="tel" data-mask="000.000.000-00" name="CPF" id="CPF" class="form-control" placeholder="Ex: 000.000.000-00" required autocomplete="off" autofocus>
            
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
	
	<?php
		}elseif($_SESSION['USER_CADASTRADO'] == true) {	
	?>
		
	<form action="index.php" method="post" onsubmit="buttonlogin.disabled = true; buttonlogin.value = 'Validando Senha...'; return true;">
        <label for="">Informa sua senha:</label>
		<div class="input-group mb-3 bounce-in-right">
            <input type="password" name="senha" id="senha" class="form-control" placeholder="Ex: **********" required autocomplete="off">
            
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
	
	<?php
		}else{ header('Location: /?logoff'); }
	?>
    <div class="" style="width: 100%; text-align: center; display: flex; justify-content: center">
        <!-- /.col -->
        <div class="w-100">
            <input type="hidden" name="login">
            <input type="submit" name="buttonlogin" class="btn btn-primary btn-block bounce-in-bottom" value="Continuar">
        </div>
        <!-- /.col -->
    </div>
    </form>
	<hr>
    <div class="w-100 d-flex justify-content-center">
        <a href="https://www.instagram.com/vitoriasupermercados_/?hl=pt-br">
            <i class="fa fa-instagram fa-2x" aria-hidden="true"></i>
        </a>      
    </div>
    <br>
    </div>
    <!-- /.card-body -->
    </div>
<!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.min.js"></script>

<script src="//assets.locaweb.com.br/locastyle/2.0.6/javascripts/locastyle.js"></script>

<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script src="plugins/sweetalert/sweetalert.min.js"></script>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>


<script>

<?php
	if(isset($_SESSION['TASK']) == true && $_SESSION['TASK'] != false){
    ?>

        $(document).ready(function () {
            swal({ text: "\n<?php echo $_SESSION['TASK'][1]; ?>", icon: "<?php echo $_SESSION['TASK'][0]; ?>" });
        });

    <?php $_SESSION['TASK'] = false; } ?>
</script>

</body>
</html>
