<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table="transactions";

//@var array
    protected $fillable = [
        'txHash',
        'amount',
        'status',
    ];
    public function pendingTransactions()
    {
        return $this->where('status', 1)->where('created_at', '<', Carbon::NOW()->subMinutes(20))->get();
    }
}
