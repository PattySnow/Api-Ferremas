<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use Transbank\Webpay\WebpayPlus;
use Illuminate\Support\Facades\Auth;
use Transbank\Webpay\WebpayPlus\Transaction;

class WebpayController extends Controller
{
    public function __construct()
    {
        if (app()->environment('production')) {
            WebpayPlus::configureForProduction(
                env('WEBPAY_PLUS_CC'),
                env('WEBPAY_PLUS_API_KEY')
            );
        } else {
            WebpayPlus::configureForTesting();
        }
    }

    public function checkout(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Encontrar el último carrito asociado al usuario (el más reciente)
        $cart = $user->cart()
            ->where('status', 'Pending')
            ->latest()
            ->first();

        if (!$cart) {
            return response()->json(['error' => 'Carrito no encontrado'], 404);
        }

        // Verificar el estado del carrito
        if ($cart->status === 'Paid') {
            return response()->json(['message' => 'No tiene nada pendiente por pagar'], 200);
        }

        // Buscar una compra pendiente para este carrito
        $pendingOrder = Order::where('cart_id', $cart->id)
            ->where('status', 'Pending') // Pendiente
            ->first();

        if ($pendingOrder) {
            // Retornar la URL de pago de la compra pendiente
            $url_to_pay = $this->startWebpayPlusTransaction($pendingOrder);
            return response()->json(['url' => $url_to_pay], 200);
        }

        // Crear una nueva compra si no hay una pendiente
        $order = new Order();
        $order->total = $cart->total; // Tomar el total del carrito
        $order->user_id = $user->id;
        $order->cart_id = $cart->id;
        $order->status = 'Pending'; // Estado por defecto 'Pendiente'
        $order->save();

        // Iniciar la transacción con Webpay
        $url_to_pay = $this->startWebpayPlusTransaction($order);
        return response()->json(['url' => $url_to_pay], 201); // Devuelve la URL para el pago
    }



    public function startWebpayPlusTransaction($order)
    {
        $session_id = rand();
        $transaccion = (new Transaction)->create(
            $order->id, // buy_order
            $session_id, // session_id
            $order->total, // amount
            route('confirmar_pago') // return_url
        );

        // Almacenar el token_ws en la compra
        $order->token_ws = $transaccion->getToken();
        $order->update(); // Actualizar la compra con el token_ws

        // Guardar session_id en OrderDetail
        $orderDetail = new OrderDetail([
            'order_id' => $order->id, // o 'compra_id'
            'session_id' => $session_id,
            // Otros campos según sea necesario
        ]);
        $orderDetail->save();

        $url = $transaccion->getUrl() . '?token_ws=' . $transaccion->getToken();
        return $url;
    }



