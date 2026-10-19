<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'branches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // YAHAN CHANGE KIYA HAI: Sirf 'name' ki jagah saare fields add kar diye
    protected $fillable = [
        'name', 
        'contact_email', 
        'contact_phone', 
        'address', 
        'descriptions', 
        'state', 
        'district',
        'branch_image',
        'pincode',
        'agreements'
    ];

    public function __construct() {
        parent::__construct(); // Ye line Laravel ke liye zaroori hai
        $this->attributes['name'] = get_option('default_branch_name', 'Main Branch');
    }

}