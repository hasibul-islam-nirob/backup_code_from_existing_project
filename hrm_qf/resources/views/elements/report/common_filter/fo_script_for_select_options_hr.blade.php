@php

if(count(Request::all())>0){
    $requestData = Request::all();
}else{
    $requestData = null;
}

use App\Services\CommonService as Common;

@endphp

<script type="text/javascript">

        var requestDataH = {!! json_encode($requestData) !!};
        requestDataH = (requestDataH != null)?  requestDataH : [];

        var departmentG = {'exist': false};
        var designationG = {'exist': false};
        var employeeG = {'exist': false};
        var fiscalYearG = {'exist': false};
        var searchByG = {'exist': false};
        var currentYearG = {'exist': false};
        var fiscalYearDateRangeG = {'exist': false};

        //##departmentG check by assumed id
        @if(isset($elements['department']))
            departmentG['exist'] = true;
            departmentG['id'] = "{{$elements['department']['id']}}";
            departmentG['name'] = "{{$elements['department']['name']}}";
            departmentG['type'] = "{{$elements['department']['type']}}";
            departmentG['onload'] = "{{isset($elements['department']['onload'])? $elements['department']['onload'] : 0}}";
            if (requestDataH.hasOwnProperty(departmentG['name'])) {
                departmentG['selected'] = requestDataH[departmentG['name']];
            }
        @endif

        //##designationG check by assumed id
        @if(isset($elements['designation']))
            designationG['exist'] = true;
            designationG['id'] = "{{$elements['designation']['id']}}";
            designationG['name'] = "{{$elements['designation']['name']}}";
            designationG['type'] = "{{$elements['designation']['type']}}";
            designationG['onload'] = "{{isset($elements['designation']['onload'])? $elements['designation']['onload'] : 0}}";
            if (requestDataH.hasOwnProperty(designationG['name'])) {
                designationG['selected'] = requestDataH[designationG['name']];
            }
        @endif

        //##employeeG check by assumed id
        @if(isset($elements['employee']))
            employeeG['exist'] = true;
            employeeG['id'] = "{{$elements['employee']['id']}}";
            employeeG['name'] = "{{$elements['employee']['name']}}";
            employeeG['type'] = "{{$elements['employee']['type']}}";
            employeeG['onload'] = "{{isset($elements['employee']['onload'])? $elements['employee']['onload'] : 0}}";
            if (requestDataP.hasOwnProperty(employeeG['name'])) {
                employeeG['selected'] = requestDataP[employeeG['name']];
            }
        @endif

        //##employeeG check by assumed id
        @if(isset($elements['fiscalYear']))
            fiscalYearG['exist'] = true;
            fiscalYearG['id'] = "{{$elements['fiscalYear']['id']}}";
            fiscalYearG['name'] = "{{$elements['fiscalYear']['name']}}";
            fiscalYearG['type'] = "{{$elements['fiscalYear']['type']}}";
            fiscalYearG['onload'] = "{{isset($elements['fiscalYear']['onload'])? $elements['fiscalYear']['onload'] : 0}}";
            if (requestDataP.hasOwnProperty(fiscalYearG['name'])) {
                fiscalYearG['selected'] = requestDataP[fiscalYearG['name']];
            }
        @endif


        @if(isset($elements['searchBy']))
            searchByG['exist'] = true;
            searchByG['id'] = "{{$elements['searchBy']['id']}}";
            searchByG['name'] = "{{$elements['searchBy']['name']}}";
            searchByG['type'] = "{{$elements['searchBy']['type']}}";
            searchByG['onload'] = "{{isset($elements['searchBy']['onload'])? $elements['searchBy']['onload'] : 0}}";
            if (requestDataC.hasOwnProperty(searchByG['name'])) {
                searchByG['selected'] = requestDataC[searchByG['name']];
            }
            
        @endif
       

</script>

<script type="text/javascript">

    $(document).ready(function() {

        if (departmentG['exist'] == true && departmentG['onload'] != 0) {
            fnAjaxGetDepartment();
        }

        if (designationG['exist'] == true && designationG['onload'] != 0) {
            fnAjaxGetDesignation();
        }

        if (employeeG['exist'] == true && employeeG['onload'] != 0) {
            fnAjaxGetEmployee();
        }

        if (fiscalYearG['exist'] == true && fiscalYearG['onload'] != 0) {
            fnAjaxGetFiscalYear();
            // $("#startDate, #endDate").attr("readonly", true);
        }
    });

    if (searchByG['exist'] == true){

        var currentYearG = fiscalYearDateRangeG = 1;

        /* for Fiscal / Current / serchby */
        $('#search_by').change(function () {
            /* for Fiscal / Current / serchby */
            
            var searchByLoad = $('#search_by').val();

            if (searchByLoad == "1" || searchByLoad == "5") {
                fnAjaxFiscalYear();
            } else if (searchByLoad == "2") {
                currentYearG = 1;
                fnAjaxCurrentFY();
            } else {
                fnForSearchBy();
            }
        });

        $("#fiscal_year").change(function () {
            fnForSearchBy();
        });

        if (currentYearG == 1 || fiscalYearDateRangeG == 1) {
            $("#end_date_cy").datepicker({
                dateFormat: 'dd-mm-yy',
                orientation: 'bottom',
                autoclose: true,
                todayHighlight: true,
                changeMonth: true,
                changeYear: true,
            });
        }
    }


    // if (employeeG['exist'] == true){

    //     $("#"+employeeG['id']).change(function(e){

    //         if(productG['exist'] == true){
    //             fnAjaxGetProduct();
    //         }
    //     });
    // }

