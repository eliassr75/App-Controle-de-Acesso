<?php 
include("../php/functions.php");
include("../php/connect.php");

if(isset($_SESSION['CPF']) == false || strlen($_SESSION['CPF']) < 1){
    header('Location: /?logoff');
    die();
}

$User = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM promotores WHERE cpf = '".$_SESSION['CPF']."'"));

if(mysqli_fetch_row(mysqli_query($con, "SELECT * FROM promotores WHERE cpf = '".$_SESSION['CPF']."'")) > 0) {


if(isset($_POST["save"]) == true){	
    
    if(strlen($_POST["nome-pessoa"]) < 1){
        $_SESSION['TASK'] = array("error","Não é possível adicionar registro com campos vazios!");
		header('Location: /');
		die();
    }else{
        
        if(strlen($_POST["add-destino"]) < 1 && strlen($_POST["destino"]) < 4){
            $_SESSION['TASK'] = array("error","Não é possível adicionar registro com campos vazios!");
            header('Location: /');
            die();    
        }else{
	
            if(strlen($_POST["add-destino"]) > 3){

                $Q = "INSERT INTO destinos (destino) VALUES ('".strtoupper($_POST["add-destino"])."')";
                $R = mysqli_query($con, $Q) or die(mysqli_error($con));

                $destino = strtoupper($_POST["add-destino"]);

            }else{
                $destino = strtoupper($_POST["destino"]);
            }

            $user = $User["nome"];
            $nome = strtoupper($_POST["nome-pessoa"]);
            $documento = $_POST["documento"];
            $data = date("Y-m-d H:i:s");

            $Q = "INSERT INTO controle_ponto(data_entrada, nome, destino, documento, registrador, status) VALUES ('$data', '$nome', '$destino', '$documento', '$user', 'Aberto')";
            $R = mysqli_query($con, $Q) or die(mysqli_error($con));

            if($R){
                $_SESSION['TASK'] = array("success","Registro Adicionado. Lembre o Visitante que deve apenas informar o documento cadastrado na Próxima visita!");
                header('Location: /');
                die();
            }
            
        }
    }
}

if(isset($_POST["save-user"]) == true){
    
    $CPF = md5(RemoveMascaraCPF($_POST["CPF"]));
    $NOME = strtoupper($_POST["nome"]);
    $SENHA = $_POST["senha"];
    $TYPE = $_POST["type"];
    
    $Q = mysqli_query($con, "INSERT INTO promotores (nome, cpf, senha, admin, data_cadastro) VALUES ('$NOME', '$CPF', '$SENHA', '$TYPE', '".date('Y-m-d H:i:s')."');") or die(mysqli_error($con));
    
    $_SESSION['TASK'] = array("success","Usuário Adicionado!");
    header('Location: index.php');
    die();

}
    
if(isset($_POST["relatorio"]) == true){
    $documento = $_POST["documento"];
        if(strlen($documento) > 1){
            $F_D = "documento='$documento'";
        }else{
            $F_D = "documento IS NOT NULL";
        }
    $nome = $_POST["nome"];
        if(strlen($nome) > 1){
            $F_N = "nome LIKE '%$nome%'";
        }else{
            $F_N = "nome IS NOT NULL";
        }
    $data = $_POST["data"];
        if(strlen($data) > 1){
            $F_DT = "data_entrada LIKE '$data%'";
        }else{
            $F_DT = "data_entrada IS NOT NULL";
        }
    $destino = $_POST["destino"];
        if(strlen($destino) > 1){
            $F_DTN = "destino='$destino'";
        }else{
            $F_DTN = "destino IS NOT NULL";
        }
    
        $Filtro = $F_D." AND ".$F_N." AND ".$F_DT." AND ".$F_DTN;
}
    
if(isset($_POST["redefinir"]) == true){
    
    $Q = mysqli_query($con, "UPDATE promotores SET senha='".$_POST["senha"]."' WHERE id=".$_POST["usuario"].";") or die(mysqli_error($con));
    
    $_SESSION['TASK'] = array("success","Senha Alterada!");
    header('Location: index.php');
    die();
    
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Controle de Acessos - Vitória Supermercados</title>
	<link href="../img/Vitoria/vagas.png" rel="icon" type="image/x-icon" />
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="../plugins/bootstrap/bootstrap.min.css" type="text/css">
	<!-- DataTables -->
	<link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
	<link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
	<link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="../css/adminlte.min.css">
	<!-- BS Stepper -->
  	<link rel="stylesheet" href="../plugins/bs-stepper/css/bs-stepper.min.css">
    
</head>
<body class="hold-transition sidebar-mini sidebar-collapse layout-navbar-fixed" style="background-image: url('../img/backgrounds/image.jpg'); background-size: cover">
<div class="wrapper">


<!-- Content Header (Page header) -->
<br>
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<!-- /.row -->
		<div class="row">
			<div class="col-12">

        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title w-100">
					<div class="text-center">
                        <br>
                        <label for="">Olá <?php echo $User["nome"]; ?>!</label><hr>
                        <label for="">Fluxo de Registros: <?php echo date('d/m/Y'); ?></label>
                        
                        <?php if($_SESSION['ADMIN'] == false){ ?>
						<button class="btn btn-primary w-100" data-toggle="modal" data-target="#add-batida-modal" onClick="Clean()">
				            Adicionar Registro
			            </button>
                        <?php }else{ ?>
                        <br>
                        <button class="btn btn-primary w-49" data-toggle="modal" data-target="#add-batida-modal" onClick="Clean()">
				            Adicionar Registro
			            </button>
                        
                        <!-- Button Menu -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            Menu
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" data-toggle="modal" data-target="#relatorio-modal">Relarório</a></li>
                                <li><a class="dropdown-item" data-toggle="modal" data-target="#add-user-modal">Adicionar Usuário</a></li>
                                <li><a class="dropdown-item" data-toggle="modal" data-target="#senha-modal">Redefinir Senha</a></li>
                                <li><a class="dropdown-item" href="index.php">Mostrar Registros (Hoje)</a></li>
                            </ul>
                        </div>
                        
                        <a href="/?logoff" class="btn btn-primary" onClick="window.onbeforeunload = null;"><i class="fa fa-sign-out"></i> Sair</a>
                        <?php } ?>
                        
                        <!-- Modal Add Registro -->
                        <div class="modal fade" id="add-batida-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="add-folders-modal" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-center w-100" id="add-folders-modal">Adicionar Registro de Entrada</h5>
                                    </div>

                                    <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="myButton.disabled = true; myButton.value = 'Por favor aguarde...'; return true;">
                                    <div class="modal-body col-12" style="text-align: left">
                                        <div class="card-body">
                                            <label for="">Procurar Cadastro (Informe o RG ou CPF):</label>
                                            <div class="w-100 p-1 d-flex text-center">
                                                <input type="tel" class="form-control w-95" id="search-input" placeholder="Faça uma busca. Ex: 1234567 ou 12345678910" minlength="4">
                                                &nbsp;
                                                <button type="button" id="search-button" class="btn btn-primary w-4" onClick="Run_Search()"><i class="fa fa-search" aria-hidden="true"></i></button>
                                            </div>
                                            <hr>
                                            <div id="form">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="save">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Add Registro End -->
                        
                        <!-- Modal Add User -->
                        <div class="modal fade" id="add-user-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="add-folders-modal" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title text-center w-100" id="add-user-modal">Adicionar Usuário</h5>
                                    </div>

                                    <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="myButton.disabled = true; myButton.value = 'Por favor aguarde...'; return true;">
                                    <div class="modal-body col-12" style="text-align: left">
                                        <div class="card-body">
                                            <label for="">Informe o CPF do usuário:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <input type="tel" data-mask="000.000.000-00" name="CPF" id="CPF" class="form-control" placeholder="Ex: 000.000.000-00" required autocomplete="off" autofocus>
                                            </div>

                                            <label for="">Nome e Sobrenome:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <input type="text" name="nome" class="form-control" placeholder="Ex: Marcos Gustavo" required autocomplete="off">
                                            </div>

                                            <label for="">Escolha uma Senha:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <input type="password" name="senha" class="form-control" placeholder="Ex: ******" required autocomplete="off"  minlength="6">
                                            </div>
                                            
                                            <label for="">Tipo de Usuário:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <select name="type" id="type" class="form-control" onChange="myButton.disabled = false; myButton.value = 'Salvar';">
                                                    <option value="" disabled selected>Selecione uma opção:</option>
                                                    <option value="0">Registrador</option>
                                                    <option value="1">Administrador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="save-user">
                                        <input type="submit" disabled class="btn btn-primary" name="myButton" onClick="window.onbeforeunload = null;" id="myButton" value="Selecione um tipo de usuário">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Add User End -->
                        
                        <!-- Modal Relatório -->
                        <div class="modal fade" id="relatorio-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="add-folders-modal" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title text-center w-100" id="relatorio-modal">Relatório</h5>
                                    </div>

                                    <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="myButton.disabled = true; myButton.value = 'Por favor aguarde...'; return true;">
                                    <div class="modal-body col-12" style="text-align: left">
                                        <div class="card-body">
                                            <label for="">Buscar com CPF ou RG (Apenas Números):</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <input type="tel" name="documento" class="form-control" placeholder="Ex: 12345678 ou 12345678910" autocomplete="off" autofocus>
                                            </div>

                                            <label for="">Data:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <input type="date" name="data" class="form-control">
                                            </div>
                                            
                                            <label for="">Nome do Visitante:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <input type="text" name="nome" class="form-control" autocomplete="off" placeholder="João Silva">
                                            </div>
                                            
                                            <label for="">Destino:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <select name="destino" id="destino" class="form-control">

                                                    <option value="bah" selected disabled>Clique para Selecionar:</option>

                                                    <?php
                                                    //Destinos    
                                                    $QD = mysqli_query($con, "SELECT * from destinos;") or die(mysqli_error($con));
                                                    $RQD = mysqli_fetch_assoc($QD);

                                                    do{    

                                                    $temp = $temp.'<option value="'.$RQD["destino"].'">'.$RQD["destino"].'</option>';

                                                    }while($RQD = mysqli_fetch_assoc($QD)); 
                                                    //Fim Destinos

                                                    echo $temp;

                                                    ?>

                                                </select>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="relatorio">
                                        <input type="submit" class="btn btn-primary" name="myButton" onClick="window.onbeforeunload = null;" id="myButton" value="Fazer Busca">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Relatório End -->
                        
                        <!-- Modal Senha -->
                        <div class="modal fade" id="senha-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="add-folders-modal" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title text-center w-100" id="senha-modal">Alteração de Senha</h5>
                                    </div>

                                    <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="myButton.disabled = true; myButton.value = 'Por favor aguarde...'; return true;">
                                    <div class="modal-body col-12" style="text-align: left">
                                        <div class="card-body">
                                            
                                            <label for="">Selecione um Usuário para Redefinir:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <select name="usuario" id="usuario" class="form-control" onChange="myButton.disabled=false; myButton.value='Redefinir Senha';">

                                                    <option value="bah" selected disabled>Clique para Selecionar:</option>

                                                    <?php
                                                    //Usuários    
                                                    $QU = mysqli_query($con, "SELECT * from promotores;") or die(mysqli_error($con));
                                                    $RQU = mysqli_fetch_assoc($QU);

                                                    do{    

                                                    $temU = $temU.'<option value="'.$RQU["id"].'">'.$RQU["nome"].'</option>';

                                                    }while($RQU = mysqli_fetch_assoc($QU)); 
                                                    //Fim Usuários

                                                    echo $temU;

                                                    ?>

                                                </select>
                                            </div>
                                            
                                            <label for="">Informe a Nova Senha:</label>
                                            <div class="input-group mb-3 bounce-in-right">
                                                <input type="password" name="senha" class="form-control" minlength="6" placeholder="Ex: ******" required>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="redefinir">
                                        <input type="submit" disabled class="btn btn-primary" name="myButton" onClick="window.onbeforeunload = null;" id="myButton" value="Selecione um usuário">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Senha End -->
                        
					</div>
				</h3>
              </div>
              <!-- /.card-header -->
              
              <div class="card-body" id="relatorio">
					
                 <div id="accordion">
                     
                <?php
    
                if(isset($Filtro) == true && strlen($Filtro) > 10){
                    $Query = "SELECT * FROM controle_ponto WHERE $Filtro order by data_entrada DESC;";
                    $Result = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM controle_ponto WHERE $Filtro;"));
                    echo "<div class='w-100 text-center'>Resultados para o Filtro: ".$Result["total"]."</div><hr>";
                }else{
                    $Query = "SELECT * FROM controle_ponto WHERE status='Aberto' and data_entrada LIKE '%".date('Y-m-d')."%' order by data_entrada DESC;";
                    $Result = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM controle_ponto WHERE status='Aberto' and data_entrada LIKE '%".date('Y-m-d')."%';"));
                }
                
                $QR = mysqli_query($con, $Query) or die(mysqli_error($con));
    
                if(mysqli_fetch_row($QR) < 1){
                    
                    echo "<div class='w-100 text-center'>Nenhum Registro Encontrado!</div>";
                    
                }else{
                    
                $QR = mysqli_query($con, $Query) or die(mysqli_error($con));
                $RQR = mysqli_fetch_assoc($QR);
                 
                 $C = 0;
                 $id = $Result["total"];
                 do{
                     
                 ?>
                    
                  <!-- Endereço -->
                  <div class="card card-primary">
                    <div class="card-header">
                      <h4 class="card-title w-100">
                        <a class="d-flex w-100 justify-content-between" data-toggle="collapse" href="#collapse<?php echo $C; ?>">
                          <?php echo $id. " - ". $RQR["nome"]; ?>  <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
                        </a>
                      </h4>
                    </div>
                    <div id="collapse<?php echo $C; ?>" class="collapse" data-parent="#accordion">
                      <div class="card-body">
                        <div class="form-group">
				            <span>NOME:</span> <label for=""><?php echo $RQR["nome"]; ?></label>
							<br>
							<span>DOCUMENTO:</span> <label for=""><?php echo $RQR["documento"]; ?></label>
							<br>
                            <span>DESTINO:</span> <label for=""><?php echo $RQR["destino"]; ?></label>
							<br>
							<span>ENTRADA:</span> <label for=""><?php echo date('d/m/Y H:i', strtotime($RQR["data_entrada"])); ?></label>
							<br>
							<span>REGISTRADOR:</span> <label for=""><?php echo $RQR["registrador"]; ?></label>
                            
                            <hr>
                            <?php if($_SESSION['ADMIN'] == true){ ?>
                            
                            <div class="w-100 text-center">
                                <button class="btn btn-danger col-6" id="delete<?php echo $RQR["id"]; ?>" onClick="Delete(<?php echo $RQR["id"]; ?>)">Apagar Registro</button>
                            </div>
                            
                            <?php } ?>
						</div>
                      </div>
                    </div>
                  </div>
                  <!-- Endereço -->   
                     
                  <?php
                  $C++;
                  $id--;
                  }while($RQR = mysqli_fetch_assoc($QR));
                    
                  }
                  ?>
                        
                </div>
                
              </div>
              <!-- /.card-body -->
            </div>
			  
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        	
			</div>
		</div>
		<!-- /.row -->
	</div>
	<!-- /.container-fluid -->
</section>
<!-- /.content -->
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
<!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>   

<script src="//assets.locaweb.com.br/locastyle/2.0.6/javascripts/locastyle.js"></script>
    
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- InputMask -->
<script src="../plugins/moment/moment.min.js"></script>
<script src="../plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- AdminLTE App -->
<script src="../js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../js/demo.js"></script>
<!-- Page specific script -->
<script src="../plugins/sweetalert/sweetalert.min.js"></script>
<script src="../plugins/bs-stepper/js/bs-stepper.min.js"></script>
	
<script>
    
    function Clean(){
        $("#form").html(" ");
    }
    
    function Delete(Id){
        swal({
          title: "É isso mesmo?",
          text: "Você está prestes a remover um registro. Esse processo é irreversível!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $("#delete"+Id).load("../php/functions.php", {delete:Id})
            swal("Certo! Registro Removido.", {icon: "success",}).then(window.onbeforeunload = null)
            setTimeout(function(){
                window.location.reload();
            }, 1000)
          }
        });
    }
	
    $('.select2').select2();

	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	  return new bootstrap.Tooltip(tooltipTriggerEl)
	});    

