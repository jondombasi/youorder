<?php
include_once("config.php");
?>

<html>
  <head>
    <title>Test PayPal</title>
  </head>
  <body>
    <h1>Test Products</h1>

    <form method="post" action="process.php?paypal=checkout">
      <input type="hidden" name="itemname" value="Abonnement"/> 
      <input type="hidden" name="itemdesc" value=""/> 
      <input type="hidden" name="itemQty" value="1"/>
      <input type="hidden" name="MembreId" value="1000"/>

      <input type="radio" name="itemprice" value="1"/>1 mois<br/>
      <input type="radio" name="itemprice" value="3"/>3 mois<br/>
      <input type="radio" name="itemprice" value="6"/>6 mois<br/>

      <input type='submit' value="Acheter"/>
    </form>

    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script>
      $('input[type=radio][name=itemprice]').change(function() {
        if (this.value == '1') {
          $('input[type=hidden][name=itemdesc]').val("Abonnement 1 mois");
        }
        else if (this.value == '3') {
          $('input[type=hidden][name=itemdesc]').val("Abonnement 3 mois");
        }
        else {
          $('input[type=hidden][name=itemdesc]').val("Abonnement 6 mois");
        }
      });
    </script>
  </body>
</html>
