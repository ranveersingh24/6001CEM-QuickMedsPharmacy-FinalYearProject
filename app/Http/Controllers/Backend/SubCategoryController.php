<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\SubCategory;
use App\Category;
use Validator, Redirect, Toastr, DB, File;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sub_categories = SubCategory::select('sub_categories.*', 'c.category_name')
                                     ->join('categories AS c', 'c.id', 'sub_categories.category_id')
                                     ->where('sub_categories.status', '!=', '3')
                                     ->orderBy('sub_category_name','asc');
        $queries = [];
        $columns = [
            'sub_category_name', 'category_name', 'status'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                $sub_categories = $sub_categories->where($column, 'like', "%".request($column)."%");

                $queries[$column] = request($column);

            }
        }
        $per_page = 10;
        if(!empty(request('per_page'))){
            $per_page = request('per_page');
        }
        $sub_categories = $sub_categories->paginate($per_page)->appends($queries);

        return view('backend.sub_categories.index', ['sub_categories'=>$sub_categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('status', '1')->get();
        return view('backend.sub_categories.create', ['categories'=>$categories]);
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
            'sub_category_code' => ['required', 'unique:sub_categories'],
            'sub_category_name' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        $input = $request->all();
        $input['sub_category_name'] = trim($request->sub_category_name);

        $sub_category = SubCategory::create($input);

        Toastr::success("Sub Category $sub_category->sub_category_name Create Successfully!");
        return redirect()->route('sub_category.sub_categories.index');
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
        $sub_category = SubCategory::find($id);
        $categories = Category::where('status', '1')->get();
        return view('backend.sub_categories.edit', ['sub_category'=>$sub_category, 'categories'=>$categories]);
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
        if ($request->sub_category_name == '') {
            return Redirect::back()->withInput(Input::all())->withErrors('Sub Category Name is required!');
        }

        $update = SubCategory::find($id);
        $sub_category_name = $update->sub_category_name;
        $update = $update->update($request->all());

        Toastr::success("Sub Category $sub_category_name Update Successfully!");
        return redirect()->route('sub_category.sub_categories.edit', $id);
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
