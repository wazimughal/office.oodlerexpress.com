<?php

namespace App\Http\Controllers\adminpanel;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
      }

    public function index($id=NULL){
        

        if(isset($_GET['resetpassword']) && $_GET['resetpassword']==1) 
        return redirect()->route('admin.logout');

         $user=Auth::user();
        // if ($user->group_id!=config('constants.groups.admin')){
        //     abort(403, sprintf('Only admin are allowed '));
        // }
        
        
        $record_count=get_record_count();
        return view('adminpanel.home'.$id, get_defined_vars());
        
    }
}
