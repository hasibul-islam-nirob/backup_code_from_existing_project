

<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Bank Details'])
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response != ""){

                $("#draftView").show();
                showAttendanceRulesData(response).then(()=>{
                    
                    showModal({
                        titleContent: "View Bank Rule",
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
            '<td style="width: 20%"><b>Bank Name</b></td>' +
            '<td style="width: 27%">'+ response.name +'</td>' +
            '<td rowspan="4" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Bank Address</b></td>' +
            '<td style="width: 30%">'+ response.address +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Bank Phone No</b></td>' +
            '<td style="width: 27%">'+ response.phone +'</td>' +
            '<td style="width: 20%"><b>Bank Email Address</b></td>' +
            '<td style="width: 30%">'+ response.email +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Contact Person</b></td>' +
            '<td style="width: 27%">'+ response.contact_person +'</td>' +
            '<td style="width: 20%"><b>Contact Person Designation</b></td>' +
            '<td style="width: 30%">'+ response.contact_person_designation +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Contact Person Phone</b></td>' +
            '<td style="width: 27%">'+ response.contact_person_phone +'</td>' +
            '<td style="width: 20%"><b>Contact Person Email Address</b></td>' +
            '<td style="width: 30%">'+ response.contact_person_email +'</td>' +
        '</tr>';
        

        $('#details_table_body').html(html);
    }

</script>
