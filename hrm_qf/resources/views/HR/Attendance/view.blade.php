@include('HR.CommonBlade.detailsGrid',['title' => 'Attendance Details'])

<script>
    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',
        function(response, textStatus, xhr) {
console.log(response);
            showApplicantData(response.result_data).then(() => {

                showModal({
                    titleContent: "View attendance",
                });

            });
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );

    async function showApplicantData(response) {

        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>Employee</b></td>' +
            '<td style="width: 27%">' + response.employee.emp_name + " (" + response.employee.emp_code + ")" + '</td>' +
            '<td rowspan="3" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Device</b></td>' +
            '<td style="width: 30%">' + response.device_id +
            '</td>' +
            '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Date & Time</b></td>' +
            '<td style="width: 27%">' + response.time_and_date + '</td>' +
            '</tr>';

        $('#details_table_body').html(html);
    }
</script>
