@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Test | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add New Patient Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add New Patient Report</li>
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
                                <h3 class="card-title">Patient Report</h3>
                            </div>
                            <div class="card-body">
                                <!-- flash-message -->
                                <div class="row form-group">
                                    <div class="col-2">&nbsp;</div>
                                    <div class="col-8">
                                        <div class="flash-message">
                                            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                                @if (Session::has('alert-' . $msg))
                                                    <p class="alert alert-{{ $msg }}">
                                                        {{ Session::get('alert-' . $msg) }}
                                                        <a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                                                    </p>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-2">&nbsp;</div>
                                </div> <!-- end .flash-message -->

                                <form method="POST" action="{{ url('/admin/patient-reports/update') }}">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT">
                                    @php
                                        $advisedTestDataWithPatient=$advisedTestDataWithPatient[0];
                                        $patient=$advisedTestDataWithPatient['patient'][0];
                                    @endphp
                                    <input type="hidden" name="patient_id" value="{{$patient['id']}}">
                                    <input type="hidden" name="patient_test_id" value="{{$advisedTestDataWithPatient['id']}}">
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-8"><input required="required" type="text"
                                                value="{{ old('name')?old('name'):$patient['name'] }}" name="name" 
                                                class=" @error('name') is-invalid @enderror form-control form-control-lg" autofocus
                                                placeholder="Enter Patient Name">
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-8"><input required="required" type="text"
                                                value="{{ old('gaudian_name')?old('gaudian_name'):$patient['gaudian_name'] }}" name="gaudian_name"
                                                class="@error('gaudian_name') is-invalid @enderror form-control form-control-lg"
                                                placeholder="Enter Patient's Gaurdian (Father,Husband) Name">
                                                @error('gaudian_name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-8"><input required type="text" value="{{ old('address')?old('address'):$patient['address'] }}"
                                                name="address" class="form-control form-control-lg"
                                                placeholder="Enter Patient Address"></div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3">
                                            <select name="gender" class="form-control form-control-lg">
                                                <option {{ ($patient['gender']=='m')?'selected':''}}  value="m">Male</option>
                                                <option {{ ($patient['gender']=='f')?'selected':''}} value="f">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3"><input type="text" value="{{ old('phone')?old('phone'):$patient['phone'] }}"
                                                name="phone" class="@error('phone') is-invalid @enderror form-control form-control-lg"
                                                placeholder="Enter Phone No">
                                                @error('phone')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                              </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3"><input type="text" value="{{ old('cnic')?old('cnic'):$patient['cnic'] }}"
                                                name="cnic" class="form-control form-control-lg" placeholder="Enter CNIC #">
                                        </div>
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3">
                                          <div class="input-group">

                                          <div class="input-group-prepend">
                                            <span class="input-group-text">{{$advisedTestDataWithPatient['opdno']}}</span>
                                          </div>
                                          <input type="text" disabled value="{{ $advisedTestDataWithPatient['opdno'] }}"
                                                 class=" form-control form-control-lg"
                                                placeholder="Unique RegNo (e.g 201)"></div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3"><input type="text"
                                                value="{{ old('prescription_srno')?old('prescription_srno'):$advisedTestDataWithPatient['prescription_srno'] }}" name="prescription_srno"
                                                class="form-control form-control-lg" placeholder="Enter Slip Sr.No"></div>
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3">
                                            <input type="text" value="{{ old('advised_by') }}" name="advised_by"
                                                class="form-control form-control-lg" placeholder="Advised by Doctor(Name)">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3"><input type="text"
                                                value="{{ (old('prescription_date'))?old('prescription_date'):date('d/m/Y') }}" name="prescription_date"
                                                class="form-control form-control-lg" placeholder="Patient Visit Date"></div>
                                        <div class="col-2">&nbsp;</div>

                                        <div class="col-3">
                                            <select name="patient_type" class="form-control form-control-lg">
                                                <option value="indoor">Indoor</option>
                                                <option value="outdoor">Outdoor</option>
                                                <option value="emergency">Emergency</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-8">
                                            <div class="card card-default">
                                                <div class="card-header">
                                                    <h3 class="card-title">Advised Tests</h3>
                                                    @error('advised_tests')
                                                    <div style="text-align: center; display:block;" class="invalid-feedback">
                                                        {{ $message }}*
                                                    </div>
                                                     @enderror
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool"
                                                            data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-tool"
                                                            data-card-widget="remove">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                {{-- <label>Select advised Tests</label> --}}
                                                                @php
                                                                    $patientTestID=$advisedTestDataWithPatient['id'];
                                                                    $advisedTests=json_decode($advisedTestDataWithPatient['advised_tests'],true);
                                                                    $advisedTests=(is_array($advisedTests))?(array_reverse($advisedTests)):array();
                                                                        // foreach($advisedTests as $advisedTestsID){
                                                                        //     echo '<br> Data ID :'.$advisedTestsID.'<br>';
                                                                        //     p(getReportByIDs($advisedTestsID,$patientTestID));
                                                                           
                                                                        // }
                                                                    //p($LabTestsDataWithParams);
                                                                   // die;
                                                                @endphp
                                                                <select  name="advised_tests[]"
                                                                    id="advised_tests_option" class="select"
                                                                    multiple="multiple">
                                                                    @php
                                                                        $htmlElement=$htmlElementHidden = '';
                                                                    foreach ($LabTestsDataWithParams as $testData) {

                                                                        


                                                                            if(in_array($testData['id'],$advisedTests)){
                                                                                echo '<option selected value="' . $testData['id'] . '">' . $testData['test_name'] . '</option>';
                                                                                //$data['ids']=$testData['id'].'='.$patientTestID;
                                                                                $data[]=$patientReport=getReportByIDs($testData['id'],$patientTestID);//getReportByTestId($testData['id']);
                                                                           // break;
                                                                            $htmlElement .= '<div id="test_rows_' . $testData['id'] . '" >';
                                                                            $htmlElement .= '<div class="row form-group" id="row_title_' . $testData['id'] . '"><div class="col-3">&nbsp;</div><div class="col-6 card-title"><h3 style="background-color:#007bff; font-size:22px;padding:6px; color:#fff;" class="text-center">' . $testData['test_name'] . '</h3></div></div>';
                                                                            foreach ($patientReport as $key => $params) {

                                                                                $htmlElement .= '<input type="hidden" name="lab_test_param_result[' . $params['id'] . '][]" value="'.$testData['id'].'">';
                                                                                $htmlElement .= '<input type="hidden" name="lab_test_param_result[' . $params['id'] . '][]" value="'.$testData['test_name'].'">';

                                                                                $htmlElement .= '<div class="row form-group">';
                                                                                $htmlElement .= '<div class="col-3"><input name="lab_test_param_result[' . $params['id'] . '][]" value=' . $params['parameter_name'] . ' disabled type="text" class="form-control" ></div>';
                                                                                $htmlElement .= '<div class="col-2">';

                                                                                if ($params['parameter_result'] != 'Neg' && $params['parameter_result'] != 'Pos') {
                                                                                    $htmlElement .= '<input  required="required" value="'.$params['parameter_result'].'" type="text" name="lab_test_param_result[' . $params['id'] . '][]" class="form-control" placeholder="Enter Result (e.g 894)">';
                                                                                } else {
                                                                                    $htmlElement .= '<select required="required" name="lab_test_param_result[' . $params['id'] . '][]" class="form-control" placeholder="Select one">';
                                                                                        
                                                                                        $pos='';
                                                                                        $neg='selected';
                                                                                        if($params['parameter_result']=='Pos'){
                                                                                            $pos='selected';
                                                                                            $neg='';
                                                                                        }
                                                                                    $htmlElement .= '<option '.$pos.' value="Pos">Positive </option>';
                                                                                    $htmlElement .= '<option '.$neg.' value="Neg">Negative</option>';
                                                                                    $htmlElement .= '</select>';
                                                                                }
                                                                                $htmlElement .= '</div>';
                                                                                $htmlElement .= '<div class="col-2">';
                                                                                $htmlElement .= '<input value=' . $params['parameter_unit'] . ' readonly type="text" class="form-control">';
                                                                                $htmlElement .= '</div>';
                                                                                $htmlElement .= '<div class="col-2">';
                                                                                $htmlElement .= '<input value=' . $params['parameter_normal_range'] . ' readonly type="text" class="form-control" >';
                                                                                $htmlElement .= '</div>';
                                                                                $htmlElement .= '<div class="col-3">';
                                                                                $htmlElement .= '<input value=' . $params['comments'] . ' readonly type="text" class="form-control" >';
                                                                                $htmlElement .= '</div>';
                                                                                $htmlElement .= '</div>';
                                                                            }
                                                                            $htmlElement .= '</div>';
                                                                        }
                                                                        else{

                                                                        
                                                                            echo '<option value="' . $testData['id'] . '">' . $testData['test_name'] . '</option>';
                                                                          
                                                                            $htmlElementHidden .= '<div id="test_rows_' . $testData['id'] . '" >';
                                                                            $htmlElementHidden .= '<div class="row form-group" id="row_title_' . $testData['id'] . '"><div class="col-3">&nbsp;</div><div class="col-6 card-title"><h3 style="background-color:#007bff; font-size:22px;padding:6px; color:#fff;" class="text-center">' . $testData['test_name'] . '</h3></div></div>';
                                                                            foreach ($testData['get_params'] as $key => $params) {

                                                                                $htmlElementHidden .= '<input type="hidden" name="lab_test_param_result[' . $params['id'] . '][]" value="'.$testData['id'].'">';
                                                                                $htmlElementHidden .= '<input type="hidden" name="lab_test_param_result[' . $params['id'] . '][]" value="'.$testData['test_name'].'">';

                                                                                $htmlElementHidden .= '<div class="row form-group">';
                                                                                $htmlElementHidden .= '<div class="col-3"><input name="lab_test_param_result[' . $params['id'] . '][]" type="text" value="'.$params['parameter_name'].'" class="form-control" readonly > </div>';
                                                                                $htmlElementHidden .= '<div class="col-2">';
                                                                                if ($params['parameter_result'] == 'input') {
                                                                                    $htmlElementHidden .= '<input name="lab_test_param_result[' . $params['id'] . '][]" required="required" type="text" name="lab_test_param_result[' . $params['id'] . '][]" class="form-control" placeholder="Enter Result (e.g 894)">';
                                                                                } else {
                                                                                    $htmlElementHidden .= '<select required="required" name="lab_test_param_result[' . $params['id'] . '][]" class="form-control" placeholder="Select one">';
                                                                                    $htmlElementHidden .= '<option value="Pos">Positive </option>';
                                                                                    $htmlElementHidden .= '<option value="Neg">Negative</option>';
                                                                                    $htmlElementHidden .= '</select>';
                                                                                }
                                                                                $htmlElementHidden .= '</div>';
                                                                                $htmlElementHidden .= '<div class="col-2">';
                                                                                $htmlElementHidden .= '<input name="lab_test_param_result[' . $params['id'] . '][]" value=' . $params['parameter_unit'] . ' readonly type="text" class="form-control">';
                                                                                $htmlElementHidden .= '</div>';
                                                                                $htmlElementHidden .= '<div class="col-2">';
                                                                                $htmlElementHidden .= '<input name="lab_test_param_result[' . $params['id'] . '][]" value=' . $params['parameter_normal_range'] . ' readonly type="text" class="form-control" >';
                                                                                $htmlElementHidden .= '</div>';
                                                                                $htmlElementHidden .= '<div class="col-3">';
                                                                                $htmlElementHidden .= '<input name="lab_test_param_result[' . $params['id'] . '][]" value=' . $params['comments'] . ' readonly type="text" class="form-control" >';
                                                                                $htmlElementHidden .= '</div>';
                                                                                $htmlElementHidden .= '</div>';
                                                                            }
                                                                            $htmlElementHidden .= '</div>';
                                                                        }
                                                                    }
                                                                    $htmlElementHidden =$htmlElement.$htmlElementHidden;   
                                                                    @endphp

                                                                </select>
                                                            </div>
                                                            <!-- /.form-group -->
                                                        </div>
                                                        <!-- /.col -->
                                                    </div>
                                                    <!-- /.row -->
                                                </div>

                                            </div>
                                        </div>
                                        <!-- /.card -->
                                    </div>
                                    <div id="htmlElement">
                                      {{-- Here all Test Parameters are going to be pasted --}}
                                      {!! $htmlElement !!}
                                      {{-- @php
                                          p($data);
                                      @endphp --}}
                                    </div>
                                    {{-- New Row Button --}}
                                    <div class="row form-group">
                                        <div class="col-4">&nbsp;</div>
                                        <div class="col-3">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i
                                                    class="fa fa-save"></i> Save</button>
                                        </div>
                                        <div class="col-5">&nbsp;</div>

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
    <div style="display: none;">
    {!! $htmlElementHidden !!}
    </div>
    {{-- <div class="row"><div class="col-4">&nbsp;</div><div class="col-8">&nbsp;
  @php
      p($data);
  @endphp
  </div></div> --}}
@endsection

@section('head-js-css')
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
@endsection

@section('footer-js-css')
    <!-- DataTables  & Plugins -->
    <!-- Bootstrap4 Duallistbox -->
    <script src="{{ url('adminpanel/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script>
      
        //Bootstrap Duallistbox
        var demo2 = $('.select').bootstrapDualListbox({
            filterOnValues: true,
            showFilterInputs: true,
            nonSelectedListLabel: 'Non-selected',
            selectedListLabel: 'Selected',

        });


        $(document).ready(function() {
            
          $('select[name="advised_tests[]_helper2"]').on('click', function() {
            //ids = ($('[select="advised_tests[]_helper2"]').val());
            removedID=$(this).val();
            console.log('Removed ID :'+removedID);
          });

          var ids = [];
            $('#advised_tests_option').on('change', function() {
                ids = ($('[name="advised_tests[]"]').val());
                console.log('ids : '+ids);
                $('#htmlElement').html('');
                $.each(ids,function(index,element){
                  
                  $('#htmlElement').append('<div id="appended_test_rows_'+element+'" >'+$('#test_rows_'+element).html()+'</div>');
                  
                 });

                
            });
           
        });
 

    </script>
@endsection
