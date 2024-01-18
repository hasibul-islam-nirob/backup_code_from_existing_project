<?php
use App\Services\CommonService as Common;
use App\Services\HtmlService as HTML;
use App\Services\HrService as HRS;
?>

<form id="sms_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div id="smsEditModal" data-backdrop="static" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">EDIT MESSAGE</h5>
                    <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-window-close" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="modal-body shadow mb-5 rounded modal-form-bg" style="margin: 0 15px 0 15px;">

                    <div class="row">
                        <div class="col-sm-10 offset-sm-1">

                            <input hidden id="sms_id" name="sms_id">
    
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label class="input-title RequiredStar">SMS Text Type</label>
                                    <select id="edit_sms_type" name="sms_type" required class="form-control clsSelect2"  style="width: 100%">
                                        <option value="">Select</option>
                                        <option value="text">English</option>
                                        <option value="SMS/unicode">Bangla</option>
                                    </select>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label class="input-title">Title</label>
                                    <input placeholder="Sms title" id="edit_sms_title" name="sms_title" type="text" class="form-control">
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label class="input-title RequiredStar">Body</label>
                                    <textarea rows="4" id="edit_sms_body" required name="sms_body" maxlength = "900" class="form-control" style="width: 100%"></textarea>
                                </div>

                                <div class="col-sm-6" style="padding-top:3%;">
                                    <p style="color: #000;"><span id="edit_char_count"></span></p>
                                    <p style="color: firebrick;">[ <strong>N.B:</strong> 160 character cover 1 sms. 
                                        Maximam Character length is 900 and 6 sms ]</p>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label class="input-title RequiredStar">Send To</label>
                                    <select id="edit_sms_to" name="sms_to" required class="form-control clsSelect2"  style="width: 100%">
                                        <option value="others">Others</option>
                                        <option value="employee">Employee</option>
                                        <option value="member">Member</option>
                                    </select>
                                </div>
    
                                <div class="col-sm-5 offset-sm-1 form-group" id="edit_send_type_div" style="display: none;">
                                    <label class="input-title RequiredStar">Send Type</label>
                                    <div class="input-group">

                                        <div class="radio-custom radio-primary">
                                            <input type="radio" id="edit_send_type_all" name="send_type" value="all">
                                            <label class="input-title" for="edit_send_type_all">All Branch &nbsp &nbsp </label>
                                        </div>
                                        <div class="radio-custom radio-primary">
                                            <input type="radio" id="edit_send_type_selected" name="send_type" value="selected">
                                            <label class="input-title" for="edit_send_type_selected">Selected  Branch &nbsp &nbsp </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row" id="edit_branch_div" style="display: none;">

                                <div class="col-sm-6 form-group">
                                    <label class="input-title RequiredStar">Branch</label>

                                    {!! HTML::forBranchFieldHr('edit_branch_id') !!}

                                    <p style="color: firebrick;">[ <strong>N.B:</strong> Sms will be sent to 
                                        <span id="edit_branch_comment">the selected branch </span> ]
                                    </p>
                                </div>
    
                                <div class="col-sm-5 offset-sm-1 form-group" id="edit_send_type_samity_div" style="display: none;">
                                    <label class="input-title RequiredStar">Send Type</label>

                                    <div class="input-group">
                                        <div class="radio-custom radio-primary">
                                            <input type="radio" id="edit_send_type_samity_all" name="send_type_samity" value="all">
                                            <label class="input-title" for="edit_send_type_samity_all">All Samity &nbsp &nbsp </label>
                                        </div>
                                        <div class="radio-custom radio-primary">
                                            <input type="radio" id="edit_send_type_samity_selected" name="send_type_samity" value="selected">
                                            <label class="input-title" for="edit_send_type_samity_selected">Selected Samity &nbsp &nbsp </label>
                                        </div>
                                    </div>
                                </div>
    
                            </div>
    
                            <div class="row" id="edit_samity_div" style="display: none;">
                                <div class="col-sm-6 form-group">
                                    <label class="input-title RequiredStar">Samity</label>

                                    <select id="edit_samity_id" name="samity_id" class="form-control clsSelect2"  style="width: 100%">
    
                                    </select>

                                    <p style="color: firebrick;">[ <strong>N.B:</strong> Sms will be sent to 
                                        <span id="edit_samity_comment">the selected samity </span> ]
                                    </p>
                                </div>
                            </div>
    
                            <div class="row" id="edit_others_div" style="display: none;">
                                <div class="col-sm-6 form-group">
                                    <label class="input-title RequiredStar">Mobile Numbers</label>

                                    <textarea placeholder="Ex. 01766xxxxxx,01855xxxxxx" rows="3" id="edit_others_number" name="others_number" class="form-control" style="width: 100%"></textarea>
                                </div>

                                <div class="col-sm-6" style="padding-top:5%;">
                                    <p style="color: green;">[ <strong>N.B:</strong> Muliple number should be separated by comma(,) and do not use  +88 before numbers.]
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
    
                </div>

                <div class="modal-footer">
                    <button id="edit_save_as_draft" type="button" class="btn btn-primary btn-round text-uppercase">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Update
                    </button>
                    <button id="edit_sendBtn" type="button" class="btn btn-primary btn-round text-uppercase">
                        <i class="fa-solid mr-2 fa-paper-plane"></i> Send
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    
    let smsEditForm = {
            'sms_type': $('#edit_sms_type'),
            'sms_title': $('#edit_sms_title'),
            'sms_body': $('#edit_sms_body'),
            'sms_to': $('#edit_sms_to'),
            'branch': $('#edit_branch_id'),
            'samity': $('#edit_samity_id'),
            'sms_id': $('#sms_id'),

            'send_type': $('input[type=radio][name=send_type]'),
            'send_type_samity': $('input[type=radio][name=send_type_samity]'),

            'send_all': $('#edit_send_type_all'),
            'send_all_samity': $('#edit_send_type_samity_all'),

            'send_selected': $('#edit_send_type_selected'),
            'send_selected_samity': $('#edit_send_type_samity_selected'),

            'other_numbers': $('#edit_others_number'),

            'branch_section': $('#edit_branch_div'),
            'others_section': $('#edit_others_div'),
            'samity_section': $('#edit_samity_div'),
            'send_type_section': $('#edit_send_type_div'),
            'send_type_section_samity': $('#edit_send_type_samity_div'),

            'sendBtn': $('#edit_sendBtn'),
            'draftBtn': $('#edit_save_as_draft'),
        }

        smsEditForm.sms_to.change(function (event){

            let smsTo = $(this).val();
            let sendAll = smsEditForm.send_all.prop('checked');
            let sendSelected = smsEditForm.send_selected.prop('checked');

            if (smsTo === 'employee'){
                smsEditForm.others_section.hide();
                smsEditForm.samity_section.hide();
                smsEditForm.send_type_section.show();
                smsEditForm.send_type_section_samity.hide();

                if (sendSelected){
                    smsEditForm.branch_section.show();
                }
                else if(sendAll){
                    smsEditForm.branch_section.hide();
                }
            }

            else if(smsTo === 'member'){

                smsEditForm.others_section.hide();
                smsEditForm.send_type_section.hide();
                smsEditForm.branch_section.show();
                smsEditForm.send_type_section_samity.show();
            }

            else if(smsTo === 'others'){
                smsEditForm.branch_section.hide();
                smsEditForm.samity_section.hide();
                smsEditForm.send_type_section.hide();
                smsEditForm.others_section.show();
                smsEditForm.send_type_section_samity.hide();
            }

        });

        smsEditForm.send_type.change(function (event){
            if (this.value === 'all'){
                smsEditForm.branch_section.hide();
            }
            else if (this.value === 'selected'){
                smsEditForm.branch_section.show();
            }
        });

        smsEditForm.send_type_samity.change(function (event){
            if (this.value === 'all'){
                smsEditForm.samity_section.hide();
            }
            else if (this.value === 'selected'){
                smsEditForm.samity_section.show();
            }
        });


        smsEditForm.sendBtn.click(function (event){
            event.preventDefault();
            callApi("{{ route('edit_sms', 'send') }}", 'post', new FormData($('#sms_edit_form')[0]),
                function (response, textStatus, xhr){
                    showApiResponse(xhr.status, '');
                    ajaxDataLoad();
                    modal.smsEditModal.modal('hide');
                },
                function (response){
                    showApiResponse(response.status, JSON.parse(response.responseText).msg);
                }
            );
        });

        smsEditForm.draftBtn.click(function (event){
            event.preventDefault();
            callApi("{{ route('edit_sms', 'draft') }}", 'post', new FormData($('#sms_edit_form')[0]),
                function (response, textStatus, xhr){
                    showApiResponse(xhr.status, '');
                    ajaxDataLoad();
                    modal.smsEditModal.modal('hide');
                },
                function (response){
                    showApiResponse(response.status, JSON.parse(response.responseText).msg);
                }
            );
        });

        smsEditForm.branch.change(function (event){
            let data = $(this).select2('data');
            if ($(this).val() !== ''){
                $('#edit_branch_comment').html('all the members of ' + data[0].text + ' branch');
            }
            else {
                $('#edit_branch_comment').html('the selected branch');
            }
            loadSamity($(this).val(), "edit");
        });

        smsEditForm.sms_body.keyup(function(event){
            $('#edit_char_count').html($(this).val().length + " (" + Number(Math.ceil($(this).val().length / 160)) + ")");
        });
</script>

