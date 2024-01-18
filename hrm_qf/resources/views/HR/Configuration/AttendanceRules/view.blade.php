
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Attendance Rule Details'])
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response != ""){

                $("#draftView").show();

                showAttendanceRulesData(response).then(()=>{
                    
                    showModal({
                        titleContent: "View Attendance Rule",
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
            '<td style="width: 20%"><b>Duty Start Time</b></td>' +
            '<td style="width: 27%">'+ response.start_time +'</td>' +
            '<td rowspan="4" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Duty End Time</b></td>' +
            '<td style="width: 30%">'+ response.end_time +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Early Accepted (<small>Minutes</small>)</b></td>' +
            '<td style="width: 27%">'+ response.early_accept_minute +'</td>' +
            '<td style="width: 20%"><b>Over Time Cycle (<small>Minutes</small>)</b></td>' +
            '<td style="width: 30%">'+ response.ot_cycle_minute +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Late Accepted (<small>Minutes</small>)</b></td>' +
            '<td style="width: 27%">'+ response.late_accept_minute +'</td>' +
            '<td style="width: 20%"><b>Effective Date Start</b></td>' +
            '<td style="width: 30%">'+ response.eff_date_start +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Late Present Accepted</b></td>' +
            '<td style="width: 27%">'+ response.lp_accept +'</td>' +
            '<td style="width: 20%"><b>Acction For Late Present</b></td>' +
            '<td style="width: 30%">'+ response.acction_for_lp +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Leave Accepted Each Month</b></td>' +
            '<td style="width: 30%">'+ response.leave_allow +'</td>' +
        '</tr>';

        if(response.eff_date_end != null){

            html += '<tr>' +
                '<td style="width: 20%"><b>Effective Date End</b></td>' +
                '<td style="width: 27%">'+ response.eff_date_end +'</td>' +
            '</tr>';
        }

        $('#details_table_body').html(html);
    }

</script>