@extends('Layouts.erp_master')
@section('content')
    <style>
        .select2-container{
            z-index:100000;
        }
        h6{
            color:#1a1919
        }

        .RequiredStar  span{
            color:#fff!important;
        }
    </style>
    <!-- Page -->
    <?php
    use App\Services\CommonService as Common;
    use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;
    ?>

    {{--Add new Modal--}}
    @include('GNL.SmsForward.add')
    {{--Add new Modal--}}

    {{--Edit Modal--}}
    @include('GNL.SmsForward.edit')
    {{--Edit Modal--}}

    {{--View Modal--}}
    @include('GNL.SmsForward.view')
    {{--View Modal--}}

    {{--Add new Button--}}
    <div class="d-flex flex-row-reverse">
        <div class="p-2"><button id="add_new" class="btn btn-primary">Add New</button></div>
    </div>
    {{--Add new Button--}}

    {{--Datatable--}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                <tr>
                    <th style="width:5%;">SL</th>
                    <th style="width:20%;">Title</th>
                    <th style="width:30%;">Body</th>
                    <th style="width:15%;">Sms To</th>
                    <th style="width:15%;">Status</th>
                    <th style="width:15%;" class="text-center">Action</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    {{--Datatable--}}

    <!-- End Page -->
    <script>

        $(document).ready(function() {
            $('.page-header-actions').hide(); //Hide new entry button
            ajaxDataLoad();
        });

        let modal = {
            'smsAddModal' : $('#smsAddModal'),
            'smsEditModal' : $('#smsEditModal'),
            'smsViewModal' : $('#smsViewModal'),
        };

        $('#add_new').click(function (e){
            modal.smsAddModal.modal('show');
        });

        // modal close
        $('.modal').on('hidden.bs.modal', function(){

            $.each($(this).parents().find('form'), function () {
                $(this)[0].reset();
            });

            $(this).children().find('select').trigger('change');

            // $(this).parents().find('form')[0].reset();
            // $(this).find('form')[0].reset();
            $('#add_char_count').html(0);
        });

        // let smsEditForm = {
        //     'sms_type': $('#edit_sms_type'),
        //     'sms_title': $('#edit_sms_title'),
        //     'sms_body': $('#edit_sms_body'),
        //     'sms_to': $('#edit_sms_to'),
        //     'branch': $('#edit_branch_id'),
        //     'samity': $('#edit_samity_id'),
        //     'sms_id': $('#sms_id'),

        //     'send_type': $('input[type=radio][name=send_type]'),
        //     'send_type_samity': $('input[type=radio][name=send_type_samity]'),

        //     'send_all': $('#edit_send_type_all'),
        //     'send_all_samity': $('#edit_send_type_samity_all'),

        //     'send_selected': $('#edit_send_type_selected'),
        //     'send_selected_samity': $('#edit_send_type_samity_selected'),

        //     'other_numbers': $('#edit_others_number'),

        //     'branch_section': $('#edit_branch_div'),
        //     'others_section': $('#edit_others_div'),
        //     'samity_section': $('#edit_samity_div'),
        //     'send_type_section': $('#edit_send_type_div'),
        //     'send_type_section_samity': $('#edit_send_type_samity_div'),

        //     'sendBtn': $('#edit_sendBtn'),
        //     'draftBtn': $('#edit_save_as_draft'),
        // }

        function ajaxDataLoad() {

            $('.clsDataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                stateSave: true,
                stateDuration: 1800,
                ordering: false,
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                },
                columns: [
                    {
                        data: 'sl',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'sms_title',
                        orderable: false,
                        width: '20%'
                    },
                    {
                        data: 'sms_body',
                        orderable: false,
                        width: '30%'
                    },
                    {
                        data: 'sms_to',
                        orderable: false,
                        width: '15%'
                    },
                    {
                        data: 'status',
                        orderable: false,
                        width: '15%'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none',
                        width: '15%'
                    },
                ],
                'fnRowCallback': function(nRow, aData, Index) {
                    
                    let editBtn = '<a class="editData" data-id = '+ aData.id +' href="#"><i class="icon wb-edit mr-2 blue-grey-600"></i></a>';
                    let viewBtn = '<a class="viewData" data-id = '+ aData.id +' href="#"><i class="icon wb-eye mr-2 blue-grey-600"></i></a>';
                    let deleteBtn = '<a class="deleteData" data-id = '+ aData.id +' href="#"><i class="icon wb-trash mr-2 blue-grey-600"></i></a>';
                    let sendBtn = '<a style="color: #3b3b38;" class="sendData" data-id = '+ aData.id +' href="#"><i class="fa-solid mr-2 fa-paper-plane"></i></a>';

                    let actionHTML = "";

                    if(aData.status == "Draft"){
                        actionHTML = editBtn + viewBtn + sendBtn + deleteBtn;
                    }
                    else{
                        actionHTML = viewBtn;
                    }

                    $('td:last', nRow).html(actionHTML);
                },
                'fnDrawCallback': function (){
                    
                    $('.editData').click(function (event){
                        event.preventDefault();
                        let smsId = $(this).data('id');
                        callApi("{{ route('get_sms', '') }}/"+smsId, 'post', '',
                            function (response, textStatus, xhr){

                                // let formObject = document.forms['sms_edit_form'].elements;

                                // console.log(formObject);

                                // var formObject = $('#entry_form')[0].elements;
                                // $.each(formObject, function () {
                                //     let nameElement = $(this).attr('name');
                                //     // $(this).val(result_data[nameElement]);
                                //     $(this).val(response[nameElement]);

                                //     console.log(nameElement, response[nameElement]);
                                // });

                                // $('.clsSelect2').trigger('change');

                                smsEditForm.sms_title.val(response.sms_title);
                                smsEditForm.sms_type.val(response.sms_type);
                                smsEditForm.sms_body.val(response.sms_body);
                                smsEditForm.sms_to.val(response.sms_to);
                                smsEditForm.branch.val(response.branch_id);

                                if (response.sms_to === 'others'){
                                    smsEditForm.other_numbers.val(response.receiver_numbers);
                                }

                                smsEditForm.sms_id.val(smsId);

                                $('.clsSelect2').select2();

                                modal.smsEditModal.modal('show');

                                if(response.branch_id === null){
                                    smsEditForm.send_all.prop('checked', true);
                                }
                                else{
                                    smsEditForm.send_selected.prop('checked', true);
                                }

                                if(response.samity_id === null){
                                    smsEditForm.send_all_samity.prop('checked', true);
                                }
                                else{
                                    smsEditForm.send_selected_samity.prop('checked', true);
                                    smsEditForm.send_type_samity.change();
                                    smsEditForm.branch.change();
                                    setTimeout(function(){ smsEditForm.samity.val(response.samity_id); }, 3000);
                                    $('.clsSelect2').select2();
                                }
                                smsEditForm.send_type.change();
                                smsEditForm.sms_to.change();
                                smsEditForm.sms_body.keyup();

                            },
                            function (response){
                                showApiResponse(response.status, JSON.parse(response.responseText).msg);
                            }
                        );
                    });

                    $('.viewData').click(function (event){
                        event.preventDefault();
                        let smsId = $(this).data('id');
                        callApi("{{ route('get_sms', '') }}/"+smsId, 'post', '',
                            function (response, textStatus, xhr){

                                $('#view_sms_title').html(response.sms_title);
                                $('#view_sms_body').html(response.sms_body);
                                $('#view_sms_send_to').html(response.sms_to);

                                $('.clsSelect2').select2();
                                modal.smsViewModal.modal('show');
                            },
                            function (response){
                                showApiResponse(response.status, JSON.parse(response.responseText).msg);
                            }
                        );
                    });

                    $('.deleteData').click(function (event){
                        event.preventDefault();
                        let smsId = $(this).data('id');
                        swal({
                            title: "Are you sure to delete record?",
                            text: "Once Delete, this will be permanently delete!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                callApi("{{ route('delete_sms', '') }}/" + smsId,'post','',
                                    function (response, textStatus, xhr){
                                        swal(
                                            'Deleted!',
                                            'Record has been deleted.',
                                            'success'
                                        )
                                        ajaxDataLoad();
                                    },

                                    function (response){
                                        console.log(response);
                                        showApiResponse(response.status, response.msg);
                                    });
                            }
                        });
                    });

                    $('.sendData').click(function (event){
                        event.preventDefault();
                        let smsId = $(this).data('id');
                        callApi("{{ route('draft_send_sms', '') }}/"+smsId, 'post', '',
                            function (response, textStatus, xhr){

                                showApiResponse(xhr.status, '');
                                ajaxDataLoad();

                            },
                            function (response){
                                showApiResponse(response.status, JSON.parse(response.responseText).msg);
                            }
                        );
                    });
                }

            });
        }

        function loadSamity(branch_id, section){

            if(branch_id == ''){
                return false;
            }

            let target = (section === 'add') ? $('#add_samity_id') : $('#edit_samity_id');

            callApi("{{ route('get_samity_by_branch', '') }}/" + branch_id, 'post', '',
                function (response, textStatus, xhr){

                    target.html('');

                    $.each(response, function (index, item){
                        target.append($('<option>', {
                            value: item["id"],
                            text : item["name"] + " (" + item["samityCode"] + ")"
                        }));
                    });

                },
                function (response){
                    showApiResponse(response.status, JSON.parse(response.responseText).msg);
                }
            );
        }

    </script>
@endsection
