<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class CatalogDetails extends Model
{
    use Sluggable;

    protected $table = 'catalog_details';

    protected $fillable = [
        'catalog_id',
        'name',
        'slug',
        'status'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    /**
     * @param $query
     * @return mixed
     */
    public function scopeIsActive($query){
        return $query->where("status", 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function catalog()
    {
        return $this->belongsTo('App\Models\Catalog', 'catalog_id');
    }
}