// BS-Stepper Init
  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
  })
	
</script>
	
<script language="JavaScript">
    
function Relatorio(){
    $("#accordion").hide()
    $("#relatorio").html('<label for="">Procurar Cadastro (Nome ou Documento):</label><div class="w-100 p-1 d-flex text-center"><input type="tel" class="form-control w-95" id="search-input" placeholder="Faça uma busca. Ex: João da Silva, 1234567 ou 12345678910" minlength="4">&nbsp;<button type="button" id="search-button" class="btn btn-primary w-4" onClick="Run_Search_Relatorio()"><i class="fa fa-search" aria-hidden="true"></i></button></div>')
    
}
    
//Script para Fazer Pesquisa de Cadasro
function Run_Search() {
    var Text = $("#search-input").val();
    if (Text.length < 8) {
        swal({ text: "\nIdentificação Inválida!", icon: "warning" });
    }else{
        $("#form").html('<div class="col-12 d-flex justify-content-center"><div class="text-center">Buscando Dados...</div></div>');
        setTimeout(function(){
            $("#form").load("../php/functions.php", {"search":Text}).css("text-align", "left");
        }, 500)
    }
}    
    
//Script para Fazer Pesquisa de Relatório
function Run_Search_Relatorio() {
    var Text = $("#search-input").val();
    if (Text.length < 4) {
        swal({ text: "\nMínimo de Caracteres: 4!", icon: "warning" });
    }else{
        $("#form").html('<div class="col-12 d-flex justify-content-center"><div class="text-center">Buscando Dados...</div></div>');
        setTimeout(function(){
            $("#form").load("../php/functions.php", {"search_relatório":Text}).css("text-align", "left");
        }, 500)
    }
}  

