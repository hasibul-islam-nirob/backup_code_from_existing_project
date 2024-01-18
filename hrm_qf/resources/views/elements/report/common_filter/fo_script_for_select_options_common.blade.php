@php
if(count(Request::all())>0){
    $requestData = Request::all();
}else{
    $requestData = null;
}
@endphp

<script type="text/javascript">

        // ## 1## Initialize global variable here
        var branchWithoutHOG = "{{ (isset($branchWithoutHO) && $branchWithoutHO) ? 1 : 0 }}";
        var branchWithoutHOButSelectAllG = "{{ (isset($branchWithoutHOButSelectAll) && $branchWithoutHOButSelectAll) ? 1 : 0 }}";

        var requestDataC = {!! json_encode($requestData) !!};
        requestDataC = (requestDataC != null)?  requestDataC : [];

        var zoneG = {'exist': false };
        var regionG = {'exist': false };
        var areaG = {'exist': false };
        var branchG = {'exist': false};

        //##zoneG check by assumed id
        @if(isset($elements['zone']))
            zoneG['exist'] = true;
            zoneG['id'] = "{{$elements['zone']['id']}}";
            zoneG['name'] = "{{$elements['zone']['name']}}";
            zoneG['type'] = "{{$elements['zone']['type']}}";
            zoneG['onload'] = "{{isset($elements['zone']['onload'])? $elements['zone']['onload'] : 0}}";
            if (requestDataC.hasOwnProperty(zoneG['name'])) {
                zoneG['selected'] = requestDataC[zoneG['name']];
            }
        @endif

        //##RegionG check by assumed id
        @if(isset($elements['region']))
            regionG['exist'] = true;
            regionG['id'] = "{{$elements['region']['id']}}";
            regionG['name'] = "{{$elements['region']['name']}}";
            regionG['type'] = "{{$elements['region']['type']}}";
            regionG['onload'] = "{{isset($elements['region']['onload'])? $elements['region']['onload'] : 0}}";
            if (requestDataC.hasOwnProperty(regionG['name'])) {
                regionG['selected'] = requestDataC[regionG['name']];
            }
        @endif

        //##areaG check by assumed id
        @if(isset($elements['area']))
            areaG['exist'] = true;
            areaG['id'] = "{{$elements['area']['id']}}";
            areaG['name'] = "{{$elements['area']['name']}}";
            areaG['type'] = "{{$elements['area']['type']}}";
            areaG['onload'] = "{{isset($elements['area']['onload'])? $elements['area']['onload'] : 0}}";
            if (requestDataC.hasOwnProperty(areaG['name'])) {
                areaG['selected'] = requestDataC[areaG['name']];
            }
        @endif

        //##branchG check by assumed id
        @if(isset($elements['branch']))
            branchG['exist'] = true;
            branchG['id'] = "{{$elements['branch']['id']}}";
            branchG['name'] = "{{$elements['branch']['name']}}";
            branchG['type'] = "{{$elements['branch']['type']}}";
            branchG['onload'] = "{{isset($elements['branch']['onload'])? $elements['branch']['onload'] : 0}}";
            if (requestDataC.hasOwnProperty(branchG['name'])) {
                branchG['selected'] = requestDataC[branchG['name']];
            }
        @endif
</script>

<script type="text/javascript">

    $(document).ready(function() {
        //##not dependent on ajax

        console.log(branchG['exist'])

        // ## 3## onload get data options call
        //## onload a code load filter data if exist
        // if (zoneG['exist'] == true && zoneG['onload'] != 0) {
        //     fnAjaxGetZone();
        // }
        // if (areaG['exist'] == true && areaG['onload'] != 0) {
        //     fnAjaxGetArea();
        // }
        // if (branchG['exist'] == true && branchG['onload'] != 0) {
        //     fnAjaxGetBranch();
        // }

    });

    if (zoneG['exist'] == true){

        $("#"+zoneG['id']).change(function(e){

            // if(regionG['exist'] == true){
            //     fnAjaxGetRegion();
            // }

            // if(areaG['exist'] == true){
            //     fnAjaxGetArea();
            // }

            // if(branchG['exist'] == true){
            //     fnAjaxGetBranch();
            // }

        });
    }

    if (regionG['exist'] == true){

        $("#"+regionG['id']).change(function(e){

            // if(areaG['exist'] == true){
            //     fnAjaxGetArea();
            // }

            // if(branchG['exist'] == true){
            //     fnAjaxGetBranch();
            // }

        });
    }

    if (areaG['exist'] == true){

        $("#"+areaG['id']).change(function(e){

            // if(branchG['exist'] == true){
            //     fnAjaxGetBranch();
            // }
        });
    }

    if (branchG['exist'] == true){

        $("#"+branchG['id']).change(function(e){

            console.log($(this).val(), $("#"+branchG['id']).val());

            if(loanProductG['exist'] == true){
                fnAjaxGetLoanProduct();
            }
            if(samityG['exist'] == true){
                fnAjaxGetSamity();
            }
            if(fieldofficerdropdownG['exist'] == true){
                fnAjaxGetFieldoFficerdropdown();
            }
            if(creditofficerdropdownG['exist'] == true){
                fnAjaxGetCriditOfficerdropdown();
            }
        });
    }

