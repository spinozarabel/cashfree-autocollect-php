<?php

if(empty($_REQUEST)){
  die();
}

include("cfAutoCollect.inc.php");
include("cfKeyPair.inc.php"); // This should be fetched from Config

$authParams["clientId"] = $clientId;
$authParams["clientSecret"] = $clientSecret;
$authParams["stage"] = $stage;

$apiResponse = "";
$apiResponse["status"] = "UNEXPECTED ERROR";

try {
  $autoCollect = new CfAutoCollect($authParams);
} catch (Exception $e) {
  $apiResponse["message"] = $e->getMessage();
}

if(!$autoCollect){
  $apiResponse["message"] = "Error creating Cashfree instance. Check credentials entered.";
}

else if($_POST["event"] == "createVirtualAccount") {
  foreach ($_POST["account"] as $key => $value) {
    $account[$key] = trim(strip_tags($value));
  }
  $apiResponse = $autoCollect->createVirtualAccount($_POST["account"]);
}
echo json_encode($apiResponse);

?>
