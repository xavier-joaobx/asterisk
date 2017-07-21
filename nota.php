#!/usr/bin/php
<?php
/*
This script answers the call and prompts for an extension, provided your caller ID is approved.
When it receives 4 digits it will read them back to you and hang up.
*/
$debug_mode = true; //debug mode writes extra data to the log file below whenever an AGI command is executed
$log_file = '/tmp/agitest.log'; //log file to use in debug mode
//get the AGI variables; we will check caller id
$agivars = array();
while (!feof(STDIN)) {
    $agivar = trim(fgets(STDIN));
    if ($agivar === '') {
   	 break;
    }
    else {
   	 $agivar = explode(':', $agivar);
   	 $agivars[$agivar[0]] = trim($agivar[1]);
    }
}
foreach($agivars as $k=>$v) {
    log_agi("Got $k=$v");
}
extract($agivars);
//ask for an extension
$ext = '';
$count = 0;
while (($ext == '') && ($count < 3 ))
{
    //they haven't entered anything yet
    log_agi("Esperando nota");
    $result = execute_agi('GET DATA nota 5000 2'); // get data Som timeout quantidade de dígitos
    if ($result['result'] > 0)
    {
   	 $ext = $result['result'];
    }
    $count++;
}
if (($ext != '') and ($ext <= 10) and ($ext >=0))
{
	$atendente=$argv[2];
	$fila=$argv[1];
    log_agi("Conectando ao banco");
    $link = mysql_connect("127.0.0.1","asterisk","asterisk") or log_agi(mysql_error($link));
    log_agi("Selecionando DATABASE") or log_agi(mysql_error($link));
    mysql_select_db("BdAsterisk") or log_agi(mysql_error($link));
    log_agi("Registrando LOG banco");
    $sql=sprintf("Insert into Nota(Data_nota,Caller_id,Atendente,Fila,Nota) Values (NOW(),'%s','%s','%s',%d)",$agi_callerid,$atendente,$fila,$ext);
    mysql_query($sql) or log_agi(mysql_error($link));
}
else
{
	execute_agi('STREAM FILE invalida ""');
}
log_agi("Got extension $ext");
execute_agi('STREAM FILE vm-goodbye ""');
execute_agi('HANGUP');
exit;
function execute_agi($command) {
    global $debug_mode, $log_file;
    fwrite(STDOUT, "$command\n");
    fflush(STDOUT);
    $result = trim(fgets(STDIN));
    $ret = array('code'=> -1, 'result'=> -1, 'timeout'=> false, 'data'=> '');
    if (preg_match("/^([0-9]{1,3}) (.*)/", $result, $matches)) {
   	 $ret['code'] = $matches[1];
   	 $ret['result'] = 0;
   	 if (preg_match('/^result=([0-9a-zA-Z]*)\s?(?:\(?(.*?)\)?)?$/', $matches[2], $match))  {
   		 $ret['result'] = $match[1];
   		 $ret['timeout'] = ($match[2] === 'timeout') ? true : false;
   		 $ret['data'] = $match[2];
   	 }
    }
    if ($debug_mode && !empty($logfile)) {
   	 $fh = fopen($logfile, 'a');
   	 if ($fh !== false) {
   		 $res = $ret['result'] . (empty($ret['data']) ? '' : " / $ret[data]");
   		 fwrite($fh, "-------\n>> $command\n<< $result\n<< 	parsed $res\n");
   		 fclose($fh);
   	 }
    }
    return $ret;
}
function log_agi($entry, $level = 1) {
    if (!is_numeric($level)) {
   	 $level = 1;
    }
    $result = execute_agi("VERBOSE \"$entry\" $level");
}
?>
