<h2> Média selecionando a fila</h2>
<form action="relatorio.php" method="get">
<table border="0">
<TR><td>Fila:</td><td> 
<?php
include 'conexao.php';
$sql=sprintf("select Distinct(fila) from Nota");
$result = mysql_query($sql) or die("Consulta ao Banco de Dados falhou: <BR>" . $sql . "<BR>" . mysql_error());
$num_rows = mysql_num_rows($result);
if ($num_rows==0)
{
  echo("Nenhum registro na tabela.");
}
else
{	printf("<select name='fila' >");
//		<option value="1">Brasil</option>
//		<option value="2">Argentina</option>
//	</select>

  while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    printf("<option value='%s'>'%s'</option>",$line['fila'],$line['fila']);
  }
 };
?>
</select>
</td>
</tr>
</table>
<input type="submit" value="Enviar">
</form>

