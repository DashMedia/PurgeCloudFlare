<?php
/*-----------------------------------------------------------------
 * Lexicon keys for System Settings follows this format:
 * Name: setting_ + $key
 * Description: setting_ + $key + _desc
 -----------------------------------------------------------------*/
return array(

    array(
        'key'  		=>     'cloudflare.api_key',
		'value'		=>     '',
		'xtype'		=>     'textfield',
		'namespace' => 'cloudflare',
		'area' 		=> 'cloudflare:default'
    ),
    array(
        'key'  		=>     'cloudflare.email_address',
		'value'		=>     '',
		'xtype'		=>     'textfield',
		'namespace' => 'cloudflare',
		'area' 		=> 'cloudflare:default'
    ),
);
/*EOF*/