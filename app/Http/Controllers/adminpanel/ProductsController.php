<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\products;
use App\Models\adminpanel\product_categories;
use App\Models\adminpanel\FilesManage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    
    

    function __construct() {
        $this->files= new FilesManage;
        $this->products= new products;
        $this->pro_category= new product_categories;
      }
      // List All the products 
    public function products($type=NULL){
        $user=Auth::user();
        
            $productsData=$this->products->with('category')
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel/products',compact('productsData','user'));
    }
      // List All the Categories 
    public function categoreis(){
        $user=Auth::user();
        
            $categoriesData=$this->pro_category
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel/categories',compact('categoriesData','user'));
    }
      public function addproducts(){
        $user=Auth::user(); 
        
         return view('adminpanel/add_products',compact('user'));
     }
    public function add_documents($id){
        $user=Auth::user(); 
        $userData=$this->users->where('id',$id)->with('files')->with('city')->with('ZipCode')->with('getGroups')->get()->toArray();
       
         return view('adminpanel/uploadform',compact('user','userData'));
         return view('adminpanel/add_product_documents',compact('user','userData'));
     }
    public function upload_documents($id,Request $request){
        $user=Auth::user();
            $image = $request->file('file');
            $imageExt=$image->extension();
            $imageName = time().'.'.$imageExt;

     

            $image->move(public_path('uploads'),$imageName);
            $orginalImageName=$image->getClientOriginalName();
        
        //return response()->json(['success'=>$imageName]);

            $this->files->name=$orginalImageName;
            $this->files->slug=phpslug($imageName);
            $this->files->path=url('uploads').'/'.$imageName;
            $this->files->description=$orginalImageName.' file uploaded';
            $this->files->otherinfo=$imageExt;
            $this->files->user_id=$id;
            $this->files->save();
        //             ->update($data);
        // $this->files->where('id', $id)
        //             ->update($data);

                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' uploaded documents for product';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'product_documents_added',
                        'comments'=>$activityComment,
                        'others'=>'files',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return response()->json(['success'=>$imageName]);

        
     }
     public function add_new_product(Request $request){
       
        $validator=$request->validate([
            'name'=>'required',
            //'sizes'=>'required',
            'cat_id'=>'required',
        ]);
        
        
        $this->products->name=$request['name'];
        $this->products->slug=phpslug($request['name']);
        $this->products->sizes=($request['sizes']);
        $this->products->additional_notes=($request['additional_notes']);
        $this->products->is_active=1;
        $this->products->user_id=get_session_value('id');
       

       
       
        if(isset($request['other_category']) && !empty($request['other_category']))
        $cat_id = getOtherCategory($request['other_category']);
        else
        $cat_id=$request['cat_id'];
        $this->products->cat_id=$cat_id;

   
  
        $request->session()->flash('alert-success', 'product Added! Please Check in products list Tab');
        $this->products->save();
       
        // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new product '.$this->products->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->products->id,
                        'action_slug'=>'product_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    
    public function UpdateUsersData($id,Request $request)
    {
        $dataArray['error']='No';
        

        $validated =  $request->validate([
            'name' => 'required',
            'group_id' => 'required'
            ]);
            if(!$validated){

                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
                echo json_encode($dataArray);
                die;

            }
     
        $data['name']=$request['name'];
        $data['group_id']=$request['group_id'];

        $groupData=Groups::find($request['group_id']);
       
        $groupData=$groupData->toArray();
        // p($groupData);
        // die;
        $dataArray['name']=$data['name'];
        $dataArray['id']=$id;
        $dataArray['group_title']=$groupData['title'];
        $dataArray['group_role']=$groupData['role'];
        
        $dataArray['msg']='Mr.'.get_session_value('name').', '.$data['name'].' record Successfully Updated !';
        $this->users->where('id', $id)
                    ->update($data);

                    $activityComment='Mr.'.get_session_value('name').' updated User '.$data['name'].' Record';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'user_record_updated',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);
        echo json_encode($dataArray);
        die;

    }
    public function DeleteLeadssData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result){
            $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';

            $activityComment='Mr.'.get_session_value('name').' moved lead to approved/pending/cancelled';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'lead_status_changed',
                'comments'=>$activityComment,
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';

        }
        echo json_encode($dataArray);
        die;
    }
    public function ajaxcall($id, Request $req){
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
        
        if(!isset($req['action'])){
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please try again later!.';
            echo json_encode($dataArray);
            die;
        }
        else if(isset($req['action']) && $req['action']=='deleteProduct')
        {
            $dataArray['title']='Record Deleted';
            $result=$this->products->where('id','=',$id)->update(array('is_active'=>0));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'product_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted product',
                'others'=>'products',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action']=='delete_category')
        {
            $dataArray['title']='Record Deleted';
            $result=$this->pro_category->where('id','=',$id)->update(array('is_active'=>0));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'category_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted category',
                'others'=>'categories',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action'] =='save_category_add_form'){
            $dataArray['error']='No';
            $dataArray['msg']='Category Successfully Added';
            $dataArray['title']='Category Panel';
            $dataArray['actionType']='categories_added';
            $dataArray['name']=$req['name'];


            $this->pro_category->name=$req['name'];
            $this->pro_category->slug=phpslug($req['name']);
            $this->pro_category->user_id=get_session_value('id');

            $this->pro_category->save();
             // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$this->pro_category->id,
                'action_slug'=>'category_update',
                'comments'=>'Mr.'.get_session_value('name').' added a category '.$req['name'],
                'others'=>'categories',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;
        }
        else if(isset($req['action']) && $req['action'] =='save_category_edit_form'){
            $dataArray['error']='No';
            $dataArray['msg']='Category Successfully Updated';
            $dataArray['title']='Category Panel';
            $dataArray['actionType']='categories_updated';

            $dataArray['name']=$req['name'];
            $dataArray['id']=$req['cat_id'];

            $this->pro_category->where('id', $req['cat_id'])->update(
                array(
                    'name'=>$req['name'],
                    'slug'=>phpslug($req['name']),
                    'user_id'=>get_session_value('id'),
                    )
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['cat_id'],
                'action_slug'=>'category_update',
                'comments'=>'Mr.'.get_session_value('name').' updated a category '.$req['name'],
                'others'=>'product',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtoproductForm'){
            $dataArray['error']='No';
            $dataArray['msg']='product Successfully Updated';
            $dataArray['title']='Product Panel';
            $dataArray['actionType']='product_update';
            //$dataArray['formdata']=$req->all();

            $dataArray['name']=$req['name'];
            $dataArray['id']=$req['product_id'];
            $dataArray['sizes']=$req['sizes'];
            $dataArray['additional_notes']=$req['additional_notes'];
            $dataArray['catname']=$req['catname'];
            
            if(isset($req['other_category']) && !empty($req['other_category']))
            $cat_id = getOtherCategory($req['other_category']);
            else
            $cat_id=$req['cat_id'];
            $this->products->cat_id=$cat_id;


            $this->products->where('id', $req['product_id'])->update(
                array(
                    'name'=>$req['name'],
                    'sizes'=>$req['sizes'],
                    'additional_notes'=>$req['additional_notes'],
                    'cat_id'=>$cat_id,
                    
                    )
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['product_id'],
                'action_slug'=>'product_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a product '.$req['name'],
                'others'=>'product',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;

        }
       
        else if(isset($req['action']) && $req['action'] =='editproductForm'){
            $dataArray['error']='No';
           
           
            $data=$this->products->with('category')
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            //p($data); die;
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditproductForm"
                                                                            method="GET" action="" onsubmit="return updateproduct('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtoproductForm" />
                                                                            <input type="hidden" name="product_id" value="'.$data['id'].'" />
                                                                            <input type="hidden" id="catname" name="catname" value="'.$data['category']['name'].'" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control"
                                                                                            placeholder=" Name"
                                                                                            value="'. $data['name'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <textarea type="text" name="sizes" class="form-control"
                                                                                            placeholder="Prodcut Sizes (e.g Size 1, Size 2, Size 3)"
                                                                                            >'. $data['sizes'].'</textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <textarea type="text" name="additional_notes" class="form-control"
                                                                                            placeholder=" Any additional Notes"
                                                                                            >'. $data['additional_notes'].'</textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                           
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select id="cat_id" onChange="changeProCategory()" name="cat_id" class="form-control select2bs4" placeholder="Select Category">'.get_product_cat_Options($data['cat_id']).'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div id="other_cat"></div>
                                                                            
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
        }
        else if(isset($req['action']) && $req['action'] =='editCategoryForm'){
            $dataArray['error']='No';
           
           
            $data=$this->pro_category
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            //p($data); die;
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="edit_categories_form"
                                                                            method="GET" action="" onsubmit="return update_category_form_data('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="save_category_edit_form" />
                                                                            <input type="hidden" name="cat_id" value="'.$data['id'].'" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control"
                                                                                            placeholder=" Name"
                                                                                            value="'. $data['name'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
        }
       
      
        echo json_encode($dataArray);
        die;
    }
    public function categoryajaxcall(){
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
            

            $dataArray['error']='No';
           
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="add_categories_form"
                                                                            method="GET" action="" onsubmit="return add_category_form_data()">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="save_category_add_form" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control"
                                                                                            placeholder=" Name"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
       
        echo json_encode($dataArray);
        die;
    }
 
}
