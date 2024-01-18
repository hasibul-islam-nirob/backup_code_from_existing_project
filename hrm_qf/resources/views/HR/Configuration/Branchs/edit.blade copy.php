{{-- @extends('Layouts.erp_master')
@section('content') --}}

<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<form id="branch_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden value="{{ $branchData->id }}" name="edit_id">
    <div class="row">
        <div class="col-lg-10">
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right RequiredStar">Bank</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <select class="form-control  text-right clsSelect2" name="bank_id" id="bank_id"
                                required data-error="Please Select a Bank">
                            <option value="" selected disabled>Select One</option>
                            @foreach ($banks as $bank)
                                <option value="{{$bank->id}}" {{( $bank->id == $branchData->bank_id) ? 'selected' : ' '}} >{{$bank->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Name</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" value="{{$branchData->name}}" placeholder="Enter Branch Name">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="address" value="{{$branchData->address}}" placeholder="Enter Branch Address">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Email Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" value="{{$branchData->email}}" placeholder="Enter Branch's Email Address">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Phone No</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="phone" value="{{$branchData->phone}}" placeholder="Enter Branch's Phone No.">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person" value="{{$branchData->contact_person}}" placeholder="Enter Contact Person's Name">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Designation</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person_designation" value="{{$branchData->contact_person_designation}}" placeholder="Enter Contact Person's Designation">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right textNumber">Contact Person's Phone</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control textNumber" name="contact_person_phone" value="{{$branchData->contact_person_phone}}" placeholder="Enter Contact Person's Phone">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Email</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="contact_person_email" value="{{$branchData->contact_person_email}}" placeholder="Enter Contact Person's Email">
                        
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-lg">
                    <div class="form-group d-flex justify-content-center">
                        <div class="example example-buttons">
                            <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                            <button type="submit" class="btn btn-primary btn-round" >Save</button>
                        </div>
                    </div>
                </div>
            </div> --}}

        </div>
    </div>
</form>

{{-- 
<form id="branch_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-lg-10">
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right RequiredStar">Bank</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <select class="form-control  text-right clsSelect2" name="bank_id" id="bank_id"
                                required data-error="Please Select a Bank">
                            <option value="">Select One</option>
                            @foreach ($banks as $bank)
                                <option value="{{$bank->id}}">{{$bank->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Name</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" value="{{$branchData->name}}" placeholder="Enter Branch Name">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="address" value="{{$branchData->address}}" placeholder="Enter Branch Address">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Email Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" value="{{$branchData->email}}" placeholder="Enter Branch's Email Address">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Phone No</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="phone" value="{{$branchData->phone}}" placeholder="Enter Branch's Phone No.">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person" value="{{$branchData->contact_person}}" placeholder="Enter Contact Person's Name">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Designation</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person_designation" value="{{$branchData->contact_person_designation}}" placeholder="Enter Contact Person's Designation">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right textNumber">Contact Person's Phone</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control textNumber" name="contact_person_phone" value="{{$branchData->contact_person_phone}}" placeholder="Enter Contact Person's Phone">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Email</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="contact_person_email" value="{{$branchData->contact_person_email}}" placeholder="Enter Contact Person's Email">
                        
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-lg">
                    <div class="form-group d-flex justify-content-center">
                        <div class="example example-buttons">
                            <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                            <button type="submit" class="btn btn-primary btn-round" >Save</button>
                        </div>
                    </div>
                </div>
            </div> 

        </div>
    </div>
</form> --}}

<script>

$(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Edit Branch",
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
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#branch_edit_form')[
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

/*
    $("[name=bank_id]").val("{{ $branchData->bank_id }}");
    $('form').submit(function (event) {
    event.preventDefault();
    $(this).find(':submit').attr('disabled', 'disabled');

    $.ajax({
                type: 'post',
                url: "{{ url()->current() }}",
                data: $('form').serialize(),
                dataType: 'json',
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
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href = "./../";
                    });
                }

            })
            .fail(function () {
                console.log("error");
            });
});
*/
</script>
{{-- @endsection --}}
