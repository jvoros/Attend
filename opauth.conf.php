<?php
/**
 * Opauth basic configuration file to quickly get you started
 * ==========================================================
 * To use: rename to opauth.conf.php and tweak as you like
 * If you require advanced configuration options, refer to opauth.conf.php.advanced
 */

$config = array(
/**
 * Path where Opauth is accessed.
 *  - Begins and ends with /
 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
 *  - if Opauth is reached via http://auth.example.org/, path is '/'
 */
	'path' => '/Sites/Attend/auth/login/',

/**
 * Callback URL: redirected to after authentication, successful or otherwise
 */
	'callback_url' => 'http://localhost/Sites/Attend/auth/response',
    
    'callback_transport' => 'post',
	
/**
 * A random string used for signing of $auth response.
 */
	'security_salt' => 'TLDFmiilYf8Fyw5W10rx4W1KsVrieQCnpBzzpTBWA5vJidQKDx8pMJbmw28R1C4m',
		
/**
 * Strategy
 * Refer to individual strategy's documentation on configuration requirements.
 * 
 * eg.
 * 'Strategy' => array(
 * 
 *   'Facebook' => array(
 *      'app_id' => 'APP ID',
 *      'app_secret' => 'APP_SECRET'
 *    ),
 * 
 * )
 *
 */
	'Strategy' => array(
		// Define strategies and their respective configs here
		'Twitter' => array(
	       'key' => 'Ar0pbuAWuezCJ3TCRaaIgg',
	       'secret' => 'PE39hDoIFHuAMqShxrATpoC8PmDgCLQkipJdsyfvYuY'  
        ),
        
        'Google' => array(
	       'client_id' => '923529728247.apps.googleusercontent.com',
	       'client_secret' => 'lLDpTx_qCnq8wE5ZRZaDKglK'
        ),
        
        'Facebook' => array(
           'app_id' => '444408008997389',
           'app_secret' => '50acc8eb54ce3843502bb434a65bc733'
       )
	),
);