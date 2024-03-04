<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNewspaper extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'newspaper_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newspaper()
    {
        return $this->belongsTo(Newspaper::class);
    }
}