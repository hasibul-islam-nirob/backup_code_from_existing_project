
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Govt Holiday Details'])
</div>



<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response != ""){

                $("#draftView").show();
                showAttendanceRulesData(response).then(()=>{
                    
                    showModal({
                        titleContent: "View Govt Holiday",
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
            '<td style="width: 20%"><b>Title</b></td>' +
            '<td style="width: 27%">'+ response.gh_title +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Date</b></td>' +
            '<td style="width: 30%">'+ response.gh_date +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Start Date</b></td>' +
            '<td style="width: 30%">'+ viewDateFormat(response.efft_start_date) +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>End Date</b></td>' +
            '<td style="width: 30%">'+ viewDateFormat(response.efft_end_date) +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Description</b></td>' +
            '<td style="width: 30%">'+ response.gh_description +'</td>' +
        '</tr>';

        $('#details_table_body').html(html);
    }
    
</script>