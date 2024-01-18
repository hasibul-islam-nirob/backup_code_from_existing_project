
@php
    $employeeType = DB::table('gnl_dynamic_form_value as gdfv')
            ->where([['gdfv.is_active', 1], ['gdfv.is_delete', 0],['gdfv.type_id', 3], ['gdfv.form_id', 'HR.1']])
            ->select('gdfv.*')
            ->get();

    $salaryMethod = DB::table('gnl_dynamic_form_value as gdfv')
        ->where([['gdfv.is_active', 1], ['gdfv.is_delete', 0],['gdfv.type_id', 3], ['gdfv.form_id', 'HR.2']])
        ->select('gdfv.*')
        ->get();

    // dd($employeeType);
@endphp
<form id="rec_type_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" >

    <input hidden name="rec_type_id" value="" id="edit_id">
    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div class="col-sm-2 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Title</label>
                </div>

                <div class="col-sm-4 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter Recruitment Title" required>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-2 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Employee Type</label>
                </div>
                <div class="col-sm-4 form-group">
                    <div class="input-group">
                        <select name='employee_type' id="edit_employee_type"class='form-control clsSelect2' style='width: 100%;'>
                            @foreach ($employeeType as $emp)
                                <option value="{{$emp->value_field}}"> {{$emp->name}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-2 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Salary Method</label>
                </div>
                <div class="col-sm-4 form-group">
                    <div class="input-group">
                        <select name='salary_method' id="edit_salary_method" class='form-control clsSelect2' style='width: 100%;'>
                            @foreach ($salaryMethod as $salary)
                                <option value="{{$salary->value_field}}"> {{$salary->name}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            

        </div>

    </div>
    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
</form>

<script>

    $(document).ready(function(){

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });


        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#rec_type_edit_form')[0]),
            function(response, textStatus, xhr) {

                $('#edit_id').val("{{ $id }}");
                $('#title').val(response.responseData.title);
                $('#edit_employee_type').val(response.responseData.employee_type).trigger('change');
                $('#edit_salary_method').val(response.responseData.salary_method).trigger('change');
                
                
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );


    });


    showModal({
        titleContent: "Add Recruitment Types",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Update',
            },
            'btnId': {
                0: 'edit_updateBtn',
            }
        }),
    });

    $('#edit_updateBtn').click(function(event) {
        $('#edit_updateBtn_submit').click();
    });

    $('#rec_type_edit_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#rec_type_edit_form')[0]),
            function(response, textStatus, xhr) {

                if (response == 1) {
                    swal({
                        icon: 'warning',
                        title: 'Warning...',
                        text: 'PERMANENT employee type already exists.. Please try other employee type.',
                    });

                }else{
                    showApiResponse(xhr.status, '');
                    hideModal();
                    ajaxDataLoad();
                }
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

</script>