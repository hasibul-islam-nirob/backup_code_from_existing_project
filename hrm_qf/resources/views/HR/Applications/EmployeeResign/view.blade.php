
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Resign Application Details'])
</div>

<div id="appView" style="display: none;">
    @include('HR.CommonBlade.applicationGrid')
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response.result_data['is_active'] == 0){//Draft view

                $("#draftView").show();

                showApplicantData(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Resign Application",
                    });
                    
                });
            }
            else if((response.result_data['is_active'] == 1)){//Approved 

                $("#appView").show();

                showApplication(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Resign Application",
                    });

                });
            }
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    
    async function showApplicantData(response){

        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>Resign Code</b></td>' +
            '<td style="width: 27%">'+ response.resign_code +'</td>' +
            '<td rowspan="4" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Employee</b></td>' +
            '<td style="width: 30%">'+ response.employee.emp_name + " (" + response.employee.emp_code + ")" +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch</b></td>' +
            '<td style="width: 27%">'+ response.branch.branch_name +'</td>' +
            '<td style="width: 20%"><b>Resign Date</b></td>' +
            '<td style="width: 30%">'+ response.resign_date +'</td>' +
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
        $('#application_header_date').html(convertDate(response.resign_date));
        $('#application_subject').html("Subject: Application for Resination");
        $('#application_body').html(response.description);
        $('#application_footer').html("Sincerely, <br>" + response.employee.emp_name);
    }
</script>