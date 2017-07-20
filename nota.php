#!/usr/bin/php
<?php
/*
This script answers the call and prompts for an extension, provided your caller ID is approved.
When it receives 4 digits it will read them back to you and hang up.
*/
$debug_mode = false; //debug mode writes extra data to the log file below whenever an AGI command is executed
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
    log_agi("Esperando numeros");
    $result = execute_agi('GET DATA nota 5000 2');
    if ($result['result'] > 0)
    {
   	 $ext = $result['result'];
    }
    $count++;
}

if ($ext != '')
{
    log_agi("Conectando ao banco");
    $link = mysql_connect("127.0.0.1",'usuariodobanco','senhausuariobanco');
    log_agi("Selecionando DATABASE");
    mysql_select_db("asterisk");
    log_agi("Registrando LOG banco");
    $sql=sprintf("Insert into Log(Numero,HoraAcesso) Values (%d,NOW())",$agi_callerid);
    mysql_query($sql);
    log_agi("Enviando consulta ao banco");
    $sql=sprintf("select * from Mercadorias where idMercadoria=%s",$ext);
    log_agi($sql);
    $result = mysql_query($sql);
    $num_rows = mysql_num_rows($result);
    log_agi("num_rows=$num_rows");
    if ($num_rows>0)
    {
   	 $line = mysql_fetch_array($result, MYSQL_ASSOC);
   	 log_agi("Quantidade=" . $line['Quantidade']);
   	 execute_agi('STREAM FILE estoque ""');
   	 $msg=sprintf("SAY DIGITS %d \"\"",$line['Quantidade']);
   	 log_agi($msg);
   	 execute_agi($msg);
    }

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

