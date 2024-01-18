
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Increment Application Details'])
</div>

<div id="appView" style="display: none;">
    @include('HR.CommonBlade.applicationGrid')
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response.result_data['is_active'] == 0 || response.result_data['is_active'] == 1){//Draft view

                $("#draftView").show();

                showApplicantData(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Increment Application",
                    });
                    
                });
            }
            // else if((response.result_data['is_active'] == 1)){//Approved 

            //     $("#appView").show();

            //     showApplication(response.result_data).then(()=>{

            //         showModal({
            //             titleContent: "View Increment Application",
            //         });

            //     });
            // } 
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    
    async function showApplicantData(response){
        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>Increment Code</b></td>' +
            '<td style="width: 27%">'+ response.increment_code +'</td>' +
            '<td rowspan="6" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Employee</b></td>' +
            '<td style="width: 30%">'+ response.employee.emp_name + " (" + response.employee.emp_code + ")" +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch</b></td>' +
            '<td style="width: 27%">'+ response.branch.branch_name +'</td>' +
            '<td style="width: 20%"><b>Increment Date</b></td>' +
            '<td style="width: 30%">'+ response.increment_date +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Current salary</b></td>' +
            '<td style="width: 27%">'+ response.current_salary +'</td>' +
            '<td style="width: 20%"><b>Requested salary</b></td>' +
            '<td style="width: 30%">'+ response.requested_salary +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Expected Effective Date</b></td>' +
            '<td style="width: 27%">'+ response.exp_effective_date +'</td>' +
            '<td style="width: 20%"><b>Reason</b></td>' +
            '<td style="width: 30%">'+ response.reason +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Description</b></td>' +
            '<td style="width: 27%">'+ response.description +'</td>' +
            '<td style="width: 20%"><b>Attachment</b></td>' +
            '<td style="width: 30%"><a target="_blank" style="color: blue;" href='+ "{{ url('/') }}/" + response.attachment +'> Click here'+'</a></td>' +
        '</tr>';

        $('#details_table_body').html(html);
    }

    async function showApplication(response){
        let resData = new Date(response.retirement_date);

        $('#application_header').html(resData.getDate() + "-" + resData.getMonth() + "-" + resData.getFullYear() + "<br><br>To<br>CEO<br>Garnish Technology <br>Flat - A4, House-27/1,Road # 3<br> Shayamoli, Dhaka – 1207,Bangladesh");
        $('#application_subject').html("Subject: Application for Increment");
        $('#application_body').html(response.description);
        $('#application_footer').html("Sincerely, <br>" + response.employee.emp_name);
    }
</script>