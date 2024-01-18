
<div id="draftView" style="display: none;">
    <div class="row">
        <div class="col-lg-12">
            <h4 id="details_grid_header" style="background-color: #17b3a3; color:#fff; padding:10px 0 10px 10px;">Terminate Application Details</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped">
                <thead id="details_table_head">
    
                </thead>
                <tbody id="details_table_body">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="appView" style="display: none;">
    <div class="row">
        <div class="col-sm-12" style="padding-left: 60px; padding-top: 30px">
            <div id="application_header">
                    
            </div>
    
            <div style="padding-top: 10px; padding-bottom: 10px;">
                <strong id="application_subject"></strong>
            </div>
    
            <div>
                <p>
                    Dear Sir,
                </p>
    
                <p id="application_body">
                    
                </p>
            </div>
    
            <div id="application_footer">
                
            </div>
        </div>
    </div>
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response.result_data['is_active'] == 0 || response.result_data['is_active'] == 1){//Draft view

                $("#draftView").show();

                showApplicantData(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Terminate Application",
                    });
                    
                });
            }
            /* else if((response.result_data['is_active'] == 1)){//Approved 

                $("#appView").show();

                showApplication(response.result_data).then(()=>{

                    showModal({
                        titleContent: "View Terminate Application",
                    });

                });
            } */
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    
    async function showApplicantData(response){

        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>Terminate Code</b></td>' +
            '<td style="width: 27%">'+ response.terminate_code +'</td>' +
            '<td rowspan="3" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Employee</b></td>' +
            '<td style="width: 30%">'+ response.employee.emp_name + " (" + response.employee.emp_code + ")" +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch</b></td>' +
            '<td style="width: 27%">'+ response.branch.branch_name +'</td>' +
            '<td style="width: 20%"><b>Terminate Date</b></td>' +
            '<td style="width: 30%">'+ response.terminate_date +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Expected Effective Date</b></td>' +
            '<td style="width: 27%">'+ response.exp_effective_date +'</td>' +
            '<td style="width: 20%"><b>Attachment</b></td>' +
            '<td style="width: 30%"><a target="_blank" style="color: blue;" href='+ "{{ url('/') }}/" + response.attachment +'> Click here'+'</a></td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Reason</b></td>' +
            '<td style="width: 30%">'+ response.reason +'</td>' +
        '</tr>';

        $('#details_table_body').html(html);
    }

    async function showApplication(response){
        let resData = new Date(response.terminate_date);

        $('#application_header').html(resData.getDate() + "-" + resData.getMonth() + "-" + resData.getFullYear() + "<br><br>To<br>CEO<br>Garnish Technology <br>Flat - A4, House-27/1,Road # 3<br> Shayamoli, Dhaka – 1207,Bangladesh");
        $('#application_subject').html("Subject: Application for Termination");
        $('#application_body').html(response.description);
        $('#application_footer').html("Sincerely, <br>" + response.employee.emp_name);
    }
</script>