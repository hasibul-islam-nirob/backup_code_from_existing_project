@php
use Illuminate\Support\Facades\DB;
use App\Services\HtmlService as HTML;

$department = DB::table('hr_departments')->where([['is_active',1], ['is_delete', 0]])->get();
$designations = DB::table('hr_designations')->where([['is_active',1], ['is_delete', 0]])->get();


$events = DB::table('hr_reporting_boss_event')->where('id',$eventId)->first();
$event_title = !empty($events) ? $events->event_title : '';

@endphp
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 30px;
        height: 18px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #635f8c;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 12px;
        width: 12px;
        left: 4px;
        bottom: 3.5px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(12px);
        -ms-transform: translateX(12px);
        transform: translateX(12px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .select2-container {
        z-index: 100000;
    }

    .modal-lg {
        max-width: 80%;
    }

    #section [class^="col-sm"] {
        padding-right: 0;
    }

    /* label {
        color:#37474f;
        font-weight: bold;
    } */

    .divHeader {
        background-color: #17b3a3;
        /* text-align: center; */
        color: #fff;
        border: 1px solid #fff;
        /* height: 35px; */
        padding-top: 1%;
    }

    .border {
        border: 1px solid #fff;
        border-bottom: none !important;
        border-left: none !important;
    }

    .configureTab {
        border: 1px solid #000;

        padding-top: 5px;
        padding-bottom: 10px;

        margin-top: 5px;
        margin-bottom: 10px;
    }

</style>

{{-- Dynamic Rows --}}
<div id="test_div">
    <div style="display: none;" id="dynamic_table_div" class="row align-items-center configureTab">

        <div class="col-sm-12 text-center text-dark">
            <strong>
                Configuration For
                <span class="designationForDiv" style="color:blue;">Designation</span>&nbsp; &
                <span class="departmentForDiv" style="color:blue;">Department</span>
            </strong>
        </div>

        <div class="col-sm-5 ho_section" style="border-right:1px solid #000;">

            <div class="row pt-5">
                <div class="col-sm-1">
                    <label class="input-title step-flag">1</label>
                    <input value="1" name="ho_level[]" class="level-flag cngName" hidden type="number">
                </div>

                <div class="col-sm-4 p-0">
                    {!! HTML::forDepartmentFieldHr(null, 'ho_department[]', 'cngName') !!}
                </div>

                <div class="col-sm-4 p-0">
                    {!! HTML::forDesignationFieldHr(null, 'ho_designation[]', 'cngName') !!}
                </div>

                <div class="col-sm-3">
                    <span>
                        <input hidden class="data-mod-flag-hidden cngName" value="0" name="ho_data-modification[]"
                            type="text">

                        <label class="switch">
                            <input onchange="data_mod_change(this)" class="data-mod-flag" type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </span>
                    &nbsp;&nbsp;
                    <span class="addBtnClass">
                        <a onclick="addRow(event, this)" class="btn btn-xs btn-round btn-primary">
                            <i class="fa fa-plus" style="color: #fff;"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-sm-7 branch_section" style="border-left:1px solid #000;">
            <div class="row pt-5">

                <div class="col-sm-1">
                    <label class="input-title step-flag">1</label>
                    <input value="1" name="bo_level[]" class="level-flag cngName" hidden type="number">
                </div>

                <div class="col-sm-3 p-0">
                    <select name="bo_from[]" class="form-control clsSelect2 cngName" style="width: 100%">
                        <option value="">Select</option>
                        <option value="ho">Head Office</option>
                        <option value="bo">Branch</option>
                    </select>
                </div>

                <div class="col-sm-3 p-0">
                    {!! HTML::forDepartmentFieldHr(null, 'bo_department[]', 'cngName') !!}
                </div>

                <div class="col-sm-3 p-0">
                    {!! HTML::forDesignationFieldHr(null, 'bo_designation[]', 'cngName') !!}
                </div>

                <div class="col-sm-2">
                    <span>
                        <input hidden class="data-mod-flag-hidden cngName" value="0" name="bo_data-modification[]"
                            type="text">
                        <label class="switch">
                            <input onchange="data_mod_change(this)" class="data-mod-flag" type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </span>

                    &nbsp;&nbsp;

                    <span class="addBtnClass">
                        <a onclick="addRow(event, this)" class="btn btn-xs btn-round btn-primary">
                            <i class="fa fa-plus" style="color: #fff;"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Dynamic Rows --}}

