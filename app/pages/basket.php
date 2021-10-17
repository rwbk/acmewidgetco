<div class="page-section bg-light">
    <div class="container">
      <div class="text-center wow fadeInUp">
        <div class="subhead">Your</div>
        <h2 class="title-section">Widget Basket</h2>
        <div class="divider mx-auto"></div>
      </div>
        <?php
            // Yes this style of PHP in HTML front end stuff is horible as all hell! Never do this. A templating engine is required for a nice frontend. Simply not in scope for this project. 
            // Lets grab the basket so we can work out how best to display it.
            $myBasket = awc_basketGet();
            if (!$myBasket)
            {
        ?>
            <div style="text-align:center">Your basket is empty!</div>
        <?php // Seriously fuck this type of coding ... RestAPI + AngularJS Front and Programatic view FTW!
            } else { 

                // Lets run the current basket through our "Total Cost after QTY Discounts" calculator
                $discount = awc_basketCalcDiscount($myBasket);

                // Lets run the current basket through our "Delivery Cost based on Total Value" calculator
                $delivery = awc_basketCalcDelivery($myBasket);

                // Lets run the Basket Totaler
                $totalBefore = awc_basketItemTotal($myBasket);
                
        ?>
        <div align="center">
            <table width="500px">
                <tr>
                    <th width="60%">Product</th>
                    <th width="20%">Qty</th>
                    <th width="20%">Total</th>
                </tr>
                <?php 
                    foreach($myBasket as $item)
                    {
                ?>
                <tr>
                    <td><?php print($_AWC['store']['products'][$item['code']]['product']); ?></td>
                    <td><?php print($item['qty'] . ' <small>@ $' . $_AWC['store']['products'][$item['code']]['price'] . '</small>'); ?></td>
                    <td>$<?php print(($_AWC['store']['products'][$item['code']]['price'] * $item['qty'])); ?></td>
                </tr>
                <?php 
                    }
                ?>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
                <tr>
                    <td>Delivery</td>
                    <td>1</td>
                    <td>$<?php print($delivery); ?></td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td>1</td>
                    <td>-$<?php print($discount); ?></td>
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
                <tr>
                    <td> </td>
                    <td><strong>Order Total:</strong></td>
                    <td><strong>$<?php print(number_format($totalBefore - $discount + $delivery,2, '.', '')); ?></strong></td>
                </tr>
            </table>
        </div>
        <?php
            }
        ?>

    </div> <!-- .container -->
  </div> <!-- .page-section -->