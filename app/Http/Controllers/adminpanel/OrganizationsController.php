<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Organizations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class OrganizationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct() {
        
        $this->org= new Organizations;
        
    }
    public function index()
    {
        //
      
        $organizationsData = $this->org->latest()->paginate(200)->where('is_active', 1);
        //$organizationsData = $this->org->latest()->all();
        //$organizationsData=$organizationsData->toArray();
        //p($organizationsData);
        return view('adminpanel.organizations',compact('organizationsData'));
    
            //->with('i', (request()->input('page', 1) - 1) * 5);
    }
    public function add()
    {
         $user=Auth::user();  
         
        if (!Gate::allows('isAdmin')) 
            abort(403,'Only Admins are allowed to add new Organizations');
          
        return view('adminpanel.add_organizations',compact('user'));
    
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
     * @param  \App\Http\Requests\StoreOrganizationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function SaveOrgData(Request $request)
    {
        $validated =  $request->validate([
            'name' => 'required',
            'tehsil' => 'required',
            'district' => 'required',
            'state' => 'required',
            'address' => 'required',
            'hod_name' => 'required',
            'hod_designation' => 'required',
            'description' => 'required',
            'lab_name' => 'required',
            'lab_hod_name' => 'required',
            'lab_hod_desination' => 'required',
            'lab_hod_cnic' => 'required',
            'lab_hod_email' => 'required|unique:organizations|max:255',
            'lab_hod_phone' => 'required'
            ]);

        $this->org->name=$request['name'];
        $this->org->org_slug=phpslug($request['name']);
        $this->org->district=$request['district'];
        $this->org->tehsil=$request['tehsil'];
        $this->org->state=phpslug($request['state']);
        $this->org->lang=$request['lang'];
        $this->org->lat=$request['lat'];
        $this->org->address=$request['address'];
        $this->org->hod_name=$request['hod_name'];
        $this->org->hod_designation=$request['hod_designation'];
        $this->org->description=$request['description'];
        $this->org->lab_name=$request['lab_name'];
        $this->org->lab_hod_name=$request['lab_hod_name'];
        $this->org->lab_hod_desination=$request['lab_hod_desination'];
        $this->org->lab_hod_cnic=$request['lab_hod_cnic'];
        $this->org->lab_hod_email=$request['lab_hod_email'];
        $this->org->lab_hod_phone=$request['lab_hod_phone'];
        //$this->org->user_id=get_session_value('id');
        $this->org->is_active=1;
        $this->org->created_at=time();
     
        // p($this->org);
        // die;

            $request->session()->flash('alert-success', 'Successfully Added');
          $this->org->save();

            return redirect('/admin/organizations');

    }
    public function updateOrgData($id,Request $request)
    {
        $dataArray['error']='No';
        

        $validated =  $request->validate([
            'name' => 'required',
            'tehsil' => 'required',
            'district' => 'required',
            'state' => 'required',
            'address' => 'required',
            'hod_name' => 'required',
            'hod_designation' => 'required',
            'description' => 'required',
            'lab_name' => 'required',
            'lab_hod_name' => 'required',
            'lab_hod_desination' => 'required',
            'lab_hod_cnic' => 'required',
            'lab_hod_phone' => 'required'
            ]);
            if(!$validated){

                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
                echo json_encode($dataArray);
                die;

            }
            
       
     
        $data['name']=$request['name'];
        $data['org_slug']=phpslug($request['name']);
        $data['district']=$request['district'];
        $data['tehsil']=$request['tehsil'];
        $data['state']=phpslug($request['state']);
        $data['lang']=$request['lang'];
        $data['lat']=$request['lat'];
        $data['address']=$request['address'];
        $data['hod_name']=$request['hod_name'];
        $data['hod_designation']=$request['hod_designation'];
        $data['description']=$request['description'];
        $data['lab_name']=$request['lab_name'];
        $data['lab_hod_name']=$request['lab_hod_name'];
        $data['lab_hod_desination']=$request['lab_hod_desination'];
        $data['lab_hod_cnic']=$request['lab_hod_cnic'];
        $data['lab_hod_phone']=$request['lab_hod_phone'];

        $dataArray['name']=$data['name'];
        $dataArray['id']=$id;
        $dataArray['hod_name']=$data['hod_name'].' ('.$data['hod_designation'].')';
        $dataArray['lab_name']=$data['lab_name'];
        $dataArray['lab_hod_name']=$data['lab_hod_name'].' ('.$data['lab_hod_desination'].')';
        $dataArray['lab_hod_phone']=$data['lab_hod_phone'];
        
        $dataArray['msg']='Mr.'.get_session_value('name').', '.$data['name'].' record Successfully Updated !';
        $this->org->where('is_active', 1)
                    ->where('id', $id)
                    ->update($data);
        echo json_encode($dataArray);
        die;
        
          

          

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\adminpanel\Organizations  $organizations
     * @return \Illuminate\Http\Response
     */
    public function show(Organizations $organizations)
    {
      
          $user=Auth::user();  
          $filter['is_active']=1;
    
       if($user->group_id !=config('constants.groups.admin'))
       $filter['id']=$user->org_id;
       
        $organizationsData = $this->org->where($filter)->get();
        
        return view('adminpanel.organizations',compact('organizationsData','user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\adminpanel\Organizations  $organizations
     * @return \Illuminate\Http\Response
     */
    public function edit(Organizations $organizations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrganizationsRequest  $request
     * @param  \App\Models\adminpanel\Organizations  $organizations
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organizations $organizations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\adminpanel\Organizations  $organizations
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organizations $organizations)
    {
        //
    }
}
