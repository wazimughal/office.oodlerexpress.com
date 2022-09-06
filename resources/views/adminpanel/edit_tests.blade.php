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
                        <h1>Edit Lab Test</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Edit Lab Test</li>
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
                                <h3 class="card-title">Edit Lab Test</h3>
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
                                <form method="POST" action="{{ url('/admin/lab-tests/edit/') . '/' . $data[0]['id'] }}">
                                    @csrf
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-8"><input value="{{ $data[0]['test_name'] }}"
                                                required="required" type="text" name="test_title"
                                                class="form-control form-control-lg"
                                                placeholder="Enter Test Name (e.g CBC, RA Factor, Lipod Profile)"></div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-8"><textarea 
                                                 type="text" name="description"
                                                class="form-control form-control-lg"
                                                placeholder="Description (if any) e.g This test performed on Medonic M32 Cell">{{ $data[0]['description'] }}</textarea></div>
                                    </div>
                                    @php
                                        $counter = 1;
                                    @endphp
                                    @foreach ($data[0]['get_params'] as $params)
                                        <div class="row form-group" id="row_{{ $counter }}">
                                            <input type="hidden" name="params_id[]" value="{{ $params['id'] }}">
                                            <div class="col-3">
                                                <input required="required" value="{{ $params['parameter_name'] }}"
                                                    name="test_param[]" type="text" class="form-control"
                                                    placeholder="Enter Parameter Name">
                                            </div>
                                            <div class="col-1">
                                                <select required="required" name="test_result[]" class="form-control"
                                                    placeholder="Select one">
                                                    <option value="">Result Type </option>
                                                    <option @if ($params['parameter_result'] == 'input') selected @endif value="input">
                                                        Input text</option>
                                                    <option @if ($params['parameter_result'] == 'pos-neg') selected @endif
                                                        value="pos-neg">+ve/-ve</option>
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <input value="{{ $params['parameter_unit'] }}" required="required"
                                                    type="text" name="test_unit[]" class="form-control"
                                                    placeholder="Enter Unit (e.g mg/dl)">
                                            </div>
                                            <div class="col-2">
                                                <input value="{{ $params['parameter_normal_range'] }}" required="required"
                                                    type="text" name="test_value_range[]" class="form-control"
                                                    placeholder=" Normal (e.g 20-60 mg/dl)">
                                            </div>
                                            <div class="col-3">
                                                <input value="{{ $params['comments'] }}" required="required" type="text"
                                                    name="test_comments[]" id="comments" class="form-control"
                                                    placeholder="Enter Comments">
                                            </div>
                                            <div class="col-1">

                                                <button
                                                    onClick="removeFormRecord({{ $params['id'] }},{{ $counter }})"
                                                    type="button" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-minus"></i>
                                                    Remove</button>
                                            </div>
                                        </div>
                                        @php
                                            $counter++;
                                        @endphp
                                    @endforeach
                                    <div id="htmlElement"></div>
                                    {{-- New Row Button --}}
                                    <div class="row form-group">
                                        <div class="col-4">&nbsp;</div>
                                        <div class="col-3">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i
                                                    class="fa fa-save"></i> Save</button>
                                        </div>
                                        <div class="col-4">&nbsp;</div>
                                        <div class="col-1">
                                            <button type="button" class="btn btn-success btn-block btn-sm"
                                                onClick="addmore()"><i class="fas fa-plus"></i> Add More</button>
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
        <?php
        $counter = count($data[0]['get_params']);
        if ($counter > 0) {
            echo 'var counter=' . $counter;
        } else {
            echo 'var counter=1';
        }
        ?>

        function addmore() {
            counter++;
            var htmlElement = '<div class="row form-group" id="row_' + counter + '">';
            htmlElement +=
                '<div class="col-3"><input required="required" name="test_param[]" type="text" class="form-control" placeholder="Enter Parameter Name"></div>';
            htmlElement += '<div class="col-1">';
            htmlElement +=
                '<select required="required" name="test_result[]" class="form-control" placeholder="Select one">';
            htmlElement += '<option value="">Result Type </option>';
            htmlElement += '<option value="input">Input text</option>';
            htmlElement += '<option value="pos-neg">+ve/-ve</option>';
            htmlElement += '</select>';
            htmlElement += '</div>';
            htmlElement += '<div class="col-2">';
            htmlElement +=
                '<input required="required" type="text" name="test_unit[]" class="form-control" placeholder="Enter Unit (e.g mg/dl)">';
            htmlElement += '</div>';
            htmlElement += '<div class="col-2">';
            htmlElement +=
                '<input required="required" type="text" name="test_value_range[]" class="form-control" placeholder=" Normal (e.g 20-60 mg/dl)">';
            htmlElement += '</div>';
            htmlElement += '<div class="col-3">';
            htmlElement +=
                '<input required="required" type="text" name="test_comments[]" id="comments" class="form-control" placeholder="Enter Comments">';
            htmlElement += '</div>';
            htmlElement += '<div class="col-1">';
            htmlElement += '<button onClick="removeRow('+counter+')" type="button" class="btn btn-danger btn-block btn-sm"><i class="fas fa-minus"></i> Remove</button>';
            htmlElement += '</div>';
            htmlElement += '</div>';
            $('#htmlElement').append(htmlElement);
          
        }

        function removeRow(counter_id=0){
            $('#row_' + counter_id).remove();
            console.log('#row_' + counter_id);
        }
        function removeFormRecord(id, counter_id) {
            
            if (confirm("Are you sure you want to delete this?")) {
                $.ajax({
                    url: "{{ url('/admin/lab-tests-params/delete/') }}/" + id,
                    //data: formData,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error == 'No') {
                            // Close modal and success Message
                            $('#row_' + counter_id).remove();
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
                        console.log(data);
                        //alert('i am here');
                    }

                });

            }
           
        }
    </script>
@endsection
