<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Brand;
use Validator, Redirect, Toastr, DB, File;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $brands = Brand::where('status', '!=', '3')
                       ->orderBy('created_at','desc');
        $queries = [];
        $columns = [
            'category_name', 'status'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                $brands = $brands->where($column, 'like', "%".request($column)."%");

                $queries[$column] = request($column);

            }
        }
        $per_page = 10;
        if(!empty(request('per_page'))){
            $per_page = request('per_page');
        }
        $brands = $brands->paginate($per_page)->appends($queries);
        return view('backend.brands.index', ['brands'=>$brands]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        $input = $request->all();
        $input['brand_name'] = htmlspecialchars(trim($request->brand_name));

        $brand = Brand::create($input);

        Toastr::success("Brand $brand->brand_name Create Successfully!");
        return redirect()->route('brand.brands.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::find($id);
        return view('backend.brands.edit', ['brand'=>$brand]);
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
        $validator = Validator::make($request->all(), [
            
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        $update = Brand::find($id);
        $brand_name = $update->category_name;
        $brand_chinese_name = $update->category_name;
        $update = $update->update($request->all());

        Toastr::success("Brand $brand_name Update Successfully!");
        return redirect()->route('brand.brands.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
