<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogUserActivity extends Model
{
    protected $table = 'logs_user_activity';

    protected $fillable = [
        'user_id', 
        'ip_address', 
        'user_agent',
        'current_date_b',
        'table_name',
        'type_query',
        'sql_query',
        'current_url'
    ];

    protected $appends = ['browser', 'date'];

    public function getBrowserAttribute()
    {
        $browser = parse_user_agent($this->user_agent);
        return $browser;
    }

    public function getDateAttribute(){
        return date('Y-m-d', strtotime($this->current_date_b));
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
