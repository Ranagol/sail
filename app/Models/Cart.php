<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * This is a very simple cart. We collect here all the products, that have been scanned.
 * As a result, we will have here a list of all products that the customer wants to buy.
 */
class Cart extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'carts';

    //Do not use timestamps
    public $timestamps = false;
}
