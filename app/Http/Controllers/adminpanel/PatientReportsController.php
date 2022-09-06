<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use App\Models\adminpanel\PatientReports;
use Illuminate\Http\Request;
use App\Models\adminpanel\Patients;
use App\Models\adminpanel\Patient_Tests;
use App\Models\adminpanel\LabTests;
use App\Models\adminpanel\LabTestsParams;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
//use Barryvdh\DomPDF\Facade\Pdf;
use PDF;

class PatientReportsController extends Controller
{

      //
    function __construct() {
  
        $this->Patients= new Patients;
        $this->Patient_Tests= new Patient_Tests;
        $this->PatientReports= new PatientReports;
        
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user=Auth::user();
        
     
        $TestsDatawithPatient=$this->Patient_Tests->where('org_id',$user->org_id)->with('patient')->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        
        //$TestsDatawithPatient=$this->Patient_Tests->where('org_id',$user->org_id)->with('patient')->orderBy('created_at', 'desc')->get();
         //if($TestsDatawithPatient)
         //$TestsDatawithPatient=$TestsDatawithPatient->toArray();
        //p($TestsDatawithPatient); die;
        // if($user->group_id==config('constants.groups.admin')) // If the User is Admin
        // $PatientWithAdvisedTests=$this->Patients->with('getAdvisedTests')->where('is_active', 1)->orderBy('created_at', 'desc')->get();
        // else if($user->group_id==config('constants.groups.hod')) // If the User is HOD of the Department
        // $PatientWithAdvisedTests=$this->Patients->with('getAdvisedTests')->where('org_id',$user->org_id)->where('is_active', 1)->orderBy('created_at', 'desc')->get();
        // else if($user->group_id==config('constants.groups.staff')) // If User is Staff
        // $PatientWithAdvisedTests=$this->Patients->with('getAdvisedTests')->where('org_id',$user->org_id)->where('user_id',$user->id)->where('is_active', 1)->orderBy('created_at', 'desc')->get();

        //$PatientWithAdvisedTests=$PatientWithAdvisedTests->toArray();
        
       //p($PatientWithAdvisedTests); die;
        return view('adminpanel/advisedTestsPatients',compact('TestsDatawithPatient','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user=Auth::user(); 
        $allTestsData=getAllTestsByOrg($user->org_id);
        //p($allTestsData);
        
        return view('adminpanel/addPatientReport',compact('user','allTestsData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveReport(Request $request)
    {
        $user=Auth::user();
        
        $request->validate([
            'name'=>'required',
            'gaudian_name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'advised_tests'=>'required',
        ]);
      
    //   p($request->all());  
    //   die;
        $patientData=$this->Patients->where('phone',$request['phone'])->orWhere('cnic',$request->cnic)->first();
        
       
        $this->Patients->name=$data['name']=$request['name'];
        $this->Patients->gaudian_name=$data['gaudian_name']=$request['gaudian_name'];
        $this->Patients->cnic=$data['cnic']=$request['cnic'];
        $this->Patients->phone=$data['phone']=$request['phone'];
        $this->Patients->address=$data['address']=$request['address'];
        $this->Patients->gender=$data['gender']=$request['gender'];
        $this->Patients->group_id=$data['group_id']=$user->group_id;
        $this->Patients->user_id=$data['user_id']=$user->id;
        $this->Patients->org_id=$data['org_id']=$user->org_id;
        
        //$patient_id=$patientData->id;
       if(empty($patientData)){
        $this->Patients->created_at=time();
        $this->Patients->save();
        $patient_id=$this->Patients->id;
        
       }
       else{
        $this->Patients->where('id', $patientData->id)->update($data);
        $patient_id=$patientData->id;
        
       }
         
        
        $this->Patient_Tests->opdno=time().'/'.$request['opdno'];
        $this->Patient_Tests->prescription_date=(!empty($request['prescription_date'])?time() : strtotime($request['prescription_date']));
        $this->Patient_Tests->prescription_srno=$request['prescription_srno'];
        $this->Patient_Tests->patient_type=$request['patient_type'];
        $this->Patient_Tests->advised_tests=json_encode($request['advised_tests']);
        $this->Patient_Tests->advised_by=$request['advised_by'];
        $this->Patient_Tests->status=1;
        $this->Patient_Tests->user_id=$user->id;
        $this->Patient_Tests->org_id=$user->org_id;
        
        $this->Patient_Tests->patient_id =$patient_id;
        $this->Patient_Tests->created_at=time();

       // p($this->Patient_Tests);
        $this->Patient_Tests->save();
       $patient_test_id=$this->Patient_Tests->id;

        if(isset($request['lab_test_param_result']) && !empty($request['lab_test_param_result'])){
            $data=array();
            $i=0;
            
            foreach($request['lab_test_param_result'] as $key=>$value){
                $PatientReportsObj= new PatientReports;    
                // $PatientReportsObj->lab_test_param_result=$data[$i][]=$value[0];
                // $PatientReportsObj->lab_tests_params_id=$data[$i][]=$key;
                // $PatientReportsObj->patient_test_id=$data[$i][]=$patient_test_id;
                // $PatientReportsObj->patient_id=$data[$i][]=$patient_id;
                $PatientReportsObj->lab_test_id=$data[$i][]=$value[0];
                $PatientReportsObj->lab_test_title=$data[$i][]=$value[1];
                $PatientReportsObj->parameter_name=$data[$i][]=$value[2];
                $PatientReportsObj->parameter_result=$data[$i][]=$value[3];
                $PatientReportsObj->parameter_unit=$data[$i][]=$value[4];
                $PatientReportsObj->parameter_normal_range=$data[$i][]=$value[5];
                $PatientReportsObj->comments=$data[$i][]=$value[6];
                //$PatientReportsObj->lab_tests_params_id=$data[$i][]=$key;
                $PatientReportsObj->patient_test_id=$data[$i][]=$patient_test_id;
                $PatientReportsObj->patient_id=$data[$i][]=$patient_id;

                $i++;
               $PatientReportsObj->save();
               
            }
            //p($data);
        }
        
        
        
        $request->session()->flash('alert-success', $request['name'].'\'s Report added Successfully!');
        //$this->users->save();

         return redirect()->back();
    }
    public function store(Request $request)
    {
        
        $requested_data = $request->all();
        print_r($requested_data);
       die;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\adminpanel\PatientReports  $patientReports
     * @return \Illuminate\Http\Response
     */
    public function show($id,PatientReports $patientReports)
    { 
        // $id is the Patient Test ID from the table patient_tests
        $user=Auth::user(); 
        $advisedTestDataWithPatient=$this->Patient_Tests->where('id',$id)->with('patient')->get();
        
        ($advisedTestDataWithPatient=$advisedTestDataWithPatient->toArray());
       // p($advisedTestDataWithPatient);
        
        $advisedTests=json_decode($advisedTestDataWithPatient[0]['advised_tests'],true); 

        $LabTestsDataWithParams=LabTests::where('organization_id',$user->org_id)->with('getParams')->get();
        $LabTestsDataWithParams=$LabTestsDataWithParams->toArray();
        //p($LabTestsDataWithParams);
        // die;
        // if(empty($LabTestsDataWithParams))
        // $allTestsData=getAllTestsByOrg($user->org_id);
        // //p($allTestsData);
        // die;
        
        return view('adminpanel/editPatientReport',compact('user','advisedTestDataWithPatient','LabTestsDataWithParams'));
    }

    public function viewReport($id,Request $request)
    { 
        // $id is the Patient Test ID from the table patient_tests
        $user=Auth::user(); 

        $advisedTestDataWithPatient=$this->Patient_Tests->where('id',$id)->with('patient')->get();
        
        ($advisedTestDataWithPatient=$advisedTestDataWithPatient->toArray());
        
        $advisedTests=json_decode($advisedTestDataWithPatient[0]['advised_tests'],true); 
       
        $LabTestsDataWithParams=LabTests::where('organization_id',$user->org_id)->with('getParams')->get();
        $LabTestsDataWithParams=$LabTestsDataWithParams->toArray();
       
        if( $request->has('exportpdf') ) {
            $pdata=$advisedTestDataWithPatient[0];
            
            $fileName=$pdata['patient'][0]['name'].'-'.date('d/m/Y h:i:s',time());
            
            //return view('adminpanel/PDFviewPatientReport',compact('id','user','advisedTestDataWithPatient','LabTestsDataWithParams'));
                  // share data to view
                view()->share('adminpanel/PDFviewPatientReport',compact('id','user','advisedTestDataWithPatient','LabTestsDataWithParams'));
                $pdf = PDF::loadView('adminpanel/PDFviewPatientReport', compact('id','user','advisedTestDataWithPatient','LabTestsDataWithParams'));
                // download PDF file with download method
                 return $pdf->download($fileName.'.pdf');
        }
        
        
        return view('adminpanel/viewPatientReport',compact('id','user','advisedTestDataWithPatient','LabTestsDataWithParams'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\adminpanel\PatientReports  $patientReports
     * @return \Illuminate\Http\Response
     */
    public function edit($id,PatientReports $patientReports)
    {
       
        $user=Auth::user(); 
        $advisedTestDataWithPatient=$this->Patient_Tests->where('id',$id)->with('patient')->get();
        //echo '<h2>Patietn Test Details</h2>';
        ($advisedTestDataWithPatient=$advisedTestDataWithPatient->toArray());
        
        $advisedTests=json_decode($advisedTestDataWithPatient[0]['advised_tests'],true);
         
        // echo '<h2>Lab Test Tab ID Advised Tests</h2>';
        //  p($advisedTests);

        // echo '<h2>Lab Tests With Parametes</h2>';

        //$LabTestsDataWithParams=LabTests::whereIn('id',$advisedTests)->with('getParams')->get();
        $LabTestsDataWithParams=LabTests::where('organization_id',$user->org_id)->with('getParams')->get();
        $LabTestsDataWithParams=$LabTestsDataWithParams->toArray();
        //p($LabTestsDataWithParams);

        // echo '<h2>Lab Tests With Parameters with Results</h2>';
        // $ParamswithResult=LabTestsParams::whereIn('lab_test_id',$advisedTests)->with('ParamResult')->with('LabTest')->get();
        // $ParamswithResult=$ParamswithResult->toArray();
        // p($ParamswithResult);
        // die;
        
        
        // $LabTestsParams=new LabTestsParams();
        // $LabTestsParamsData=$LabTestsParams->whereIn('id',$advisedTests)->get();
        // echo '<h2>Paramenters of tests '.$TestData[0]['advised_tests'].'</h2>';
        // p($LabTestsParamsData->toArray());

        
        //$report=$this->PatientReports->where('patient_test_id',$id)->get();
        //echo '<h2>Results for Reports  '.$advisedTestDataWithPatient[0]['advised_tests'].'</h2>';
        //($resultReport=$report->toArray());

        
        $allTestsData=getAllTestsByOrg($user->org_id);
        //p($allTestsData);
        
        return view('adminpanel/editPatientReport',compact('user','advisedTestDataWithPatient','LabTestsDataWithParams','allTestsData'));
    }

    /**
     * Update the specified resource in storage. helpdesk@strongfiber.com
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\adminpanel\PatientReports  $patientReports
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PatientReports $patientReports)
    {
        
        $user=Auth::user();
        $PatientReportsObj= new PatientReports; 
        
        $request->validate([
            'name'=>'required',
            'gaudian_name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'patient_test_id'=>'required|integer',
            'advised_tests'=>'required',
        ]);
    
    //   echo '<h2>Request array</h2><br>';
    //    p($request->all());  
    //     die;
        $patientData=$this->Patients->where('phone',$request['phone'])->orWhere('cnic',$request->cnic)->orWhere('id',$request->patient_id)->first();
        
       
        $this->Patients->name=$data['name']=$request['name'];
        $this->Patients->gaudian_name=$data['gaudian_name']=$request['gaudian_name'];
        $this->Patients->cnic=$data['cnic']=$request['cnic'];
        $this->Patients->phone=$data['phone']=$request['phone'];
        $this->Patients->address=$data['address']=$request['address'];
        $this->Patients->gender=$data['gender']=$request['gender'];
        $this->Patients->group_id=$data['group_id']=$user->group_id;
        $this->Patients->user_id=$data['user_id']=$user->id;
        $this->Patients->org_id=$data['org_id']=$user->org_id;
        
        //$patient_id=$patientData->id;
       if(empty($patientData)){
        $this->Patients->created_at=time();
        $this->Patients->save();
        $patient_id=$this->Patients->id;
        
       }
       else{
        $this->Patients->where('id', $patientData->id)->update($data);
        //echo '<h2>Patient Data Array</h2><br>';
        //p($data);
        $patient_id=$patientData->id;
        
       }
        //p($request['advised_tests']); die;
        if(!isset($request['advised_tests'])){ // Delete All Previous Test if No Test Is Advised
            $PatientReportsObj->where('patient_test_id',$request->patient_test_id)->delete();
            $this->Patient_Tests->where('id',$request->patient_test_id)->delete();

            return redirect('/admin/patient-reports');
        
        }
        else{

        //$this->Patient_Tests->opdno=time().'/'.$request['opdno'];
        $this->Patient_Tests->prescription_date=$testData['prescription_date']=(!empty($request['prescription_date'])?time() : strtotime($request['prescription_date']));
        $this->Patient_Tests->prescription_srno=$testData['prescription_srno']=$request['prescription_srno'];
        $this->Patient_Tests->patient_type=$testData['patient_type']=$request['patient_type'];
        
        $this->Patient_Tests->advised_tests=$testData['advised_tests']=json_encode($request['advised_tests']);
        
        $this->Patient_Tests->advised_by=$testData['advised_by']=$request['advised_by'];
        $this->Patient_Tests->user_id=$testData['user_id']=$user->id;
        $this->Patient_Tests->org_id=$testData['org_id']=$user->org_id;
        
        $this->Patient_Tests->patient_id =$testData['patient_id']=$patient_id;
        

        $this->Patient_Tests->where('id', $request->patient_test_id)->update($testData);
       $patient_test_id=$request->patient_test_id;
        // If Result Parameters are not Empty then 
        //p($request['lab_test_param_result']);
        //                                                         die;

        if(isset($request['lab_test_param_result']) && !empty($request['lab_test_param_result'])){

            $reportResultData=$data=array();
            $i=0;
            foreach($request['lab_test_param_result'] as $key=>$value){
                $PatientReportsObj= new PatientReports;    
                $PatientReportsObj->parameter_result=$data[$i][]=$reportResultData['parameter_result']=$value[2];
                
                
                $patientReportResultData=$PatientReportsObj->where('id',$key)->first();
                //echo 'key:'.$key;
                //p($patientReportResultData);
               //die;
                if(empty($patientReportResultData)){
               
                $PatientReportsObj->lab_test_id=$data[$i][]=$value[0];
                $PatientReportsObj->lab_test_title=$data[$i][]=$value[1];
                $PatientReportsObj->parameter_name=$data[$i][]=$value[2];
                $PatientReportsObj->parameter_result=$data[$i][]=$value[3];
                $PatientReportsObj->parameter_unit=$data[$i][]=$value[4];
                $PatientReportsObj->parameter_normal_range=$data[$i][]=$value[5];
                $PatientReportsObj->comments=$data[$i][]=$value[6];
                $PatientReportsObj->patient_test_id=$data[$i][]=$patient_test_id;
                $PatientReportsObj->patient_id=$data[$i][]=$patient_id;
                
                    $PatientReportsObj->save();
                    
                }
                else{
                    
                    $PatientReportsObj->where('id',$key)->update($reportResultData);
                }
                $i++;
               
               
            }
            // echo 'patient_id :'.$patient_id;
            // echo '<br>patient_test_id: '.$patient_test_id;
            // echo '<br>';
            // p($request['advised_tests']);
            // echo '<br>';
            //delete from `patient_reports` where `patient_id` = 1 and `patient_test_id` = 2 and `lab_test_id` not in (2, 3)
            //DB::enableQueryLog();
            $sql=$PatientReportsObj->where('patient_id',$patient_id)->where('patient_test_id',$patient_test_id)->whereNotIn('lab_test_id', $request['advised_tests'])->delete();
            //dd(DB::getQueryLog());
        } // End Result Params IF Condition
    } 
        
        $request->session()->flash('alert-success', $request['name'].'\'s Report updated Successfully!');
        

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\adminpanel\PatientReports  $patientReports
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,PatientReports $patientReports)
    {
        $data['error']='yes';
        $data['msg']='There is some error !';
        if($id>0){
            $data['error']='No';
            $PatientReportsObj= new PatientReports; 
            $PatientReportsObj->where('patient_test_id',$id)->delete();
            $this->Patient_Tests->where('id',$id)->delete();
            $data['msg']='Report Record Deleted !';
            echo json_encode($data); die;
        }
        echo json_encode($data);
        
    }
    public function showReportJson($id,PatientReports $patientReports)
    {
        $data['error']='yes';
        $data['msg']='There is some error !';
        if($id>0){
            $data['error']='No';
            //$patientReports;
            $TestsDatawithPatient=$this->Patient_Tests->where('id',$id)->with('patient')->orderBy('created_at', 'desc')->get();
            if($TestsDatawithPatient)
            $TestsDatawithPatient=$TestsDatawithPatient->toArray();
            $TestsDatawithPatient=$TestsDatawithPatient[0];
            $patient=$TestsDatawithPatient['patient'][0];
            //p($TestsDatawithPatient);
            $htmlElement ='<div class="row border rounded" style="padding:20px 0">
            <div class="col-3"><strong>
            Patient\'s Name :
            <br> Date & Time :
            <br> Referred By :
            </strong></div>
            <div class="col-5">
            '.$patient['name'].' S/W/H '.$patient['gaudian_name'].'
            <br>
            '.date('d/m/Y - h:i:m',strtotime($TestsDatawithPatient['created_at'])).'
            <br>
            '.$TestsDatawithPatient['advised_by'].'
            </div>
            
            <div class="col-2"><strong>
            Gender :
            <br>
            Patient Type :
            </strong></div>
            <div class="col-2">
            '.$TestsDatawithPatient['patient_type'].'
            <br>
            '.(($patient['gender']=='m')?'Male':'Female').'
            
            </div>
        </div>';
        $advisedTests=json_decode($TestsDatawithPatient['advised_tests'],true);
        $advisedTests=(is_array($advisedTests))?(array_reverse($advisedTests)):array();

        $testNameArr='';
        
                $htmlElement .= '<div class="row border-bottom form-group" style="font-size:16px; padding:22px 0 0 0;">';
                $htmlElement .= '<div class="col-3"><strong>Parameter Name</strong></div>';
                $htmlElement .= '<div class="col-2"><strong>Result</strong></div>';
                $htmlElement .= '<div class="col-2"><strong>Unit</strong></div>';
                $htmlElement .= '<div class="col-2"><strong>Normal Value</strong></div>';
                $htmlElement .= '<div class="col-3"><strong>Comments</strong></div>';
                $htmlElement .= '</div>';

        foreach($advisedTests as $key=>$val){
            
            $patientReportData=getReportByIDs($val,$TestsDatawithPatient['id']);
          
            
            foreach($patientReportData as $patientReport){
              
            if($patientReport['lab_test_title']!=$testNameArr)
            {
                    $testNameArr=$patientReport['lab_test_title'];
                    $htmlElement .= '<div class="row form-group"><div class="col-1">&nbsp;</div><div class="col-10 card-title"><h3 style="text-decoration:underline; font-weight:bold;font-size:16px;padding:6px;" class="text-center">' . $patientReport['lab_test_title'] . '</h3></div></div>';
            }
            
            
              
                $htmlElement .= '<div class="border row form-group">';
                $htmlElement .= '<div class="col-3">'.$patientReport['parameter_name'] .'</div>';
                $htmlElement .= '<div class="col-2">'.$patientReport['parameter_result'].'</div>';
                $htmlElement .= '<div class="col-2">' . $patientReport['parameter_unit'] . '</div>';
                $htmlElement .= '<div class="col-2">'. $patientReport['parameter_normal_range'] . '</div>';
                $htmlElement .= '<div class="col-3">'. $patientReport['comments'] .'</div>';
                $htmlElement .= '</div>';
            }
            
        }
       
        //echo $html;
            $data['msg']=$htmlElement;//'Report Fetched Successfully'.$id;
            echo json_encode($data); die;
        }
        echo json_encode($data);
        
    }


    public function patient_name_suggestions(Request $request){
        $patientData=$this->Patients->where('name','like', $request->pname.'%')->get();
        

       // p($patientData);
        //die;
    //    $data['error']='no';
    //    $data['msg']='Successfull';
    //    $data['res']=$request->pname;
       echo json_encode($patientData);
    }
}
