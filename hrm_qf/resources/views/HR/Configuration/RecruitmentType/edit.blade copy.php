@php
    $sm = DB::table('gnl_company_config')->where('form_id', 23)->where('module_id', 13)->where('company_id', 1)->first();
    $is_sm_both = false;
    if(!empty($sm) && $sm->form_value == 'both'){
        $is_sm_both = true;
    }
@endphp
<form id="rec_type_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden name="rec_type_id" value="{{ $rec_type->id }}">
    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div class="col-sm-2 offset-sm-3 form-group">
                    <label class="input-title">Title</label>
                </div>

                <div class="col-sm-4 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $rec_type->title }}" name="title" placeholder="Enter Recruitment Title">
                    </div>
                </div>

            </div>

            @if ($is_sm_both)
            <div class="row" id="salary_method_div">

                <div class="col-sm-2 offset-sm-3 form-group">
                    <label class="input-title">Salary Method</label>
                </div>

                <div class="col-sm-4 form-group">
                    <div class="input-group">
                        <select name="salary_method" class="form-control clsSelect2" style="width: 100%;">
                            <option {{ ($rec_type->salary_method == 'auto') ? 'selected' : '' }} value="auto">Auto</option>
                            <option {{ ($rec_type->salary_method == 'manual') ? 'selected' : '' }} value="manual">Manual</option>
                        </select>
                    </div>
                </div>

            </div>
            @else
            <input hidden value="" name="salary_method">
            @endif

        </div>

    </div>

</form>

<script>

    $(document).ready(function(){

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

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
                0: 'updateBtn',
            }
        }),
    });

    $('#updateBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#rec_type_edit_form')[0]),
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

</script>