<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthenticationLogs extends Model
{
    protected $table = 'authentication_log';

    protected $fillable = [
        'authenticatable_id', 'ip_address', 'user_agent', 'login_at', 'logout_at'
    ];

    protected $appends = ['browser'];

    public function getBrowserAttribute()
    {
        $browser = parse_user_agent($this->user_agent);
        return $browser;
    }

    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\User', 'authenticatable_id');
    }
}