{{-- Add new form --}}
<form id="event_config_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <div class="container">

        <div class="row pb-10 align-items-center">
            <input name="event" hidden type="text" value="{{ $eventId }}">

            <div class="col-sm-12 col-md-12">
                <label class="h4 ">Configuration For  {{$event_title}} Application</label>
            </div>

            <div class="col-sm-3">
                <label class="input-title">Designation</label>
                <div class="input-group">
                    {{-- {!! HTML::forDesignationFieldHr('add_designation_for', 'designation_for', '', '', true) !!} --}}
                    <select id="add_designation_for" name="designation_for[]" multiple class="form-control clsSelect2" style="width: 100%">
                        <option value="0">All</option>
                        @foreach($designations as $desi)
                        <option value="{{$desi->id}}">{{$desi->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <label class="input-title">Department</label>
                <div class="input-group">
                    {{-- {!! HTML::forDepartmentFieldHr('add_department_for', '', '', '', true) !!} --}}
                    <select id="add_department_for" name="department_for" multiple class="form-control clsSelect2" style="width: 100%">
                        <option value="0">All</option>
                        @foreach($department as $dept)
                        <option value="{{$dept->id}}">{{$dept->dept_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-2" style="padding-top: 2%;">
                <a onclick="addTable(event);" class="btn btn-sm btn-primary" style="color: #fff;">
                    <i class="fa fa-plus"></i> &nbsp; Add
                </a>
            </div>
        </div>

        <div class="row text-dark">
            <small><i><b>DMP</b> = Data Modification Permission</i></small>
        </div>

        <div class="row align-items-center">
            <div class="col-sm-5">
                <div class="row divHeader" style="text-align: center;">
                    <label class="col-sm-12 text-center">Head Office</label>

                    <label class="col-sm-1 text-center border"><small>Step</small></label>
                    <label class="col-sm-4 text-center border"><small>Department</small></label>
                    <label class="col-sm-4 text-center border"><small>Designation</small></label>
                    <label class="col-sm-3 text-center border-top"><small>DMP</small></label>
                </div>
            </div>

            <div class="col-sm-7">
                <div class="row divHeader" style="text-align: center;">
                    <label class="col-sm-12 text-center">Branch Office</label>

                    <label class="col-sm-1 text-center border"><small>Step</small></label>
                    <label class="col-sm-3 text-center border"><small>From</small></label>
                    <label class="col-sm-3 text-center border"><small>Department</small></label>
                    <label class="col-sm-3 text-center border"><small>Designation</small></label>
                    <label class="col-sm-2 text-center border-top"><small>DMP</small></label>
                </div>
            </div>
        </div>

        <div id="add_dynamic_config_table_div">

        </div>

        <br>
    </div>
</form>
{{-- Add new form --}}

<script>
    showModal({
        titleContent: "Add New Configuration",
        footerContent: getModalFooterElement({
            'btnNature': {
                1: 'save',
            },
            'btnName': {
                1: 'Save',
            },
            'btnId': {
                1: 'addBtn',
            }
        }),
    });


    $('#addBtn').click(function(e) {
        callApi("{{ url()->current() }}/../../insert/api", "post", new FormData($('#event_config_add_form')[0]),
            function(response, textStatus, xhr) {

                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();

            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

    function addRow(event, node) {

        event.preventDefault();

        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }
        let element = $(node).parent().parent().parent().parent();

        let rmvBtn = '<a type="button" onclick="removeRow(this)" class="btn btn-xs btn-danger btn-round"><i class="fas fa-minus" style="color:#fff;"></i></a>';

        let divClone = element[0].lastElementChild.cloneNode(true);

        $(divClone).insertAfter($(element[0].lastElementChild));

        element[0].lastElementChild.lastElementChild.lastElementChild.innerHTML = rmvBtn;

        $('.clsSelect2').select2();
        setLevel(element);
    }

    function addTable(event) {

        let desForElem = $('#add_designation_for');
        let depForElem = $('#add_department_for');

        let desForId = desForElem.val();
        let depForId = depForElem.val();
        let prefix = desForId + "_" + depForId + "_";

        if (desForId != "" && depForId != "") {
            desForElem.parent().append('<input hidden name="designation_for" value="' + desForId + '">');
            depForElem.parent().append('<input hidden name="department_for[]" value="' + depForId + '">');
            desForElem.prop('disabled', true);
        } else if(desForId == "") {

            const wrapper = document.createElement('div');
            wrapper.innerHTML = 'Designation is required!';

            swal({
                icon: 'error',
                title: 'Oops...',
                content: wrapper,
            });
            return;

        } else if(depForId == "") {

            const wrapper = document.createElement('div');
            wrapper.innerHTML = 'Department is required!';

            swal({
                icon: 'error',
                title: 'Oops...',
                content: wrapper,
            });
            return;

        }

        event.preventDefault();

        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }

        let element = document.querySelector('#dynamic_table_div');
        let divClone = element.cloneNode(true);

        $(divClone).attr('id', prefix + 'dynamic_table_div');

        $(divClone).find('.cngName').each(function(index) {
            this.name = prefix + this.name;
        });

        $(divClone).find('.designationForDiv').html($('#add_designation_for option:selected').text());
        $(divClone).find('.departmentForDiv').html($('#add_department_for option:selected').text());
        $(divClone).show();

        document.querySelector('#add_dynamic_config_table_div').append(divClone);
        $('.clsSelect2').select2();

    }

    function data_mod_change(node) {

        if ($(node).prop('checked') === true) {
            $(node.parentElement.parentElement.firstElementChild).val(1);
        } else {
            $(node.parentElement.parentElement.firstElementChild).val(0);
        }
    }

    function removeRow(node) {
        let parent = $(node).parents()[3];

        node.parentElement.parentElement.parentElement.remove();

        setLevel($(parent));
    }

    function setLevel(element) {

        element.find(".step-flag").each(function(index) {
            $(this).html(index + 1);
        });
        element.find(".level-flag").each(function(index) {
            $(this).val(index + 1);
            console.log(this.name);
        });

    }
</script>
