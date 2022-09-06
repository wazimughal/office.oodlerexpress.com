<?php

namespace App\Http\Controllers\adminpanel;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Venue_groups;
use App\Models\adminpanel\venue_users;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->venueGroup= new Venue_groups;
        $this->venue_users= new venue_users;
      }

    public function index($id=NULL){
        $user=Auth::user();
        $leads_info = DB::table('users')
                 ->select('status', DB::raw('count(*) as total'))
                 ->groupBy('status')
                 ->where('group_id',config('constants.groups.subscriber'))
                 ->orderBy('status', 'asc')
                 ->get()->toArray();
        $user_info = DB::table('users')
                 ->select('group_id', DB::raw('count(*) as total'))
                 ->groupBy('group_id')
                 ->where('is_active',1)
                 ->orderBy('group_id', 'asc')
                 ->get();

        return view('adminpanel/home'.$id,compact('user','leads_info','user_info'));
        
    }
}
