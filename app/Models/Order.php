<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_state_id',
        'payment_id',
        'total',
        'total_items',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => 'decimal:2',
        'total_items' => 'integer',
    ];

    /**
     * Obtiene el usuario relacionado con esta orden.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el estado relacionado con esta orden.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(OrderState::class, 'order_state_id');
    }

    /**
     * Obtiene el método de pago relacionado con esta orden.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Obtiene los items relacionados con esta orden.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
