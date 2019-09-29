<?php
/* Modified by Madhu Avasarala 09/29/2013
* ver 1.0 add Moodle and WP compatibility and get settings appropriately
*/
// if directly called die. Use standard WP and Moodle practices
if (!defined( "ABSPATH" ) && !defined( "MOODLE_INTERNAL" ) )
    {
    	die( 'No script kiddies please!' );
    }

class CfAutoCollect
{
    protected $token;
    protected $baseUrl;
    const TEST_PRODUCTION  = "TEST";
    const VERBOSE          = true;

    public function __construct($site_name = null)
    {
        $this->verbose      = self::VERBOSE;
        if ( defined("ABSPATH") )
		{
			// we are in wordpress environment, don't care about $site_name since get_option is site dependendent
            // ensure key and sercret set correctly no check is made wether set or not
			$api_key		= $this->getoption("sritoni_settings", "cashfree_key");
			$api_secret		= $this->getoption("sritoni_settings", "cash_secret");
		}
        if ( defined("MOODLE_INTERNAL") )
		{
			// we are in MOODLE environment
			// based on passed in $site_name change the strings for config select.
            // $site must be passed correctlt for this to work, no check is made
			if (stripos($site_name, 'hset') !== false)
			{
				$key_string 	= 'pg_api_key_hset';
				$secret_string 	= 'pg_api_secret_hset';
			}

			if (stripos($site_name, 'llp') !== false)
			{
				$key_string 	= 'pg_api_key_llp';
				$secret_string 	= 'pg_api_secret_llp';
			}

			$api_key		= get_config('block_configurable_reports', $key_string);
			$api_secret		= get_config('block_configurable_reports', $secret_string);
		}
        // add these as properties of object
        $this->clientId		= $api_key;
		$this->clientSecret	= $api_secret;
        // these are legay variables so we keep them
        $clientId           = $api_key;
        $clientSecret       = $api_secret;
        $stage = self::TEST_PRODUCTION;

        if ($stage == "PROD")
        {
          $this->baseUrl = "https://cac-api.gocashfree.com/cac/v1";
        } else {
          $this->baseUrl = "https://cac-gamma.gocashfree.com/cac/v1";
        }

        $this->token     = $this->authorizeAndGetToken();
    }       // end construct function

    /**
    *  authenticates to pg server using key and secret
    *  returns the token
    */
    protected function authorizeAndGetToken()
    {
        $token              = null;                     // initialize to null
        $clientId           = $this->clientId;
        $clientSecret       = $this->clientSecret;

        $headers =
        [
         "X-Client-Id: $clientId",
         "X-Client-Secret: $clientSecret"
        ];

        $endpoint = $this->baseUrl."/authorize";
        $curlResponse = $this->postCurl($endpoint, $headers);
        if ($curlResponse)
        {
           if ($curlResponse["status"] == "SUCCESS") {
             $token = $curlResponse["data"]["token"];
             return $token;
           } else
           {
              throw new Exception("Authorization failed. Reason : ". $curlResponse["message"]);
              return $token;
           }
        }
    }

    /**
    * @param vAccountId is the sriToni ID number limited to 8 characters
    * @param name is the full name of the user as in SriToni
    * @param phone is the user's principal phone number
    * @param email is the SriToni email of user
    * returns an array with keys "accountNumber" and "ifsc"
    */
    public function createVirtualAccount ($vAccountId, $name, $phone, $email)
    {
      $response =["status" => "FAILED", "message" => "Authorization failed"];
      if ($this->token)
      {
        $endpoint   = $this->baseUrl."/createVA";
        $authToken  = $this->token;
        $headers    = [
            "Authorization: Bearer $authToken"
            ];
        $params     =
        [
            "vAccountId: $vAccountId",
            "name: $name",
            "phone: $phone",
            "email: $email"
        ];
        $curlResponse = $this->postCurl($endpoint, $headers, $params);
        if ($curlResponse["status"] == "SUCCESS")
        {
          $response = $curlResponse["data"]; // returns an array
          return $response;
        } else
        {
          return null;
        }
      }
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
