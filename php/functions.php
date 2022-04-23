<?php

include("connect.php");
session_start();

date_default_timezone_set("America/Manaus");
// Funções do Sistema

function SendNotification ($DE, $PARA, $Mensagem, $Funcao, $Plataforma){
	
	include("connect.php");
		
	$Q = mysqli_query($con, "INSERT INTO history_messages (DE, PARA, PLATAFORMA, FUNCAO, MESSAGE, TIME_ENVIO, STATUS) VALUES ('$DE', '$PARA', '$Plataforma', '$Funcao', '$Mensagem', '".date('Y-m-d H:i:s')."', 'PENDENTE')");

	return("Mensagem agendada para Envio. Consulte a relação de mensagens para ver o status.");
	
}

function CountSituacoesVagas($vaga){
    
    include("connect.php");
    
    $Q = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM candidatos_dados_gerais WHERE CANDIDATO_VAGA = '$vaga' AND STATUS_NOVO = 'SIM';"));
    $T_Novos = $Q["total"];
    
    $Q = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM candidatos_dados_gerais WHERE CANDIDATO_VAGA = '$vaga' AND STATUS_PENDENTE = 'SIM';"));
    $T_Pendentes = $Q["total"];
    
    $Q = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM candidatos_dados_gerais WHERE CANDIDATO_VAGA = '$vaga' AND STATUS_BLOQUEADO = 'SIM';"));
    $T_Bloqueados = $Q["total"];
    
    $Q = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM candidatos_dados_gerais WHERE CANDIDATO_VAGA = '$vaga' AND STATUS_LIXEIRA = 'SIM';"));
    $T_Lixeira = $Q["total"];
    
    $Q = mysqli_fetch_assoc(mysqli_query($con, "SELECT QTD_VAGAS FROM vagas WHERE NOME_VAGA = '$vaga';"));
    $T_vagas = $Q["QTD_VAGAS"];
    
    $Total = $T_Novos+$T_Pendentes+$T_Bloqueados+$T_Lixeira+$T_vagas;
    
    return(array($T_Novos, $T_Pendentes, $T_Bloqueados, $T_Lixeira, $T_vagas, $Total));
}

function RemoveMascaraTelefone ($Telefone){
    if (strpos($Telefone, " ") !== false){
	$Telefone = str_replace(" ", "", $Telefone);
    }
    if (strpos($Telefone, "(") !== false){
	$Telefone = str_replace("(", "", $Telefone);
    }
    if (strpos($Telefone, ")") !== false){
	$Telefone = str_replace(")", "", $Telefone);
    }
    if (strpos($Telefone, "-") !== false){
	$Telefone = str_replace("-", "", $Telefone);
    }
    return($Telefone);
}

function RemoveAspaSimples ($String){
    
    if (strpos($String, "'") !== false){
	$String = str_replace("'", "&lsquo;", $String);
    }
	if (strpos($String, "'") !== false){
	$String = str_replace("'", "&rsquo;", $String);
    }
    return($String);
}

function RemoveMascaraCPF ($String){
    
    if (strpos($String, ".") !== false){
	$String = str_replace(".", "", $String);
    }
	if (strpos($String, "-") !== false){
	$String = str_replace("-", "", $String);
    }
    return($String);
}

function verifyCPF($cpf) {
	$cpf = "$cpf";
	if (strpos($cpf, "-") !== false)
	{
	  $cpf = str_replace("-", "", $cpf);
	}
	if (strpos($cpf, ".") !== false)
	{
		$cpf = str_replace(".", "", $cpf);
	}
	$sum = 0;
	$cpf = str_split( $cpf );
	$cpftrueverifier = array();
	$cpfnumbers = array_splice( $cpf , 0, 9 );
	$cpfdefault = array(10, 9, 8, 7, 6, 5, 4, 3, 2);
	for ( $i = 0; $i <= 8; $i++ )
	{
		$sum += $cpfnumbers[$i]*$cpfdefault[$i];
	}
	$sumresult = $sum % 11;  
	if ( $sumresult < 2 )
	{
		$cpftrueverifier[0] = 0;
	}
	else
	{
		$cpftrueverifier[0] = 11-$sumresult;
	}
	$sum = 0;
	$cpfdefault = array(11, 10, 9, 8, 7, 6, 5, 4, 3, 2);
	$cpfnumbers[9] = $cpftrueverifier[0];
	for ( $i = 0; $i <= 9; $i++ )
	{
		$sum += $cpfnumbers[$i]*$cpfdefault[$i];
	}
	$sumresult = $sum % 11;
	if ( $sumresult < 2 )
	{
		$cpftrueverifier[1] = 0;
	}
	else
	{
		$cpftrueverifier[1] = 11 - $sumresult;
	}
	$returner = "FALSO";
	if ( $cpf == $cpftrueverifier )
	{
		$returner = "VALIDO";
	}


	$cpfver = array_merge($cpfnumbers, $cpf);

	if ( count(array_unique($cpfver)) == 1 || $cpfver == array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0) )

	{

		$returner = "FALSO";

	}
	return $returner;
}