</script>

<script>
    /* Get area branch and zone */

    function fnAjaxGetZone() {

        // let zone_id = zoneG['id'];
        let selectedValue = $("#"+zoneG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(zoneG['exist'] == true && typeof zoneG['selected'] != "undefined" && zoneG['selected'] != ''){
                selectedValue = zoneG['selected'];
            }
        }

        $("#"+zoneG['id']).empty().append($('<option>', {
            value: "",
            text: "All"
        }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetZone') }}",
            dataType: "json",
            async:false,
            data: {
                returnType: 'json',
                returnFor: 'search'
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    let result_data = response['result_data'];
                    let idArr = [];   // New code for zone

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#"+zoneG['id']).append($('<option>', {
                            value: item.id,
                            text: item.zone_name + " [" + item.zone_code + "]",

                        }));
                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#"+zoneG['id']).val(selectedValue);

                        if (areaG['exist'] == true){
                            $("#"+zoneG['id']).trigger("change");
                        }

                    }else{
                        $("#"+zoneG['id']).val();
                    }

                }
            }
        });
    }

    function fnAjaxGetRegion() {
        var zoneId = $("#"+zoneG['id']).val();

        let selectedValue = $("#"+regionG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(regionG['exist'] == true && typeof regionG['selected'] != "undefined" && regionG['selected'] != ''){
                selectedValue = regionG['selected'];
            }
        }

        $("#"+regionG['id']).empty().append($('<option>', {
            value: "",
            text: "All"
        }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetRegion') }}",
            dataType: "json",
            async:false,
            data: {
                zoneId: zoneId,
                returnType: 'json',
                returnFor: 'search'
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    let result_data = response['result_data'];
                    let idArr = [];   // New code for zone,area

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#"+regionG['id']).append($('<option>', {
                            value: item.id,
                            text: item.region_name + " [" + item.region_code + "]",
                            // defaultSelected: false,
                            // selected: true
                        }));
                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#"+regionG['id']).val(selectedValue);

                        if (branchG['exist'] == true){
                            $("#"+regionG['id']).trigger("change");
                        }

                    }else{
                        $("#"+regionG['id']).val();
                    }
                    // // console.log('fn 3');
                }
            }
        });
    }

    function fnAjaxGetArea() {
        var zoneId = $("#"+zoneG['id']).val();
        var regionId = $("#"+regionG['id']).val();

        let selectedValue = $("#"+areaG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(areaG['exist'] == true && typeof areaG['selected'] != "undefined" && areaG['selected'] != ''){
                selectedValue = areaG['selected'];
            }
        }

        $("#"+areaG['id']).empty().append($('<option>', {
            value: "",
            text: "All"
        }));

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetArea') }}",
            dataType: "json",
            async:false,
            data: {
                zoneId: zoneId,
                regionId: regionId,
                returnType: 'json',
                returnFor: 'search'
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    let result_data = response['result_data'];
                    let idArr = [];   // New code for zone,area

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#"+areaG['id']).append($('<option>', {
                            value: item.id,
                            text: item.area_name + " [" + item.area_code + "]",
                            // defaultSelected: false,
                            // selected: true
                        }));
                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#"+areaG['id']).val(selectedValue);

                        if (branchG['exist'] == true){
                            $("#"+areaG['id']).trigger("change");
                        }

                    }else{
                        $("#"+areaG['id']).val();
                    }
                    // // console.log('fn 3');
                }
            }
        });
    }

    function fnAjaxGetBranch() {

        var zoneId = $("#"+zoneG['id']).val();
        var areaId = $("#"+areaG['id']).val();
        var regionId = $("#"+regionG['id']).val();

        let selectedValue = $("#"+branchG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(branchG['exist'] == true && typeof branchG['selected'] != "undefined" && branchG['selected'] != ''){
                selectedValue = branchG['selected'];
            }
        }
        // console.log(branchWithoutHOG);
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetBranch') }}",
            dataType: "json",
            async:false,
            data: {
                zoneId: zoneId,
                regionId: regionId,
                areaId: areaId,
                ignorHO: branchWithoutHOG,
                returnType: 'json',
                returnFor: 'search'
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    let idArr = [];   // New code for zone,area

                    $("#"+branchG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    if (branchWithoutHOG == 1) {
                        if (branchWithoutHOButSelectAllG == 1)
                        {
                            $("#"+branchG['id']).empty().append($('<option>', {
                                value: "",
                                text: "All"
                            }));
                        }
                        else
                        {
                            $("#"+branchG['id']).empty().append($('<option>', {
                                value: "",
                                text: "Select One"
                            }));
                        }

                    }

                    $.each(result_data, function (i, item) {

                        idArr.push($(this).attr("id"));  // New code for zone,area

                        $("#"+branchG['id']).append($('<option>', {
                            value: item.id,
                            text: item.branch_name + " [" + item.branch_code + "]"
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined' && idArr.includes(parseInt(selectedValue))){
                        $("#"+branchG['id']).val(selectedValue);

                    }else{
                        $("#"+branchG['id']).val();
                    }
                }
            }
        });
        // }
    }
    /* Get area branch and zone end */
</script>
