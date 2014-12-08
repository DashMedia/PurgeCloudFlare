<?php
$contexts = $modx->getCollection('modContext', array('key:NOT IN' => array('mgr')));
foreach ($contexts as $context) {
    // do stuff with each modContext object
    $contextObj = $modx->getContext($context->key);
    $domain = $contextObj->getOption('http_host');
    $token =  $contextObj->getOption('cf_api_key');
    $email =  $contextObj->getOption('cf_email');
    $skip =  $contextObj->getOption('cf_skip') || 0;
    $devMode =  intval($contextObj->getOption('cf_use_dev', [], 1));
    $data = array(
        "a" => "fpurge_ts", //action
        "tkn" => $token, //account token
        "email" => $email, //email address associated with account
        "z" => $domain, //Target Domain
        "v" => "1" //just set it to 1
        );
    $devData = array(
        "a" => "devmode", //action
        "tkn" => $token, //account token
        "email" => $email, //email address associated with account
        "z" => $domain, //Target Domain
        "v" => "1" //enable
        );
    if($skip != 1 && $domain != '' && !is_null($domain)){
        $ch = curl_init("https://www.cloudflare.com/api_json.html");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        if($result->result == 'success'){
            $modx->log(MODX_LOG_LEVEL_INFO,'Cloudflare: cache for ' . $domain . ' successfully cleared');
        } else {
            $modx->log(MODX_LOG_LEVEL_ERROR,'Cloudflare ('.$domain.'):' . $result->msg);
        }
        ob_flush();
        curl_close($ch);
        if($devMode == 1){
            $ch = curl_init("https://www.cloudflare.com/api_json.html");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $devData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = json_decode(curl_exec($ch));
            if($result->result == 'success'){
                $modx->log(MODX_LOG_LEVEL_INFO,'Cloudflare ('.$domain.'): Development mode activated');
            } else {
                $modx->log(MODX_LOG_LEVEL_ERROR,'Cloudflare ('.$domain.'):' . $result->msg);
            }
            ob_flush();
            curl_close($ch);
        }
    }
}