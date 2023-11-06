<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Category;
use App\CategoryImage;
use Validator, Redirect, Toastr, DB, File;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {

        $categories = Category::where('status', '!=', '3')
                              ->orderBy('created_at','desc');
        $queries = [];
        $columns = [
            'code', 'category_name', 'status'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                $categories = $categories->where($column, 'like', "%".request($column)."%");

                $queries[$column] = request($column);

            }
        }
        $per_page = 10;
        if(!empty(request('per_page'))){
            $per_page = request('per_page');
        }
        $categories = $categories->paginate($per_page)->appends($queries);


        return view('backend.categories.index', ['categories'=>$categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.categories.create');
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
            'code' => ['required', 'unique:categories'],
            'category_name' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        $input = $request->all();
        $input['category_name'] = trim($request->category_name);
        $input['menu_bar'] = !empty($request->menu_bar) ? $request->menu_bar : 0;

        $category = Category::create($input);

        
        if(!empty($request->upload_image)){
            
            $files = $request->file('upload_image'); 
            $name = $files->getClientOriginalName();
            $exp = explode(".", $name);
            $file_ext = end($exp);
            $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;

            $input2 = [];
            $input2['category_id'] = $category->id;
            $input2['status'] = '1';
            $input2['image'] = "uploads/category/".$name;
            $files->move("uploads/category/", $name);
            $category_image = CategoryImage::create($input2);            
        }

        Toastr::success("Category $category->category_name Create Successfully!");
        return redirect()->route('category.categories.index');
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
        $category = Category::find($id);
        return view('backend.categories.edit', ['category'=>$category]);
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

        $input = $request->all();
        $input['category_name'] = trim($request->category_name);
        $input['category_chinese_name'] = trim($request->category_chinese_name);
        $input['menu_bar'] = isset($request->menu_bar) ? $request->menu_bar : 0;

        $update = Category::find($id);
        $category_name = $update->category_name;
        $category_chinese_name = $update->category_chinese_name;
        $update = $update->update($input);

        if(!empty($request->upload_image)){
            
            $files = $request->file('upload_image'); 
            $name = $files->getClientOriginalName();
            $exp = explode(".", $name);
            $file_ext = end($exp);
            $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;



            $input2 = [];

            $input2['category_id'] = $id;
            $input2['status'] = '1';
            $input2['image'] = "uploads/category/".$name;
            $files->move("uploads/category/", $name);

            $category_image = CategoryImage::where('category_id', $id)->first();
            if(!empty($category_image->id)){
                $update_category_image = CategoryImage::find($category_image->id);
                $update_category_image = $category_image->update($input2);
            }else{
                $category_image = CategoryImage::create($input2);
            }

        }


        Toastr::success("Category $category_name Update Successfully!");
        return redirect()->route('category.categories.edit', $id);
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
