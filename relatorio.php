<?php
require 'conexao.php';
//printf("teste : '%s'",$_GET['fila']);
$fila=$_GET['fila'];
$sql=sprintf("select nota from Nota where fila='%s'",$fila);
$result = mysql_query($sql) or die("Consulta ao Banco de Dados falhou: <BR>" . $sql . "<BR>" . mysql_error());
$num_rows = mysql_num_rows($result);
$media=0.0;
if ($num_rows==0)
{
  echo("Nenhum registro na tabela.");
}
else
{  
//	printf("<select name='fila' >");
//              <option value="1">Brasil</option>
//              <option value="2">Argentina</option>
//      </select>
  while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    //printf("<option value='%s'>'%s'</option>",$line['fila'],$line['fila']);
	$media+=$line['nota'];
  }
	$mediaf=$media/$num_rows;
	printf("Media: %f",$mediaf);
 };
?>
