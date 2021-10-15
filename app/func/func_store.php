<?php

    // Basket Init

    function awc_basketInit()
    {
        Global $_AWC; // Again Globals are Nasty ... but for the purposes of this demonstration in the time required. Let's keep it simple if not so optimized!

        if (!isset($_COOKIE['awc_bid']) || !isset($_COOKIE['awc_bsecret']))
        {
            // No basket exists for the client, so create one. 
            awc_basketCreate();

        } else {
            // We have a cookie set, so lets load that basket instead!
            $_AWC['user']['basketid'] = $_COOKIE['awc_bid'];
            $_AWC['user']['basketsecret'] = $_COOKIE['awc_bsecret'];

            // We do need to check now that the basket & secret match. We can do this with a single SQL Statement
            // ** Security Warning - No i am not escaping the cookie fields for SQL Injective attacks. I should wrap them in mysqli_real_escape_string() functions or a SQL Attack Wrapper Kit. I didn't forget i just didnt do it for demo purposes.

            if (mysqli_num_rows(mysqli_query($_AWC['db'], "SELECT * FROM baskets WHERE `bid` = '" . $_AWC['user']['basketid']. "' AND `secret` = '" . $_AWC['user']['basketsecret'] . "'")) <> 1)
            {
                // There is a problem for some reason. So create a new Basket for the user. Most likely an attempt by someone dodgy to scrape existing baskets.  
                awc_basketCreate();
            }
        }

        awc_basketCheckAddItem(); // Check for adding items to basket

    }

    function awc_basketCreate()
    {
        // Creating a new Basket Record in the Database and Securiting it with a 4 digit PIN
        Global $_AWC;

        // Make a random key
        $rndsecret = rand(1000,9999);

        // Date & Time now for SQL Formatted DateStrings
        $dtnow = date('Y-m-d h:i:s');

        // Inset a new Basket for the User to use in the Database so it persists with cookies. 
        $sql = "INSERT INTO baskets (`bid`, `secret` ,`created`) VALUES (NULL, '".$rndsecret."', '".$dtnow."');";
        $createBasket = mysqli_query($_AWC['db'], $sql);
        $bid = mysqli_insert_id($_AWC['db']);

        // Set cookies with the data just created so we can keep persistance
        setcookie('awc_bid',$bid,$_AWC['cookies']['awc_bid']['expires'], $_AWC['cookie-domain']);
        setcookie('awc_bsecret',$rndsecret,$_AWC['cookies']['awc_bsecret']['expires'], $_AWC['cookie-domain']);

        // Update the Global variable so we are stateful
        $_AWC['user']['basketid'] = $bid;
        $_AWC['user']['basketsecret'] = $rndsecret;
    }

    function awc_basketCheckAddItem()
    {
        // Used to look for $_POST['basketAdd'] && $_POST['basketAddQTY'] to add a product to the Database Basket table. 
        Global $_AWC;

        if (isset($_POST['basketAdd']) && isset($_POST['basketAddQTY']))
        {
            // Add the Item & QTY to the Database
            awc_basketAddItem($_POST['basketAdd'], $_POST['basketAddQTY']);
        }

    }

    function awc_basketAddItem($itemCode, $itemQTY)
    {
        // Function to Add a Basket Item.
        Global $_AWC;

        // I will show an example of escaping user provided variables (non-safe-sourced) from SQL injection in a basic fassion!
        $itemCode = mysqli_real_escape_string($_AWC['db'], $itemCode);
        $itemQTY = mysqli_real_escape_string($_AWC['db'], $itemQTY);

        // Now the User Provided Vars are "safe" we can do some logic to add cart items.
        
        // 1st lets get the Basket Items

        $basket = awc_basketGet();

        // Now if the Basket has no items we can straight add the item and QTY to the basket. However, if it has items, we will then need to check if the specific item we are adding is allready there. 
        // Nested 3 case logic.

        if ($basket == false)
        {
            // Add the item as basket has no items
            $sql = "INSERT INTO `baskets_items` (`bitemid`,`code`,`qty`) VALUES ('" . $_AWC['user']['basketid'] . "','" . $itemCode . "','" . $itemQTY . "')";

        } else {
            // Add the item into a basket with items

            $qtyInBasket = 0;
            foreach ($basket as $bitem)
            {
                if ($bitem['code']==$itemCode)
                {
                    $qtyInBasket = $bitem['qty'];
                }
            }
            // Now if the Qty is 0 we need to create a record. If the Qty is > 0 then we need to update the record with the additional Qty
            if ($qtyInBasket == 0)
            {
                $sql = "INSERT INTO `baskets_items` (`bitemid`,`code`,`qty`) VALUES ('" . $_AWC['user']['basketid'] . "','" . $itemCode . "','" . $itemQTY . "')";
            } else {
                $itemQTY = $itemQTY + $qtyInBasket;
                $sql = "UPDATE `baskets_items` SET `qty` = '" . $itemQTY . "' WHERE `bitemid` = '" . $_AWC['user']['basketid'] . "' AND `code` = '" . $itemCode . "'";
            }
        }
        mysqli_query($_AWC['db'], $sql);

    }

    function awc_basketGet()
    {
        // This returns the current cookie based Basket ID Items as an Array
        Global $_AWC;

        // SQL TO get the Basket
        $sql = "SELECT * FROM `baskets_items` WHERE `bitemid` = '" . $_AWC['user']['basketid'] . "' ";
        $itemsobj = mysqli_query($_AWC['db'], $sql);

        // A little sanity checking against 0 item array returns
        if (mysqli_num_rows($itemsobj)==0)
        {
            // 0 rows in return
            $items = false;
        } else {
            // Something in return
            $items = mysqli_fetch_all($itemsobj, MYSQLI_ASSOC);
        }

        // Now we return the items. We can use (if !$result) now if the basket has no items in it. 
        return $items;
    }

    function awc_getProducts()
    {
        Global $_AWC;
        // Returns the product table from the database for dynamic pricing!
        $productQuery = mysqli_query($_AWC['db'], "SELECT * FROM widgets");
        $productsall = mysqli_fetch_all($productQuery, MYSQLI_ASSOC);
        foreach ($productsall as $product)
        {
            $products[$product['code']]['product'] = $product['product'];
            $products[$product['code']]['price'] = $product['price'];
        }
        return $products;
    }

    function awc_basketItemTotal($basket)
    {
        Global $_AWC;
        // Simply returns the total value of a basket without delivery
        $total = 0;
        foreach ($basket as $item)
        {
            $total = $total + ( $_AWC['store']['products'][$item['code']]['price'] * $item['qty'] );
        }

        return $total;
    }

    function awc_basketCalcDiscount($basket) {
        Global $_AWC;
        // This takes a basket and returns the Discount to Apply to the Basket.
        // 1st lets grab the Product QTY Discount items from the Database
        $getDiscounts = mysqli_query($_AWC['db'], "SELECT * FROM discounts_qty");
        $discountsArray = mysqli_fetch_all($getDiscounts, MYSQLI_ASSOC);

        $totalOff = 0; // Placeholder Variable for adding discounts!

        foreach($discountsArray as $did => $discount)
        {
            $discounts[$discount['code']][$discount['qty']]['percentoff'] = $discount['percentoff']; // This reformats the fetch into something sain for the below loop. More efficient. 
            $discounts[$discount['code']][$discount['qty']]['qty'] = $discount['qty'];
        }

        foreach ($basket as $item)
        {
            if (isset($discounts[$item['code']])) // Check to see if the item in the basket actually matches a product specific code 1st.
            {
                foreach($discounts[$item['code']] as $iid => $iDiscount) // the fact the item exists means that there are QTY discounts for this line item. So lets see if they have enough now.
                {
                    if ($item['qty'] >= $iDiscount['qty']) 
                    {
                        // We need the requirements. 
                        $totalOff = $totalOff + ((($iDiscount['qty']*$_AWC['store']['products'][$item['code']]['price']) / 100) * $iDiscount['percentoff']);
                    }
                }
            }
        }

        return round($totalOff,2); 

    }

    function awc_basketCalcDelivery($basket) {
        Global $_AWC;
        // This takes a basket and returns the Delivery Cost based on the total post-discounted basket value.
        $totalOrder = awc_basketItemTotal($basket);
        switch (true)
        {
            case $totalOrder < 50:
                $delivery = 4.95;
                break;

            case $totalOrder < 90:
                $delivery = 2.95;
                break;
            
            case $totalOrder >= 90:
                $delivery = 0;
                break;

        }

        return $delivery;
    }

