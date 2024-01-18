@extends('Layouts.erp_master')
@section('content')
    <form id="leave_type_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row">
            <div class="col-sm-12 offset-sm-3">

                <div class="form-row form-group align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Leave Name</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input id="leave_name" name="leave_name" type="text" class="form-control round" style="width: 100%">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Short Name</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input id="short_name" name="short_name" type="text" class="form-control round" style="width: 100%">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Leave Type</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <div class="radio-custom radio-primary">
                                <input type="radio" name="leave_type" value="Pay">
                                <label for="g1">Pay &nbsp &nbsp </label>
                            </div>
                            <div class="radio-custom radio-primary">
                                <input type="radio" name="leave_type" value="Non Pay">
                                <label for="g2">Non Pay &nbsp &nbsp </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group d-flex justify-content-center">
                    <div class="example example-buttons">
                        <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                        <button type="submit" class="btn btn-primary btn-round" id="saveBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <script>
        $(document).ready(function (){
            let leaveTypeForm = {
                'leave_name': $('#leave_name'),
                'short_name': $('#short_name'),
                'leave_type': $('#leave_type'),
                'submitBtn': $('#saveBtn'),
            }

            leaveTypeForm.submitBtn.click(function (event){
                event.preventDefault();
                callApi("{{ url()->current() }}", 'post', new FormData($('#leave_type_form')[0]),
                    function (response, textStatus, xhr){
                        showApiResponse(xhr.status, '');
                        window.location = './';
                    },
                    function (response){
                        showApiResponse(response.status, JSON.parse(response.responseText).message);
                    }
                );
            });
        });

    </script>
@endsection

