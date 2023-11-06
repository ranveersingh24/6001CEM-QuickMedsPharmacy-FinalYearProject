<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Admin;
use App\Merchant;
use App\WebsiteSetting;
use Toastr, Auth, DB;

class AdminController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = WebsiteSetting::find(1);
        return view('backend.admins.index', ['setting'=>$setting]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
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
        //
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

        $this->validate($request,
        [
           'f_name'=> 'required',
           'l_name'=> 'required',
        ]);

        $input = $request->all();
        $input = $request->all();

        if(Auth::guard('admin')->check()){
            $admin = Admin::find($id);
        }else{
            $admin = Merchant::find($id);
        }
        
        
        $setting = WebsiteSetting::find(1);

        if(!empty($setting->id)){
            $setting = $setting->update(['about_us'=>$request->about_us,
                                         'faqs'=>$request->setting_faqs_description,
                                         'address'=>$request->address,
                                         'privacy_policy_description'=>$request->privacy_policy_description,
                                         'return_policy_description'=>$request->return_policy_description,
                                         'shipping_policy_description'=>$request->shipping_policy_description,
                                         'sst_registration_no'=>$request->sst_registration_no,
                                         'company_registration_no'=>$request->company_registration_no]);
        }else{
            $createSetting = WebsiteSetting::create(['about_us'=>$request->about_us,
                                                     'faqs'=>$request->setting_faqs_description,
                                                     'address'=>$request->address,
                                                     'privacy_policy_description'=>$request->privacy_policy_description,
                                                     'return_policy_description'=>$request->return_policy_description,
                                                     'shipping_policy_description'=>$request->shipping_policy_description,
                                                     'sst_registration_no'=>$request->sst_registration_no,
                                                     'company_registration_no'=>$request->company_registration_no]);
        }
        
        if(!empty($request->file('website_logo'))){
            $files = $request->file('website_logo'); 
            $name = $files->getClientOriginalName();
            $exp = explode(".", $name);
            $file_ext = end($exp);
            $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;
        
            $files->move("uploads/logo/", $name);
            $input['website_logo'] = "uploads/logo/".$name;
            
        }

        if(!empty($request->file('profile_logo'))){
            $files = $request->file('profile_logo'); 
            $name = $files->getClientOriginalName();
            $exp = explode(".", $name);
            $file_ext = end($exp);
            $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;

            $files->move("uploads/profile_logo/", $name);
            $input['profile_logo'] = "uploads/profile_logo/".$name;
            
        }

        if(isset($request->logo_hidden) && !empty($request->logo_hidden)){
            $input['logo_hidden'] = 1;
        }else{
            $input['logo_hidden'] = 0;
        }

        if(isset($request->name_hidden) && !empty($request->name_hidden)){
            $input['name_hidden'] = 1;
        }else{
            $input['name_hidden'] = 0;
        }


        if(isset($request->password) && !empty($request->password)){
            $this->validate($request,
            [
               'password'=> 'confirmed',
            ]);
            $input['password'] = Hash::make($request->password);
        }else{
            $input['password'] = $admin->password;
        }

        $admin = $admin->update($input);

        Toastr::success("Profile Updated!");
        return redirect()->route('admin.admins.index');
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

    public function setting_faqs()
    {
        $setting = WebsiteSetting::find(1);
        return view('backend.admins.setting_faqs', ['setting'=>$setting]);
    }


    public function save_setting_faqs(Request $request)
    {
        $setting = WebsiteSetting::find(1);

        if(!empty($setting->id)){
            $setting = $setting->update(['faqs'=>$request->setting_faqs_description]);
        }else{
            $createSetting = WebsiteSetting::create(['faqs'=>$request->setting_faqs_description]);
        }

        Toastr::success("FAQs Updated!");
        return redirect()->route('setting_faqs');
    }
}
