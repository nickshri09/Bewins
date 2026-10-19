<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FD extends Model
{
    use HasFactory;
    protected $table = 'table_fd';
    protected $fillable=[
        'currency_id',
        'customer_id',
        'durations',
        'fd_types',
        'fd_amount',
        'total_paid',
        'due_Amount',
        'start_date',
        'end_date',
        'description',
        'remarks',
        'percentage',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'fd_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'due_Amount' => 'decimal:2',
    ];

    const STATUS_PENDING   = 0;
    const STATUS_APPROVED  = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_REJECTED  = 3;

    public function user()
   {
    return $this->belongsTo(User::class);
   }
    public function member(){
        return $this->belongsTo(Member::class,'customer_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function agreement_documents()
    {
        return $this->hasMany(FdAgreementDocument::class, 'fd_id');
    }


}
