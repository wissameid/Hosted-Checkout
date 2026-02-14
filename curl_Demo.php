<?php

  
$curlObj = curl_init();

//Configuration///////////////////////
$merchant="Company Name"; //Your commercial name
$currency="USD";          //The currency your merchant account enabled for 
$merchant_id="TESTMCWEID";
$api_password="bc881ecf4052b2e1d7bc7c4d522d08a8";  
$returnUrl="https://wwww.yourwebsite.com/Receipt.php";
///////////////////////////////////////

$RandomNbr = rand(10,100); 
$GW_URL = "https://test-gateway.mastercard.com/api/rest/version/100/merchant/$merchant_id/session";

$postData = array(
  'apiOperation' => 'INITIATE_CHECKOUT',
  'interaction' =>
  array (
    'operation' => 'PURCHASE',
    'returnUrl' => '$returnUrl',
    'merchant' =>
    array (
      'name' => '$merchant',
    ),
  ),
  'order' =>
  array (
    'id' => 'TEST-' . $RandomNbr,
    'currency' => '$currency',
    'amount' => '100.00',
    'description' => 'Test Order',
  ),
);

    curl_setopt($curlObj, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($curlObj, CURLOPT_HTTPHEADER, array("Content-Length: " . strlen(json_encode($postData))));
    curl_setopt($curlObj, CURLOPT_HTTPHEADER, array("Content-Type: Application/json;charset=UTF-8"));
    curl_setopt($curlObj, CURLOPT_URL, $GW_URL);
    curl_setopt($curlObj, CURLOPT_USERPWD, "merchant.$merchant_id:$api_password");
    curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($curlObj);

    if (curl_error($curlObj))
        $response = "cURL Error: " . curl_errno($curlObj) . " - " . curl_error($curlObj);

    echo $response;
    curl_close($curlObj);
    

$errorCode = "";
$sessionID = "";
$sessionVr = "";
$result = "";
$successIndicator = "";

$tmpArray = array();

$responseArray = json_decode($response, TRUE);

if ($responseArray == NULL) {
  print("JSON decode failed. Please review server response (enable debug in config.php).");
  die();
}

if (array_key_exists("result", $responseArray))
  $result = $responseArray["result"];

if ($result == "FAIL") {
  if (array_key_exists("reason", $responseArray)) {
    $tmpArray = $responseArray["reason"];

    if (array_key_exists("explanation", $tmpArray)) {
      $errorMessage = rawurldecode($tmpArray["explanation"]);
    }
    else if (array_key_exists("supportCode", $tmpArray)) {
      $errorMessage = rawurldecode($tmpArray["supportCode"]);
    }
    else {
      $errorMessage = "Reason unspecified.";
    }

    if (array_key_exists("code", $tmpArray)) {
      $errorCode = "Error (" . $tmpArray["code"] . ")";
    }
    else {
      $errorCode = "Error (UNSPECIFIED)";
    }
  }
}
else {
  if (array_key_exists("successIndicator", $responseArray)) {
	$successIndicator = $responseArray["successIndicator"];
	$_SESSION["successIndicator"] = $successIndicator;
    $tmpArray = $responseArray["session"];
    if (array_key_exists("id", $tmpArray))
	{
      $sessionID = rawurldecode($tmpArray["id"]);
	  $_SESSION['sessionID'] = $sessionID;
	}
    if (array_key_exists("version", $tmpArray))
      $sessionVr = rawurldecode($tmpArray["version"]);
  }
}

echo "Session ID: " .$sessionID ."<br>" ."Session Vr: " .$sessionVr ."<br>" ."Success Indicator: " .$successIndicator ."<br>";


if($sessionID != "" & $successIndicator != "")
{


echo    "<div style=\"width:300px; margin: auto; border: 0px none #FFF; color: #004D74; font-family: Arial; font-size: 14px;\">" .
          "&nbsp;&nbsp;PAYMENT IS UNDER PROCESSING... <br>" . 
		    "</div>";

echo    "<div id=\"embed-target\"> </div>";
	
echo    "<script src=\"https://test-gateway.mastercard.com/static/checkout/checkout.min.js\"".
        "            data-complete=\"CompleteResults\" ".
        "            data-error=\"errorCallback\" ".
        "            data-cancel=\"cancelCallback\">".
        "</script>";
		
echo    "<script type=\"text/javascript\">".

        "    function CompleteResults(response) {".
        "        cconsole.log(\"successIndicator: \" + JSON.stringify(response));".
        "    }".
        "    function errorCallback(error) {".
        "        console.log(JSON.stringify(error));".
        "    }".
        "    function cancelCallback() {".
        "        console.log('Payment cancelled');".
        "    }".

        "    Checkout.configure({".
        "        session: {".
        "            id: \"$sessionID\"".
        "        }".        
        "    });".
		" Checkout.showEmbeddedPage('#embed-target');" .
        "</script>";
		
}


?>