/*-----------------------------------------------------------------------
Máscara para o campo data dd/mm/aaaa hh:mm:ss
Exemplo: <input maxlength="16" name="datahora" onKeyPress="DataHora(event, this)">
-----------------------------------------------------------------------*/
function DataHora(evento, objeto){
	var keypress=(window.event)?event.keyCode:evento.which;
	campo = eval (objeto);
	if (campo.value == '00/00/0000 00:00:00')
	{

	}
 
	caracteres = '0123456789';
	separacao1 = '-';
	separacao2 = ' ';
	separacao3 = ':';
	conjunto1 = 2;
	conjunto2 = 5;
	conjunto3 = 10;
	conjunto4 = 13;
	conjunto5 = 16;
	if ((caracteres.search(String.fromCharCode (keypress))!=-1) && campo.value.length < (19))
	{
		if (campo.value.length == conjunto1 )
		campo.value = campo.value + separacao1;
		else if (campo.value.length == conjunto2)
		campo.value = campo.value + separacao1;
		else if (campo.value.length == conjunto3)
		campo.value = campo.value + separacao2;
		else if (campo.value.length == conjunto4)
		campo.value = campo.value + separacao3;
		else if (campo.value.length == conjunto5)
		campo.value = campo.value + separacao3;
	}
	else
		event.returnValue = false;
}
    
