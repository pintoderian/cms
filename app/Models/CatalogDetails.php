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
        'slug'
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

    public function catalog()
    {
        return $this->belongsTo('App\Models\Catalog', 'catalog_id');
    }
}