</script>

<script>
    /* Get department and designation */

    function fnAjaxGetDepartment() {

        let selectedValue = $("#"+departmentG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(departmentG['exist'] == true && typeof departmentG['selected'] != "undefined" && departmentG['selected'] != ''){
                selectedValue = departmentG['selected'];
            }
        }

        $("#"+departmentG['id']).empty().append($('<option>', {
            value: "",
            text: "All"
        }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxDepartmentData') }}",
            dataType: "json",
            data: {
                returnType: 'json'
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    
                    let result_data = response['result_data'];
                    let idArr = [];   // New code for zone

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#" + departmentG['id']).append($('<option>', {
                            value: item.id,
                            text: item.dept_name,
                        }));
                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#" + departmentG['id']).val(selectedValue);

                        if (areaG['exist'] == true){
                            $("#" + departmentG['id']).trigger("change");
                        }

                    }else{
                        $("#" + departmentG['id']).val();
                    }

                }
            }
        });
    }

    function fnAjaxGetDesignation() {

  
        let selectedValue = $("#" + designationG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(designationG['exist'] == true && typeof designationG['selected'] != "undefined" && designationG['selected'] != ''){
                selectedValue = designationG['selected'];
            }
        }

        $("#"+designationG['id']).empty().append($('<option>', {
            value: "",
            text: "All"
        }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxDesignationData') }}",
            dataType: "json",
            data: {
                returnType: 'json'
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['designation_data'];
                    let idArr = [];   // New code for zone

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#"+designationG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name ,

                        }));
                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#"+designationG['id']).val(selectedValue);

                        if (areaG['exist'] == true){
                            $("#"+designationG['id']).trigger("change");
                        }

                    }else{
                        $("#"+designationG['id']).val();
                    }

                }
            }
        });
    }

    function fnAjaxGetEmployee(moduleName = null) {

        let selectedValue = $('#'+employeeG['id']).val();
        var branchId = branchG['selected'];

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(employeeG['exist'] == true && typeof employeeG['selected'] != "undefined" && employeeG['selected'] != ''){
                selectedValue = employeeG['selected'];
            }
        }

        var posModule =  false;
        @if(Common::getModuleByRoute() == "pos")
            posModule  =  true;
        @endif

        $.ajax({
            method: "GET",
            url: "{{url('/ajaxGetEmployeeName')}}",
            dataType: "text",
            async:false,
            data: {
                branchId: $('#branch_id').val(),
                posModule: posModule
            },
            success: function(data) {

                $('#'+employeeG['id']).empty();
                $('#'+employeeG['id']).append(data);

                if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                    $('#'+employeeG['id']).val(selectedValue);

                    if (productG['exist'] == true){
                        $("#"+employeeG['id']).trigger("change");
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("Status: " + textStatus);
                console.log("Error: " + errorThrown);
            }
        });
    }

    function fnAjaxGetFiscalYear() {

        
        let selectedValue = $("#" + fiscalYearG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(fiscalYearG['exist'] == true && typeof fiscalYearG['selected'] != "undefined" && fiscalYearG['selected'] != ''){
                selectedValue = fiscalYearG['selected'];
            }
        }

        // $("#"+fiscalYearG['id']).empty().append($('<option>', {
        //     value: "",
        //     text: "All"
        // }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxFiscalYearData') }}",
            dataType: "json",
            data: {
                returnType: 'json'
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['fiscal_year_data'];
                    let idArr = [];   // New code for zone

                    $.each(result_data, function (i, item) {

                        console.log(item);

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#"+fiscalYearG['id']).append($('<option>', {
                            value: item['id'],
                            text: item['fy_name'],

                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#"+fiscalYearG['id']).val(selectedValue);

                        if (areaG['exist'] == true){
                            $("#"+fiscalYearG['id']).trigger("change");
                        }

                    }else{
                        $("#"+fiscalYearG['id']).val();
                    }

                }
            }
        });
    }


</script>
