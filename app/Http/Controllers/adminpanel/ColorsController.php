<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\colorsBook;
use App\Models\adminpanel\Groups;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ColorsController extends Controller
{
    //
    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->colors= new colorsBook;
      }
      public function addcolors(){
        $user=Auth::user(); 
        
         return view('adminpanel/add_colors',compact('user'));
     }
     public function SavecolorsData(Request $request){
       
        $validator=$request->validate([
            'bg_color'=>'required',
            'color_value'=>'required',
            'color_for'=>'required',
        ]);
        $colorData=$request->all();
       unset($colorData['_token']);
        foreach($colorData as $data){
           
            p($data); break;
        }
        p($request->all());
        dd('test');
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->phone=$request['phone'];
        
        $this->users->created_at=time();
        
        $request->session()->flash('alert-success', 'Customer Added! Please Check in colors list Tab');
        $this->users->save();
        
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new customer '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'customer_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    // List All the colors 
    public function colors($type=NULL){
        $user=Auth::user();
            $colorsData=$this->colors
            ->where('user_id', '=', get_session_value('id'))
            ->orderBy('created_at', 'desc')->get()->toArray();
            
        return view('adminpanel/colors',compact('colorsData','user'));
    }
}
