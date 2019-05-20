<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{   
    /**
     * Scope a query to only include users of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVuePaginate($query)
    {   
        if(isset(request()->per_page)){
            return (request()->per_page == "all") ? $query->get() : $query->paginate(request()->per_page);
        }else{
            return $query->paginate(10);
        }
    }
}
