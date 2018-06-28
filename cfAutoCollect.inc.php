<?php
class CfAutoCollect
{
    protected $token;
    protected $baseUrl;
   
    public function __construct($authParams) {
        if(!empty($authParams))
        {
            $clientId = $authParams["clientId"];
            $clientSecret = $authParams["clientSecret"];
            $stage = $authParams["stage"];
            if ($stage == "PROD") {
              $this->baseUrl = "https://cac-api.gocashfree.com/cac/v1";
            } else {
              $this->baseUrl = "https://cac-gamma.gocashfree.com/cac/v1";
            }

            $headers = [
             "X-Client-Id: $clientId",
             "X-Client-Secret: $clientSecret"
            ];
            $endpoint = $this->baseUrl."/authorize";      
            $curlResponse = $this->postCurl($endpoint, $headers);
            if ($curlResponse) {
               if ($curlResponse["status"] == "SUCCESS") {
                 $this->token = $curlResponse["data"]["token"];
               } else {
                  throw new Exception("Authorization failed. Reason : ". $curlResponse["message"]);
               }
            }
         }
    }

  
    public function createVirtualAccount ($vAccount) {
      $response =["status" => "FAILED", "message" => "Authorization failed"];
      if ($this->token) {
        $endpoint = $this->baseUrl."/createVA";
        $authToken = $this->token;
        $headers = [
            "Authorization: Bearer $authToken"
            ]; 
        $curlResponse = $this->postCurl($endpoint, $headers, $vAccount);
        return $curlResponse;
      }
      return $response;
    }

    public function getPaymentsForVirtualAccount($vAccountId) {
      if ($this->token) {
        // Validate , sanitize $vAccountId
        $endpoint = $this->baseUrl."/payments/".$vAccountId;
        $authToken = $this->token;
        $headers = [
             "Authorization: Bearer $authToken"
              ]; 
        $curlResponse = $this->getCurl($endpoint, $headers);
        if ($curlResponse["status"] == "SUCCESS") {
          $payments = $curlResponse["data"]["payments"];
        }
        else $payments = NULL;
      } 
      return $payments;
    }

 
    protected function postCurl ($endpoint, $headers, $params = []) {
      $postFields = json_encode($params);
      array_push($headers,
         'Content-Type: application/json',
         'Content-Length: ' . strlen($postFields));


      $endpoint = $endpoint."?";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $endpoint);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $returnData = curl_exec($ch);
      curl_close($ch);
      if ($returnData != "") {
        return json_decode($returnData, true);
      }
      return NULL;
    }

    protected function getCurl ($endpoint, $headers) {
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $endpoint);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       $returnData = curl_exec($ch);
       curl_close($ch);
       if ($returnData != "") {
        return json_decode($returnData, true);
       }
       return NULL;
    }

    function __destruct()
    {
      $this->token = NULL;
    }
}
?>
