<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentApiKey;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

class PaymentController extends Controller
{
    protected $notchPayClient;

    public function __construct()
    {
        $this->notchPayClient = new Client([
            'base_uri' => config('services.notchpay.base_url'),
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.notchpay.api_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function index(Request $request)
    {
        return $request->user()->payments()->with(['user'])->paginate(20);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mobile_money,bank_transfer,card,cash',
            'provider' => 'required|string',
            'account_number' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $payment = $request->user()->payments()->create([
            'type' => $request->type,
            'provider' => $request->provider,
            'account_number' => $request->account_number,
            'is_default' => $request->is_default,
        ]);

        // If this is default, make others non-default
        if ($request->is_default) {
            $request->user()->payments()
                ->where('id', '!=', $payment->id)
                ->update(['is_default' => false]);
        }

        return response()->json([
            'message' => 'Payment method created successfully',
            'payment' => $payment
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $payment = PaymentMethod::findOrFail($id);

        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to update payment method'
            ], 403);
        }

        $request->validate([
            'type' => 'required|in:mobile_money,bank_transfer,card,cash',
            'provider' => 'required|string',
            'account_number' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $payment->update($request->all());

        // If this is default, make others non-default
        if ($request->is_default) {
            $request->user()->payments()
                ->where('id', '!=', $payment->id)
                ->update(['is_default' => false]);
        }

        return response()->json([
            'message' => 'Payment method updated successfully',
            'payment' => $payment
        ]);
    }

    public function destroy($id)
    {
        $payment = PaymentMethod::findOrFail($id);

        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to delete payment method'
            ], 403);
        }

        $payment->delete();

        return response()->json([
            'message' => 'Payment method deleted successfully'
        ]);
    }

    public function createApiKey(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
            'is_live' => 'boolean',
        ]);

        $apiKey = $request->user()->apiKeys()->create($request->all());

        return response()->json([
            'message' => 'API key created successfully',
            'api_key' => $apiKey
        ], 201);
    }

    public function updateApiKey(Request $request, $id)
    {
        $apiKey = PaymentApiKey::findOrFail($id);

        if ($apiKey->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to update API key'
            ], 403);
        }

        $request->validate([
            'provider' => 'required|string',
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
            'is_live' => 'boolean',
        ]);

        $apiKey->update($request->all());

        return response()->json([
            'message' => 'API key updated successfully',
            'api_key' => $apiKey
        ]);
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'currency' => 'required|string|in:XAF',
            'description' => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        if ($paymentMethod->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized payment method'
            ], 403);
        }

        try {
            $response = $this->notchPayClient->post('/payments/initiate', [
                'json' => [
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'payment_method' => $paymentMethod->type,
                    'description' => $request->description,
                    'metadata' => [
                        'order_id' => $order->id,
                        'user_id' => $request->user()->id,
                    ],
                ],
            ]);

            $paymentData = json_decode($response->getBody(), true);

            // Create order payment record
            $orderPayment = OrderPayment::create([
                'order_id' => $order->id,
                'transaction_id' => $paymentData['transaction_id'],
                'provider' => 'notchpay',
                'amount' => $request->amount,
                'status' => 'pending',
                'payment_method_id' => $paymentMethod->id,
            ]);

            return response()->json([
                'message' => 'Payment initiated successfully',
                'payment_url' => $paymentData['payment_url'],
                'payment' => $orderPayment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to initiate payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        try {
            $response = $this->notchPayClient->get('/payments/verify/' . $request->transaction_id);
            $paymentData = json_decode($response->getBody(), true);

            if ($paymentData['status'] === 'success') {
                // Update order payment status
                $orderPayment = OrderPayment::where('transaction_id', $request->transaction_id)->first();
                if ($orderPayment) {
                    $orderPayment->update([
                        'status' => 'completed',
                        'notes' => 'Payment verified successfully',
                    ]);

                    // Update order status
                    $order = $orderPayment->order;
                    $order->update([
                        'status_id' => OrderStatus::where('name', 'paid')->first()->id,
                    ]);
                }

                return response()->json([
                    'message' => 'Payment verified successfully',
                    'payment' => $paymentData
                ]);
            }

            return response()->json([
                'message' => 'Payment verification failed',
                'payment' => $paymentData
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to verify payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();
            
            // Verify webhook signature
            $signature = $request->header('X-NotchPay-Signature');
            $expectedSignature = hash_hmac('sha256', json_encode($payload), config('services.notchpay.webhook_secret'));

            if (!hash_equals($signature, $expectedSignature)) {
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            // Handle different webhook events
            switch ($payload['event']) {
                case 'payment.completed':
                    $this->handlePaymentCompleted($payload['data']);
                    break;
                case 'payment.failed':
                    $this->handlePaymentFailed($payload['data']);
                    break;
                case 'payment.refunded':
                    $this->handlePaymentRefunded($payload['data']);
                    break;
            }

            return response()->json(['message' => 'Webhook processed successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process webhook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function handlePaymentCompleted($data)
    {
        $orderPayment = OrderPayment::where('transaction_id', $data['transaction_id'])->first();
        if ($orderPayment) {
            $orderPayment->update([
                'status' => 'completed',
                'notes' => 'Payment completed successfully',
            ]);

            // Update order status
            $order = $orderPayment->order;
            $order->update([
                'status_id' => OrderStatus::where('name', 'paid')->first()->id,
            ]);
        }
    }

    protected function handlePaymentFailed($data)
    {
        $orderPayment = OrderPayment::where('transaction_id', $data['transaction_id'])->first();
        if ($orderPayment) {
            $orderPayment->update([
                'status' => 'failed',
                'notes' => $data['reason'] ?? 'Payment failed',
            ]);
        }
    }

    protected function handlePaymentRefunded($data)
    {
        $orderPayment = OrderPayment::where('transaction_id', $data['transaction_id'])->first();
        if ($orderPayment) {
            $orderPayment->update([
                'status' => 'refunded',
                'notes' => 'Payment refunded',
            ]);
        }
    }
}
