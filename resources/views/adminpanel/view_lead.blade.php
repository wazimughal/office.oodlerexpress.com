@extends('adminpanel.admintemplate')
@push('title')
    <title>View Quote | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Lead</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">View Lead</li>
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
                                <h3 class="card-title">View Lead</h3>
                            </div>
                            <div class="card-body">


                                <!-- /.row -->

                               
                                <div class="row">
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Lead Information</strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    
                                                        <div class="row">
                                                            <div class="col-3">&nbsp;</div>
                                                            <div class="col-6">

                                                                @if ($errors->any())
                                                                    {{ implode('', $errors->all('<div>:message</div>')) }}
                                                                @endif
                                                                <!-- flash-message -->
                                                                <div class="flash-message">
                                                                    @if ($errors->any())
                                                                        {{ implode('', $errors->all('<div>:message</div>')) }}
                                                                    @endif

                                                                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                                                        @if (Session::has('alert-' . $msg))
                                                                            <p class="alert alert-{{ $msg }}">
                                                                                {{ Session::get('alert-' . $msg) }} <a
                                                                                    href="#" class="close"
                                                                                    data-dismiss="alert"
                                                                                    aria-label="close">&times;</a></p>
                                                                        @endif
                                                                    @endforeach
                                                                </div> <!-- end .flash-message -->
                                                            </div>
                                                            <div class="col-3">&nbsp;</div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-12">
                                                        <table class="table">
                                                   
                                                                <tbody>
                                                                    <tr>
                                                                        <th style="width:50%">Name</th>
                                                                        <td>{{ $userData['firstname'] }}
                                                                            {{ $userData['lastname'] }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Email</th>
                                                                        <td>{{ $userData['email'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Mobile No.</th>
                                                                        <td>{{ $userData['mobileno'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Business Name</th>
                                                                        <td>{{ $userData['business_name'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Designation</th>
                                                                        <td>{{ $userData['designation'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Business Email</th>
                                                                        <td>{{ $userData['business_email'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Business Mobile</th>
                                                                        <td>{{ $userData['business_mobile'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Business Phone</th>
                                                                        <td>{{ $userData['business_phone'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Business Age</th>
                                                                        <td>{{ $userData['years_in_business'] }} years
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>How Often Shiping</th>
                                                                        <td>{{ $userData['how_often_shipping'] }}</td>
                                                                    </tr>
                                                                    {{-- <tr>
                                                                        <th>Shiping </th>
                                                                        <td>
                                                                            @php
                                                                            //$car_names=cat_name_by_ids(json_decode($userData['shipping_cat'],true)) ;   
                                                                            //echo implode('<br>',$car_names);
                                                                            @endphp
                                                                        </td>
                                                                    </tr> --}}
            
                                                                </tbody>
                                                        
                                                        </table>
                                                    </div>
                                                    <!-- /.tab-pane -->

                                                </div>
                                                <!-- /.tab-content -->
                                            </div><!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->
                                        @if ($user->group_id==config('constants.groups.admin'))
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Notes Section </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div id="submit_comment_replace">
                                                    @php
                                                        // p($quotesData['comments']);
                                                        
                                                    @endphp
                                                    @foreach ($userData['lead_comments'] as $key => $comment)
                                                        <div class="row border">
                                                            <div class="col-12">
                                                                <strong>{{ $comment['user']['name'] }}</strong>({{ $comment['slug'] }})
                                                                {{ date('d/m/Y H:i:s', strtotime($comment['created_at'])) }}<br>
                                                                {{ $comment['comment'] }}
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                </div>
                                                @php
                                                    $userData = get_session_value();
                                                    //p($userData);
                                                @endphp
                                                <div class="tab-content">
                                                    <form method="post" id="submit_comment">
                                                        <input type="hidden" name="group_id"
                                                            value="{{ $user->group_id }}">
                                                        <input type="hidden" name="action" value="submit_comment">
                                                        <input type="hidden" name="slug"
                                                            value="{{ $userData['get_groups']['slug'] }}">
                                                        <input type="hidden" name="user_name"
                                                            value="{{ $userData['name'] }}">
                                                        <div class="form-group">
                                                            <label for="inputDescription">Comment</label>
                                                            <textarea id="comments" name="comment" placeholder="Write comment about the lead" class="form-control"
                                                                rows="4"></textarea></br>
                                                            <button
                                                                onclick="do_action({{ $userData['id'] }},'submit_comment')"
                                                                type="button" class="btn btn-success float-right"><i
                                                                    class="far fa-credit-card"></i> Send</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                    </div>

                                    <div class="col-md-2">&nbsp;</div>

                                </div>
                                
                                <!-- /.row -->




                                <!-- /.row -->
                            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('head-js-css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- dropzonecss -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.css') }}">
@endsection
@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- dropzonejs -->
    <script src="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });
        function do_action(id, action_name = '') {
            var formData = ($('#' + action_name).formToJson());

            var sendInfo = {
                data: formData,
                action: action_name,
                id: id
            };

            if (action_name == 'submit_comment') {
                if ($('#comments').val() == '')
                    return false;
            }
            $('#_loader').show();
            $.ajax({
                url: "{{ route('leads.ajaxcall',$id) }}",
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    $('#' + action_name + '_replace').append(data.response);
                    $('#comments').val('');
                    $('#_loader').hide();
                    //console.log('result :'+action_name);
                    if (data.error == 'No') {
                        $('#file_' + id).remove();
                        $(document).Toasts('create', {
                            class: 'bg-success',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });
                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }
                }
            });

        }
    </script>
@endsection
