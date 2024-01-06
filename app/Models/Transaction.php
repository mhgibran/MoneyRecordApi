<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use UuidTrait;
    protected $fillable = [
        'transaction_category_id',
        'card_source_id',
        'card_target_id',
        'type',
        'trx_date',
        'trx_number',
        'description',
        'amount',
    ];

    public function category()
    {
        return $this->belongsTo(TransactionCategory::class);    
    }

    public function source()
    {
        return $this->belongsTo(Card::class);    
    }

    public function target()
    {
        return $this->belongsTo(Card::class);    
    }
}
