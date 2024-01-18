

<style>
    .modal-lg {
        max-width: 50%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php
    $grade = intval(DB::table('hr_config')->where([['title', 'grade']])->first()->content);
    $level = intval(DB::table('hr_config')->where([['title', 'level']])->first()->content);
   
@endphp

<form id="edit_security_money_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden name="edit_id" value="{{$editData->id}}">

    <div class="row">

        <div class="col-sm-12">

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Grade</label>
                    <div class="input-group">
                        <select name="grade" class="form-control clsSelect2" style="width: 100%">
                            <option  value="">Select grade</option>
                            @for ($i= 1; $i<= $grade; $i++)
                                <option  {{$editData->grade_id == $i ? 'selected' : ''}}  value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Level</label>
                    <div class="input-group">
                        <select name="level" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select level</option>
                            @for ($i= 1; $i<= $level; $i++)
                            <option {{$editData->level_id == $i ? 'selected' : ''}} value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>
        
            </div>

            <div class="row">
                <div class="col-sm-5 offset-sm-1  form-group">
                    <label class="input-title">Amount</label>
                    <div class="input-group">
                        <input class="form-control" value="{{$editData->amount}}" name="amount">
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
                        <input id="edit_exp_effective_date" style="z-index:99999 !important;" value="{{$editData->effective_date}}" name="effective_date" type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
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
        titleContent: "Edit Security Money",
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
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#edit_security_money_form')[
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
