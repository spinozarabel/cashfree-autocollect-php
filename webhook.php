<?php

include("cfKeyPair.inc.php");

  $data = $_POST;
  $signature = $_POST["signature"];
  unset($data["signature"]);
  ksort($data);
  $postData = "";
  
  foreach ($data as $key => $value){
    if (strlen($value) > 0) {
      $postData .= $value;
    }
  }
  $hash_hmac = hash_hmac('sha256', $postData, $clientSecret, true) ;
  $computedSignature = base64_encode($hash_hmac);
  if ($signature == $computedSignature) {
    error_log("Signature verified");
    foreach ($data as $key => $value) {
      error_log($key." : ".$value);
    }
    // Process this event
  } else {
    error_log("Signature not verified");
    // Ignore this webhook call
  }

?>