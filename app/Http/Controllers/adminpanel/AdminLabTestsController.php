<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\LabTests;
use App\Models\adminpanel\LabTestsParams;
use Illuminate\Support\Facades\Auth;

class AdminLabTestsController extends Controller
{
    
    //
    function __construct() {
  
        $this->LabTests= new LabTests;
        $this->LabTestsParams= new LabTestsParams;
        
    }
    public function index(){
        $user=Auth::user();
        $LabTestsData=$this->LabTests->with('getParams')->where('organization_id',$user->org_id)->where('is_active', 1)->orderBy('created_at', 'desc')->get();
        $LabTestsData=$LabTestsData->toArray();
        return view('adminpanel/tests',compact('LabTestsData','user'));
    }

    public function add(){
        $user=Auth::user();
        return view('adminpanel/add_tests', compact('user'));
    }

    public function saveFormData(Request $request){

        $validated =  $request->validate([
            'test_title' => 'required',
            'test_param' => 'required',
            'test_result' => 'required',
            'test_unit' => 'required',
            'test_value_range' => 'required',
            'test_comments' => 'required',
            ]);

        $user=Auth::user();

        $this->LabTests->test_name=$request['test_title'];
        $this->LabTests->description=$request['description'];
        $this->LabTests->test_slug=phpslug($request['test_slug']);
        $this->LabTests->description=$request['description'];
        $this->LabTests->organization_id=$user->org_id;
        $this->LabTests->user_id=$user->id;
        $this->LabTests->save();
        $LabTests_lastId = $this->LabTests->id;

        if($LabTests_lastId>0){
        $data=array();
        //echo 'count :'.count($request['test_param']);
            for($i=0; $i<count($request['test_param']); $i++){
            $data[$i]['parameter_name']=$request['test_param'][$i];
            $data[$i]['parameter_result']=$request['test_result'][$i];
            $data[$i]['parameter_unit']=$request['test_unit'][$i];
            $data[$i]['parameter_normal_range']=$request['test_value_range'][$i];
            $data[$i]['comments']=$request['test_comments'][$i];
            $data[$i]['lab_test_id']=$LabTests_lastId;
            
            }
            p($data);
            $request->session()->flash('alert-success', 'Successfully Added');
            $this->LabTestsParams->insert($data);
        }
        else{
            $request->session()->flash('alert-danger', 'There is some Error in the System, Please try again !');
        }
        
        return redirect('/admin/lab-tests/add');
    }

    public function editTestData($id){
        $user=Auth::user();
        $data=$this->LabTests->with('getParams')->where('id',$id)->orderBy('created_at', 'desc')->get();
        $data=$data->toArray();
        return view('adminpanel/edit_tests', compact('data','user'));   
    }
    


    public function UpdateTestData($id, Request $request){

        $validated =  $request->validate([
            'test_title' => 'required',
            'test_param' => 'required',
            'test_result' => 'required',
            'test_unit' => 'required',
            'test_value_range' => 'required',
            'test_comments' => 'required',
            ]);
        $user=Auth::user();
        $LabTestData['test_name']=$request['test_title'];
        $LabTestData['description']=$request['description'];
        $LabTestData['test_slug']=phpslug($request['test_slug']);

        $LabTestData['organization_id']=$user->org_id;
        $LabTestData['user_id']=$user->id;
        
        $this->LabTests
        ->where('id', $id)
        ->update($LabTestData);

        $data=array();
       
            for($i=0; $i<count($request['test_param']); $i++){
                if(isset($request['params_id'][$i]) && $request['params_id'][$i]>0){
                    $data['id']=$request['params_id'][$i];
                    $data['parameter_name']=$request['test_param'][$i];
                    $data['parameter_result']=$request['test_result'][$i];
                    $data['parameter_unit']=$request['test_unit'][$i];
                    $data['parameter_normal_range']=$request['test_value_range'][$i];
                    $data['comments']=$request['test_comments'][$i];
                    $data['lab_test_id']=$id;
                    $this->LabTestsParams
                    ->where('id', $request['params_id'][$i])
                    ->update($data);
                }
                else{
                   
                    $dataArr[$i]['parameter_name']=$request['test_param'][$i];
                    $dataArr[$i]['parameter_result']=$request['test_result'][$i];
                    $dataArr[$i]['parameter_unit']=$request['test_unit'][$i];
                    $dataArr[$i]['parameter_normal_range']=$request['test_value_range'][$i];
                    $dataArr[$i]['comments']=$request['test_comments'][$i];
                    $dataArr[$i]['lab_test_id']=$id;

                }
            }
          
            if(!empty($dataArr))
                $this->LabTestsParams->insert($dataArr);
            
            $request->session()->flash('alert-success', 'Record Successfully Update');
        
        return redirect('/admin/lab-tests/edit/'.$id);
    }

    public function deleteTestParam($id)
    {
        $user=Auth::user();
        $dataArray['error']='No';
        $dataArray['title']='Test Parameters';
        $result=$this->LabTestsParams->where('id','=',$id)->delete();             
        if($result)
        $dataArray['msg']='Mr.'.$user->name.', record delted successfully!';
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';

        }
        echo json_encode($dataArray);
        die;

    }
}