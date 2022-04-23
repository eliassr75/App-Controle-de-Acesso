<?php
ini_set('display_errors', 0 );
error_reporting(0);
/*As duas linhas acima ocultam os erros do php para que não sejam mortrados na tela*/

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$dbhost = "app_promotores.mysql.dbaas.com.br";
$dbuser = "app_promotores";
$dbpass = "xS9waMR3aWKk2W"; //nova senha: 
$dbbase = "app_promotores";
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbase);