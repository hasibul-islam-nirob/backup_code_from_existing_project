
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Movement Application Details'])
</div>

<div id="appView" style="display: none;">
    @include('HR.CommonBlade.applicationGrid')
</div>

@php
    use App\Services\HrService as HRS;
    $companyAddress = HRS::getCompanyAddress();
    // $empinfo = HRS::fnForEmployeeData([])
    // dd($companyAddress->comp_name, $companyAddress->comp_addr)
@endphp

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            // console.log(response);
            if(response.result_data['is_active'] == 0 || response.result_data['is_active'] == 2){//Draft view (For now details view for all)

                $("#draftView").show();

                showApplicantData(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Movement Application",
                    });

                });
            }else if(response.result_data['is_active'] == 1 || response.result_data['is_active'] == 3){//Approved or Processing

                $("#appView").show();

                showApplication(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Movement Application",
                    });

                });
            }
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );

    async function showApplicantData(response){
        // concole.log(response);
        let empNameCode = '';
        if(response.employee == null){
            empNameCode = 'All Employee';
        }else{
            empNameCode = response.employee.emp_name + " (" + response.employee.emp_code + ")";
        };
        let html = "";


        html += '<tr>' +
            '<td style="width: 20%"><b>Movement Code</b></td>' +
            '<td style="width: 27%">'+ response.movement_code +'</td>' +

            '<td style="width: 10%"><b>Employee</b></td>' +
            '<td style="width: 30%">'+ empNameCode +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch</b></td>' +
            '<td style="width: 27%">'+ response.branch.branch_name +'</td>' +
            '<td style="width: 20%"><b>Movement Date</b></td>' +
            '<td style="width: 30%">'+ viewDateFormat(response.movement_date) +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Start Time</b></td>' +
            '<td style="width: 27%">'+ response.start_time +'</td>' +
            '<td style="width: 20%"><b>End Time</b></td>' +
            '<td style="width: 30%">'+ response.end_time +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Application Date</b></td>' +
            '<td style="width: 27%">'+ response.appl_date +'</td>' +
            '<td style="width: 20%"><b>Reason</b></td>' +
            '<td style="width: 30%">'+ response.reason +'</td>' +
        '</tr>';

        if (response.branch_to == undefined) {
            html += '<tr>' +
                '<td style="width: 20%"><b>Movement To</b></td>' +
                '<td style="width: 27%"> - </td>'
            '</tr>';
        }else{

            html += '<tr>' +
                '<td style="width: 20%"><b>Movement To</b></td>' +
                '<td style="width: 27%">'+ response.branch_to.branch_name+'['+response.branch_to.branch_code+'] - '+response.location_to+'</td>'
            '</tr>';
        }

        html += '<tr>' +
            '<td style="width: 20%"><b>Description</b></td>' +
            '<td style="width: 27%">'+ response.description +'</td>' +
            '<td style="width: 20%"><b>Attachment</b></td>' +
            '<td style="width: 30%"><a target="_blank" style="color: blue;" href='+ "{{ url('/') }}/" + response.attachment +'> Click here'+'</a></td>' +
        '</tr>';

        $('#details_table_body').html(html);
    }

    

    async function showApplication(response){
        let resData = new Date(response.appl_date);
        // console.log(response);
        if(response.employee == null){
            var empNameCode = 'All Employees';
        }else{
            var empNameCode = response.employee.emp_name + " (" + response.employee.emp_code + ")";
        };

        let commonInfo = 'I am writing to respectfully submit my Movement Application for <b>'+response.application_for+'</b>.<br><b>'+convertDateFormatTwo(response.movement_date, '/')+'</b> this day I want to move on <b>'+response.location_to+'</b> at the time is <b>'+convertTo12HourFormat(response.start_time)+'</b> to <b>'+convertTo12HourFormat(response.end_time)+'</b>, for the reson <b>'+response.reasons.reason+'</b> purpose.';

        $("#application_header_date").html('Date: '+dateTimeToDate(response.created_at, '/'))
        $('#application_subject').html("Subject: Application for Movement");
        $('#application_body_common').html(commonInfo);
        $('#application_body').html(response.description);
        $('#application_footer').html("Sincerely, <br>" + empNameCode);


        let createdByNameCode = '';
        let approvedByNameCode = '';
        var createdById = response.created_by ? response.created_by.emp_id : null;
        var approvedById = response.approve_by ? response.approve_by.emp_id : null;
        if(createdById != null  ||  approvedById != null){
          
            callApi("{{ url()->current() }}/../../getEmp/" +createdById+"/"+approvedById+"/api" , 'post', '',
                function(res, textStatus, xhr) {
                    console.log(res);
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
            let newRow = $("<tr>");
            let createdAtCell = $("<td class='text-center'>").css("border", "1px solid #ddd").text(takeDateTimeReturnFormettedDateTime(response.created_at));

            newRow.append(createdAtCell);

            let createdByCell = $("<td>").css("border", "1px solid #ddd").text(createdByNameCode);
            newRow.append(createdByCell);

            let approvedByCell = $("<td>").css("border", "1px solid #ddd").text(approvedByNameCode);
            newRow.append(approvedByCell);

            $("#application_process_info").append(newRow);
        }, 500);
    }
</script>
