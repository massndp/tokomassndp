<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;

class NotificationHandlerController extends Controller
{
    public function index(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);
        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        if ($notification->signatur_key != $validSignatureKey) {
            return response(['message' => 'invalid signature'], 403);
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;

        //data transaction
        $data_transaction = Invoice::where('invoide', $orderId)->first();

        if ($transaction == 'capture') {
            //for credit card, we need to check whether transaction is challange by FDS or not

            if ($type == 'credit_card') {
                //nothing
            }
        } elseif ($transaction == 'settlement') {
            //update status invoice to success
            $data_transaction->update([
                'status' => 'success'
            ]);

            //update stock product
            foreach ($data_transaction->orders->get() as $order) {
                $product = Product::whereId($order->product_id)->first();
                $product->update([
                    'stock' => $product->stock - $order->qty
                ]);
            }
        } elseif ($transaction == 'pending') {
            //update status invoice to pending
            $data_transaction->update([
                'status' => 'pending'
            ]);
        } elseif ($transaction == 'deny') {
            $data_transaction->update([
                'status' => 'failed'
            ]);
        } elseif ($transaction == 'expired') {
            $data_transaction->update([
                'status' => 'expired'
            ]);
        } elseif ($transaction == 'cancel') {
            $data_transaction->update([
                'status' => 'failed'
            ]);
        }
    }
}
