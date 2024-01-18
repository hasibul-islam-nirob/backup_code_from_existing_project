<form id="allowance_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden value="{{ $editData->id }}" name="edit_id">

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Name</label>
                    <div class="input-group">
                        <input type="text" name="name" value="{{ $editData->name }}" style="width: 100%;">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Short Name</label>
                    <div class="input-group">
                        <input type="text" value="{{ $editData->short_name }}" name="short_name" style="width: 100%;" disabled>
                        <input type="text" value="{{ $editData->short_name }}" name="short_name" style="width: 100%;" hidden>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6 form-group offset-sm-3">
                    <label class="input-title RequiredStar">Benefit</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="benifit_type_uid" style="width: 100%;">
                            @foreach ($benifits as $b)
                                <option {{ ($b->uid == $editData->benifit_type_uid) ? 'selected' : '' }} value="{{ $b->uid }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
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
    });

    showModal({
        titleContent: "Edit Allowance",
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
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#allowance_edit_form')[
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