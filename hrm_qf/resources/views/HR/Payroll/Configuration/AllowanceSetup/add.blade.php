
<form id="allowance_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Name</label>
                    <div class="input-group">
                        <input type="text" name="name" style="width: 100%;">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Short Name <small>(Not changeable)</small> </label>
                    <div class="input-group">
                        <input type="text" name="short_name" style="width: 100%;">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6 form-group offset-sm-3">
                    <label class="input-title RequiredStar">Benefit</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="benifit_type_uid" style="width: 100%;">
                            @foreach ($benifits as $b)
                                <option value="{{ $b->uid }}">{{ $b->name }}</option>
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
        titleContent: "Add Allowance",
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
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#allowance_add_form')[
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
