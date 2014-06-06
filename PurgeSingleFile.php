<?php
$page_id = $resource->get('id');
$page_url = '';
if($mode != 'new'){
	//resource updated, not created
	$page_url = $modx->makeUrl($page_id, '','','full');
}

if($page_url != '' && !is_null($page_url)){
	$token = $modx->getOption('cf_api_key');
	$email = $modx->getOption('cf_email');
	$context = $modx->getContext($resource->getOne('Context')->key);
	$skip = $context->getOption('cf_skip') || 0;
	$domain = $context->getOption('http_host');
	$data = array(
	    "a" => "zone_file_purge", //action
	    "tkn" => $token, //account token
	    "email" => $email, //email address associated with account
	    "z" => $domain, //Target Domain
	    "url" =>  $page_url
	    );

	if($skip != 1 && $domain != '' && !is_null($domain)){
		$ch = curl_init("https://www.cloudflare.com/api_json.html");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch));
		if($result->result == 'success'){
			$modx->log(MODX_LOG_LEVEL_INFO,'File cleared from CloudFlare Cache: '.$page_url);
		} else {
			$modx->log(MODX_LOG_LEVEL_ERROR,'Cloudflare:' . $result->msg);
		}
	}
}