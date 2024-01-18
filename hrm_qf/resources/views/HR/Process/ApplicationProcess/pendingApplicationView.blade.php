{{-- @dd(url()->current()) --}}
<form id="approval_form">
    @csrf

    @include('HR.CommonBlade.applicationGrid',['title' => 'Application Details'])

    @include('HR.CommonBlade.noteGrid')

    @include('HR.CommonBlade.procedSection', ['applType' => $applType, 'dmp' => $dmp, 'applId' => $applId])

</form>

<script>
    callApi("{{ url()->current() }}/../../../../get_appl_with_notes/{{ $applId }}/{{ $applType[1] }}/api",
        'post', '',
        function(response, textStatus, xhr) {

            showDetailsData(response.result_data.application, response.applType);
            showNotes(response.result_data.notes);

            showModal({
                titleContent: "VIEW APPLICATION DETAILS",
                footerContent: getModalFooterElement({
                    'btnNature': {
                        0: 'send',
                        1: 'reject',
                    },
                    'btnName': {
                        0: 'Proceed',
                        1: 'Reject',
                    },
                    'btnId': {
                        0: 'proceedBtn',
                        1: 'rejectBtn',
                    }
                }),
            });

            configureActionEvents();

        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );

    function showDetailsData(response, applType) {

        const applicationId = "{{ $applId }}";

        if (applType[3] == "leave") {

            callApi("{{ url()->current() }}/../../../../../employee_leave/get/" + "{{ encrypt($applId) }}/api" , 'post', '',
                function(res, textStatus, xhr) {
                    // console.log(res);
                    const empData = res.result_data;
                    if(empData.employee == null){
                        var empNameCode = 'All Employees';
                    }else{
                        var empNameCode = empData.employee.emp_name + " (" + empData.employee.emp_code + ")";
                    };

                    let leaveName = empData.leave_category.name;
                    let LeaveReason = empData.reasons.reason;
                    

                    let commonInfo = 'I am writing to respectfully submit my <b>'+leaveName+' Application</b> for the reson is <b>'+LeaveReason+'</b>. That"s why sorry to say I am not attented in the office from date <b>'+convertDateFormatTwo(empData.date_from,'/')+'</b> to <b>'+convertDateFormatTwo(empData.date_to,'/')+'</b>. I am kindly request to you, please accept my application';

                    $("#application_header_date").html('Date: '+dateTimeToDate(empData.created_at, '/'))
                    $('#application_subject').html("Subject: Application for "+leaveName);
                    $('#application_body_common').html(commonInfo);
                    $('#application_body').html(empData.description);
                    $('#application_footer').html("Sincerely, <br>" + empNameCode);

                    let createdByNameCode = '';
                    let approvedByNameCode = '';
                    var createdById = empData.created_by ? empData.created_by.emp_id : null;
                    var approvedById = empData.approve_by ? empData.approve_by.emp_id : null;

                    if(createdById != null  ||  approvedById != null){
                        callApi("{{ url()->current() }}/../../../../getProcess/" +createdById+"/"+approvedById+"/api" , 'post', '',
                            function(res, textStatus, xhr) {
                                // console.log(res);
                                createdByNameCode = res[createdById];
                                if(approvedById == null || approvedById == ''){
                                    approvedByNameCode = 'Processing';
                                }
                                else{
                                    approvedByNameCode = res[approvedById];
                                }
                                
                            }
                        );
                    }else{
                        createdByNameCode  = 'Supper Admin';
                        approvedByNameCode  = 'Supper Admin';
                    }

                    setTimeout(() => {
                        var newRow = $("<tr>");
                        var createdAtCell = $("<td class='text-center'>").css("border", "1px solid #ddd").text(takeDateTimeReturnFormettedDateTime(response.created_at));
                        newRow.append(createdAtCell);

                        var createdByCell = $("<td>").css("border", "1px solid #ddd").text(createdByNameCode);
                        newRow.append(createdByCell);

                        var approvedByCell = $("<td>").css("border", "1px solid #ddd").text(approvedByNameCode);
                        newRow.append(approvedByCell);

                        $("#application_process_info").append(newRow);
                    }, 500);
                },
                function(res) {
                    showApiResponse(res.status, JSON.parse(res.responseText).message);
                }
            );

        }


        if (applType[3] == "movement") {

            callApi("{{ url()->current() }}/../../../../../employee_movement/get/" + "{{ encrypt($applId) }}/api" , 'post', '',
                function(res, textStatus, xhr) {
                    // console.log(res);
                    const empData = res.result_data;

                    if(empData.employee == null){
                        var empNameCode = 'All Employees';
                    }else{
                        var empNameCode = empData.employee.emp_name + " (" + empData.employee.emp_code + ")";
                    };

                    let commonInfo = 'I am writing to respectfully submit my Movement Application for <b>'+empData.application_for+'</b>.<br><b>'+convertDateFormatTwo(empData.movement_date, '/')+'</b> this day I want to move on <b>'+empData.location_to+'</b> at the time is <b>'+convertTo12HourFormat(empData.start_time)+'</b> to <b>'+convertTo12HourFormat(empData.end_time)+'</b>, for the reson <b>'+empData.reasons.reason+'</b> purpose.';

                    $("#application_header_date").html('Date: '+dateTimeToDate(empData.created_at, '/'))
                    $('#application_subject').html("Subject: Application for Movement");
                    $('#application_body_common').html(commonInfo);
                    $('#application_body').html(empData.description);
                    $('#application_footer').html("Sincerely, <br>" + empNameCode);

                    let createdByNameCode = '';
                    let approvedByNameCode = '';
                    var createdById = empData.created_by ? empData.created_by.emp_id : null;
                    var approvedById = empData.approve_by ? empData.approve_by.emp_id : null;

                    if(createdById != null  ||  approvedById != null){
                        callApi("{{ url()->current() }}/../../../../getProcess/" +createdById+"/"+approvedById+"/api" , 'post', '',
                            function(res, textStatus, xhr) {
                                // console.log(res);
                                createdByNameCode = res[createdById];
                                if(approvedById == null || approvedById == ''){
                                    approvedByNameCode = 'Processing';
                                }
                                else{
                                    approvedByNameCode = res[approvedById];
                                }
                                
                            }
                        );
                    }else{
                        createdByNameCode  = 'Supper Admin';
                        approvedByNameCode  = 'Supper Admin';
                    }

                    setTimeout(() => {
                        var newRow = $("<tr>");
                        var createdAtCell = $("<td class='text-center'>").css("border", "1px solid #ddd").text(takeDateTimeReturnFormettedDateTime(response.created_at));
                        newRow.append(createdAtCell);

                        var createdByCell = $("<td>").css("border", "1px solid #ddd").text(createdByNameCode);
                        newRow.append(createdByCell);

                        var approvedByCell = $("<td>").css("border", "1px solid #ddd").text(approvedByNameCode);
                        newRow.append(approvedByCell);

                        $("#application_process_info").append(newRow);
                    }, 500);
                    
                },
                function(res) {
                    showApiResponse(res.status, JSON.parse(res.responseText).message);
                }
            );
            
        }



        // let html = "";

        // html += '<tr>' +
        //     '<td style="width: 20%"><b>' + applType[2] + ' Code</b></td>' +
        //     '<td style="width: 27%">' + response[applType[3] + '_code'] + '</td>' +
        //     '<td rowspan="3" style="width: 3%"></td>' +
        //     '<td style="width: 20%"><b>Employee</b></td>' +
        //     '<td style="width: 30%">' + response.employee.emp_name + " (" + response.employee.emp_code + ")" + '</td>' +
        //     '</tr>';

        // html += '<tr>' +
        //     '<td style="width: 20%"><b>Branch</b></td>' +
        //     '<td style="width: 27%">' + response.branch.branch_name + '</td>' +
        //     '<td style="width: 20%"><b>' + applType[2] + ' Date</b></td>' +
        //     '<td style="width: 30%">' + response[applType[3] + '_date'] + '</td>' +
        //     '</tr>';

        // html += '<tr>' +
        //     '<td style="width: 20%"><b>Expected Effective Date</b></td>' +
        //     '<td style="width: 27%"> Null </td>' +
        //     '<td style="width: 20%"><b>Reason</b></td>' +
        //     '<td style="width: 30%">' + '' + '</td>' +
        //     '</tr>';

        // html += '<tr>' +
        //     '<td style="width: 20%"><b>Application Date</b></td>' +
        //     '<td style="width: 27%">' + response.leave_date + '</td>' +
        //     '</tr>';

        // html += '<tr>' +
        //     '<td style="width: 20%"><b>Description</b></td>' +
        //     '<td colspan="4" style="width: 27%">' + response.description + '</td>';
        // '</tr>';

        // $('#details_table_body').html(html);
    }

    function showNotes(approvedData) {

        if (Array.isArray(approvedData) && approvedData.length) {

            $('#comment_div').show();

            let commentTableRows = "";
            $.each(approvedData, function(index, val) {
                commentTableRows += '<tr><td>' + (index + 1) + '</td><td>' + val.comment +
                    '</td><td class="text-center">' + ((val.related_data != null) ? val.related_data : "") +
                    '</td><td>' + val.employee.emp_name +
                    "<br> (" + val.employee.emp_code + ")" + '</td><td class="text-center">' + val
                    .comment_date + '</td></tr>';
            });
            $('#view_comments_table').html(commentTableRows);

        } else {
            $('#comment_div').hide();
        }

    }

    function configureActionEvents() {

        $('#proceedBtn').click(function(e) {
            e.preventDefault();
            callApi("{{ url()->current() }}/../../../../proceed/{{ $applType[1] }}/Approved/{{ $dmp }}/api",
                'post',
                new FormData($('#approval_form')[0]),
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

        $('#rejectBtn').click(function(e) {
            e.preventDefault();
            callApi("{{ url()->current() }}/../../../../proceed/{{ $applType[1] }}/Rejected/{{ $dmp }}/api",
                'post',
                new FormData($('#approval_form')[0]),
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

    }
</script>
