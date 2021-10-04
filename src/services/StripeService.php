<?php
namespace App\Services;
use App\Entity\Platform;

// class StripeService {

//     private $privateKey;

//     public function __construct()
//     {
//         $this->privateKey = $_ENV['STRIPE_PRIVATE_KEY'];
//     }

//     public function paymentIntent(Platform $platform) 
//     {
//         \Stripe\Stripe::setApiKey($this->privateKey);

//         return \Stripe\PaymentIntent::create([
//             'amount' => 50 * 100,
//             'currency' => 'eur',
//             'payment_method_types' => [
//                 'card'
//             ],
//         ]);
//     }

//     public function paiement($amount, $currency, $description, array $stripeParameter) 
//     {
//         \Stripe\Stripe::setApiKey($this->privateKey);
//         $paiement_intent = null;

//         if(isset($stripeParameter['stripeIntentId'])) {
//             $paiement_intent = \Stripe\PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
//         }

//         if($stripeParameter['stripeIntentId'] === 'succeeded') {

//         } else {
//             $paiement_intent->cancel();
//         }

//         return $paiement_intent;
//     }

//     public function stripe(array $stripeParameter, Platform $platform) 
//     {
//         return $this->paiement(
//             50 * 100,
//             'eur',
//             $platform->getName(),
//             $stripeParameter
//         );
//     }
// }