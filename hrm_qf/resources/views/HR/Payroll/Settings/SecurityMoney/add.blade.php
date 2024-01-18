

<style>
    .modal-lg {
        max-width: 50%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php

    $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
    $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
    $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();
    $rec_type = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])->get(); //title

    $grade = intval(DB::table('hr_config')->where([['title', 'grade']])->first()->content);
    $level = intval(DB::table('hr_config')->where([['title', 'level']])->first()->content);

@endphp

<form id="security_money_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-12">

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Grade</label>
                    <div class="input-group">
                        <select name="grade" class="form-control clsSelect2" style="width: 100%">

                            <option value="" selected disabled>Select grade</option>
                            @for ($i= 1; $i<= $grade; $i++)
                            <option value="{{$i}}">{{$i}}</option>
                            @endfor

                        </select>
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Level</label>
                    <div class="input-group">
                        <select name="level" class="form-control clsSelect2" style="width: 100%">
                            <option value="" selected disabled>Select level</option>
                            @for ($i= 1; $i<= $level; $i++)
                            <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>
        
                  
            </div>


            <div class="row">

                <div class="col-sm-5 offset-sm-1  form-group">
                    <label class="input-title">Amount</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="amount">
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_exp_effective_date" style="z-index:99999 !important;" name="effective_date" type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>
        
                  
            </div>

          
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


    showModal({
        titleContent: "Add Security Money",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Save',
            },
            'btnId': {
                0: 'add_saveBtn',
            }
        }),
    });

    $('#add_saveBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#security_money_form')[
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
