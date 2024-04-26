<?php
// src/Service/StripeCheckoutSession.php
namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeCheckoutSession
{
    private $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
    }

    /**
     * return url to redirect Stripe payment site
     * @param array $cart
     * @return string 
     */
    public function createSession(array $cart)
    {
        Stripe::setApiKey($this->stripeSecretKey);
        $checkout_session = Session::create([
            'line_items' => [
                array_map(fn(array $product) => [
                    'quantity'   => 1,
                    'price_data' => [
                        'currency'     => 'EUR',
                        'product_data' => [
                            'name' => $product['name']
                        ],
                        'unit_amount'  => $product["price"]
                    ]
                ], $cart)
            ],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/payment/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost:8000/payment/cancel',
            'billing_address_collection'  => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR']
            ],
        ]);
        return $checkout_session;
    }
}