function Valida_User($CPF){
    
    include("connect.php");
    
	$CPF = md5($CPF);
    
    $R = mysqli_query($con, "SELECT cpf FROM promotores WHERE cpf='$CPF';") or die(mysqli_error($con));
    
    if(mysqli_fetch_row($R) < 1){
        return("FALSE");
    }else{
        return("TRUE");
    }
    
}

function Valida_Matricula($Matricula){
    
    include("connect.php");
    
    $R = mysqli_query($con, "SELECT matricula FROM promotores WHERE matricula='$Matricula';") or die(mysqli_error($con));
    
    if(mysqli_fetch_row($R) < 1){
        return("FALSE");
    }else{
        return("TRUE");
    }
    
}

function Valida_Senha($CPF, $Senha){
    
    include("connect.php");
    
    $R = mysqli_fetch_assoc(mysqli_query($con, "SELECT senha, admin FROM promotores WHERE cpf='$CPF';"));
    
	if($Senha == $R["senha"]){
        if($R["admin"] == 1){
            session_start();
            $_SESSION['ADMIN'] = true;
        }else{
            session_start();
            $_SESSION['ADMIN'] = false;
        }
		return("TRUE");
	}else{
		return("FALSE");
	}
    
}

function RemovedorDeAcentuacao ($String) {
 //inicio função 
    //vogais minusculas acentuadas (agudas)
    if (strpos($String, "á") !== false){
	$String = str_replace("á", "a", $String);
    }
    if (strpos($String, "é") !== false){
	$String = str_replace("é", "e", $String);
    }
    if (strpos($String, "í") !== false){
	$String = str_replace("í", "i", $String);
    }
    if (strpos($String, "ó") !== false){
	$String = str_replace("ó", "o", $String);
    }
    if (strpos($String, "ú") !== false){
	$String = str_replace("ú", "u", $String);
    }
    
    //vogais maiusculas acentuadas (agudas)
    if (strpos($String, "Á") !== false){
	$String = str_replace("Á", "A", $String);
    }
    if (strpos($String, "É") !== false){
	$String = str_replace("É", "E", $String);
    }
    if (strpos($String, "Í") !== false){
	$String = str_replace("Í", "I", $String);
    }
    if (strpos($String, "Ó") !== false){
	$String = str_replace("Ó", "O", $String);
    }
    if (strpos($String, "Ú") !== false){
	$String = str_replace("Ú", "U", $String);
    }
    

    //vogais minusculas acentuadas (Circunflexo)
    if (strpos($String, "â") !== false){
	$String = str_replace("â", "a", $String);
    }
    if (strpos($String, "ê") !== false){
	$String = str_replace("ê", "e", $String);
    }
    if (strpos($String, "ô") !== false){
	$String = str_replace("ô", "o", $String);
    }
    
    //vogais maiusculas acentuadas (Circunflexo)
    if (strpos($String, "Â") !== false){
	$String = str_replace("Â", "A", $String);
    }
    if (strpos($String, "Ê") !== false){
	$String = str_replace("Ê", "E", $String);
    }
    if (strpos($String, "Ô") !== false){
	$String = str_replace("Ô", "O", $String);
    }
    
    //vogais acentuadas (Crase)
    if (strpos($String, "à") !== false){
	$String = str_replace("à", "a", $String);
    }
    if (strpos($String, "À") !== false){
	$String = str_replace("À", "A", $String);
    }
    
    //Ç Ç
    if (strpos($String, "Ç") !== false){
	$String = str_replace("Ç", "C", $String);
    }
    if (strpos($String, "ç") !== false){
	$String = str_replace("ç", "c", $String);
    }
    
    //vogais acentuadas (TIL)
    if (strpos($String, "Ã") !== false){
	$String = str_replace("Ã", "A", $String);
    }
    if (strpos($String, "ã") !== false){
	$String = str_replace("ã", "a", $String);
    }
    if (strpos($String, "Õ") !== false){
	$String = str_replace("Õ", "O", $String);
    }
    if (strpos($String, "õ") !== false){
	$String = str_replace("õ", "o", $String);
    }
    if (strpos($String, "Ñ") !== false){
	$String = str_replace("Ñ", "N", $String);
    }
    if (strpos($String, "ñ") !== false){
	$String = str_replace("ñ", "n", $String);
    }
    
    return $String;
//Fim Função
}

