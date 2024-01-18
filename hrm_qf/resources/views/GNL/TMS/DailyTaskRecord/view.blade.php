<div id="draftView" style="display: none;">
    <div class="row">
        <div class="col-lg-12">
            <h4 id="details_grid_header" style="background-color: #17b3a3; color:#fff; padding:10px 0 10px 10px;">Task Details</h4>
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
        {{-- <textarea class="form-control" id="instruction"></textarea> --}}

        </div>
    </div>
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response.result_data['is_active'] == 0 || response.result_data['is_active'] == 1){

                $("#draftView").show();

                showTaskData(response).then(()=>{

                    if($('#description').length ) {
                        CKEDITOR.replace( 'description' );
                    }

                    showModal({
                        titleContent: "View Task",
                    });

                });
            }
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );

    async function showTaskData(response){

        var status = "";
        if(response.result_data.status == 0){
            status = "Draft";
        } else if(response.result_data.status == 1){
            status = "Approve and Incomplete";
        } else if(response.result_data.status == 2){
            status = "Working";
        } else if(response.result_data.status == 5){
            status = "Completed";
        } else {
            status = "-";
        }


        let html = "";

        var assigned_by = (response.result_data.assigned_by) != null ? response.employeeInfo[response.result_data.assigned_by] : "-";
        var emp_id = (response.result_data.emp_id) != null ? response.employeeInfo[response.result_data.emp_id] : "-";

        html += '<tr>' +
            '<td style="width: 20%"><b>Task Title</b></td>' +
            '<td style="width: 30%">'+ response.result_data.task_title +'</td>' +
            '<td style="width: 3%"></td>' +
            // '<td style="width: 20%"><b>Task Code</b></td>' +
            // '<td style="width: 27%">'+ response.result_data.task_code +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Task Type</b></td>' +
            '<td style="width: 27%">'+ response.taskTypeInfo[response.result_data.task_type_id] +'</td>' +
            '<td style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Module</b></td>' +
            '<td style="width: 27%">'+ response.moduleName[response.result_data.module_id] +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Employee</b></td>' +
            '<td style="width: 30%">'+ emp_id +'</td>' +
            '<td style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Assigned By</b></td>' +
            '<td style="width: 30%">'+ assigned_by +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Task Date</b></td>' +
            '<td style="width: 27%">'+ viewDateFormat(response.result_data.task_date) +'</td>' +
            '<td style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Attachment</b></td>' +
            '<td style="width: 27%">';

        if(response.result_data.attachment != null){
            html += '<a href="' + response.result_data.attachment + '" target="_blank">View Attachment</a>';
        }

        html += '</td></tr>';

        html += '<tr><td colspan="5" style="width: 100%">&nbsp;</tr>';
        html += '<tr><td colspan="5" style="width: 100%"><b>Description</b></td></tr>';

        html += '<tr><td style="width: 100%" colspan="5">' +
                '<textarea class="form-control ckeditor" id="description" readonly>' + response.result_data.description + '</textarea>' +
                '</td></tr>';

        $('#details_table_body').html(html);
    }

</script>
