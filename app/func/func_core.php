<?php

// Init Function to programatically work within the URL Space and Dynamically load the nessiary componants for that function. 
function awc_init() {
    Global $_AWC; // This is a Dirty Practice ... but for purposes of NOT using OO approach. We will be using it alot. 

    // Turn the SCRIPT_URL into an Array - pargs or Page Arguments 
    $_AWC['pargs'] = explode('/', $_SERVER['SCRIPT_URL']);

    // Turn 1st Page Argument into a Page Identifier. We need to also escape the "Home Page"
    if ($_AWC['pargs'][1] == '') {
        $_AWC['current-page'] = 'shop';
    } else {
        $_AWC['current-page'] = $_AWC['pargs'][1];
        // We do a sanity check here to make sure the page exists. If it does not we change to a 404 page. 
        if (!file_exists($_AWC['basepath'] . '/app/pages/' . $_AWC['current-page'] . '.php'))
        {
            $_AWC['current-page'] = '404'; 
        }
    }

    // DB Connection & DB Object Creation

    $_AWC['db'] = new mysqli($_AWC['db-cfg']['host'], $_AWC['db-cfg']['username'], $_AWC['db-cfg']['password']);
    if ($_AWC['db']->connect_error)
    {
        die("DB Connection Failure");
    }
    
    if (!mysqli_select_db($_AWC['db'], $_AWC['db-cfg']['name']))
    {
        die("Database Selection Failed");
    }

    // Now we will use the func_Store.php file to include some elements that are needed for Basket Handling
    
    awc_basketInit();

    // Load the Products into the Global Variable too, for simplicity!

    $_AWC['store']['products'] = awc_getProducts();


    // Now load the very simple HTML Scaffold that is not going to be assesed for my possition. 

    require_once($_AWC['basepath'] . '/theme/' . $_AWC['theme'] . '_header.php');
    require_once($_AWC['basepath'] . '/theme/' . $_AWC['theme'] . '_navigation.php');

    require_once($_AWC['basepath'] . '/app/pages/' . $_AWC['current-page'] . '.php');

    require_once($_AWC['basepath'] . '/theme/' . $_AWC['theme'] . '_footer.php');

}