if(isset($_POST["delete"]) == true){
    
    $Q = mysqli_query($con, "delete from controle_ponto where id=".$_POST["delete"]) or die(mysqli_error($con));
    if($Q){
        echo "Removido!";
    }

}

if(isset($_POST["search"]) == true){
    
$temp = '';
$doc = $_POST["search"];
  
//Destinos    
$QD = mysqli_query($con, "SELECT * from destinos;") or die(mysqli_error($con));
$RQD = mysqli_fetch_assoc($QD);
    
do{    

$temp = $temp.'<option value="'.$RQD["destino"].'">'.$RQD["destino"].'</option>';

}while($RQD = mysqli_fetch_assoc($QD));    
//Fim Destinos

    
$QC = mysqli_query($con, "SELECT * from controle_ponto WHERE documento='$doc';") or die(mysqli_error($con));
    
if(mysqli_fetch_row($QC) > 0){
    
$QC = mysqli_query($con, "SELECT * from controle_ponto WHERE documento='$doc';") or die(mysqli_error($con));
$RQC = mysqli_fetch_assoc($QC);

echo
    
'
<div class="w-100 text-center"><label for="">Com quem vai falar?</label></div>

<div class="form-group">
    <label for="destino">Escolha uma opção:</label>
    <select name="destino" id="destino" class="form-control">

        <option value="bah" selected disabled>Clique para Selecionar:</option>

        '.$temp.'

    </select>

    <hr>
    <div class="text-center">
        <label for="">ou</label>
    </div>
    <hr>

    <label for="add">Digite aqui:</label>
    <input type="text" id="add" name="add-destino" class="form-control" minlength="5" placeholder="Ex: João Silva, Setor de Compras, RH, etc.">
    
    <input type="hidden" name="nome-pessoa" value="'.$RQC["nome"].'">
    <input type="hidden" name="documento" value="'.$RQC["documento"].'">
    
    <hr>
    
    <div class="w-100 text-center">
        <input type="submit" class="btn btn-primary col-6" name="myButton" onClick="window.onbeforeunload = null;" id="myButton" value="Salvar Registro">
    </div>
</div>
';
    
die();
    
}else{

echo
    
'
<div class="form-group">
    <label for="">Qual nome da Pessoa?</label>
    <input type="text" name="nome-pessoa" class="form-control" required minlength="5" placeholder="Ex: Agostinho Ferreira">
</div>
<div class="form-group">
    <label for="">Informe o RG ou CPF (Apenas os Números):</label>
    <input type="tel" name="documento" class="form-control" required minlength="8" placeholder="Ex: 12345678 ou 12345678910" value="'.$doc.'">
</div>
<hr>

<div class="w-100 text-center"><label for="">Com quem vai falar?</label></div>

<div class="form-group">
    <label for="destino">Escolha uma opção:</label>
    <select name="destino" id="destino" class="form-control">

        <option value="bah" selected disabled>Clique para Selecionar:</option>

        '.$temp.'

    </select>

    <hr>
    <div class="text-center">
        <label for="">ou</label>
    </div>
    <hr>

    <label for="add">Digite aqui:</label>
    <input type="text" id="add" name="add-destino" class="form-control" minlength="5" placeholder="Ex: João Silva, Setor de Compras, RH, etc.">
    
    <hr>
    
    <div class="w-100 text-center">
        <input type="submit" class="btn btn-primary col-6" name="myButton" onClick="window.onbeforeunload = null;" id="myButton" value="Salvar Registro">
    </div>
</div>
';    
    
die();
    
}
}

