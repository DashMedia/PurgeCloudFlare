<?php
$token = $modx->getOption('cf_api_key');
$email = $modx->getOption('cf_email');
$domain = $modx->getOption('cf_domain');
$data = array(
    "a" => "fpurge_ts", //action
    "tkn" => $token, //account token
    "email" => $email, //email address associated with account
    "z" => $domain, //Target Domain
    "v" => "1" //just set it to 1
    );

$ch = curl_init("https://www.cloudflare.com/api_json.html");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = json_desode(curl_exec($ch));
if($result == 'success'){
	return "CloudFlare: Your CloudFlare Cache has been successfully cleared";
} else {
	return "CloudFlare: ".$result->msg;
}
return $result;