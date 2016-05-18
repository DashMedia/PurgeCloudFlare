<?php
/**
 * @name BizenCloudFlarePurgeSingleFile
 * @description This is used to clear individual URL's from CloudFlare when they are saved
 * @PluginEvents OnDocFormSave
 * @author Massimiliano Monaro <massimiliano.monaro@gmail.com>
 */

/*
 * API Credentials
 */

$email = $modx->getOption('cloudflare.email_address');
$token = $modx->getOption('cloudflare.api_key');

/*
 * Get Zone ID
 */

if ($mode != 'new') {
    $page_url = $modx->makeUrl($resource->get('id'), '', '', 'full');
} else {
    // resource created, clear starting at parent
    $parent_id = $resource->get('parent');
    if ($parent_id != 0) {
        $parent = $modx->getObject('modResource', $parent_id);
        $page_url = $modx->makeUrl($parent->get('id'), '', '', 'full');
    }
}

$http_host = str_replace("www.", "", $_SERVER['HTTP_HOST']);

if ($page_url && $email && $token) {
    $headers = [
        'X-Auth-Email: ' . $email,
        'X-Auth-Key: ' . $token,
        'Content-Type: application/json'
    ];
    
    $ch = curl_init('https://api.cloudflare.com/client/v4/zones?name=' . $http_host . '&status=active&page=1&per_page=20&order=status&direction=desc&match=all');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = json_decode(curl_exec($ch), true);

    curl_close($ch);

    if ($result['success'] == 1) {
        $zone_id = $result['result'][0]['id'];
        $data = array("files" => array($page_url));

        $ch = curl_init('https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/purge_cache');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = json_decode(curl_exec($ch), true);

        if ($result['success'] == 1) {
            $modx->log(MODX_LOG_LEVEL_INFO, 'File cleared from CloudFlare cache: ' . $page_url);
        } else {
            $modx->log(MODX_LOG_LEVEL_ERROR, 'Cloudflare:' . $result['errors']);
        }

        curl_close($ch);
    } else {
        $modx->log(MODX_LOG_LEVEL_ERROR, 'Cloudflare:' . $result['errors']);
    }
}
