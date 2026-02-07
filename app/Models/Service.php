<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'category', 'price', 'description', 'benefits', 'estimated_resources', 'type', 'activities', 'proposal_path'];

    protected $casts = [
        'benefits' => 'array',
        'activities' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
