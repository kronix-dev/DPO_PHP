<?php
$input = file_get_contents('php://input');
require_once 'dpo.php';
$json = json_decode($input, false);
$dpo = new dpo_payment();
/*
JSON iwe hivi
****************************
In Creating Tokens
{
arg:"createToken",
val:value,
value2: values
}
***************************

Kama if($json->arg){} ikigoma use if(json["arg"])
*/
if($json->arg == "createToken"){
}
else if($json->arg == "verifyToken"){
}
