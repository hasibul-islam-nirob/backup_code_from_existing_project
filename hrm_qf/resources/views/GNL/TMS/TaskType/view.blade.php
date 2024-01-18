<div id="draftView" style="display: none;">
    <div class="row">
        <div class="col-lg-12">
            <h4 id="details_grid_header" style="background-color: #17b3a3; color:#fff; padding:10px 0 10px 10px;">Task Type Details</h4>
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
    
        </div>
    </div>
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response.result_data['is_active'] == 0 || response.result_data['is_active'] == 1){//Draft view

                $("#draftView").show();

                showTaskData(response).then(()=>{
                    showModal({
                        titleContent: "View Task Type",
                    });
                });
            }
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    
    async function showTaskData(response){
        console.log(response.result_data);
        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>Type Name</b></td>' +
            '<td style="width: 30%">'+ response.result_data.type_name +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Task Type Code</b></td>' +
            '<td style="width: 30%">'+ response.result_data.task_type_code +'</td>' +
        '</tr>';

        $('#details_table_body').html(html);
    }

</script>