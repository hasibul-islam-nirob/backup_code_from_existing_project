@extends('Layouts.erp_master')
@section('content')

<?php 
    use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;

    $selectBranchArr = array();
    $branchData = DB::table('gnl_branchs')
        ->where([['is_delete', 0], ['is_active', 1]])
        ->whereIn('id', HRS::getUserAccesableBranchIds())
        ->select('id', 'branch_name')
        ->orderBy('id', 'ASC')
        ->get();
?>

<style>
    .checkbox_branch:hover {
        background: #3e8ef7;
        transition: 0.3s;
        color: white !important;
    }
    .text_dark {
        color: #526069;
    }
    .text_dark:hover {
        color: white !important;
        transition: 0.3s;
    }
</style>

<!-- Page -->
    <form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" 
    novalidate="true" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-lg-9 offset-lg-3">

                <!-- Html View Load  -->
                {!! HTML::forCompanyFeild() !!}

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Holiday Title</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="ch_title" id="ch_title"
                            placeholder="Enter comp Holiday Title"  
                            required data-error="Please enter comp Holiday Title.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Day</label>
                    <div class="col-lg-5 form-group">
                        <div class="row">
                            @foreach($days as $day)
                            <div class="col-lg-4">
                                <div class="input-group checkbox-custom checkbox-primary">
                                        <input type="checkbox" name="ch_day[]" id="ch_day_{{$day}}" value="{{ $day }}">
                                        <label for="ch_day_{{$day}}">{{ $day}}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Branch</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text rounded checkbox_branch text_dark">
                                <a href="javascript:void(0)" id="branch_search" class="text_dark addAction" data-toggle="modal" data-target="#company_branch">
                                    <i class="fa fa-university" aria-hidden="true"></i> Select Branch
                                </a>
                            </span>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Effective Date</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group ghdatepicker">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                </span>
                            </div>
                            <input type="text"class="form-control round datepickerNotRange" onchange="fnCheckDayEnd();"
                          
                            id="ch_eff_date" name="ch_eff_date" placeholder="DD-MM-YYYY"
                            required data-error="Please Select Date">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title" >Description</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <textarea class="form-control round" id="ch_description" name="ch_description" 
                                rows="2" placeholder="Enter Description"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="company_branch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header text-center">
                                <h4 class="modal-title font-weight-bold text-center">Select Branches</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                
                            <div class="modal-body mx-3" id="branch_modal_0">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" id="branch_array_0" name="branch_array[]" 
                                        <?= (count($selectBranchArr) > 0) ? ( (in_array("0", $selectBranchArr)) ? "checked" : "") : "checked" ?> 
                                        onclick="fnAllBranch();" value='0'
                                    />
                                    <label>All Branch</label>
                                </div>
                
                                @foreach ($branchData as $BRow)
                                    <div class="checkbox-custom checkbox-primary">
                
                                        <input type="checkbox" class="branch_cls_0" onclick="fnBranch();" id="branch_array_{{$BRow->id}}" name="branch_array[]"
                                        <?= (count($selectBranchArr) > 0) ? ( (in_array($BRow->id, $selectBranchArr)) ? "checked" : "") : "checked" ?>
                                        value="{{$BRow->id}}" />
                
                                        <label for="branch_array_{{$BRow->id}}">
                                            <small>{{$BRow->branch_name}}</small>
                                        </label>
                                        <br>
                                    </div>
                                @endforeach
                
                            </div>
                
                            <div class="modal-footer d-flex justify-content-center">
                                <div class="row align-items-center">
                                    <div class="col-lg-12">
                                        <div class="form-group d-flex justify-content-center">
                                            <div class="example example-buttons">
                                                <a href="javascript:void(0)" class="btn btn-default btn-round" data-dismiss="modal">Close</a>
                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group d-flex justify-content-center">
                            <div class="example example-buttons">
                                <a href="javascript:void(0)" class="btn btn-default btn-round" onclick="goBack()">Back</a>
                                <button type="submit" class="btn btn-primary btn-round">Save</button>
                                <!-- <a href="#"><button type="button" class="btn btn-warning btn-round">Next</button></a> -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
<!-- End Page -->

<script type="text/javascript">

    $('.modal-dialog').addClass('w-25');

    function fnAllBranch() {

        if ($('#branch_array_0').is(':checked')) {
            $('#branch_modal_0 input:checkbox').each(function() {
                $(this).prop("checked", true);
            });
        } else {
            $('#branch_modal_0 input:checkbox').each(function() {
                $(this).prop("checked", false);
            });
        }
    }

    function fnBranch() {

        var flag = true;
        $('.branch_cls_0').each(function() {
            if ($(this).is(':checked') == false) {
                flag = false;
            }
        });

        if (flag) {
            $('#branch_array_0').prop("checked", true);
        } else {
            $('#branch_array_0').prop("checked", false);
        }
    }
    $('form').submit(function (event) {
        $(this).find(':submit').attr('disabled', 'disabled');
    });

     //check Day End value on database in real time

    function fnCheckDayEnd() {
            var startDateFrom = $('#ch_eff_date').val();
            // console.log(startDateFrom);

            if (startDateFrom != '') {
                $.ajax({
                    type: "get",
                    url: "{{url('gnl/compholiday/CheckDayEnd')}}",
                    data: {
                        startDateFrom: startDateFrom,
                    },
                    dataType: "json",
                    success: function (data) {

                        if (data.Table == "DayEnd") {
                            swal({
                                title: "Day end exist, please select another day !!",
                                icon: "error",
                            });

                            $('#ch_eff_date').val('');
                        }else if(data.Table == "emptydata"){
                            swal({
                                title: "Date Empty, please select a date !!",
                                icon: "error",
                            });

                            $('#ch_eff_date').val('');
                        }
                    }
                });

            }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
       var startDateFrom = $('#ch_eff_date').val();
       // console.log(startDateFrom);

       if (startDateFrom != '') {
           $.ajax({
               type: "get",
               url: "{{url('gnl/compholiday/CheckDayEnd')}}",
               data: {
                   startDateFrom: startDateFrom,
               },
               dataType: "json",
               success: function (data) {

                   if (data.Table == "DayEnd") {
                       swal({
                           title: "Day end exist, please select another day !!",
                           icon: "error",
                       });

                       $('#ch_eff_date').val('');
                   }else if(data.Table == "emptydata"){
                       swal({
                           title: "Date Empty, please select a date !!",
                           icon: "error",
                       });

                       $('#ch_eff_date').val('');
                   }
               }
           });

       }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#ch_eff_date').val();
        // console.log(startDateFrom);

        if (startDateFrom != '') {
            $.ajax({
                type: "get",
                url: "{{url('gnl/compholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                },
                dataType: "json",
                success: function (data) {

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }else if(data.Table == "emptydata"){
                        swal({
                            title: "Date Empty, please select a date !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }
                }
            });

        }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#ch_eff_date').val();
        // console.log(startDateFrom);

        if (startDateFrom != '') {
            $.ajax({
                type: "get",
                url: "{{url('hr/compholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                },
                dataType: "json",
                success: function (data) {

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }else if(data.Table == "emptydata"){
                        swal({
                            title: "Date Empty, please select a date !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }
                }
            });

        }
    }
</script>

@endsection
