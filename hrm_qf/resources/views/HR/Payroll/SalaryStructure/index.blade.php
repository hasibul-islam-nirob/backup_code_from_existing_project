@extends('Layouts.erp_master_full_width')
@section('content')

<form id="salary_structure_search">
<!-- Search Option Start -->

    <div class="w-full show">
        <div class="panel">
            <div class="panel-body panel-search pt-2">

                @include('elements.common_filter_options', [
                    'payscale' => true,
                    'grade' => true,
                    'level' => true,
                    'recruitment_type' => true,
                ])

                @include('elements.report.company_header', [
                'reportTitle' => 'Salary Structure',
                'title_excel' => 'Salary_Structure',
                'printIcon' => true,
                ])

                <div class="row ExportDiv">
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12" id="ss_body">

                    </div>
                </div>

            </div>
        </div>
    </div>

</form>


<!-- End Page -->
<script>
    $(document).ready(function() {

        $('.ajaxRequest').hide();
        $('.httpRequest').hide(); //Hide new entry button

        $('#searchFieldBtn').click(function(event){
            $("#ss_body").load('{{URL::to("hr/payroll/salary_structure/body")}}'+'?'+$("#salary_structure_search").serialize());
        });

    });

    $("#payscale_id").change(function(event){
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ url()->current() }}/getData",
            data: {context : 'getData', payScaleId : $("#payscale_id").val()},
            dataType: "json",
            success: function (response) {

                if(response.grade){
                    $('#grade').empty().append($('<option>', {
                            value: '',
                            text: 'Select grade'
                    }));;
                    $.each(response.grade, function(i, item) {
                        $('#grade').append($('<option>', {
                            value: item,
                            text: item
                        }));
                    });
                }


            },
            error: function(){
                alert('error!');
            }
        });
    });

    $("#grade").change(function(event){
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ url()->current() }}/getData",
            data: {context : 'getData', payScaleId : $("#payscale_id").val(), grade:$("#grade").val()},
            dataType: "json",
            success: function (response) {
                if (response.level) {
                    $('#level').empty();
                    $.each(response.level, function(i, item) {
                        $('#level').append($('<option>', {
                            value: item,
                            text: item
                        }));
                    });
                }

                if(response.recruitment_type_id){
                    $('#recruitment_type_id').empty();
                    
                    $.each(response.recruitment_type, function(i, item) {
                        $.each(response.recruitment_type_id, function(j, item2) {

                           if (i == item2) {
                                $('#recruitment_type_id').append($('<option>', {
                                    value: item2,
                                    text: item
                                }));
                           }

                        });
                    });
                }

            },
            error: function(){
                alert('error!');
            }
        });
    });

</script>
@endsection
