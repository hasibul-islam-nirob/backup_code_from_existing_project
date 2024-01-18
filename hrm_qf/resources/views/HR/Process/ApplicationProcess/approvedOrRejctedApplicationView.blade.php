@include('HR.CommonBlade.detailsGrid',['title' => 'Application Details'])

@include('HR.CommonBlade.noteGrid')

<script>
    callApi("{{ url()->current() }}/../../../../get_appl_with_notes/{{ $applId }}/{{ $applType[1] }}/api", 'post', '',
        function(response, textStatus, xhr) {

            showDetailsData(response.result_data.application, response.applType);
            showNotes(response.result_data.notes);

            showModal({
                titleContent: "VIEW APPLICATION DETAILS",
            });

        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );

    function showDetailsData(response, applType) {

        let html = "";

        html += '<tr>' +
            '<td style="width: 20%"><b>' + applType[2] + ' Code</b></td>' +
            '<td style="width: 27%">' + response[applType[3] + '_code'] + '</td>' +
            '<td rowspan="3" style="width: 3%"></td>' +
            '<td style="width: 20%"><b>Employee</b></td>' +
            '<td style="width: 30%">' + response.employee.emp_name + " (" + response.employee.emp_code + ")" + '</td>' +
            '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Branch</b></td>' +
            '<td style="width: 27%">' + response.branch.branch_name + '</td>' +
            '<td style="width: 20%"><b>' + applType[2] + ' Date</b></td>' +
            '<td style="width: 30%">' + response[applType[3] + '_date'] + '</td>' +
            '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Expected Effective Date</b></td>' +
            '<td style="width: 27%">' + response.exp_effective_date + '</td>' +
            '<td style="width: 20%"><b>Reason</b></td>' +
            '<td style="width: 30%">' + response.reason + '</td>' +
            '</tr>';

        html += '<tr>' +
            '<td style="width: 20%"><b>Description</b></td>' +
            '<td colspan="4" style="width: 27%">' + response.description + '</td>';
        '</tr>';

        $('#details_table_body').html(html);
    }

    function showNotes(approvedData) {

        if (Array.isArray(approvedData) && approvedData.length) {

            $('#comment_div').show();

            let commentTableRows = "";
            $.each(approvedData, function(index, val) {
                commentTableRows += '<tr><td>' + (index + 1) + '</td><td>' + val.comment +
                    '</td><td class="text-center">' + ((val.related_data != null) ? val.related_data : "") + '</td><td>' + val.employee.emp_name +
                    "<br> (" + val.employee.emp_code + ")" + '</td><td class="text-center">' + val
                    .comment_date + '</td></tr>';
            });
            $('#view_comments_table').html(commentTableRows);

        } else {
            $('#comment_div').hide();
        }

    }
</script>
