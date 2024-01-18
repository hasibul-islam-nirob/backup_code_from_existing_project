@php

    if(count(Request::all()) > 0){
        $requestData = Request::all();
    }else{
        $requestData = null;
    }

@endphp

<script type="text/javascript">

    var requestDataM     = {!! json_encode($requestData) !!};
    requestDataM         = (requestDataM != null)?  requestDataM : [];
    var buildingG               = {'exist': false};
    var floorG                  = {'exist': false};
    var roomG                   = {'exist': false};
    var seatG                   = {'exist': false};
    var statusG                 = {'exist': false};
    var promoStatusG            = {'exist': false};
    var academicDepartmentG     = {'exist': false};
    var academicStatusG         = {'exist': false};
    var academicSessionG        = {'exist': false};
    var fiscalYearFFYG          = {'exist': false};
    var studentG                = {'exist': false};
    var academicYearG            = {'exist': false};
    var academicpackagesG      = {'exist': false};
    var invoicestatusG         = {'exist': false};

    // ##buildingG check by assumed id
    @if(isset($elements['building']))
        buildingG['exist'] = true;
        buildingG['id'] = "{{ $elements['building']['id'] }}";
        buildingG['name'] = "{{ $elements['building']['name'] }}";
        buildingG['type'] = "{{ $elements['building']['type'] }}";
        buildingG['onload'] = "{{isset($elements['building']['onload'])? $elements['building']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(buildingG['name'])) {
            buildingG['selected'] = requestDataM[buildingG['name']];
        }
    @endif

    @if(isset($elements['floor']))
        floorG['exist'] = true;
        floorG['id'] = "{{ $elements['floor']['id'] }}";
        floorG['name'] = "{{ $elements['floor']['name'] }}";
        floorG['type'] = "{{ $elements['floor']['type'] }}";
        floorG['onload'] = "{{ isset($elements['floor']['onload'])? $elements['floor']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(floorG['name'])) {
            floorG['selected'] = requestDataM[floorG['name']];
        }
    @endif

    @if(isset($elements['room']))
        roomG['exist'] = true;
        roomG['id'] = "{{ $elements['room']['id'] }}";
        roomG['name'] = "{{ $elements['room']['name'] }}";
        roomG['type'] = "{{ $elements['room']['type'] }}";
        roomG['onload'] = "{{ isset($elements['room']['onload'])? $elements['room']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(roomG['name'])) {
            roomG['selected'] = requestDataM[roomG['name']];
        }
    @endif

    @if(isset($elements['seat']))
        seatG['exist'] = true;
        seatG['id'] = "{{ $elements['seat']['id'] }}";
        seatG['name'] = "{{ $elements['seat']['name'] }}";
        seatG['type'] = "{{ $elements['seat']['type'] }}";
        seatG['onload'] = "{{ isset($elements['seat']['onload'])? $elements['seat']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(seatG['name'])) {
            seatG['selected'] = requestDataM[seatG['name']];
        }
    @endif

    @if(isset($elements['studentStatus']))
        statusG['exist'] = true;
        statusG['id'] = "{{ $elements['studentStatus']['id'] }}";
        statusG['name'] = "{{ $elements['studentStatus']['name'] }}";
        statusG['type'] = "{{ $elements['studentStatus']['type'] }}";
        statusG['onload'] = "{{ isset($elements['studentStatus']['onload'])? $elements['studentStatus']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(statusG['name'])) {
            statusG['selected'] = requestDataM[statusG['name']];
        }
    @endif

    @if(isset($elements['promotionStatus']))
        promoStatusG['exist'] = true;
        promoStatusG['id'] = "{{ $elements['promotionStatus']['id'] }}";
        promoStatusG['name'] = "{{ $elements['promotionStatus']['name'] }}";
        promoStatusG['type'] = "{{ $elements['promotionStatus']['type'] }}";
        promoStatusG['onload'] = "{{ isset($elements['promotionStatus']['onload'])? $elements['promotionStatus']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(promoStatusG['name'])) {
            promoStatusG['selected'] = requestDataM[promoStatusG['name']];
        }
    @endif

    @if(isset($elements['academicDepartment']))
        academicDepartmentG['exist'] = true;
        academicDepartmentG['id'] = "{{ $elements['academicDepartment']['id'] }}";
        academicDepartmentG['name'] = "{{ $elements['academicDepartment']['name'] }}";
        academicDepartmentG['type'] = "{{ $elements['academicDepartment']['type'] }}";
        academicDepartmentG['onload'] = "{{ isset($elements['academicDepartment']['onload'])? $elements['academicDepartment']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(academicDepartmentG['name'])) {
            academicDepartmentG['selected'] = requestDataM[academicDepartmentG['name']];
        }
    @endif

    @if(isset($elements['academicStatus']))
        academicStatusG['exist'] = true;
        academicStatusG['id'] = "{{ $elements['academicStatus']['id'] }}";
        academicStatusG['name'] = "{{ $elements['academicStatus']['name'] }}";
        academicStatusG['type'] = "{{ $elements['academicStatus']['type'] }}";
        academicStatusG['onload'] = "{{ isset($elements['academicStatus']['onload'])? $elements['academicStatus']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(academicStatusG['name'])) {
            academicStatusG['selected'] = requestDataM[academicStatusG['name']];
        }
    @endif

    @if(isset($elements['academicYear']))
        academicYearG['exist'] = true;
        academicYearG['id'] = "{{ $elements['academicYear']['id'] }}";
        academicYearG['name'] = "{{ $elements['academicYear']['name'] }}";
        academicYearG['type'] = "{{ $elements['academicYear']['type'] }}";
        academicYearG['onload'] = "{{ isset($elements['academicYear']['onload'])? $elements['academicYear']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(academicYearG['name'])) {
            academicYearG['selected'] = requestDataM[academicYearG['name']];
        }
    @endif
    @if(isset($elements['academicSession']))
        academicSessionG['exist'] = true;
        academicSessionG['id'] = "{{ $elements['academicSession']['id'] }}";
        academicSessionG['name'] = "{{ $elements['academicSession']['name'] }}";
        academicSessionG['type'] = "{{ $elements['academicSession']['type'] }}";
        academicSessionG['onload'] = "{{ isset($elements['academicSession']['onload'])? $elements['academicSession']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(academicSessionG['name'])) {
            academicSessionG['selected'] = requestDataM[academicSessionG['name']];
        }
    @endif

    @if(isset($elements['academicpackages']))
        academicpackagesG['exist'] = true;
        academicpackagesG['id'] = "{{ $elements['academicpackages']['id'] }}";
        academicpackagesG['name'] = "{{ $elements['academicpackages']['name'] }}";
        academicpackagesG['type'] = "{{ $elements['academicpackages']['type'] }}";
        academicpackagesG['onload'] = "{{ isset($elements['academicpackages']['onload'])? $elements['academicpackages']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(academicpackagesG['name'])) {
            academicpackagesG['selected'] = requestDataM[academicpackagesG['name']];
        }
    @endif


    @if(isset($elements['invoice_status']))
        invoicestatusG['exist'] = true;
        invoicestatusG['id'] = "{{ $elements['invoice_status']['id'] }}";
        invoicestatusG['name'] = "{{ $elements['invoice_status']['name'] }}";
        invoicestatusG['type'] = "{{ $elements['invoice_status']['type'] }}";
        invoicestatusG['onload'] = "{{ isset($elements['invoice_status']['onload'])? $elements['invoice_status']['onload'] : 0 }}";
        if (requestDataM.hasOwnProperty(invoicestatusG['name'])) {
            invoicestatusG['selected'] = requestDataM[invoicestatusG['name']];
        }
    @endif

     //##employeeG check by assumed id
     @if(isset($elements['fiscalYearFFY']))
            fiscalYearFFYG['exist'] = true;
            fiscalYearFFYG['id'] = "{{$elements['fiscalYearFFY']['id']}}";
            fiscalYearFFYG['name'] = "{{$elements['fiscalYearFFY']['name']}}";
            fiscalYearFFYG['type'] = "{{$elements['fiscalYearFFY']['type']}}";
            fiscalYearFFYG['onload'] = "{{isset($elements['fiscalYearFFY']['onload'])? $elements['fiscalYearFFY']['onload'] : 0}}";
            if (requestDataP.hasOwnProperty(fiscalYearFFYG['name'])) {
                fiscalYearFFYG['selected'] = requestDataP[fiscalYearFFYG['name']];
            }
    @endif

