Stripe.setPublishableKey('pk_test_SLnCIww2u5HENd1UVOLE9SZr');
$(function() {
  var $form = $('#payment-form');
  $form.submit(function(event) {
    // Disable the submit button to prevent repeated clicks:
    $form.find('.submit').prop('disabled', true);
    //console.log(stripeResponseHandler);
    // Request a token from Stripe:
    Stripe.card.createToken($form, stripeResponseHandler);
    console.log(stripeResponseHandler);
    // Prevent the form from being submitted:
    return false;
  });
});