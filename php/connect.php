<?php
ini_set('display_errors', 0 );
error_reporting(0);
/*As duas linhas acima ocultam os erros do php para que não sejam mortrados na tela*/

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$dbhost = "*****";
$dbuser = "*****";
$dbpass = "*****"; //nova senha: 
$dbbase = "*****";
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbase);
