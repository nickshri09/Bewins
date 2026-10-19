<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model {
    public const MAX_TERM = 600;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loan_products';


    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    public function hasValidTerm(): bool
    {
        return is_numeric($this->term)
            && (int) $this->term >= 1
            && (int) $this->term <= self::MAX_TERM
            && in_array(strtolower(trim((string) $this->term_period)), [
                '+1 day', '+1 week', '+1 month', '+1 year',
            ], true);
    }
}
