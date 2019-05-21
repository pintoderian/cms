<?php

namespace App\Http\Controllers\Core;

use App\Models\Catalog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CatalogController extends Controller
{
    public function __construct(){
        $this->middleware('permission:view_catalog')->only(['index', 'show']);
        $this->middleware('permission:add_catalog')->only(['store']);
        $this->middleware('permission:edit_catalog')->only(['update']);
        $this->middleware('permission:delete_catalog')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Catalog::where(function($query) use($request){
            if(isset($request->texto)){
                $query->where('name', 'like', '%' . $request->texto . '%');
            }
        })->isActive()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'min:3|max:40|required|unique:catalog,name',
        ]);
        Catalog::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Catalog::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'min:3|max:40|required|unique:catalog,name,'.$id,
        ]);
        $catalog = Catalog::findOrFail($id);
        $catalog->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Catalog::findOrFail($id)->update(["status" => 0]);
    }
}