function Data(evento, objeto){
	var keypress=(window.event)?event.keyCode:evento.which;
	campo = eval (objeto);
	if (campo.value == '00/00/0000 00:00:00')
	{

	}
 
	caracteres = '0123456789';
	separacao1 = '-';
	conjunto1 = 2;
	conjunto2 = 5;
	conjunto3 = 10;
    
	if ((caracteres.search(String.fromCharCode (keypress))!=-1) && campo.value.length < (19))
	{
		if (campo.value.length == conjunto1 )
		campo.value = campo.value + separacao1;
		else if (campo.value.length == conjunto2)
		campo.value = campo.value + separacao1;
	}
	else
		event.returnValue = false;
}
    
function Telefone(evento, objeto){
	var keypress=(window.event)?event.keyCode:evento.which;
	campo = eval (objeto);
	if (campo.value == '00 00000-0000')
	{

	}
 
	caracteres = '0123456789';
	separacao0 = ' ';
    separacao1 = '-';
    conjunto0 = 2;
	conjunto1 = 8;
    
	if ((caracteres.search(String.fromCharCode (keypress))!=-1) && campo.value.length < (19))
	{
		if (campo.value.length == conjunto0 )
		campo.value = campo.value + separacao0;
		else if (campo.value.length == conjunto1)
		campo.value = campo.value + separacao1;
	}
	else
		event.returnValue = false;
}
    
function CEP(evento, objeto){
	var keypress=(window.event)?event.keyCode:evento.which;
	campo = eval (objeto);
	if (campo.value == '00000-000')
	{

	}
 
	caracteres = '0123456789';
	separacao0 = '-';
    conjunto0 = 5;

	if ((caracteres.search(String.fromCharCode (keypress))!=-1) && campo.value.length < (19))
	{
		if (campo.value.length == conjunto0 )
		campo.value = campo.value + separacao0;
	}
	else
		event.returnValue = false;
}
</script>
    


<script type="text/javascript">
    window.onbeforeunload = function() {
        return "Tem a certeza que quer sair da pagina?";
    }
	$('form').submit(function () {
    window.onbeforeunload = null;
	});
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

<?php }else{
    header('Location: /?logoff');
} ?>
