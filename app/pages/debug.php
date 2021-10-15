<?php

    // Debug Tools Page, Simply to Provide Reset Functionality and other "out of band" tasks.

    if (isset($_POST['dev-reset']))
    {
        // Truncates the baskets and baskets_items tables. Effectively a system restart
        $x = mysqli_query($_AWC['db'], "TRUNCATE TABLE baskets;");
        $x = mysqli_query($_AWC['db'], "TRUNCATE TABLE baskets_items;");
        print("Reset-Baskets: Done!");
    }


?>
<div style="text-align:center">
    <h3>Debug</h3>
    <div>
        <form action="/debug/" style="text-align: center;" method="post">
            <input type="hidden" name="dev-reset" value="1" />
            <button type="submit" class="btn btn-success">Reset Baskets</button>
        </form>    
    </div>
</div>