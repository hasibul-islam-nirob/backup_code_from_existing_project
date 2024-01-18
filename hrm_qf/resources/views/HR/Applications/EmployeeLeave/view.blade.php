
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Leave Application Details'])
</div>

<div id="appView" style="display: none;">
    @include('HR.CommonBlade.applicationGrid')
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response.result_data['is_active'] == 0 || response.result_data['is_active'] == 2){//Draft view

                $("#draftView").show();

                showApplicantData(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Leave Application",
                    });
                    
                });
            }
            else if((response.result_data['is_active'] == 1) || (response.result_data['is_active'] == 3)){//Approved 
                // console.log(response);
                $("#appView").show();

                showApplication(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Leave Application",
                    });

                });
            }
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    
    async function showApplicantData(response){

        console.log("1"+response);

        if(response.employee == null){
            var empNameCode = 'All Employee';
        }else{
            var empNameCode = response.employee.emp_name + " (" + response.employee.emp_code + ")";
        };

        if(response.resp_emp_id == null){
            var resEmpNameCode = '-';
        }else{
            var resEmpNameCode =  response.resp_employee.emp_name + " (" + response.resp_employee.emp_code + ")";
        };

        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>Resign Code</b></td>' +
            '<td style="width: 27%">'+ response.leave_code +'</td>' +
            '<td rowspan="4" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Employee</b></td>' +
            '<td style="width: 30%">'+ empNameCode +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch</b></td>' +
            '<td style="width: 27%">'+ response.branch.branch_name +'</td>' +
            '<td style="width: 20%"><b>Application Date</b></td>' +
            '<td style="width: 30%">'+ response.leave_date +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Date From</b></td>' +
            '<td style="width: 27%">'+ response.date_from +'</td>' +
            '<td style="width: 20%"><b>Date To</b></td>' +
            '<td style="width: 30%">'+ response.date_to +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Responsible Person</b></td>' +
            '<td style="width: 30%">'+ resEmpNameCode +'</td>' +
            '<td style="width: 20%"><b>Attachment</b></td>' +
            '<td style="width: 30%"><a target="_blank" style="color: blue;" href='+ "{{ url('/') }}/" + response.attachment +'> Click here'+'</a></td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Description</b></td>' +
            '<td style="width: 27%">'+ response.description +'</td>' +
        '</tr>';

        $('#details_table_body').html(html);
    }

    async function showApplication(response){
        // console.log(response);
        // if(response.employee == null){
        //     var empNameCode = 'All Employees';
        // }else{
        //     var empNameCode = response.employee.emp_name + " (" + response.employee.emp_code + ")";
        // };

        // let resData = new Date(response.leave_date);

        // $('#application_header').html(resData.getDate() + "-" + resData.getMonth() + "-" + resData.getFullYear() + "<br><br>To<br>CEO<br>Tara Tech LTD <br>Flat - 7A, House-02, Road # 3, Block # C<br> Banasree, Rampura, Dhaka-1212,Bangladesh");
        // $('#application_subject').html("Subject: Application for Leave");
        // $('#application_body').html(response.description);
        // $('#application_footer').html("Sincerely, <br>" + empNameCode);


        // New Code Start
        if(response.employee == null){
            var empNameCode = 'All Employees';
        }else{
            var empNameCode = response.employee.emp_name + " (" + response.employee.emp_code + ")";
        };
        let leaveName = response.leave_category.name;
        let LeaveReason = response.reasons.reason;

        let commonInfo = 'I am writing to respectfully submit my <b>'+leaveName+' Application</b> for the reson is <b>'+LeaveReason+'</b>. That"s why sorry to say I am not attented in the office from date <b>'+convertDateFormatTwo(response.date_from,'/')+'</b> to <b>'+convertDateFormatTwo(response.date_to,'/')+'</b>. I am kindly request to you, please accept my application';

        $("#application_header_date").html('Date: '+dateTimeToDate(response.created_at, '/'))
        $('#application_subject').html("Subject: Application for "+leaveName);
        $('#application_body_common').html(commonInfo);
        $('#application_body').html(response.description);
        $('#application_footer').html("Sincerely, <br>" + empNameCode);

        let createdByNameCode = '';
        let approvedByNameCode = '';
        var createdById = response.created_by ? response.created_by.emp_id : null;
        var approvedById = response.approve_by ? response.approve_by.emp_id : null;
        if(createdById != null  ||  approvedById != null){
          
            callApi("{{ url()->current() }}/../../getEmpLeave/" +createdById+"/"+approvedById+"/api" , 'post', '',
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
        // New Code End
    }
</script>