<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stripe extends Model
{
    protected $table = 'stripe';
    public $timestamps = true;
}
