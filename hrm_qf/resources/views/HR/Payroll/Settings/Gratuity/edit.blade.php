<style>
    .modal-lg {
        max-width: 60%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php

    $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
    $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
    $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();

@endphp

<form id="wf_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden name="edit_id" value="{{ $editData->id }}">

    <div class="row">

        <div class="col-sm-12">

            <div class="row">

    
                <div class="col-sm-5 offset-sm-1  form-group">
                    <label class="input-title">Company</label>
                    <div class="input-group">
                        <select name="company_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Company</option>
                            @foreach ($companies as $val)
                            <option {{ ($val->id == $editData->company_id) ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title">Project</label>
                    <div class="input-group">
                        <select name="project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Project</option>
                            @foreach ($projects as $val)
                            <option {{ ($val->id == $editData->project_id) ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input value="{{ $editData->effective_date }}" style="z-index:99999 !important;" name="effective_date" type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>

                <div class="col-sm-5  form-group  d-none">
                    <label class="input-title">Salary Structure</label>
                    <div class="input-group">
                        <select name="salary_structure" class="form-control clsSelect2" style="width: 100%">
                            <option {{ ($editData->salary_structure  == 1) ? 'selected' : '' }} value="1">Enable</option>
                            <option {{ ($editData->salary_structure  == 0) ? 'selected' : '' }} value="0">Disable </option>
                        </select>
                    </div>
                </div>

            </div>

            @php
                $detailsData = $editData->gratuity_details;
            @endphp

            <br>

            <div class="row">

                <div class="col-sm-12" id="col_div_1">

                    <div class="row">

                        <div class="col-sm-2 offset-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Steps</label>
                        </div>
            
                        <div class="col-sm-2 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Year From</label>
                        </div>
            
                        <div class="col-sm-2 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Year To</label>
                        </div>
            
                        <div class="col-sm-3 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Gratuity</label>
                        </div>
            
                        <div class="col-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            
                        </div>
                        
                    </div>

                    @foreach ($detailsData as $key => $val)
                    <div class="row">

                        <div class="col-sm-2 offset-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="{{ $val->steps }}" name="steps[]">
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="{{ $val->year_from }}" name="year_from[]">
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="{{ $val->year_to }}" name="year_to[]">
                            </div>
                        </div>
        
                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="{{ $val->gratuity }}" name="gratuity[]">
                            </div>
                        </div>

                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="removeRow(this)" class="">
                                    <i class="fa fa-minus-circle" style="color: red;"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                    @endforeach

                    <div class="row" id="col_div_1_row">

                        <div class="col-sm-2 offset-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="" name="steps[]">
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="" name="year_from[]">
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="" name="year_to[]">
                            </div>
                        </div>
        
                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="" name="gratuity[]">
                            </div>
                        </div>
            
                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="addCol_1Row(this)" class="">
                                    <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                </div>
                
            </div>

            <br><br>

        </div>

    </div>

</form>


<script>

$(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.clsSelect2').select2();
    });

    function addCol_1Row(){
        
        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }

        $('#col_div_1').find('.addBtnClass')
        .html(
            '<a onclick="removeRow(this)" class="">' +
                '<i class="fa fa-minus-circle" style="color: red;"></i>' +
            '</a>'
        );

        $('#col_div_1_row').clone().find('.addBtnClass')
        .html(
            '<a onclick="addCol_1Row(this)" class="">' +
                '<i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>' +
            '</a>'
        ).end()
        .find("input").val("").end()
        .appendTo('#col_div_1');

        $('.clsSelect2').select2();
    }

    function removeRow(node){
        $(node).parent().parent().parent().remove();
    }

    showModal({
        titleContent: "Edit Gratuity",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'update',
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
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#wf_form')[
                0]),
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        )
    });
</script>