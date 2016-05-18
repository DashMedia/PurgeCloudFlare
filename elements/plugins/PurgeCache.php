<?php

/**
 * @name PurgeCache
 * @description This is used to clear your CloudFlare cache when the clear cache button is pressed in the main site menu
 * @PluginEvents OnBeforeCacheUpdate
 * @author Massimiliano Monaro <massimiliano.monaro@gmail.com>
 */

/*
 * API Credentials
 */

$email = $modx->getOption('cloudflare.email_address');
$token = $modx->getOption('cloudflare.api_key');

$contexts = $modx->getCollection('modContext', array('key:NOT IN' => array('mgr')));
foreach ($contexts as $context) {

    /*
     * Get Zone ID of the context
     */

    $contextObj = $modx->getContext($context->key);
    $skip = $contextObj->getOption('cf_skip') || 0;
    $http_host = str_replace("www.", "", $contextObj->getOption('http_host'));
    $dev_mode = intval($contextObj->getOption('cloudflare.use_dev'));

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

    if ($skip != 1 && $result['success'] == 1) {
        $zone_id = $result['result'][0]['id'];

        // purge all cache        
        $data = array("purge_everything" => true);
        $ch = curl_init('https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/purge_cache');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = json_decode(curl_exec($ch), true);

        if ($result['success'] == 1) {
            $modx->log(MODX_LOG_LEVEL_INFO, 'CloudFlare: cache for ' . $http_host . ' successfully cleared');
        } else {
            $modx->log(MODX_LOG_LEVEL_ERROR, 'CloudFlare (' . $http_host . '):' . $result['errors']);
        }

        curl_close($ch);
        ob_flush();

        // set dev mode to on        
        if ($dev_mode == 1) {
            $data = array("value" => 'on');
            $ch = curl_init('https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/settings/development_mode');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = json_decode(curl_exec($ch), true);

            if ($result['success'] == 1) {
                $modx->log(MODX_LOG_LEVEL_INFO, 'CloudFlare (' . $http_host . '): Development mode activated');
            } else {
                $modx->log(MODX_LOG_LEVEL_ERROR, 'CloudFlare (' . $http_host . '):' . $result['errors']);
            }

            curl_close($ch);
            ob_flush();
        }
    }
}