</script>

<script type="text/javascript">

    $(document).ready(function() {
        if (buildingG['exist'] == true && buildingG['onload'] != 0) {
            fnAjaxGetBuilding();
        }

        if (statusG['exist'] == true && statusG['onload'] != 0) {
            fnAjaxGetStudentStatus();
        }

        if (promoStatusG['exist'] == true && promoStatusG['onload'] != 0) {
            fnAjaxGetStudentPromoStatus();
        }

        if (academicDepartmentG['exist'] == true && academicDepartmentG['onload'] != 0) {
            fnAjaxGetAcademicDepartment();
        }

        if (academicYearG['exist'] == true && academicYearG['onload'] != 0) {
            fnAjaxGetAcademicYear();
        }

        if (academicStatusG['exist'] == true && academicStatusG['onload'] != 0) {
            fnAjaxGetAcademicStatus();
        }

        if (academicSessionG['exist'] == true && academicSessionG['onload'] != 0) {
            fnAjaxGetAcademicSession();
        }

        if (academicpackagesG['exist'] == true && academicpackagesG['onload'] != 0) {
            fnAjaxGetAcademicPackage();
        }

        if (invoicestatusG['exist'] == true && invoicestatusG['onload'] != 0) {
            fnAjaxGetInvoiceStatus();
        }

        if (fiscalYearFFYG['exist'] == true && fiscalYearFFYG['onload'] != 0) {
            fnAjaxGetFiscalYear();
            // $("#startDate, #endDate").attr("readonly", true);
        }

    });

    //This part for on change element
    if (buildingG['exist'] == true){
        $("#"+buildingG['id']).change(function(e){
            if(floorG['exist'] == true){
                fnAjaxGetFloor();
            }
            if(roomG['exist'] == true){
                fnAjaxGetRoom();
            }
        });
    }
    if (floorG['exist'] == true){
        $("#"+floorG['id']).change(function(e){
            if(roomG['exist'] == true){
                fnAjaxGetRoom();
            }
        });
    }
    if (roomG['exist'] == true){
        $("#"+roomG['id']).change(function(e){
            if(seatG['exist'] == true){
                fnAjaxGetSeat();
            }
        });
    }

