<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use UuidTrait;
    protected $fillable = [
        'name', 'type', 'balance', 'image', 'description',
    ];
}
