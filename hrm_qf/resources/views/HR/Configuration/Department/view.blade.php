
<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Departments Details'])
</div>



<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response != ""){

                $("#draftView").show();
                showAttendanceRulesData(response).then(()=>{
                    
                    showModal({
                        titleContent: "View Departments Rule",
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
            '<td style="width: 20%"><b>Department Name</b></td>' +
            '<td style="width: 27%">'+ response.dept_name +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Short Name</b></td>' +
            '<td style="width: 27%">'+ response.short_name +'</td>' +
        '</tr>';

        $('#details_table_body').html(html);
    }

</script>