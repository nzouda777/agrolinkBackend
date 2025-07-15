<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['buyer', 'status', 'items.product']);

        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->has('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        return $query->paginate(20);
    }

    public function show($id)
    {
        return Order::with([
            'buyer',
            'status',
            'items.product',
            'payments',
            'statusHistory'
        ])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_method' => 'required|string',
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'buyer_id' => $request->user()->id,
                'status_id' => OrderStatus::where('name', 'pending')->first()->id,
                'shipping_fee' => 0, // Calculate based on shipping method
                'tax_amount' => 0, // Calculate based on items
                'shipping_method' => $request->shipping_method,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $product->price;
                $total += $unitPrice * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $item['quantity'],
                    'status' => 'pending'
                ]);
            }

            $order->total_amount = $total;
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status_id' => 'required|exists:order_statuses,id',
            'notes' => 'nullable|string',
        ]);

        // Update order status
        $order->status_id = $request->status_id;
        $order->save();

        // Create status history
        DB::table('order_status_history')->insert([
            'order_id' => $order->id,
            'status_id' => $request->status_id,
            'user_id' => $request->user()->id,
            'notes' => $request->notes,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    public function getItems($id)
    {
        $order = Order::findOrFail($id);
        return $order->items()->with(['product'])->get();
    }

    public function createPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'transaction_id' => 'required|string',
            'provider' => 'required|string',
            'amount' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $payment = OrderPayment::create([
            'order_id' => $id,
            'transaction_id' => $request->transaction_id,
            'provider' => $request->provider,
            'amount' => $request->amount,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment
        ]);
    }
}
