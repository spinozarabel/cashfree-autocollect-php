This Kit performs 2 major operations
  1) Add virtual account for receiving payments
  2) Verify webhook call against a test Payment triggered


How to get started?
  1) Copy the integration kit (entire autocollect folder) to your Webserver root
  2) Copy clientId/clientSecret from your Cashfree Merchant Dashboard (Go to Auto Collect -> Access Control -> API Keys) and replace these values in "cfKeyPair.php"
   IMPORTANT NOTE : Store these values securely. Make sure this is not exposed on client side for any reason. For keeping the demo code readable and simple, these values are hardcoded here.
  3) Whitelist the IP of the system this script is going to be run on (IP Whitelist tab)
  4) Hit the url in your browser : http://localhost/autocollect/add-account.php
  5) You can enter the details and add cashfree virtual accounts here


How to test Webhook ?
  1) Place "webhook.php" in a publically accessible path.
  2) Contact support@gocashfree.com and update your webhook endpoint to this.
  3) Now login to your dashboard -> Select 'Auto Collect' -> 'Notifications' -> 'Webhooks'
  4) Initiate a test trigger for Payment received against a virtual account (Not available on Production)
