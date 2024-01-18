
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Fiscal Year Details'])
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response != ""){

                $("#draftView").show();

                showAttendanceRulesData(response).then(()=>{
                    showModal({
                        titleContent: "View Fiscal Year",
                    });
                });
            }
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    
    async function showAttendanceRulesData(response){

        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>Fiscal Name</b></td>' +
            '<td style="width: 27%">'+ response.fy_name +'</td>' +
            '<td rowspan="4" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Fiscal For</b></td>' +
            '<td style="width: 30%">'+ response.fy_for +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Fiscal Start Date</b></td>' +
            '<td style="width: 27%">'+ response.fy_start_date +'</td>' +
            '<td style="width: 20%"><b>Fiscal End Date</b></td>' +
            '<td style="width: 30%">'+ response.fy_end_date +'</td>' +
        '</tr>';

        // if(response.eff_date_end != null) {
        //     '<td style="width: 20%"><b>Fiscal End Date</b></td>' +
        //     '<td style="width: 30%">'+ response.fy_end_date +'</td>' +
        // }

        $('#details_table_body').html(html);
    }

</script>