</script>

<script>
    function fnAjaxGetBuilding(){
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetBuilding') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+buildingG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        var optionText = item.code ? item.name + ' [' + item.code + ']' : item.name;
                        $('#' + buildingG['id']).append($('<option>', {
                            value: item.id,
                            text: optionText
                        }));
                    });

                }
            }
        });
    }


    function fnAjaxGetAcademicPackage(){
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetPackages') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['packages'];

                    $('#'+academicpackagesG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        var optionText = item.name_bn ;
                        $('#' + academicpackagesG['id']).append($('<option>', {
                            value: item.id,
                            text: optionText
                        }));
                    });

                }
            }
        });
    }

    function fnAjaxGetInvoiceStatus(){
        // $.ajax({
        //     method: "GET",
        //     url: "{{ url('ajaxGetPackages') }}",
        //     dataType: "json",
        //     success: function (response) {

        //         if (response['status'] == 'success') {
        //             let result_data = response['packages'];

        //             $('#'+academicpackagesG['id']).empty().append($('<option>', {
        //                 value: "",
        //                 text: "All"
        //             }));

        //             $.each(result_data, function (i, item) {
        //                 var optionText = item.name_bn ;
        //                 $('#' + academicpackagesG['id']).append($('<option>', {
        //                     value: item.id,
        //                     text: optionText
        //                 }));
        //             });

        //         }
        //     }
        // });
        var selectElement = $('#invoicestatusG');

        // Create and append the first option
        var option1 = $('<option>', {
            value: '',
            text: 'Select All'
        });
        selectElement.append(option1);

        // Create and append the second option
        var option2 = $('<option>', {
            value: 'complete',
            text: 'Completed'
        });
        selectElement.append(option2);

        // Create and append the third option
        var option3 = $('<option>', {
            value: 'incomplete',
            text: 'Incomplete'
        });
        selectElement.append(option3);

    }

    function fnAjaxGetFloor(){

        var buildingId = $('#'+buildingG['id']).val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetFloor') }}",
            dataType: "json",
            data: {
                buildingId: buildingId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+floorG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    // $.each(result_data, function (i, item) {
                    //     $('#'+floorG['id']).append($('<option>', {
                    //         value: item.id,
                    //         text: item.name
                    //     }));
                    // });
                    if(response['flag']){
                        $.each(result_data, function (i, item) {
                            var optionText = item.code ? item.name + ' [' + item.code + ']' : item.name;
                            $('#' + floorG['id']).append($('<option>', {
                                value: item.id,
                                text: optionText
                            }));
                        });
                    }
                    else{
                        $.each(result_data, function (i, item) {
                            var optionText = item.name;
                            $('#' + floorG['id']).append($('<option>', {
                                value: item.id,
                                text: optionText
                            }));
                        });
                    }

                }
            }
        });
    }

    function fnAjaxGetRoom(){

        var buildingId = $('#'+buildingG['id']).val();
        var floorId = $('#'+floorG['id']).val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetRoom') }}",
            dataType: "json",
            data: {
                buildingId: buildingId,
                floorId: floorId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+roomG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    // $.each(result_data, function (i, item) {
                    //     $('#'+roomG['id']).append($('<option>', {
                    //         value: item.id,
                    //         text: item.room_no
                    //     }));
                    // });
                    if(response['flag']){
                        $.each(result_data, function (i, item) {
                            var optionText = item.code ? item.room_no + ' [' + item.code + ']' : item.room_no;
                            $('#' + roomG['id']).append($('<option>', {
                                value: item.id,
                                text: optionText
                            }));
                        });
                    }
                    else{
                        $.each(result_data, function (i, item) {
                            var optionText = item.room_no;
                            $('#' + roomG['id']).append($('<option>', {
                                value: item.id,
                                text: optionText
                            }));
                        });
                    }
                }
            }
        });
    }

    function fnAjaxGetSeat(){

        var roomId = $('#'+roomG['id']).val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetSeat') }}",
            dataType: "json",
            data: {
                roomId: roomId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+seatG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    if(response['flag']){
                        $.each(result_data, function (i, item) {
                            $('#'+seatG['id']).append($('<option>', {
                                value: item.id,
                                text: item.seat_no+' ['+item.code+']',
                            }));
                        });
                    }
                    else{
                        $.each(result_data, function (i, item) {
                            $('#'+seatG['id']).append($('<option>', {
                                value: item.id,
                                text: item.seat_no
                            }));
                        });
                    }
                }
            }
        });
    }

    function fnAjaxGetStudentStatus(){

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetStudentStatus') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+statusG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        $('#'+statusG['id']).append($('<option>', {
                            value: item.value_field,
                            text: item.name
                        }));
                    });
                }
            }
        });
    }

    function fnAjaxGetStudentPromoStatus(){

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetStudentPromoStatus') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+promoStatusG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        $('#'+promoStatusG['id']).append($('<option>', {
                            value: item.value_field,
                            text: item.name
                        }));
                    });
                }
            }
        });
    }

    function fnAjaxGetAcademicDepartment(){

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetAcademicDepartment') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+academicDepartmentG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        $('#'+academicDepartmentG['id']).append($('<option>', {
                            value: item.id,
                            text: item.dept_name
                        }));
                    });
                }
            }
        });
    }

    function fnAjaxGetAcademicStatus(){

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetAcademicStatus') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+academicStatusG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        $('#'+academicStatusG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                }
            }
        });
    }

    function fnAjaxGetAcademicYear(){

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetAcademicYear') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+academicYearG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        $('#'+academicYearG['id']).append($('<option>', {
                            value: i,
                            text: item
                        }));
                    });
                }
            }
        });
    }

    function fnAjaxGetAcademicSession(){

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetAcademicSession') }}",
            dataType: "json",
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+academicSessionG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        $('#'+academicSessionG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                }
            }
        });
    }


    function fnAjaxGetFiscalYear() {

        let selectedValue = $("#" + fiscalYearFFYG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(fiscalYearFFYG['exist'] == true && typeof fiscalYearFFYG['selected'] != "undefined" && fiscalYearFFYG['selected'] != ''){
                selectedValue = fiscalYearFFYG['selected'];
            }
        }

        // $("#"+fiscalYearFFYG['id']).empty().append($('<option>', {
        //     value: "",
        //     text: "All"
        // }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxFinancialFY') }}",
            dataType: "json",
            data: {
                returnType: 'json'
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['fiscal_year_data'];
                    let idArr = [];   // New code for zone

                    $('#'+fiscalYearFFYG['id']).empty().append($('<option>', {
                            value: "",
                            text: "Select One"
                        }));

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#"+fiscalYearFFYG['id']).append($('<option>', {
                            value: item['id'],
                            text: item['fy_name'],

                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#"+fiscalYearFFYG['id']).val(selectedValue);

                        if (areaG['exist'] == true){
                            $("#"+fiscalYearFFYG['id']).trigger("change");
                        }

                    }else{
                        $("#"+fiscalYearFFYG['id']).val();
                    }

                }
            }
        });
    }
</script>
