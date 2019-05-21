<?php

namespace App\Http\Controllers\Core;

use App\Models\CatalogDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CatalogDetailsController extends Controller
{
    public function __construct(){
        $this->middleware('permission:view_catalog_details')->only(['show']);
        $this->middleware('permission:add_catalog_details')->only(['store']);
        $this->middleware('permission:edit_catalog_details')->only(['update']);
        $this->middleware('permission:delete_catalog_details')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'name' => 'min:3|max:40|required|unique:catalog_details,name',
        ]);
        CatalogDetails::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        return CatalogDetails::where('catalog_id', $id)
            ->where(function($q) use($request){
                if(isset($request->text)){
                    $q->where('name', 'like', '%' . $request->texto . '%');
                }
            })
            ->isActive()
            ->paginate(10);
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
            'name' => 'min:3|max:40|required|unique:catalog_details,name,'.$id,
        ]);
        $catalog_details = CatalogDetails::findOrFail($id);
        $catalog_details->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CatalogDetails::findOrFail($id)->update(["status" => 0]);
    }
}
