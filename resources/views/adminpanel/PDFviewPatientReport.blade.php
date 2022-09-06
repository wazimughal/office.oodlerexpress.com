<!DOCTYPE html>
<html lang="en">

<body>
@php
    $width=800;
    $borderBottom='border-bottom: 1px solid #dee2e6;';
    $border='border: 1px solid #dee2e6';
    $row='width:'.$width.'px; clear:both;';
    $padding10='padding:10px;';
    $float_left='float:left;';
    $textCenter='text-align: center;';
    $col_full='width:'.$width.'px;';
    $cal_half='width: '.($width/2).'px;';
    $col_quarter='width: '.($width/4).'px;float:left;';
    $col_6th='width: '.($width/6).'px;float:left;';
    $col_7th='width: '.($width/7).'px;float:left;';
    $col_8th='width: '.($width/8).'px; float:left;';
@endphp
       <div style="width:{{$width}}px; margin:0 auto;">                 
                               
                              
                                            <div style="{{$row.$padding10}}">
                                                @php
                                                    
                                                   $advisedTestDataWithPatient=$advisedTestDataWithPatient[0];
                                                   
                                                    $patient=$advisedTestDataWithPatient['patient'][0];
                                                    $org=get_OrganizationsById($advisedTestDataWithPatient['org_id']);
                                                    $org=$org[0];
                                                    
                                                @endphp
                                                   
                                                <div style="width:700px; margin-left:50px; font-size:65px; font-weight:bold;">{{$org['name'];}}</div>
                                                
                                            </div>
                                            <div style="{{$row.$padding10}}">
                                                <div style="width:800px; text-align:center; margin:0 0 10px 0px; font-size:40px; font-weight:bold;">{{$org['lab_name'];}}</div>
                                            </div>
                                            <div style="{{$row.$padding10}}">
                                                <div style="{{$cal_half.$float_left}}"><strong>{{$org['name'];}}</strong></div>
                                                <div style="{{$cal_half.$float_left}}"><strong>{{$org['district'];}}</strong></div>
                                            </div>
                                            
                                            <div style="{{$row.$padding10}}">
                                                <div style="{{$cal_half.$float_left}}"><strong>M.R. NO. : </strong> {{$advisedTestDataWithPatient['prescription_srno']}}</div>
                                                <div style="{{$cal_half.$float_left}}"><strong>Registration No. :</strong> {{$advisedTestDataWithPatient['opdno']}}</div>
                                            </div>
                                            <div style="{{$row.$padding10}}">
                                                <div style="{{$cal_half.$float_left}}"><strong>Patient Name :</strong> {{$patient['name']}} s/d/w {{$patient['gaudian_name'] }}</div>
                                                <div style="{{$cal_half.$float_left}}"><strong>Gender :</strong> @php echo ($patient['gender']=='m')?'Male':'Female';@endphp</div>
                                            </div>
                                            <div style="{{$row.$padding10}}">
                                                <div style="{{$cal_half.$float_left}}"><strong>Address :</strong>{{$patient['address']}}</div>
                                                <div style="{{$cal_half.$float_left}}"><strong>Phone No. :</strong>{{$patient['phone']}}</div>
                                            </div>
                                            <div style="{{$row.$padding10}}">
                                                <div style="{{$cal_half.$float_left}}"><strong>Reffered by :</strong> {{$advisedTestDataWithPatient['advised_by']}}</div>
                                                <div style="{{$cal_half.$float_left}}"><strong>Patient Type :</strong> {{$advisedTestDataWithPatient['patient_type']}}</div>
                                            </div>
                                            <div style="{{$row.$padding10}} clear:both;">
                                                <div style="{{$cal_half.$float_left}}"><strong>Date and Time :</strong> {{$advisedTestDataWithPatient['created_at']}}</div>
                                                <div style="{{$cal_half.$float_left}}"><strong>Registration No. :</strong></div>
                                            </div>
                                            <hr style="width:{{$width}}px;clear:both;margin-top: 1rem;margin-bottom: 1rem;border: 0;border-top: 1px solid rgba(0,0,0,.1);box-sizing: content-box;height: 0;overflow: visible;">

                                            @php
                                            $patientTestID=$advisedTestDataWithPatient['id'];
                                            $advisedTests=json_decode($advisedTestDataWithPatient['advised_tests'],true);
                                            $htmlElement='';
                                            foreach ($advisedTests as $key=>$val) {
                                                $data[]=$patientReportData=getReportByIDs($val,$advisedTestDataWithPatient['id']);
                                              

                                                    $htmlElement .='<div style="'.$row.'">
                                                        
                                                        <div style="'.$cal_half.' margin:0 auto; ">
                                                            <h3 style="background-color:#93989e; font-size:22px;padding:6px; color:#fff;text-align: center" >'.$patientReportData[0]['lab_test']['test_name'].'</h3>
                                                        </div>
                                                        </div>';
                                                    foreach ($patientReportData as $key => $params) {

                                                        $htmlElement .= '<div style="'.$row.$borderBottom.'">';
                                                        $htmlElement .= '<div style="'.$col_quarter.'">'. $params['parameter_name'] . '</div>';
                                                        $htmlElement .= '<div style="'.$col_6th.'">'.$params['parameter_result'].'</div>';
                                                        $htmlElement .= '<div style="'.$col_6th.'">'.$params['parameter_unit'].'</div>';
                                                        $htmlElement .= '<div style="'.$col_6th.'">'. $params['parameter_normal_range'].'</div>';
                                                        $htmlElement .= '<div style="'.$col_quarter.'">' . $params['comments'].' </div>';
                                                        $htmlElement .= '</div>';
                                                    }
                                            }
                                            @endphp
                                            
                                                {!! $htmlElement !!}
                                            
                                 
                                
                             
      
        <footer style=" clear:both; text-align:center; #fff;border-top: 1px solid #dee2e6;color: #869099;padding: 1rem;">
        <strong>Copyright &copy; 2021-2025 <a href="https://softheights.com">Softheights.co</a>.</strong>
        All rights reserved.
        
      </footer>
       </div>
    
    </body>
    </html>
    