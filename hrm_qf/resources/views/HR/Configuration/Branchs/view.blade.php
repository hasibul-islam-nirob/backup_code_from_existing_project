



<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Branch Details'])
</div>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            if(response != ""){

                $("#draftView").show();

                showAttendanceRulesData(response).then(()=>{
                    
                    showModal({
                        titleContent: "View Branch Rule",
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
            '<td style="width: 27%">'+ response.branchData['bank_name'] +'</td>' +
            '<td rowspan="4" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Branch Name</b></td>' +
            '<td style="width: 30%">'+ response.branchData['name'] +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch Phone No</b></td>' +
            '<td style="width: 27%">'+ response.branchData['phone'] +'</td>' +
            '<td style="width: 20%"><b>Branch Email Address</b></td>' +
            '<td style="width: 30%">'+ response.branchData['email'] +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch Contact Person</b></td>' +
            '<td style="width: 27%">'+ response.branchData['contact_person'] +'</td>' +
            '<td style="width: 20%"><b>Branch Contact Person Designation</b></td>' +
            '<td style="width: 30%">'+ response.branchData['contact_person_designation'] +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch Contact Person Phone</b></td>' +
            '<td style="width: 27%">'+ response.branchData['contact_person_phone'] +'</td>' +
            '<td style="width: 20%"><b>Branch Contact Person Email Address</b></td>' +
            '<td style="width: 30%">'+ response.branchData['contact_person_email'] +'</td>' +
        '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch Address</b></td>' +
            '<td style="width: 27%">'+ response.branchData['address'] +'</td>' +
            // '<td rowspan="4" style="width: 3%"></td>' +
            // '<td style="width: 20%"><b>Contact Person Email Address</b></td>' +
            // '<td style="width: 30%">'+ response.branchData['contact_person_email'] +'</td>' +
        '</tr>';
        

        $('#details_table_body').html(html);
    }

</script>
