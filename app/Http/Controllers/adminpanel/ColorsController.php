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
            'color_book'=>'required',
        ]);
        $colorData=$request->all();
       unset($colorData['_token']);
       //p($colorData);
        foreach($colorData['color_book'] as $key=>$data){
           // p($data);break;
            $dataToUpdate['bg_color']=$data[0];
            $dataToUpdate['color_value']=$data[1];
            $dataToUpdate['color_for']=$data[2];
            $dataToUpdate['description']=$data[3];
            $this->colors->where('id',$key)->update($dataToUpdate);
        }
        
        $request->session()->flash('alert-success', 'Colors updated Successfully');
        
        
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' updated colors';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>get_session_value('id'),
                        'action_slug'=>'color_updated',
                        'comments'=>$activityComment,
                        'others'=>'colors_book',
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
