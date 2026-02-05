<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['order_id', 'title', 'description', 'start_time', 'end_time', 'meeting_link', 'google_event_id', 'location_type', 'address'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
