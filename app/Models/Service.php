<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'category', 'price', 'description', 'benefits', 'estimated_resources', 'type', 'activities'];

    protected $casts = [
        'benefits' => 'array',
        'activities' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
