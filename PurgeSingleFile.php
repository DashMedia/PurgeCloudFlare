<?php
/**
* 
*/
class CloudflarePageHandler
{
	private $modx;

	public function __construct($modx){
		$this->modx = $modx;
	}
	public function clear_page($page_document){
		$modx = $this->modx;
		$page_url = $modx->makeUrl($page_document->get('id'),'','','full');
		if($page_url != '' && !is_null($page_url)){
			$token = $modx->getOption('cf_api_key');
			$email = $modx->getOption('cf_email');
			$context = $modx->getContext($page_document->getOne('Context')->key);
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
		$parent_id = $page_document->get('parent');
		if($parent_id != 0){
			//parent exists
			$parent = $modx->getObject('modResource', $parent_id);
			$this->clear_page($parent);
		}
	}	
}


$page_id = $resource->get('id');
$cf_pageHandler = new CloudflarePageHandler($modx);

if($mode != 'new'){
	//resource updated, not created
	$cf_pageHandler->clear_page($resource);
} else {
	//resource created, clear starting at parent
	$parent_id = $resource->get('parent');
	if($parent_id != 0){
		//parent exists
		$parent = $modx->getObject('modResource', $parent_id);
		$cf_pageHandler->clear_page($parent);
	}
}