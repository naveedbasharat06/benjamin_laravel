<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{


	protected $table = 'users_verification';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pin', 'status', 'user_id'
    ];
}
