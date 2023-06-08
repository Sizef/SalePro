<?php

namespace App\Http\Controllers;

use App\Service;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Keygen\Keygen;
use Spatie\Permission\Models\Role;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('services-index'))
        {
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission){
                $all_permission[] = $permission->name;
            }
            if(empty($all_permission)){
                $all_permission[] = 'dummy text';
            }

            $lims_service_list = Service::where('is_active' , 1)->get();
            
            // dd($lims_service_list);

            return view('backend.service.index' , compact('all_permission' , 'lims_service_list'));
        }else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.service.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => [
                'required',
                'max:255',
                    Rule::unique('services')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'code' => [
                'required',
                'max:255',
                    Rule::unique('services')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'price' => [
                'required',
                'max:255',
            ],
        ]);
        
        $data = $request->all();
        $data['is_active'] = 1;
        //dd($data);

        $message = 'Service created successfully';

        if(Service::create($data)){
            return redirect('services')->with('message', $message);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('services-edit')){
            $lims_service_data = Service::find($id);
            return view('backend.service.edit', compact('lims_service_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $data['title'] = $request['title'];
        $data['code'] = $request['code'];
        $data['price'] = $request['price'];

        $lims_service_data = Service::find($id);

        $message = 'Service updated successfully';

        $lims_service_data->update($data);

        return redirect('services')->with('message', $message);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service_id = $service->id;
        DB::table('services')->where('id', $service_id)->update(['is_active' => 0]);
        return redirect('services')->with('not_permitted','Service deleted Successfully');

        
    }

    public function deleteBySelection(Request $request) 
    {
        $service_id = $request['serviceIdArray'];
        //dd($service_id);
        foreach ($service_id as $id) {
            $lims_service_data = Service::find($id);
            //dd($lims_service_data);
            //$lims_product_quotation_data = ProductQuotation::where('quotation_id', $id)->get();
            // foreach ($lims_service_data as $service_data) {
            //     $service_data->delete();
            // }
            //$lims_service_data->is_active = 0;

            DB::table('services')->where('id', $id)->update(['is_active' => 0]);
        }
        return 'Quotation deleted successfully!';
    }

    public function generateCode()
    {
        $id = Keygen::numeric(6)->generate();
        return $id;
    }



}
