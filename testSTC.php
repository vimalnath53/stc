<!DOCTYPE html>

<?php
// echo 'dsa';exit;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("vendor/braintree/braintree_php/lib/Braintree.php");
require("vendor/braintree/braintree_php/lib/autoload.php");

$gateway = new Braintree\Gateway([
  'environment' => 'sandbox',
  'merchantId' => 'zynjg7c9rd5c95z2',
  'publicKey' => 'y44n7myzmhg8x2vz',
  'privateKey' => 'e67402552c930712192d5a656f0c29dd'
]);

$clientToken = $gateway->clientToken()->generate([
 //"customerId"=> '147408530',
  //"merchantAccountId" => "vimalfraud2"
]); 

?>
<!-- Load the PayPal JS SDK with your PayPal Client ID-->


<!-- Load the Braintree components -->
<script src="https://js.braintreegateway.com/web/3.83.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.83.0/js/data-collector.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.83.0/js/paypal-checkout.min.js"></script>


<button id="submit-button">PAY  </button>
<br>
<div id="paypal-button"></div>

<script>
var client_metadata_id;
// Create a client.
var clientToken = "<?php echo $clientToken; ?>";
braintree.client.create({
  authorization: clientToken
}, function (clientErr, clientInstance) {


//data collector start
  braintree.dataCollector.create({
    client: clientInstance,
    paypal: true,
    riskCorrelationId: client_metadata_id,
  }, function (err, dataCollectorInstance) {
    if (err) {
      // Handle error in creation of data collector
      return;
    }
    // At this point, you should access the dataCollectorInstance.deviceData value and provide it
    // to your server, e.g. by injecting it into your form as a hidden input.
    var deviceData = dataCollectorInstance.deviceData;

    t = JSON.parse(deviceData);
    client_metadata_id = t['correlation_id'];
    console.log(client_metadata_id);
  });

//data collector end

  braintree.paypalCheckout.create({
    client: clientInstance
  }, function (paypalCheckoutErr, paypalCheckoutInstance) {

    paypalCheckoutInstance.loadPayPalSDK({
      components: 'buttons,messages',
      currency: 'AUD',
       dataAttributes: {
          amount: '150.00'
        },
      locale: 'en_AU',
      intent: 'capture',
    }, function () {
     var button =  paypal.Buttons({
    fundingSource: paypal.FUNDING.PAYPAL,
       createOrder: function () {
          return paypalCheckoutInstance.createPayment({
            flow: 'checkout', // Required
            riskCorrelationId: client_metadata_id,
            amount: '150.00', // Required
            currency: 'AUD', // Required, must match the currency passed in with loadPayPalSDK
            intent: 'capture', // Must match the intent passed in with loadPayPalSDK
            shippingAddressOverride: {
              recipientName: 'S2S Scruff McGruff',
              line1: '1234 Main St.',
              line2: 'Unit 1',
              city: 'Chicago',
              countryCode: 'US',
              postalCode: '60652',
              state: 'IL',
              phone: '123.456.7890'
            },
          });
        },

        onApprove: function (data, actions) {
          return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
            window.location.href = 'checkout_discount.php?nounce='+payload.nonce;
          });
        },
      });
     if (!button.isEligible()) {
        return;
      }
     button.render('#paypal-button');

    });

  });

});
// end Pi4 




</script>
  </body>
</html>