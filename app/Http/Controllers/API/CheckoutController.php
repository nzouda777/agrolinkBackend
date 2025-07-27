<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use NotchPay\NotchPay;
use NotchPay\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\PaymentApiKey;
use Illuminate\Support\Facades\Http;
class CheckoutController extends Controller
{
    // initiate payment with notchpay latest version

    public function initiatePayment(Request $request)
    {
        // recupere l'apikey du vendeur selon le produit
        $vendor = Product::find($request->product_id)->seller_id;
        $apiKey = PaymentApiKey::where('user_id', $vendor)->first();
        
        NotchPay::setApiKey($apiKey->api_key);

        $reference = uniqid('agro_');

        $payment = Payment::initialize([
            'amount' => $request->amount,
            'email'=> $request->email,
            'currency' => 'XAF',
            'callback' => route('checkout'),
            'reference' => $reference,
            'metadata' => [
                'order_id' => $request->order_id,
                'user_id' => $request->user_id,
            ],
        ]);

        return response()->json([
            'message' => 'Payment initiated successfully',
            'payment_url' => $payment->authorization_url,
            'payment' => $payment,
            // 'reference' => $payment->reference,
        ], 201);
    }

}
