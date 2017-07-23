<?php
$host = '127.0.0.1';
$user= 'asterisk';
$pass = 'asterisk';
$banco = 'BdAsterisk';
$link = mysql_connect($host,$user,$pass) or die("Sem acesso ao servidor local de banco de dados: " . mysql_error());
mysql_select_db($banco) or die("Base de dados ASTERISK nao encontrada.");

?>
