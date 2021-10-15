<?php

/* 
 *  Acme Widget Co - Example Project  
 *  
 *  Written by Robert Baxter-Kaneen <robert@rwbk.net>
 *  Copyright 2021 Vidalytics LLC
 * 
 */

// Enable PHP error Codes in Development

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Web App Init Sanity Check. Before for Error Handler & Core Functions have been included. 

if (!file_exists('./app/func/func_core.php') || !file_exists('./app/func/func_errorhandle.php') || !file_exists('./app/cfg/cfg_global.php'))
{
    die("Critical Error - Missing Code!");
}

// All three componants of the core do exist, so lets try to load them.

require_once('./app/cfg/cfg_global.php');
require_once('./app/func/func_errorhandle.php');
require_once('./app/func/func_core.php');

// If we have time, we could wrap the below in a nice asset loader... but not 100% nessisary for this Demo Project. Obviously, it would need Error Trapping. 

require_once('./app/func/func_store.php');

// Now we have our core functions we can call awc_init() that will start the application. 

awc_init();