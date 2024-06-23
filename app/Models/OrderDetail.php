<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'orders_details';
    protected $fillable = [
        'order_id',
        'status',
        'vci',
        'amount',
        'buy_order',
        'session_id',
        'card_number',
        'accounting_date',
        'transaction_date',
        'authorization_code',
        'payment_type_code',
        'response_code',
        'installments_amount',
        'installments_number',
        'balance',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
