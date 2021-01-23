<?php

  //start session in all pages
  if (session_status() == PHP_SESSION_NONE) { session_start(); } //PHP >= 5.4.0
  //if(session_id() == '') { session_start(); } //uncomment this line if PHP < 5.4.0 and comment out line above

	// sandbox or live
	define('PPL_MODE', 'sandbox');

	if(PPL_MODE=='sandbox'){
		
		define('PPL_API_USER', 'deuscom-facilitator_api1.deuscom.fr');
		define('PPL_API_PASSWORD', 'W789WFLNV6GJ85L8');
		define('PPL_API_SIGNATURE', 'AjoCyRzQBwh-g2KJ8-D7Vw7HOnLhAvsIb9OXQI2ufstuHiaZL4-cy5Rf');
	}
	else{
		
		define('PPL_API_USER', 'deuscom_api1.deuscom.fr');
		define('PPL_API_PASSWORD', 'ZJWVDWQTF5UEAXPZ');
		define('PPL_API_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31ADzrbBELmkDNvNv6PdpN5WhjrMF8');
	}
	
	define('PPL_LANG', 'EN');
	
	define('PPL_LOGO_IMG', 'http://www.sanwebe.com/wp-content/themes/sanwebe/img/logo.png');
	
	define('PPL_RETURN_URL', 'https://www.you-order.eu/admin/test_paypal/process.php');
	define('PPL_CANCEL_URL', 'https://www.you-order.eu/admin/test_paypal/index.php');

	define('PPL_CURRENCY_CODE', 'EUR');
