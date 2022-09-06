@extends('adminpanel.admintemplate')
@push('title')
  <title>Add Test | {{config('constants.app_name')}}</title>
@endpush
@section('main-section')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add New Test</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add New Test</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-12">
          <div class="card card-success">
            <div class="card-header">
              <h3 class="card-title">Add New Test</h3>
            </div>
            <div class="card-body">
                <!-- flash-message -->
                <div class="row form-group">
                  <div class="col-2">&nbsp;</div>
                  <div class="col-8">
              <div class="flash-message">
                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                      @if (Session::has('alert-' . $msg))
                          <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                              <a href="#" class="close" data-dismiss="alert"
                                  aria-label="close">&times;</a>
                          </p>
                      @endif
                  @endforeach
              </div>
                  </div>
                  <div class="col-2">&nbsp;</div>
              </div> <!-- end .flash-message -->

            <form method="POST" action="{{url('/admin/patient-reports/saveform')}}">
              @csrf
            
              <div class="row form-group">
                <div class="col-2">&nbsp;</div>
                <div class="col-8"><input required="required" type="text" value="{{old('test_title')}}" name="test_title" class="form-control form-control-lg" placeholder="Enter Test Name (e.g CBC, RA Factor, Lipod Profile)"></div>
              </div>
              <div class="row form-group">
                <div class="col-2">&nbsp;</div>
                <div class="col-8"><textarea type="text" name="description" class="form-control form-control-lg" placeholder="Description (optional) e.g This test performed on Medonic M32 Cell">{{old('description')}}</textarea></div>
              </div>
              <div class="row form-group" id="row_1">
                <div class="col-3">
                  <input required="required" name="test_param[]" type="text" class="form-control" placeholder="Enter Parameter Name">
                </div>
                <div class="col-1">
                  <select required="required" name="test_result[]" class="form-control" placeholder="Select one">
                    <option value="">Result Type </option>
                    <option value="input">Input text</option>
                    <option value="pos-neg">+ve/-ve</option>
                  </select>
                </div>
                <div class="col-2">
                  <input required="required" type="text" name="test_unit[]" class="form-control" placeholder="Enter Unit (e.g mg/dl)">
                </div>
                <div class="col-2">
                  <input required="required" type="text" name="test_value_range[]" class="form-control" placeholder=" Normal (e.g 20-60 mg/dl)">
                </div>
                <div class="col-3">
                  <input required="required" type="text" name="test_comments[]" id="comments" class="form-control" placeholder="Enter Comments">
                </div>
                <div class="col-1">
                  <button onClick="removeFormRecord(1)" type="button" class="btn btn-danger btn-block btn-sm"><i class="fas fa-minus"></i> Remove</button>
                </div>
              </div>
              <div id="htmlElement"></div>
              {{-- New Row Button --}}
              <div class="row form-group">
                <div class="col-4">&nbsp;</div>
                <div class="col-3">
                  <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i class="fa fa-save"></i> Save</button>
                </div>
                <div class="col-4">&nbsp;</div>
                <div class="col-1">
                  <button type="button" class="btn btn-success btn-block btn-sm" onClick ="addmore()"><i class="fas fa-plus"></i> Add More</button>
                </div>
              </div>
            </form>
            </div>
            <!-- /.card-body -->
          </div>
            </div>
        </div>

        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script>
    var counter=1;
  function addmore(){
        counter++;
    var htmlElement ='<div class="row form-group" id="row_'+counter+'">';
        htmlElement+='<div class="col-3"><input required="required" name="test_param[]" type="text" class="form-control" placeholder="Enter Parameter Name"></div>';
        htmlElement+='<div class="col-1">';
        htmlElement+='<select required="required" name="test_result[]" class="form-control" placeholder="Select one">';
        htmlElement+='<option value="">Result Type </option>';
        htmlElement+='<option value="input">Input text</option>';
        htmlElement+='<option value="pos-neg">+ve/-ve</option>';
        htmlElement+='</select>';
        htmlElement+='</div>';
        htmlElement+='<div class="col-2">';
        htmlElement+='<input required="required" type="text" name="test_unit[]" class="form-control" placeholder="Enter Unit (e.g mg/dl)">';
        htmlElement+='</div>';
        htmlElement+='<div class="col-2">';
        htmlElement+='<input required="required" type="text" name="test_value_range[]" class="form-control" placeholder=" Normal (e.g 20-60 mg/dl)">';
        htmlElement+='</div>';
        htmlElement+='<div class="col-3">';
        htmlElement+='<input required="required" type="text" name="test_comments[]" id="comments" class="form-control" placeholder="Enter Comments">';
        htmlElement+='</div>';
        htmlElement+='<div class="col-1">';
        htmlElement+='<button onClick="removeFormRecord('+counter+')" type="button" class="btn btn-danger btn-block btn-sm"><i class="fas fa-minus"></i> Remove</button>';
        htmlElement+='</div>';
        htmlElement+='</div>';
        $('#htmlElement').append(htmlElement);
              console.log('test '+ htmlElement);
              
    }
function removeFormRecord(id){
  $('#row_'+id).remove();
}

  </script>
  
  @endsection
