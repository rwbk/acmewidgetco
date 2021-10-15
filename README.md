# Acme Widget Co - Sales System PoC

This is the source code for a new Billing system based upon the brief as supplied.

## Credits

100% the work of Robert Baxter-Kaneen, there has been no usage of any framework or model code in the PHP.

The theme is a HTML5 boiler plate theme by MACode ID (https://macodeid.com/). I was told no assesment would be made on the UI Componants. 

## Assumptions

1. I have built from Scratch a "PoC" Framework. Mainly so the PoC work in a demo fassion.
2. Postage I have put in case statement logic to demonstrate this ability. 
3. QTY deals are far more complicated as these are applied Dynamically from a Database Table to enable the client to change deals when nessisary. For PoC, there is no admin tool. Only direct DB manipulations
4. I also assume that delivery metrics are applied BEFORE that of any Discounts. If that is wrong, it's easy to change. Appologies if I made the wrong assumption. That does mean that testcase 2 fails because of this.

## Hacky PoC Stuff

1.  Evil DB Handling. It was not in scope to make the DB Code "Nice". Specifically, alot of security escaping and attention to Variable Sanity Checking. 
2.  Lack of any Object Orientation. It makes sense in larger projects to make usage of OO but it's not always the best way. Especially if your after Speed of Exicution. I made a choice NOT to use it.
3.  Usage of function Globals all over the place. Again... pretty poor design practice ... however, for the purposes of a PoC / Time Constrained implementation. It works! It might eat a bit more memory passing the var about, however, not in something this small. 
4.  The brief said NO Frameworks. So I have the worlds most god awful template system i made in an hour. Please don't bash me to hard on that.

## Demo Site

https://awc.rwbk.net is avalible for you to see the application in action. Of course since this is part of an assesment for the Company Infrastructure Stack. It's hosted in GCP. SSL via LetsEncrypt and Certbot. Standalone VPS with Apache2 & MariaDB installed. 

I assume someone will comprimise and break it soon enough! So please be quick to take a look.

## Install Instructions

I don't think they are needed. However, a Starting Point SQL File is included in ./sql/ which needs importing to the Database used. I assume however, you won't actually try to install this :). If that is your intention:- PHP8 / MariaDB are the only nessisary pre-requisites. Please read /app/cfg/cfg_global.php to configure a Database.

## Closing Remarks

It's pretty dirty / fast and areas are not up to production standards. Certainly this was done in a "rush" mentality to get a Viable PoC Demo as fast as possible. No care or attention has been given to Project Ageing.

I have done my best to comment "overly" in some places to give you a feel for my work process.

Start at index.php. I hope my logic makes sense :)

## Thank you!

I have to say it was alot of fun doing this! 