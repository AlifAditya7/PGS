<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = ['order_id', 'revenue', 'cogs', 'expense_details', 'expense_items'];

    protected $casts = [
        'expense_details' => 'json',
        'expense_items' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
