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

        var requestData = {!! json_encode($requestData) !!};
        requestData = (requestData != null)?  requestData : [];


        var zoneG = {'exist': false , 'id': 'zone_id'};
        var areaG = {'exist': false , 'id': 'area_id'};
        var branchG = {'exist': false , 'id': 'branch_id'};

        var groupG = {'exist': false,'id': 'group_id'};
        var categoryG = {'exist': false , 'id': 'cat_id'};
        var subcategoryG = {'exist': false , 'id': 'sub_cat_id'};
        var brandG = {'exist': false , 'id': 'brand_id'};
        var modelG = {'exist': false , 'id': 'model_id'};
        var productG = {'exist': false , 'id': 'product_id'};
        var supplierG = {'exist': false , 'id': 'supplier_id'};
        var stockG = {'exist': false , 'id': 'stock_id'};

        // var textBoxG = {'exist': false , 'id': 'text_box'};
        // var textBoxG1 = {'exist': false , 'id': 'text_box1'};

        // var selectBoxG = {'exist': false , 'id': 'select_box'};
        // var selectBoxG1 = {'exist': false , 'id': 'select_box1'};
        // var selectBoxG2= {'exist': false , 'id': 'select_box2'};

        // var startDateG = {'exist': false , 'id': 'startDate'};
        // var endDateG = {'exist': false , 'id': 'endDate'};

        var temp_id ='';
        var temp_name ='';
        var field_name ='';
        var onload_var = 0;

        var  spanIdN = "";

        // ## 2## find filter options which is called and set exist true
        @if(isset($elements))
            @foreach ($elements as $key => $element)
                temp_id = "{{$element['id']}}";
                temp_name = "{{$element['name']}}";
                field_name = "{{$key}}";
                onload_var = "{{isset($element['onload'])? $element['onload'] : 0}}";
                spanIdN = "{{ $element['id'] }}_rptxt";


                // //## startDateG
                // if(field_name == "startDate"){
                //     startDateG['exist'] = true;
                //     startDateG['id'] = temp_id;
                //     startDateG['rh_tx'] = spanIdN;
                //     startDateG['name'] = temp_name;
                //     startDateG['onload'] = onload_var;
                //     if (requestData.hasOwnProperty(temp_name)) {
                //         startDateG['selected'] = requestData[temp_name];
                //     }
                // }

                // //## startDateG
                // if(field_name == "endDate"){
                //     endDateG['exist'] = true;
                //     endDateG['id'] = temp_id;
                //     endDateG['rh_tx'] = spanIdN;
                //     endDateG['name'] = temp_name;
                //     endDateG['onload'] = onload_var;
                //     if (requestData.hasOwnProperty(temp_name)) {
                //         endDateG['selected'] = requestData[temp_name];
                //     }
                // }

                // //## text box 0
                // if(field_name == "text_box"){
                //     textBoxG['exist'] = true;
                //     textBoxG['id'] = temp_id;
                //     textBoxG['rh_tx'] = spanIdN;
                //     textBoxG['name'] = temp_name;
                //     textBoxG['onload'] = onload_var;
                //     if (requestData.hasOwnProperty(temp_name)) {
                //         textBoxG['selected'] = requestData[temp_name];
                //     }
                // }

                // //## text box 1
                // if(field_name == "text_box1"){
                //     textBoxG1['exist'] = true;
                //     textBoxG1['id'] = temp_id;
                //     textBoxG1['rh_tx'] = spanIdN;
                //     textBoxG1['name'] = temp_name;
                //     textBoxG1['onload'] = onload_var;
                //     if (requestData.hasOwnProperty(temp_name)) {
                //         textBoxG1['selected'] = requestData[temp_name];
                //     }
                // }

                // //## select_box 0
                // if(field_name == "select_box"){
                //     selectBoxG['exist'] = true;
                //     selectBoxG['id'] = temp_id;
                //     selectBoxG['rh_tx'] = spanIdN;
                //     selectBoxG['name'] = temp_name;
                //     selectBoxG['onload'] = onload_var;
                //     if (requestData.hasOwnProperty(temp_name)) {
                //         selectBoxG['selected'] = requestData[temp_name];
                //     }
                // }

                // //## select_box 1
                // if(field_name == "select_box1"){
                //     selectBoxG1['exist'] = true;
                //     selectBoxG1['id'] = temp_id;
                //     selectBoxG1['rh_tx'] = spanIdN;
                //     selectBoxG1['name'] = temp_name;
                //     selectBoxG1['onload'] = onload_var;
                //     if (requestData.hasOwnProperty(temp_name)) {
                //         selectBoxG1['selected'] = requestData[temp_name];
                //     }
                // }

                // //## select_box 2
                // if(field_name == "select_box2"){
                //     selectBoxG2['exist'] = true;
                //     selectBoxG2['id'] = temp_id;
                //     selectBoxG2['rh_tx'] = spanIdN;
                //     selectBoxG2['name'] = temp_name;
                //     selectBoxG2['onload'] = onload_var;
                //     if (requestData.hasOwnProperty(temp_name)) {
                //         selectBoxG2['selected'] = requestData[temp_name];
                //     }
                // }


                //##zoneG check by assumed id
                if(temp_id == "zone_id" || temp_id == "zoneId"){
                    zoneG['exist'] = true;
                    zoneG['id'] = temp_id;
                    zoneG['rh_tx'] = spanIdN;
                    zoneG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        zoneG['selected'] = requestData[temp_name];
                    }
                }
                //##areaG check by assumed id
                if(temp_id == "area_id" || temp_id == "areaId"){
                    areaG['exist'] = true;
                    areaG['id'] = temp_id;
                    areaG['rh_tx'] = spanIdN;
                    areaG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        areaG['selected'] = requestData[temp_name];
                    }
                }
                //##branchG check by assumed id
                if(temp_id == "branch_id" || temp_id == "branchId"){
                    branchG['exist'] = true;
                    branchG['id'] = temp_id;
                    branchG['rh_tx'] = spanIdN;
                    branchG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        branchG['selected'] = requestData[temp_name];
                    }
                }
                //##groupG check by assumed id
                if(temp_id == "group_id" || temp_id == "groupId"){
                    groupG['exist'] = true;
                    groupG['id'] = temp_id;
                    groupG['rh_tx'] = spanIdN;
                    groupG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        groupG['selected'] = requestData[temp_name];
                    }
                }
                //##categoryG check by assumed id
                if(temp_id == "category_id" || temp_id == "categoryId"){
                    categoryG['exist'] = true;
                    categoryG['id'] = temp_id;
                    categoryG['rh_tx'] = spanIdN;
                    categoryG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        categoryG['selected'] = requestData[temp_name];
                    }
                }
                //##subcategoryG check by assumed id
                if(temp_id == "sub_cat_id" || temp_id == "subCatId"){
                    subcategoryG['exist'] = true;
                    subcategoryG['id'] = temp_id;
                    subcategoryG['rh_tx'] = spanIdN;
                    subcategoryG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        subcategoryG['selected'] = requestData[temp_name];
                    }
                }
                //##modelG check by assumed id
                if(temp_id == "model_id" || temp_id == "modelId"){
                    modelG['exist'] = true;
                    modelG['id'] = temp_id;
                    modelG['rh_tx'] = spanIdN;
                    modelG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        modelG['selected'] = requestData[temp_name];
                    }
                }
                //##brandG check by assumed id
                if(temp_id == "brand_id" || temp_id == "brandId"){
                    brandG['exist'] = true;
                    brandG['id'] = temp_id;
                    brandG['rh_tx'] = spanIdN;
                    brandG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        brandG['selected'] = requestData[temp_name];
                    }
                }
                //##productG check by assumed id
                if(temp_id == "product_id" || temp_id == "productId"){
                    productG['exist'] = true;
                    productG['id'] = temp_id;
                    productG['rh_tx'] = spanIdN;
                    productG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        productG['selected'] = requestData[temp_name];
                    }
                }
                //##supplierG check by assumed id
                if(temp_id == "supplier_id" || temp_id == "supplierId"){
                    supplierG['exist'] = true;
                    supplierG['id'] = temp_id;
                    supplierG['rh_tx'] = spanIdN;
                    supplierG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        supplierG['selected'] = requestData[temp_name];
                    }
                }
                //##stockG check by assumed id
                if(temp_id == "stock_id" || temp_id == "stockId"){
                    stockG['exist'] = true;
                    stockG['id'] = temp_id;
                    stockG['rh_tx'] = spanIdN;
                    stockG['onload'] = onload_var;
                    if (requestData.hasOwnProperty(temp_name)) {
                        stockG['selected'] = requestData[temp_name];
                    }
                }


            @endforeach
            onload_var = 0;
            temp_id ='';
            temp_name ='';
            field_name ='';
            spanIdN = "";

        @endif



    $(document).ready(function() {
        //##not dependent on ajax

        // ## 3## onload get data options call
        //## onload a code load filter data if exist
        if (zoneG['exist'] == true && zoneG['onload'] != 0) {
            fnAjaxGetZone();
        }
        if (areaG['exist'] == true && areaG['onload'] != 0) {
            fnAjaxGetArea();
        }
        if (branchG['exist'] == true && branchG['onload'] != 0) {
            fnAjaxGetBranch();
        }
        if (groupG['exist'] == true && groupG['onload'] != 0) {
            fnAjaxGetGroup();
        }
        if (categoryG['exist'] == true && categoryG['onload'] != 0) {
            fnAjaxGetCategory();
        }
        if (subcategoryG['exist'] == true && subcategoryG['onload'] != 0) {
            fnAjaxGetSubCat();
        }

        if (brandG['exist'] == true && brandG['onload'] != 0) {
            fnAjaxGetBrand();
        }
        if (modelG['exist'] == true && modelG['onload'] != 0) {
            fnAjaxGetModel();
        }
        if (productG['exist'] == true && productG['onload'] != 0) {
            fnAjaxGetProduct();
        }
        if (supplierG['exist'] == true && supplierG['onload'] != 0) {
            fnAjaxGetSupplier();
        }

        // var brandG = {'exist': false , 'id': 'brand_id'};
        // var supplierG = {'exist': false , 'id': 'supplier_id'};
        // var stockG = {'exist': false , 'id': 'stock_id'};
        // bad ase agula emplement kora

        // $('#end_date_txt').html('');
        // $('#start_date_txt').html('');
        // $('#text_to').html('to ');
        // $('#end_date_txt').show();
        // $('#text_to').show();
        // $('#start_date_txt').show();

        // if (startDateG['exist'] == true) {
        //     if(startDateG['exist'] == true && typeof startDateG['selected'] != "undefined" && startDateG['selected'] != ''){
        //         let selectedValue = startDateG['selected'];
        //         if (selectedValue != '' && typeof (selectedValue) != 'undefined'){

        //             // $("#"+startDateG['id']).val(selectedValue);

        //             // if( $("#"+startDateG['id']).val() != "" && $("#start_date_txt").length != 0){
        //             //     $('#start_date_txt').html(viewDateFormat($("#"+startDateG['id']).val()));
        //             // }else{
        //             //     $('#start_date_txt').hide();
        //             //     $('#text_to').html('Up to ');
        //             // }

        //         }
        //     }
        // }

        // if (endDateG['exist'] == true) {
        //     if(endDateG['exist'] == true && typeof endDateG['selected'] != "undefined" && endDateG['selected'] != ''){
        //         let selectedValue = endDateG['selected'];
        //         if (selectedValue != '' && typeof (selectedValue) != 'undefined'){

        //             // $("#"+endDateG['id']).val(selectedValue);

        //             // if( $("#"+endDateG['id']).val() != "" && $("#end_date_txt").length != 0){
        //             //     $('#end_date_txt').html(viewDateFormat($("#"+endDateG['id']).val()));
        //             // }else{
        //             //     $('#end_date_txt').hide();
        //             //     $('#text_to').hide();
        //             // }

        //         }
        //     }
        // }

        // if (textBoxG['exist'] == true || textBoxG1['exist'] == true) {
        //     if (textBoxG['exist'] == true) {
        //         if(textBoxG['exist'] == true && typeof textBoxG['selected'] != "undefined" && textBoxG['selected'] != ''){
        //             let selectedValue = textBoxG['selected'];
        //             if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
        //                 // $("#"+textBoxG['id']).val(selectedValue);
        //             }
        //         }
        //     }

        //     if (textBoxG1['exist'] == true) {
        //         if(textBoxG1['exist'] == true && typeof textBoxG1['selected'] != "undefined" && textBoxG1['selected'] != ''){
        //             let selectedValue = textBoxG1['selected'];
        //             if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
        //                 // $("#"+textBoxG1['id']).val(selectedValue);
        //             }
        //         }
        //     }
        // }

        // if (selectBoxG['exist'] == true || selectBoxG1['exist'] == true  || selectBoxG2['exist'] == true) {
        //     if (selectBoxG['exist'] == true) {
        //         if(selectBoxG['exist'] == true && typeof selectBoxG['selected'] != "undefined" && selectBoxG['selected'] != ''){
        //             let selectedValue = selectBoxG['selected'];
        //             if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
        //                 // $("#"+selectBoxG['id']).val(selectedValue);
        //             }
        //         }
        //     }

        //     if (selectBoxG1['exist'] == true) {
        //         if(selectBoxG1['exist'] == true && typeof selectBoxG1['selected'] != "undefined" && selectBoxG1['selected'] != ''){
        //             let selectedValue = selectBoxG1['selected'];
        //             if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
        //                 // $("#"+selectBoxG1['id']).val(selectedValue);
        //             }
        //         }
        //     }

        //     if (selectBoxG2['exist'] == true) {
        //         if(selectBoxG2['exist'] == true && typeof selectBoxG2['selected'] != "undefined" && selectBoxG2['selected'] != ''){
        //             let selectedValue = selectBoxG2['selected'];
        //             if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
        //                 // $("#"+selectBoxG2['id']).val(selectedValue);
        //             }
        //         }
        //     }
        // }


    });

    if (zoneG['exist'] == true && areaG['exist'] == true){
        $("#"+zoneG['id']).change(function(e){
            fnAjaxGetArea();
        });
    }

    if (areaG['exist'] == true && branchG['exist'] == true){
        $("#"+areaG['id']).change(function(e){
            fnAjaxGetBranch();
        });
    }

    if (groupG['exist'] == true && categoryG['exist'] == true){
        $("#"+groupG['id']).change(function(e){
            fnAjaxGetCategory();
            if(subcategoryG['exist'] == true){
                fnAjaxGetSubCat();
            }
            if(modelG['exist'] == true){
                fnAjaxGetModel();
            }
            if(productG['exist'] == true){
                fnAjaxGetProduct();
            }
        });


    }

    if (categoryG['exist'] == true && subcategoryG['exist'] == true){
        $("#"+categoryG['id']).change(function(e){
            fnAjaxGetSubCat();
            if(modelG['exist'] == true){
                fnAjaxGetModel();
            }
            if(productG['exist'] == true){
                fnAjaxGetProduct();
            }
        });


    }

    if (subcategoryG['exist'] == true && modelG['exist'] == true){

        $("#"+subcategoryG['id']).change(function(e){
            if(modelG['exist'] == true){
                fnAjaxGetModel();
            }
            if(productG['exist'] == true){
                fnAjaxGetProduct();
            }
        });

    }

    if (modelG['exist'] == true && productG['exist'] == true){
        $("#"+modelG['id']).change(function(e){
            fnAjaxGetProduct();

        });
    }
    if (brandG['exist'] == true && productG['exist'] == true){
        $("#"+brandG['id']).change(function(e){
            fnAjaxGetProduct();
        });
    }

    if (supplierG['exist'] == true && productG['exist'] == true){
        $("#"+supplierG['id']).change(function(e){
            fnAjaxGetProduct();
        });
    }


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
            data: {
                returnType: 'json'
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

    function fnAjaxGetArea() {
        var zoneId = $("#"+zoneG['id']).val();

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
            data: {
                zoneId: zoneId,
                returnType: 'json'
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

        let selectedValue = $("#"+branchG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(branchG['exist'] == true && typeof branchG['selected'] != "undefined" && branchG['selected'] != ''){
                selectedValue = branchG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetBranch') }}",
            dataType: "json",
            data: {
                areaId: areaId,
                zoneId: zoneId,
                ignorHO: branchWithoutHOG,
                returnType: 'json'
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
                        $("#"+branchG['id']).empty().append($('<option>', {
                            value: "",
                            text: "Select One"
                        }));
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

    function fnAjaxGetGroup(moduleName = null) {

        let selectedValue = $("#"+groupG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            // console.log('w');
            if(groupG['exist'] == true && typeof groupG['selected'] != "undefined" && groupG['selected'] != ''){
                selectedValue = groupG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetGroup') }}",
            dataType: "json",
            data: {
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $("#"+groupG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $("#"+groupG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    console.log('test 1');

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){

                        $("#"+groupG['id']).val(selectedValue);

                        if (categoryG['exist'] == true){
                            $("#"+groupG['id']).trigger("change");
                        }
                    }

                }
            }
        });
    }

    function fnAjaxGetCategory(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();

        // console.log('essee');

        let selectedValue = $('#'+categoryG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(categoryG['exist'] == true && typeof categoryG['selected'] != "undefined" && categoryG['selected'] != ''){
                selectedValue = categoryG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetCategory') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];


                    $('#'+categoryG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+categoryG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+categoryG['id']).val(selectedValue);

                        if (subcategoryG['exist'] == true){
                            $("#"+categoryG['id']).trigger("change");
                        }
                    }

                }
            }
        });
    }

    function fnAjaxGetSubCat(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();

        let selectedValue = $('#'+subcategoryG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(subcategoryG['exist'] == true && typeof subcategoryG['selected'] != "undefined" && subcategoryG['selected'] != ''){
                selectedValue = subcategoryG['selected'];
            }
        }


        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetSubCat') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];

                    $('#'+subcategoryG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+subcategoryG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#'+subcategoryG['id']).val(selectedValue).select2({'width': '100%'});
                        $('#'+subcategoryG['id']).val(selectedValue);

                        if (productG['exist'] == true || modelG['exist'] == true || brandG['exist'] == true ){
                            $("#"+subcategoryG['id']).trigger("change");
                        }
                    }

                    // console.log('fn 6');

                }
            }
        });
    }

    function fnAjaxGetModel(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();

        let selectedValue = $('#'+modelG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(modelG['exist'] == true && typeof modelG['selected'] != "undefined" && modelG['selected'] != ''){
                selectedValue = modelG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetModel') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#'+modelG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+modelG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+modelG['id']).val(selectedValue);

                        if (productG['exist'] == true){
                            $("#"+modelG['id']).trigger("change");
                        }
                    }

                }
            }
        });
    }

    function fnAjaxGetBrand(moduleName = null) {
        // var groupId = $('#'+groupG['id']).val();
        // var categoryId = $('#'+categoryG['id']).val();
        // var subCatId = $('#'+subcategoryG['id']).val();

        let selectedValue = $('#'+brandG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(brandG['exist'] == true && typeof brandG['selected'] != "undefined" && brandG['selected'] != ''){
                selectedValue = brandG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetBrand') }}",
            dataType: "json",
            data: {
                // groupId: groupId,
                // categoryId: categoryId,
                // subCatId: subCatId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#'+brandG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+brandG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+brandG['id']).val(selectedValue);

                        if (productG['exist'] == true){
                            $("#"+brandG['id']).trigger("change");
                        }
                    }

                }
            }
        });
    }

    function fnAjaxGetProduct(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();
        var brandId = $('#'+brandG['id']).val();
        var modelId = $('#'+modelG['id']).val();
        var supplierId = $('#'+supplierG['id']).val();

        let selectedValue = $('#'+productG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(productG['exist'] == true && typeof productG['selected'] != "undefined" && productG['selected'] != ''){
                selectedValue = productG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProduct') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                brandId: brandId,
                modelId: modelId,
                supplierId : supplierId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#'+productG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+productG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+productG['id']).val(selectedValue);
                    }

                    // console.log('fn 8');

                }
            }
        });
    }

    function fnAjaxGetSupplier(moduleName = null) {

        let selectedValue = $('#'+supplierG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(supplierG['exist'] == true && typeof supplierG['selected'] != "undefined" && supplierG['selected'] != ''){
                selectedValue = supplierG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetSupplier') }}",
            dataType: "json",
            data: {
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#'+supplierG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+supplierG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+supplierG['id']).val(selectedValue);

                        if (productG['exist'] == true){
                            $("#"+supplierG['id']).trigger("change");
                        }
                    }

                }
            }
        });
    }

    //## product type product name not useed yet but will be used may be
    function fnAjaxGetProductType(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();
        var brandId = $('#'+brandG['id']).val();
        var modelId = $('#'+modelG['id']).val();


        let selectedValue = $('#type_id').val();
        // if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
        //     if(productG['exist'] == true && typeof productG['selected'] != "undefined" && productG['selected'] != ''){
        //         selectedValue = productG['selected'];
        //     }
        // }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProductType') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#type_id').empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#type_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#type_id').val(selectedValue).select2({'width': '100%'});
                        $('#type_id').val(selectedValue);
                    }

                    // console.log('fn 9 get product type');

                }
            }
        });
    }

    function fnAjaxGetProductName(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();
        var typeId = $('#type_id').val();
        var modelId = $('#'+modelG['id']).val();


        let selectedValue = $('#name_id').val();
        // if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
        //     if(productG['exist'] == true && typeof productG['selected'] != "undefined" && productG['selected'] != ''){
        //         selectedValue = productG['selected'];
        //     }
        // }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProductName') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                typeId: typeId,
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#name_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#name_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#name_id').val(selectedValue).select2({'width': '100%'});
                        $('#name_id').val(selectedValue);
                    }

                    // console.log('fn 10');

                }
            }
        });
    }

</script>
