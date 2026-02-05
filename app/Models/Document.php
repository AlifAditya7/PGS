<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['order_id', 'type', 'file_path', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
