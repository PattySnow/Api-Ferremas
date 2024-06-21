<?php
namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function index()
    {
        // Obtener todos los detalles de las órdenes con la información del usuario
        $orderDetails = OrderDetail::with('order.user')->get();

        // Formatear la respuesta
        $response = $orderDetails->map(function($orderDetail) {
            return [
                'id' => $orderDetail->id,
                'order_id' => $orderDetail->order_id,
                'user_id' => $orderDetail->order->user_id,
                'status' => $orderDetail->status,
                'vci' => $orderDetail->vci,
                'amount' => $orderDetail->amount,
                'buy_order' => $orderDetail->buy_order,
                'session_id' => $orderDetail->session_id,
                'card_number' => $orderDetail->card_number,
                'accounting_date' => $orderDetail->accounting_date,
                'transaction_date' => $orderDetail->transaction_date,
                'authorization_code' => $orderDetail->authorization_code,
                'payment_type_code' => $orderDetail->payment_type_code,
                'response_code' => $orderDetail->response_code,
                'installments_amount' => $orderDetail->installments_amount,
                'installments_number' => $orderDetail->installments_number,
                'balance' => $orderDetail->balance,
            ];
        });

        return response()->json($response);
    }

    public function show($id)
    {
        // Obtener el detalle de la orden con la información del usuario
        $orderDetail = OrderDetail::with('order.user')->findOrFail($id);

        // Formatear la respuesta
        $response = [
            'id' => $orderDetail->id,
            'order_id' => $orderDetail->order_id,
            'user_id' => $orderDetail->order->user_id,
            'status' => $orderDetail->status,
            'vci' => $orderDetail->vci,
            'amount' => $orderDetail->amount,
            'buy_order' => $orderDetail->buy_order,
            'session_id' => $orderDetail->session_id,
            'card_number' => $orderDetail->card_number,
            'accounting_date' => $orderDetail->accounting_date,
            'transaction_date' => $orderDetail->transaction_date,
            'authorization_code' => $orderDetail->authorization_code,
            'payment_type_code' => $orderDetail->payment_type_code,
            'response_code' => $orderDetail->response_code,
            'installments_amount' => $orderDetail->installments_amount,
            'installments_number' => $orderDetail->installments_number,
            'balance' => $orderDetail->balance,
        ];

        return response()->json($response);
    }
}
