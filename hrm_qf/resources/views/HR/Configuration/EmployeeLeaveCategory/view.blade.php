<style>
    .modal-lg {
        max-width: 60%;
    }
</style>

@include('HR.CommonBlade.detailsGrid', ['class' => 'text-center'])

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {
            console.log(response);
            showModal({
                titleContent: "View Leave Category",
            });

            showApplicantData(response.result_data);
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    
    async function showApplicantData(response){

        let html = "";

        $('#details_grid_header').html(response.name + ' ('+ response.short_form +') - ' + response.leave_type.name);

        if(response.leave_type_uid == 1){

            html += '<tr style = "background-color: #c7c6c3;">' +
                        '<th style="width:5%;" class="text-center">SL</th>' +
                        '<th style="width:12%;" class="text-center">Recruitment Type</th>' +
                        '<th style="width:35%;" colspan="2" class="text-center">Consume Policy</th>' +
                        '<th style="width:12%;" class="text-center">Application Submit Policy</th>' +
                        '<th style="width:12%;" class="text-center">Is Capable Of Provision Period?</th>' +
                        '<th style="width:12%;" class="text-center">Effective Date</th>' +
                        '<th style="width:12%;" class="text-center">Allocated Leaves <small style="text-transform: lowercase;">(yearly)</small></b></th>' +
                    '</tr>';

            $.each(response.leave_details, function(key, val){

                html += '<tr>' +
                            '<td style="width:5%;" class="text-center">'+ (key+1) +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ val.rec_type.title +'</td>' +
                            '<td style="width:17%;" class="text-center">'+ ((val.consume_policy == 'eligible') ? 'Eligible' : 'Yearly Allocated') +'</td>' +
                            '<td style="width:18%;" class="text-center">'+ ((val.remaining_leave_policy == 'flash') ? 'Flash' : 'Add Next Year') +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ ((val.app_submit_policy == 'before') ? 'Before' : 'After') +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ ((val.capable_of_provision == 1) ? 'Yes' : 'No') +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ convertDate(val.effective_date_from) +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ val.allocated_leave +' days</th>' +
                        '</tr>';
            });
        }
        else if(response.leave_type_uid == 3){
            html += '<tr style = "background-color: #c7c6c3;">' +
                        '<th style="width:5%;" class="text-center">SL</th>' +
                        '<th style="width:14%;" class="text-center">Recruitment Type</th>' +
                        '<th style="width:14%;" class="text-center">Eligibility Start From</th>' +
                        '<th style="width:14%;" class="text-center">Allocated Leaves <small style="text-transform: lowercase;">(yearly)</small></b></th>' +
                        '<th style="width:14%;" class="text-center">Max Leaves Entitled</th>' +
                        '<th style="width:13%;" class="text-center">Consume After</th>' +
                        '<th style="width:13%;" class="text-center">Effective From</th>' +
                        '<th style="width:13%;" class="text-center">Withdrawal Policy</th>' +
                    '</tr>';

            $.each(response.leave_details, function(key, val){

                html += '<tr>' +
                            '<td style="width:5%;" class="text-center">'+ (key+1) +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ val.rec_type.title +'</td>' +
                            '<td style="width:17%;" class="text-center">'+ val.eligibility_counting_from +'</td>' +
                            '<td style="width:18%;" class="text-center">'+ val.allocated_leave +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ val.max_leave_entitle +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ val.consume_after +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ convertDate(val.effective_date_from) +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ ((val.leave_withdrawal_policy == 'cash') ? 'Cash' : 'Non-Cash') +'</th>' +
                        '</tr>';
            });

        }
        else if(response.leave_type_uid == 4){

            html += '<tr style = "background-color: #c7c6c3;">' +
                        '<th style="width:5%;" class="text-center">SL</th>' +
                        '<th style="width:14%;" class="text-center">Recruitment Type</th>' +
                        '<th style="width:14%;" class="text-center">Allocated Leaves <small style="text-transform: lowercase;">(yearly)</small></b></th>' +
                        '<th style="width:13%;" class="text-center">Times of Leaves</th>' +
                        '<th style="width:13%;" class="text-center">Effective Date</th>' +
                    '</tr>';

            $.each(response.leave_details, function(key, val){

                html += '<tr>' +
                            '<td style="width:5%;" class="text-center">'+ (key+1) +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ val.rec_type.title +'</td>' +
                            '<td style="width:18%;" class="text-center">'+ val.allocated_leave +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ val.times_of_leave +'</td>' +
                            '<td style="width:12%;" class="text-center">'+ convertDate(val.effective_date_from) +'</td>' +
                        '</tr>';
            });

        }

        $('#details_table_body').html(html);
    }

</script>