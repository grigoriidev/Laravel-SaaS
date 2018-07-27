<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'token_info';
    public $timestamps = true;

}
// <script src='https://www.google.com/recaptcha/api.js'></script&gt;
// Paste this snippet at the end of the <form> where you want the reCAPTCHA widget to appear:
// <div class="g-recaptcha" data-sitekey="6LfqzFcUAAAAACc2OU7HIbamP0QXbsSFNXSvxsRv"></div>