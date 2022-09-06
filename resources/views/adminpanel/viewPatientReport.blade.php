@extends('adminpanel.admintemplate')
@push('title')
    <title>View Patient Report | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Patient Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">View Patient Report</li>
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
                                <div class="row">
                                    <div class="col-11 text-right mb-5"><a class="btn btn-primary" href="{{ URL::to('/admin/patient-reports/view/'.$id.'?exportpdf=1') }}">Export to PDF</a></div>
                                </div>
                                <div class="row">
                                    <div class="col-1">&nbsp;</div>
                                    <div class="col-10 border">
                                            <div class="row">
                                                @php
                                                    
                                                   $advisedTestDataWithPatient=$advisedTestDataWithPatient[0];
                                                    $patient=$advisedTestDataWithPatient['patient'][0];
                                                    $org=get_OrganizationsById($advisedTestDataWithPatient['org_id']);
                                                    $org=$org[0];
                                                @endphp
                                                   
                                                <div class="col-6"><strong>{{$org['name'];}}</strong></div>
                                                <div class="col-6"><strong>{{$org['district'];}}</strong></div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-6"><strong>M.R. NO. : </strong> {{$advisedTestDataWithPatient['prescription_srno']}}</div>
                                                <div class="col-6"><strong>Registration No. :</strong> {{$advisedTestDataWithPatient['opdno']}}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6"><strong>Patient Name :</strong> {{$patient['name']}} s/d/w {{$patient['gaudian_name'] }}</div>
                                                <div class="col-6"><strong>Gender :</strong> @php echo ($patient['gender']=='m')?'Male':'Female';@endphp</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6"><strong>Address :</strong>{{$patient['address']}}</div>
                                                <div class="col-6"><strong>Phone No. :</strong>{{$patient['phone']}}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6"><strong>Reffered by :</strong> {{$advisedTestDataWithPatient['advised_by']}}</div>
                                                <div class="col-6"><strong>Patient Type :</strong> {{$advisedTestDataWithPatient['patient_type']}}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6"><strong>Date and Time :</strong> {{$advisedTestDataWithPatient['created_at']}}</div>
                                                <div class="col-6"><strong>Registration No. :</strong></div>
                                            </div>
                                            <hr>

                                            @php
                                            $patientTestID=$advisedTestDataWithPatient['id'];
                                            $advisedTests=json_decode($advisedTestDataWithPatient['advised_tests'],true);
                                            $htmlElement='';
                                            foreach ($advisedTests as $key=>$val) {
                                                $data[]=$patientReportData=getReportByIDs($val,$advisedTestDataWithPatient['id']);
                                              

                                                    $htmlElement .= '<div class="row">
                                                        <div class="col-3">&nbsp;</div>
                                                        <div class="col-6 card-title">
                                                            <h3 style="background-color:#93989e; font-size:22px;padding:6px; color:#fff;" class="text-center">'.$patientReportData[0]['lab_test']['test_name'].'</h3>
                                                        </div>
                                                        </div>';
                                                    foreach ($patientReportData as $key => $params) {

                                                        $htmlElement .= '<div class="row text-center border">';
                                                        $htmlElement .= '<div class="col-3">'. $params['parameter_name'] . '</div>';
                                                        $htmlElement .= '<div class="col-2">'.$params['parameter_result'].'</div>';
                                                        $htmlElement .= '<div class="col-2">'.$params['parameter_unit'].'</div>';
                                                        $htmlElement .= '<div class="col-2">'. $params['parameter_normal_range'].'</div>';
                                                        $htmlElement .= '<div class="col-3">' . $params['comments'].' </div>';
                                                        $htmlElement .= '</div>';
                                                    }
                                            }
                                            @endphp
                                            <div>
                                                {!! $htmlElement !!}
                                            </div>
                                    </div>
                                    <div class="col-1">&nbsp;</div>
                                </div>
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


