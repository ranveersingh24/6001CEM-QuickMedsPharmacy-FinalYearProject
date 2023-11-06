<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserShippingAddress;
use App\AgentLevel;
use App\State;

use DB, Auth, Validator, Redirect, Toastr;

class UserShippingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $address_book = UserShippingAddress::select('user_shipping_addresses.*')
                                         ->where('user_id', Auth::user()->code)
                                         ->groupBy('user_shipping_addresses.id');
        $queries = [];
        $columns = [
           'name', 'address', 'phone'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
              
                $address_book = $address_book->where($column, 'like', "%".request($column)."%");

                $queries[$column] = request($column);

            }
        }

        $address_book = $address_book->paginate(10)->appends($queries);

        $count = count($address_book);

        
        

        return view('frontend.address_book', ['address_book'=>$address_book, 'count'=>$count]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $states = State::get();

        return view('frontend.address_book_create', ['states'=>$states]);
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
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'email' => 'required',
            'city' => 'required',
            'state' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        $input = $request->all();
        $input['user_id'] = Auth::user()->code;
        $create = UserShippingAddress::create($input);

        Toastr::success("Address Created Successfully!");
        return redirect()->route('AddressBook.AddressBook.index');
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
        $address = UserShippingAddress::where(DB::raw('md5(id)'), $id)->first();
        if(empty($address->id)){
          abort(404);
        }

        
        $states = State::get();

        return view('frontend.address_book_edit', ['address'=>$address, 'states'=>$states]);
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
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'email' => 'required',
            'city' => 'required',
            'state' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        $input = $request->all();
        // $input = $request->except(['_method', '_token', 'address_imp']);

        $update = UserShippingAddress::find($id);
        $update = $update->update($input);

        Toastr::success("Address Book Updated Successfully!");
        return redirect()->back();
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
