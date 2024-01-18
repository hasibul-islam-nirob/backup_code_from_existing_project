@extends('Layouts.erp_master')
@section('content')

    <form method="post" enctype="multipart/form-data" data-toggle="validator" novalidate="true">
        @csrf

        <input type="hidden" name="company_id" value="{{ $companyId }}">
        <div class="row">
            <div class="col-lg-8 offset-lg-3">

                <div class="row">
                    <div class="col-lg-12">
                        {!! App\Services\HtmlService::forBranchFeild(true,'branch_id','branch_id',null,'','Branch') !!}
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Employee</label>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="emp_id" id="employee_no">
                                <option value="">Select One</option>
                                @foreach ($employees as $row)
                                    <option value="{{ $row->id }}">
                                        {{ $row->emp_name." (".$row->emp_code.")" }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                <div class="form-row form-group align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Terminate Date</label>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="text" class="form-control datepicker-custom" name="terminate_date"
                                id="terminate_date" value="{{ $sysDate }}"
                                placeholder="DD-MM-YYYY">
                        </div>
                    </div>
                </div>
                @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'title' => 'Save',
                            'id' => 'validateButton2',
                            'exClass' => 'float-right'
                        ]])
            </div>
        </div>
    </form>

    <script type="text/javascript">

        $("#branch_id").change(function(e) {

            var branchId = $(this).val(); 
            // fnAjaxSelectBox(
            //     'employee_no',
            //     branchId,
            //     '{{base64_encode('hr_employees')}}',
            //     '{{base64_encode('branch_id')}}',
            //     '{{base64_encode('id,emp_code,emp_name')}}',
            //     '{{url('/ajaxSelectBox')}}',
            //     '',
            //     '{{base64_encode('1')}}',
            //     'id',
            //     '{{base64_encode('1')}}'
            // );

            fnAjaxSelectBox("employee_no", 
                branchId,
                "{{base64_encode('hr_employees')}}",
                "{{base64_encode('branch_id')}}",
                "{{base64_encode('id,employee_no,emp_name,emp_code')}}",
                "{{url('/ajaxSelectBox')}}",
                null,1);
        });


        $('form').submit(function (event) {
            event.preventDefault();
            // $(this).find(':submit').attr('disabled', 'disabled');

            $.ajax({
                    url: "{{ url()->current() }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('form').serialize(),
                })
                .done(function (response) {

                    if (response['alert-type'] == 'error') {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            text: response['message'],
                        });
                        $('form').find(':submit').prop('disabled', false);
                    } else {
                        $('form').trigger("reset");
                        swal({
                            icon: 'success',
                            title: 'Success...',
                            text: response['message'],
                            timer: 2000
                        }).then(function() {
                            window.location = './';
                        });

                        // setTimeout(function () {
                        //     window.location = './'
                        // }, 2000);
                    }

                })
                .fail(function () {
                    console.log("error");
                })
                .always(function () {
                    console.log("complete");
                });

        });

    </script>
@endsection
