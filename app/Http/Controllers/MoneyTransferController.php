<?php

namespace App\Http\Controllers;

use App\MoneyTransfer;
use App\Account;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class MoneyTransferController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('money-transfer')){
            $lims_money_transfer_all = MoneyTransfer::get();
            $lims_account_list = Account::where('is_active', true)->get();
            return view('backend.money_transfer.index', compact('lims_money_transfer_all', 'lims_account_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['reference_no'] = 'mtr-' . date("Ymd") . '-'. date("his");
        MoneyTransfer::create($data);
        return redirect()->back()->with('message', 'Money transfered successfully');
    }

    public function show(MoneyTransfer $moneyTransfer)
    {
        //
    }

    public function edit(MoneyTransfer $moneyTransfer)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        MoneyTransfer::find($data['id'])->update($data);
        return redirect()->back()->with('message', 'Money transfer updated successfully');
    }

    public function destroy($id)
    {
        MoneyTransfer::find($id)->delete();
        return redirect()->back()->with('not_permitted', 'Data deleted successfully');
    }


    public function deleteBySelection(Request $request) {

        $money_transfer_id = $request['money_transferIdArray'];

        foreach($money_transfer_id as $id){
            $lims_return_data = MoneyTransfer::find($id);
            $lims_return_data->delete();
            return('Data deleted successfully');
    
        }

    }
}
