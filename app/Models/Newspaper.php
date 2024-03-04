<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newspaper extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_newspaper');
    }
}