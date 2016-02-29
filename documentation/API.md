Google Calendar API
https://developers.google.com/google-apps/calendar/quickstart/php

******************************************************************************************************
Google Gmail API
https://developers.google.com/gmail/api/quickstart/php
------------------------------------------------------------------------------------------------------
    Scopes
        https://www.googleapis.com/auth/gmail.readonly
        https://www.googleapis.com/auth/gmail.send

******************************************************************************************************
Stripe API
https://stripe.com/docs/api#intro
------------------------------------------------------------------------------------------------------
    { // Dans composer.json
      "require": {
        "stripe/stripe-php": "3.*"
      }
    }

PayPal API
cf cours OpenClassRoom

*******************************************************************************************************
API QR Code
http://www.scoco.fr/generer-qr-codeapi-google-chart/
-------------------------------------------------------------------------------------------------------
    <form onsubmit="return false;">
        <input type="text" id="montext" value="mon texte ou mon lien" >
        <button onclick="document.getElementById('imageQRcode').src = 'http://chart.apis.google.com/chart?cht=qr&amp;chs=150x150&amp;chl=' + document.getElementById('montext').value;">Créer le QRcode</button>
    </form>
    <img id="imageQRcode" alt="QR Code" src="http://chart.apis.google.com/chart?cht=qr&amp;chs=150x150&amp;chl=www.scoco.fr" name="imageQRcode">
    
    http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=Test
    -> Elément retourné = image PNG
-------------------------------------------------------------------------------------------------------
API QR Code
http://qrickit.com/qrickit_apps/qrickit_api.php
-------------------------------------------------------------------------------------------------------
    <img src="http://qrickit.com/api/qr?d=http://anyurl&addtext=Hello+World&txtcolor=442EFF&fgdcolor=76103C&bgdcolor=C0F912&logotext=QRickit&qrsize=150&t=p&e=m">
    
    http://qrickit.com/api/qr?d=http://anyurl&addtext=Hello+World&txtcolor=442EFF&fgdcolor=76103C&bgdcolor=C0F912&logotext=QRickit&qrsize=150&t=p&e=m
    -> Elément retourné = Image PNG

********************************************************************************************************
API PDF (html->PDF)
http://www.html2pdfrocket.com
--------------------------------------------------------------------------------------------------------
    // Set parameters
    $apikey = 'ABCD-1234';
    $value = '<title>Test PDF conversion</title>This is the body'; // can aso be a url, starting with http..

    // Convert the HTML string to a PDF using those parameters.  Note if you have a very long HTML string use POST rather than get.  See example #5
    $result = file_get_contents("http://api.html2pdfrocket.com/pdf?apikey=" . urlencode($apikey) . "&value=" . urlencode($value));

    // Save to root folder in website
    file_put_contents('mypdf-1.pdf', $result);
    
    http://api.html2pdfrocket.com/pdf?apikey=<API KEY>&value=Toto
    -> Element retourné = fichier PDF.
*********************************************************************************************************
API Captcha
https://2captcha.com/api-2captcha
---------------------------------------------------------------------------------------------------------