    public function confirm(Request $request)
    {
        $confirmation = (new Transaction)->commit($request->get('token_ws'));
        $order = Order::where('id', $confirmation->buyOrder)->first();

        if ($confirmation->isApproved()) {
            $order->status = "Approved"; // Aprobada
            $this->updateStock($order); // Actualizar el stock
        } else {
            $order->status = "Rejected"; // Rechazada
        }

        $order->token_ws = $request->get('token_ws');
        $order->update();

        if ($confirmation->isApproved()) {
            $cart = Cart::find($order->cart_id);
            if ($cart) {
                $cart->status = 'Paid';
                $cart->update();
            }
        }

        $transactionDate = date('Y-m-d H:i:s', strtotime($confirmation->getTransactionDate()));
        $cardDetail = $confirmation->getCardDetail();
        $cardNumber = isset($cardDetail['card_number']) ? $cardDetail['card_number'] : null;

        \Log::info('Confirmation Details:', [
            'status' => $confirmation->getStatus(),
            'vci' => $confirmation->getVci(),
            'amount' => $confirmation->getAmount(),
            'buy_order' => $confirmation->getBuyOrder(),
            'session_id' => $confirmation->getSessionId(),
            'card_number' => $cardNumber,
            'accounting_date' => $confirmation->getAccountingDate(),
            'transaction_date' => $transactionDate,
            'authorization_code' => $confirmation->getAuthorizationCode(),
            'payment_type_code' => $confirmation->getPaymentTypeCode(),
            'response_code' => $confirmation->getResponseCode(),
            'installments_amount' => $confirmation->getInstallmentsAmount(),
            'installments_number' => $confirmation->getInstallmentsNumber(),
            'balance' => $confirmation->getBalance(),
        ]);

        $orderDetail = OrderDetail::firstOrNew(['order_id' => $order->id]);
        $orderDetail->status = $confirmation->getStatus();
        $orderDetail->vci = $confirmation->getVci();
        $orderDetail->amount = $confirmation->getAmount();
        $orderDetail->buy_order = $confirmation->getBuyOrder();
        $orderDetail->session_id = $confirmation->getSessionId();
        $orderDetail->card_number = $cardNumber;
        $orderDetail->accounting_date = $confirmation->getAccountingDate();
        $orderDetail->transaction_date = $transactionDate;
        $orderDetail->authorization_code = $confirmation->getAuthorizationCode();
        $orderDetail->payment_type_code = $confirmation->getPaymentTypeCode();
        $orderDetail->response_code = $confirmation->getResponseCode();
        $orderDetail->installments_amount = $confirmation->getInstallmentsAmount();
        $orderDetail->installments_number = $confirmation->getInstallmentsNumber();
        $orderDetail->balance = $confirmation->getBalance();

        \Log::info('OrderDetail data before save:', [
            'order_id' => $orderDetail->order_id,
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
        ]);

        $orderDetail->save();

        if ($confirmation->isApproved()) {
            return response()->json([
                'message' => 'Compra aprobada',
                'compra_id' => $order->id,
                'status' => $confirmation->getStatus(),
                'vci' => $confirmation->getVci(),
                'amount' => $confirmation->getAmount(),
                'buy_order' => $confirmation->getBuyOrder(),
                'session_id' => $confirmation->getSessionId(),
                'card_detail' => [
                    'card_number' => $cardNumber,
                ],
                'accounting_date' => $confirmation->getAccountingDate(),
                'transaction_date' => $confirmation->getTransactionDate(),
                'authorization_code' => $confirmation->getAuthorizationCode(),
                'payment_type_code' => $confirmation->getPaymentTypeCode(),
                'response_code' => $confirmation->getResponseCode(),
                'installments_amount' => $confirmation->getInstallmentsAmount(),
                'installments_number' => $confirmation->getInstallmentsNumber(),
                'balance' => $confirmation->getBalance(),
            ], 200);
        } else {
            return response()->json([
                'message' => 'Compra rechazada',
                'compra_id' => $order->id,
                'status' => $confirmation->getStatus(),
                'vci' => $confirmation->getVci(),
                'amount' => $confirmation->getAmount(),
                'buy_order' => $confirmation->getBuyOrder(),
                'session_id' => $confirmation->getSessionId(),
                'card_detail' => [
                    'card_number' => $cardNumber,
                ],
                'accounting_date' => $confirmation->getAccountingDate(),
                'transaction_date' => $confirmation->getTransactionDate(),
                'authorization_code' => $confirmation->getAuthorizationCode(),
                'payment_type_code' => $confirmation->getPaymentTypeCode(),
                'response_code' => $confirmation->getResponseCode(),
                'installments_amount' => $confirmation->getInstallmentsAmount(),
                'installments_number' => $confirmation->getInstallmentsNumber(),
                'balance' => $confirmation->getBalance(),
            ], 200);
        }
    }


    private function updateStock($order)
    {
        // Obtener el carrito de la compra
        $cart = Cart::find($order->cart_id);

        // Verificar que el carrito existe y tenga una sucursal asociada
        if ($cart && $cart->branch_id) {
            foreach ($cart->items as $item) {
                // Encontrar el inventario del producto en la sucursal del carrito
                $inventory = Inventory::where('item_id', $item->pivot->item_id)
                    ->where('branch_id', $cart->branch_id)
                    ->first();

                if ($inventory) {
                    // Actualizar el stock en el inventario de la sucursal
                    $inventory->quantity -= $item->pivot->quantity;
                    $inventory->save();
                }
            }
        }
    }


    public function cancel(Request $request)
    {
        // Verificar que el usuario esté autenticado
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Encontrar la última compra pendiente del usuario
        $pendingOrder = Order::where('user_id', $user->id)
            ->where('status', 'Pending') // Pendiente
            ->latest() // Ordenar por fecha de creación descendente
            ->first(); // Obtener el primero (más reciente)

        if (!$pendingOrder) {
            return response()->json(['error' => 'No hay pago pendiente'], 404);
        }

        // Obtener el carrito asociado a la compra pendiente
        $cart = Cart::where('id', $pendingOrder->cart_id)->first();

        if (!$cart) {
            return response()->json(['error' => 'Carrito no encontrado'], 404);
        }

        // Actualizar el estado de la compra a 'cancelada'
        $pendingOrder->status = "Cancelled"; // Cancelada
        $pendingOrder->save();

        // Actualizar el estado del carrito a 'cancelado'
        $cart->status = 'Cancelled';
        $cart->save();

        // Verificar si se realizaron los cambios correctamente
        if ($pendingOrder->status === "Cancelled" && $cart->status === 'Cancelled') {
            return response()->json(['message' => 'Compra cancelada exitosamente'], 200);
        } else {
            return response()->json(['error' => 'Error al cancelar la compra'], 500);
        }
    }
}
