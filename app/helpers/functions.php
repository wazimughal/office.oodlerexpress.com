<?php

if(!function_exists('p')){
    function p($data){ 
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
if(!function_exists('get_formatted_date')){
    function get_formatted_date($data,$formate){
        $formattedDate= date($formate, strtotime($date));
        return $formattedDate; 
    }
}

if(!function_exists('get_session_value')){
    function get_session_value($key=NULL){

        $userData=session()->get('userData'); 
        if($key==NULL)
        return $userData;
        return $userData[$key];
    }
}

if(!function_exists('phpslug')){
    function phpslug($string)
    {
        $slug = preg_replace('/[-\s]+/', '_', strtolower(trim($string)));
        return $slug;
    }
}
if(!function_exists('getParamsByTestID')){
    function getParamsByTestID($id){
        $testParams = App\Models\adminpanel\LabTestsParams::where('lab_test_id',$id)->orderBy('created_at', 'desc')->get();
        if($testParams)
        return $testParams->toArray();
        
        return array();
    }
}
if(!function_exists('getGroups')){
    function getGroups(){
        $userGroups = App\Models\adminpanel\Groups::orderBy('created_at', 'desc')->where('id','!=',config('constants.groups.admin'))->get();
        if($userGroups)
        return $userGroups->toArray();
        
        return array();
    }
}
if(!function_exists('getUsersByGroupId')){
    function getUsersByGroupId($group_id){
        $userData = App\Models\adminpanel\Users::orderBy('created_at', 'desc')->where('group_id','=',$group_id)->get();
        if($userData){
            $userData=$userData->toArray();
            return $userData;
        }
        return array();
    }
}
if(!function_exists('getTypesOfLeads')){
    function getTypesOfLeads(){
        $leads=config('constants.lead_types');
        return $leads;
    }
}
if(!function_exists('getLeadByType')){
    function getLeadByType($lead_type, $status=0){
        $userData = App\Models\adminpanel\Users::orderBy('created_at', 'desc')->where('lead_type','=',$lead_type)->where('status','=',$status)->get();
        if($userData){
            $userData=$userData->toArray();
            return $userData;
        }
        return array();
    }
}
if(!function_exists('getAllGroups')){
    function getAllGroups(){
        $userGroups = App\Models\adminpanel\Groups::orderBy('created_at', 'desc')->get();
        if($userGroups)
        return $userGroups->toArray();
        
        return array();
    }
}

if(!function_exists('getVenueGrpupById')){
    function getVenueGrpupById($id){
        $Organizations = App\Models\adminpanel\Venue_groups::orderBy('created_at', 'desc')->with('ownerinfo')->where('id',$id)->get();
        if($Organizations)
        return $Organizations->toArray();
        
        return array();
    }
}
// Add City in cities table if already city not exist 
if(!function_exists('getOtherCity')){
    function getOtherCity($name){
        $nameSlug=phpslug($name);
        $cityData = App\Models\adminpanel\cities::where('slug',$nameSlug)->get();
        $cityData=$cityData->toArray();
        if(!empty($cityData)){
            return $cityData[0]['id'];
        }
        $cityId = DB::table('cities')->insertGetId(array('name'=>strtolower($name),'slug'=>phpslug($name),'is_active'=>1),'id');
        return $cityId;
        
    }
}
// Add zipcode in zipcode table if already zipcode not exist 
if(!function_exists('getOtherZipCode')){
    function getOtherZipCode($code){
        
        $zipcodeData = App\Models\adminpanel\zipcode::where('code',phpslug($code))->get();
        $zipcodeData=$zipcodeData->toArray();
        if(!empty($zipcodeData)){
            return $zipcodeData[0]['id'];
        }
        $zipcodeId = DB::table('zipcode')->insertGetId(array('code'=>strtolower(phpslug($code)),'is_active'=>1),'id');
        return $zipcodeId;
        
    }
}
if(!function_exists('log_activity')){
    function log_activity($data){
        
        $activityID = DB::table('activities_log')->insertGetId($data,'id');
        return $activityID;
        
    }
}
if(!function_exists('getAllTestsByOrg')){
    function getAllTestsByOrg($org_id){
        $allTestData = App\Models\adminpanel\LabTests::with('getParams')->where('organization_id',$org_id)->orderBy('created_at', 'desc')->get();
        if($allTestData)
        return $allTestData->toArray();
        
        return array();
    }
}
if(!function_exists('getAdvisedTestsNames')){
    function getAdvisedTestsNames($ids){
        //return $ids;
        $allTestData = App\Models\adminpanel\LabTests::whereIn('id',$ids)->orderBy('created_at', 'desc')->get('test_name');
        if($allTestData)
        return $allTestData->toArray();
        
        return array();
    }
}

// Get Test Report by Test ID
// if(!function_exists('getReportByTestId')){
//     function getReportByTestId($id){
//         //return array($id);
//         $testReport = App\Models\adminpanel\LabTestsParams::where('lab_test_id',$id)->with('ParamResult')->with('LabTest')->get();
//         if($testReport)
//         return $testReport->toArray();
        
//         return array();
//     }
// }
// Get Options of States
if(!function_exists('getStatesOptions')){
    function getStatesOptions($selectID=NULL){
    
        $statesData = App\Models\adminpanel\states::where('is_active',1)->orderBy('id', 'desc')->get();
        if($statesData)
         $statesData=$statesData->toArray();
         $options='';
         
        foreach($statesData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Get Options of Cities
if(!function_exists('getCitiesOptions')){
    function getCitiesOptions($selectID=NULL){
    
        $cityData = App\Models\adminpanel\cities::where('is_active',1)->orderBy('id', 'asc')->get();
        if($cityData)
         $cityData=$cityData->toArray();
         $options='';
         
        foreach($cityData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Get Options of Cities
if(!function_exists('getZipCodeOptions')){
    function getZipCodeOptions($selectID=NULL){
    
        $zipcodeData = App\Models\adminpanel\zipcode::where('is_active',1)->orderBy('id', 'asc')->get();
        if($zipcodeData)
         $zipcodeData=$zipcodeData->toArray();
         $options='';
         
        foreach($zipcodeData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['code'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
if(!function_exists('getReportByIDs')){
    function getReportByIDs($advisedTestID, $patientTestID){
        //return array($id);
        $testReport = App\Models\adminpanel\PatientReports::with('LabTest')->where('lab_test_id',$advisedTestID)->where('patient_test_id',$patientTestID)->orderBy('id', 'desc')->get();
        if($testReport)
        return $testReport->toArray();
        
        return array();
    }
}

?>