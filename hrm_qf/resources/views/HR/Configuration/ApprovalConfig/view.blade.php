<?php
use App\Services\HtmlService as HTML;
use Illuminate\Support\Facades\DB;

$conArr = explode('-', $con);
$events = DB::table('hr_reporting_boss_event')->where('id',$conArr[0])->first();
$event_title = !empty($events) ? $events->event_title : '';
?>

<style>
    .modal-lg {
        max-width: 95%;
    }

</style>
<div id="detailsView">
    @include('HR.CommonBlade.detailsGrid',['title' => $event_title.' Approval Configuration Details'])
</div>

<script>

    var elementDataArr_view;
    var isProcessing_view = false;
    $("table").on("click", ".viewAction",function () {
        if (!isProcessing_view) {
            isProcessing_view = true;
            elementDataArr_view = [];
            var parentTr_view = $(this).closest("tr");
            var tdElements_view = parentTr_view.find("td");

            tdElements_view.each(function () {
                var tdData_view = $(this).text();
                elementDataArr_view.push(tdData_view);
            });

            setTimeout(function() {
                isProcessing_view = false;
            }, 500);
        }
    });
    console.log(elementDataArr_view);
   
    if(typeof elementDataArr_view != 'undefined' && elementDataArr_view.length > 0){

        callApi("{{ url()->current() }}/../../get/{{ $con }}/"+elementDataArr_view+"/api", 'post', '',
            function(response, textStatus, xhr) {

                showDetailsData(response.result_data).then(() => {

                    showModal({
                        titleContent: "View  Approval Configuration Details",
                    });

                });
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

    }else{
        console.log('Please try again...');
        // swal({
        //     icon: 'warning',
        //     title: 'Please try again...',
        // });
    }

    

    async function showDetailsData(response) {

        let html_b = "";
        let html_h = "";

        html_h += '<tr><th colspan = "4" style="width:45%">Head Office</th>' +
            '<th colspan = "5" style="width:55%">Branch Office</th></tr>';

        html_h += '<tr>' +

            '<th style="width:5%;">Step</th>' +
            '<th style="">Department</th>' +
            '<th style="">Designation</th>' +
            '<th style="">DMP</th>' +

            '<th style="width:5%;">Step</th>' +
            '<th style="">Department</th>' +
            '<th style="">Designation</th>' +
            '<th style="">Employee From</th>' +
            '<th style="">DMP</th>' +

            '</tr>';

        $.each(response, function(index, data) {


            html_b += '<tr>' +
                '<td class="text-center h4" colspan = "9">Configuration for <strong style="color:blue;">'+ data[0].designation_for.name +'</strong> & <strong style="color:blue;">'+ data[0].department_for.dept_name +'</strong></td>' +
                '</tr>';

            const hoArr = [];
            const boArr = [];
            let len = 0;

            $.each(data, function(index, val) {
                if (val.permission_for === 'ho') {
                    hoArr.push(val);
                } else {
                    boArr.push(val);
                }
            });

            if (hoArr.length > boArr.length) {
                len = hoArr.length;
            } else {
                len = boArr.length;
            }

            for (let i = 0; i < len; i++) {

                html_b += '<tr>' +

                    '<th style="width:5%;">' + ((typeof hoArr[i] !== "undefined") ? hoArr[i].level : "") +
                    '</th>' +
                    '<th style="">' + ((typeof hoArr[i] !== "undefined") ? hoArr[i].department.dept_name :
                        "") + '</th>' +
                    '<th style="">' + ((typeof hoArr[i] !== "undefined") ? hoArr[i].designation.name : "") +
                    '</th>' +
                    '<th style="">' + ((typeof hoArr[i] !== "undefined") ? hoArr[i].data_modification :
                        "") + '</th>';


                html_b += '<th style="width:5%;">' + ((typeof boArr[i] !== "undefined") ? boArr[i].level :
                        "") + '</th>' +
                    '<th style="">' + ((typeof boArr[i] !== "undefined") ? boArr[i].department.dept_name :
                        "") + '</th>' +
                    '<th style="">' + ((typeof boArr[i] !== "undefined") ? boArr[i].designation.name : "") +
                    '</th>' +
                    '<th style="">' + ((typeof boArr[i] !== "undefined") ? boArr[i].employee_from : "") +
                    '</th>' +
                    '<th style="">' + ((typeof boArr[i] !== "undefined") ? boArr[i].data_modification :
                        "") + '</th>' +

                    '</tr>';

            }
        });

        $('#details_table_head').html(html_h);
        $('#details_table_body').html(html_b);
    }
</script>
