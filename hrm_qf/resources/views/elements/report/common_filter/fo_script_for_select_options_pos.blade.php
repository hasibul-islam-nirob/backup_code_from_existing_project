@php

    if(count(Request::all())>0) {
        $requestData = Request::all();
    } else {
        $requestData = null;
    }

@endphp

<script type="text/javascript">

    // ## 1## Initialize global variable here
    var requestDataP = {!! json_encode($requestData) !!};
    requestDataP = (requestDataP != null)?  requestDataP : [];

    var groupG = {'exist': false};
    var categoryG = {'exist': false};
    var subcategoryG = {'exist': false};
    var brandG = {'exist': false};
    var modelG = {'exist': false};
    var productG = {'exist': false};
    var supplierG = {'exist': false};
    var customerG = {'exist': false};
    // var branchG = {'exist': false};
    var sizeG = {'exist': false};

    var prodTypeG = {'exist': false};
    var colorG = {'exist': false};
    var uomG = {'exist': false};

    var prodIdArr = [];

    //##prodTypeG check by assumed id
    @if(isset($elements['prod_type']))
        prodTypeG['exist'] = true;
        prodTypeG['id'] = "{{$elements['prod_type']['id']}}";
        prodTypeG['name'] = "{{$elements['prod_type']['name']}}";
        prodTypeG['type'] = "{{$elements['prod_type']['type']}}";
        prodTypeG['onload'] = "{{isset($elements['prod_type']['onload'])? $elements['prod_type']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(prodTypeG['name'])) {
            prodTypeG['selected'] = requestDataP[prodTypeG['name']];
        }
    @endif

    //##groupG check by assumed id
    @if(isset($elements['group']))
        groupG['exist'] = true;
        groupG['id'] = "{{$elements['group']['id']}}";
        groupG['name'] = "{{$elements['group']['name']}}";
        groupG['type'] = "{{$elements['group']['type']}}";
        groupG['onload'] = "{{isset($elements['group']['onload'])? $elements['group']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(groupG['name'])) {
            groupG['selected'] = requestDataP[groupG['name']];
        }
    @endif

    //##categoryG check by assumed id
    @if(isset($elements['category']))
        categoryG['exist'] = true;
        categoryG['id'] = "{{$elements['category']['id']}}";
        categoryG['name'] = "{{$elements['category']['name']}}";
        categoryG['type'] = "{{$elements['category']['type']}}";
        categoryG['onload'] = "{{isset($elements['category']['onload'])? $elements['category']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(categoryG['name'])) {
            categoryG['selected'] = requestDataP[categoryG['name']];
        }
    @endif

    //##subcategoryG check by assumed id
    @if(isset($elements['subcategory']))
        subcategoryG['exist'] = true;
        subcategoryG['id'] = "{{$elements['subcategory']['id']}}";
        subcategoryG['name'] = "{{$elements['subcategory']['name']}}";
        subcategoryG['type'] = "{{$elements['subcategory']['type']}}";
        subcategoryG['onload'] = "{{isset($elements['subcategory']['onload'])? $elements['subcategory']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(subcategoryG['name'])) {
            subcategoryG['selected'] = requestDataP[subcategoryG['name']];
        }
    @endif

    //##modelG check by assumed id
    @if(isset($elements['model']))
        modelG['exist'] = true;
        modelG['id'] = "{{$elements['model']['id']}}";
        modelG['name'] = "{{$elements['model']['name']}}";
        modelG['type'] = "{{$elements['model']['type']}}";
        modelG['onload'] = "{{isset($elements['model']['onload'])? $elements['model']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(modelG['name'])) {
            modelG['selected'] = requestDataP[modelG['name']];
        }
    @endif

        //##customerG check by assumed id
        @if(isset($elements['customer']))
            customerG['exist'] = true;
            customerG['id'] = "{{$elements['customer']['id']}}";
            customerG['name'] = "{{$elements['customer']['name']}}";
            customerG['type'] = "{{$elements['customer']['type']}}";
            customerG['onload'] = "{{isset($elements['customer']['onload'])? $elements['customer']['onload'] : 0}}";
            if (requestDataP.hasOwnProperty(customerG['name'])) {
                customerG['selected'] = requestDataP[customerG['name']];
            }
        @endif

        //##sizeG check by assumed id
        @if(isset($elements['sizeName']))
            sizeG['exist'] = true;
            sizeG['id'] = "{{$elements['sizeName']['id']}}";
            sizeG['name'] = "{{$elements['sizeName']['name']}}";
            sizeG['type'] = "{{$elements['sizeName']['type']}}";
            sizeG['onload'] = "{{isset($elements['sizeName']['onload'])? $elements['sizeName']['onload'] : 0}}";
            if (requestDataP.hasOwnProperty(sizeG['name'])) {
                sizeG['selected'] = requestDataP[sizeG['name']];
            }
        @endif

    //##colorG check by assumed id
    @if(isset($elements['color']))
        colorG['exist'] = true;
        colorG['id'] = "{{$elements['color']['id']}}";
        colorG['name'] = "{{$elements['color']['name']}}";
        colorG['type'] = "{{$elements['color']['type']}}";
        colorG['onload'] = "{{isset($elements['color']['onload'])? $elements['color']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(colorG['name'])) {
            colorG['selected'] = requestDataP[colorG['name']];
        }
    @endif

    //##uomG check by assumed id
    @if(isset($elements['uom']))
        uomG['exist'] = true;
        uomG['id'] = "{{$elements['uom']['id']}}";
        uomG['name'] = "{{$elements['uom']['name']}}";
        uomG['type'] = "{{$elements['uom']['type']}}";
        uomG['onload'] = "{{isset($elements['uom']['onload'])? $elements['uom']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(uomG['name'])) {
            uomG['selected'] = requestDataP[uomG['name']];
        }
    @endif

    //##supplierG check by assumed id
    @if(isset($elements['supplier']))
        supplierG['exist'] = true;
        supplierG['id'] = "{{$elements['supplier']['id']}}";
        supplierG['name'] = "{{$elements['supplier']['name']}}";
        supplierG['type'] = "{{$elements['supplier']['type']}}";
        supplierG['onload'] = "{{isset($elements['supplier']['onload'])? $elements['supplier']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(supplierG['name'])) {
            supplierG['selected'] = requestDataP[supplierG['name']];
        }
    @endif

    //##sizeG check by assumed id
    @if(isset($elements['sizeName']))
        sizeG['exist'] = true;
        sizeG['id'] = "{{$elements['sizeName']['id']}}";
        sizeG['name'] = "{{$elements['sizeName']['name']}}";
        sizeG['type'] = "{{$elements['sizeName']['type']}}";
        sizeG['onload'] = "{{isset($elements['sizeName']['onload'])? $elements['sizeName']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(sizeG['name'])) {
            sizeG['selected'] = requestDataP[sizeG['name']];
        }
    @endif

    //##productG check by assumed id
    @if(isset($elements['product']))
        productG['exist'] = true;
        productG['id'] = "{{$elements['product']['id']}}";
        productG['name'] = "{{$elements['product']['name']}}";
        productG['type'] = "{{$elements['product']['type']}}";
        productG['onload'] = "{{isset($elements['product']['onload'])? $elements['product']['onload'] : 0}}";
        if (requestDataP.hasOwnProperty(productG['name'])) {
            productG['selected'] = requestDataP[productG['name']];
        }
    @endif

</script>

<script type="text/javascript">

    $(document).ready(function() {

        //##not dependent on ajax

        // ## 3## onload get data options call
        //## onload a code load filter data if exist
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

        if (colorG['exist'] == true && colorG['onload'] != 0) {
            fnAjaxGetColor();
        }

        if (uomG['exist'] == true && uomG['onload'] != 0) {
            fnAjaxGetUom();
        }

        if (productG['exist'] == true && productG['onload'] != 0) {
            fnAjaxGetProduct();
        }

        if (supplierG['exist'] == true && supplierG['onload'] != 0) {
            fnAjaxGetSupplier();
        }

        if (customerG['exist'] == true && customerG['onload'] != 0) {
            fnAjaxGetCustomer();
        }

        if (sizeG['exist'] == true && sizeG['onload'] != 0) {
            fnAjaxGetProductSize();
        }

        if (prodTypeG['exist'] == true && prodTypeG['onload'] != 0) {
            fnAjaxGetPosProductType("pos");
        }

    });

    if (groupG['exist'] == true){

        $("#"+groupG['id']).change(function(e){

            if(categoryG['exist'] == true && $("#" + groupG['id']).val() != '' && $("#" + groupG['id']).val() != null){
                if(categoryG['exist'] == true) {
                    fnAjaxGetCategory();
                }

                // if(modelG['exist'] == true) {
                //     fnAjaxGetModel();
                // }

                // if(colorG['exist'] == true) {
                //     fnAjaxGetColor();
                // }

                // if(sizeG['exist'] == true) {
                //     fnAjaxGetProductSize();
                // }
            }

        });
    }

    if (categoryG['exist'] == true){

        $("#"+categoryG['id']).change(function(e){

            if(subcategoryG['exist'] == true && $("#" + categoryG['id']).val() != '' && $("#" + categoryG['id']).val() != null){

                if(subcategoryG['exist'] == true) {
                    fnAjaxGetSubCat();
                }

                if(modelG['exist'] == true) {
                    fnAjaxGetModel();
                }

                if(colorG['exist'] == true) {
                    fnAjaxGetColor();
                }

                // if(sizeG['exist'] == true && prodTypeG.selected_value != 3) {
                if(sizeG['exist'] == true) {
                    fnAjaxGetProductSize();
                }

                if(productG['exist'] == true){
                    fnAjaxGetProduct();
                }
            }

        });
    }

    if (subcategoryG['exist'] == true){

        $("#"+subcategoryG['id']).change(function(e){

            if(modelG['exist'] == true && $("#" + subcategoryG['id']).val() != '' && $("#" + subcategoryG['id']).val() != null){
                if(modelG['exist'] == true) {
                    fnAjaxGetModel();
                }

                if(colorG['exist'] == true) {
                    fnAjaxGetColor();
                }

                // if(sizeG['exist'] == true && prodTypeG.selected_value != 3) {
                if(sizeG['exist'] == true) {
                    fnAjaxGetProductSize();
                }
            }

            if(productG['exist'] == true){
                fnAjaxGetProduct();
            }
        });
    }

    if (modelG['exist'] == true){

        $("#"+modelG['id']).change(function(e){

            // if(modelG['exist'] == true && $("#" + modelG['id']).val() != '' && $("#" + modelG['id']).val() != null && prodTypeG.selected_value != 3){
            if(modelG['exist'] == true && $("#" + modelG['id']).val() != '' && $("#" + modelG['id']).val() != null){
                fnAjaxGetProductSize();
            }

            if(productG['exist'] == true && $("#" + modelG['id']).val() != '' && $("#" + modelG['id']).val() != null){
                fnAjaxGetProduct();
            }
        });
    }

    if (brandG['exist'] == true){

        $("#"+brandG['id']).change(function(e){

            if(productG['exist'] == true && $("#" + brandG['id']).val() != '' && $("#" + brandG['id']).val() != null){
                fnAjaxGetProduct();
            }

        });
    }

    if (supplierG['exist'] == true){

        $("#"+supplierG['id']).change(function(e){

            if(productG['exist'] == true && $("#" + supplierG['id']).val() != '' && $("#" + supplierG['id']).val() != null){
                fnAjaxGetProduct();
            }
        });
    }

    if (sizeG['exist'] == true){

        $("#"+sizeG['id']).change(function(e){

            let selectedValue = $('#'+sizeG['id']).val();

            if(prodTypeG.selected_value == 3 && productG['exist'] == true && $("#" + sizeG['id']).val() != '' && $("#" + sizeG['id']).val() != null){
                fnAjaxGetProduct(null, selectedValue);
            }
        });
    }

    if (productG['exist'] == true){

        $("#"+productG['id']).change(function(e){

            if(productG['exist'] == true){

                let selectedValue = $('#'+productG['id']).val();

                if(prodTypeG.selected_value == 3){

                    fnAjaxGetProductSize(selectedValue);
                }
            }
        });
    }

</script>

<script>

    function fnAjaxGetGroup(moduleName = null) {

        let selectedValue = $("#"+groupG['id']).val();
        let prodTypeId = prodTypeG['selected_value'];
        prodTypeId = $('#prod_type_id').val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){

            if(groupG['exist'] == true && typeof groupG['selected'] != "undefined" && groupG['selected'] != ''){
                selectedValue = groupG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetGroup') }}",
            dataType: "json",
            async:false,
            data: {
                isActive: 1,
                moduleName: moduleName,
                prodTypeId: prodTypeId
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
        let prodTypeId = prodTypeG['selected_value'];
        prodTypeId = $('#prod_type_id').val();

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
            async: false,
            data: {
                groupId: groupId,
                isActive: 1,
                moduleName: moduleName,
                prodTypeId: prodTypeId
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
            async: false,
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
            async: false,
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

    function fnAjaxGetColor(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();

        let selectedValue = $('#'+modelG['id']).val();
        // if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
        //     if(modelG['exist'] == true && typeof modelG['selected'] != "undefined" && modelG['selected'] != ''){
        //         selectedValue = modelG['selected'];
        //     }
        // }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetColor') }}",
            dataType: "json",
            async: false,
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


                    $('#'+colorG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+colorG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+colorG['id']).val(selectedValue);

                        if (productG['exist'] == true){
                            $("#"+colorG['id']).trigger("change");
                        }
                    }

                }
            }
        });
    }

    function fnAjaxGetUom(moduleName = null) {

        let selectedValue = $('#'+modelG['id']).val();
        // if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
        //     if(modelG['exist'] == true && typeof modelG['selected'] != "undefined" && modelG['selected'] != ''){
        //         selectedValue = modelG['selected'];
        //     }
        // }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetUom') }}",
            dataType: "json",
            async: false,
            data: {
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#'+uomG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+uomG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+uomG['id']).val(selectedValue);

                        if (productG['exist'] == true){
                            $("#"+uomG['id']).trigger("change");
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
            async:false,
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

    function fnAjaxGetProduct(moduleName = null, sizeId = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();
        var brandId = $('#'+brandG['id']).val();
        var modelId = $('#'+modelG['id']).val();
        var supplierId = $('#'+supplierG['id']).val();
        var prodTypeId = prodTypeG.selected_value;

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
            async:false,
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                brandId: brandId,
                modelId: modelId,
                supplierId : supplierId,
                prodTypeId : prodTypeId,
                isActive: 1,
                moduleName: moduleName,
                sizeId: sizeId
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];
                    // var prodIdArr = [];

                    $('#'+productG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {
                        // prodIdArr.push(item.field_id);

                        $('#'+productG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    // if(prodTypeId == 3){
                    //     fnAjaxGetProductSize(prodIdArr, null);
                    // }

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
            async:false,
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

    function fnAjaxGetCustomer(moduleName = null) {

        let selectedValue = $('#'+customerG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(customerG['exist'] == true && typeof customerG['selected'] != "undefined" && customerG['selected'] != ''){
                selectedValue = customerG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetCustomer') }}",
            dataType: "json",
            async:false,
            data: {
                isActive: 1,
                moduleName: moduleName
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let result_data = response['result_data'];


                    $('#'+customerG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+customerG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+customerG['id']).val(selectedValue);

                        if (productG['exist'] == true){
                            $("#"+customerG['id']).trigger("change");
                        }
                    }

                }
            }
        });
    }

    //## product type product name not useed yet but will be used may be
    function fnAjaxGetPosProductType(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();
        var brandId = $('#'+brandG['id']).val();
        var modelId = $('#'+modelG['id']).val();

        let selectedValue = $('#'+prodTypeG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(prodTypeG['exist'] == true && typeof prodTypeG['selected'] != "undefined" && prodTypeG['selected'] != ''){
                selectedValue = prodTypeG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProductType') }}",
            dataType: "json",
            async:false,
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

                    // $('#'+prodTypeG['id']).empty().append($('<option>', {
                    //     value: "",
                    //     text: "All"
                    // }));

                    $.each(result_data, function (i, item) {

                        $('#'+prodTypeG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });
                    // $('#'+prodTypeG['id']).find('option[value=""]').attr('selected', true);

                    // if (prodTypeId != '' && typeof (prodTypeId) != 'undefined'){
                    //     $('#'+prodTypeG['id']).find('option[value=""]').attr('selected', true);
                    // }

                    // if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                    //     $('#'+prodTypeG['id']).val(selectedValue);
                    // }

                    // console.log('fn 9 get product type');
                    fnShowHideProdTypeElements();

                }
            }
        });
    }

    function fnAjaxGetProductTypeBackup(moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();
        var brandId = $('#'+brandG['id']).val();
        var modelId = $('#'+modelG['id']).val();

        let selectedValue = $('#type_id').val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(productG['exist'] == true && typeof productG['selected'] != "undefined" && productG['selected'] != ''){
                selectedValue = productG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProductType') }}",
            dataType: "json",
            async:false,
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


        let selectedValue = $('#product_id').val();
        // if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
        //     if(productG['exist'] == true && typeof productG['selected'] != "undefined" && productG['selected'] != ''){
        //         selectedValue = productG['selected'];
        //     }
        // }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProductName') }}",
            dataType: "json",
            async:false,
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


                    $('#product_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#product_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        // $('#product_id').val(selectedValue).select2({'width': '100%'});
                        $('#product_id').val(selectedValue);
                    }

                    // console.log('fn 10');

                }
            }
        });
    }

    function fnAjaxGetProductSize(prodIdArr, moduleName = null) {
        var groupId = $('#'+groupG['id']).val();
        var categoryId = $('#'+categoryG['id']).val();
        var subCatId = $('#'+subcategoryG['id']).val();
        // var prodIdArr = [];

        // if(prodTypeG.selected_value == 3){
        //     prodIdArr = prodIdArr;
        // }

        var branchId = branchG['selected'];
        let selectedValue = $('#'+sizeG['id']).val();

        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(sizeG['exist'] == true && typeof sizeG['selected'] != "undefined" && sizeG['selected'] != ''){
                selectedValue = sizeG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetSizeForGold') }}",
            dataType: "json",
            async:false,
            data: {
                branchId: $('#branch_from').val(),
                // reportFlag: true
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                isActive: 1,
                moduleName: moduleName,
                prodIdArr: prodIdArr,
                elementArrayFlag: true,
            },

            success: function (response) {

                let sizeData = response.ProductSizeData;

                if(sizeData) {

                    $('#'+sizeG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(sizeData, function (i, item) {

                        $('#'+sizeG['id']).append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                    if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                        $('#'+sizeG['id']).val(selectedValue);

                        if (productG['exist'] == true){
                            $("#"+sizeG['id']).trigger("change");
                        }
                    }

                    // $('#product_id').empty().append($('<option>', {
                    //     value: "",
                    //     text: "Select All"
                    // }));

                    // $.each(sizeData, function (i, item) {

                    //     $('#product_id').append($('<option>', {
                    //         value: item.field_id,
                    //         text: item.field_name
                    //     }));

                    // });

                    // if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                    //     // $('#product_id').val(selectedValue).select2({'width': '100%'});
                    //     $('#product_id').val(selectedValue);
                    // }

                }
            }
        });
    }


    function fnShowHideProdTypeElements() {

        if ($("#prod_type_id").val() == 3) {

            $('#carat_id').parent('div').parent('div').show();
            prodTypeG['selected_value'] = $('#prod_type_id').val();

            $('#product_id').parent('div').parent('div').show();
            productG['selected_value'] = $('#prod_type_id').val();

            $('#nameorcode').parent('div').parent('div').hide();
            productG['selected_value'] = $('#prod_type_id').val();
        }
        else {
            $('#nameorcode').parent('div').parent('div').show();
            productG['selected_value'] = $('#prod_type_id').val();

            $('#carat_id').parent('div').parent('div').hide();
            prodTypeG['selected_value'] = $('#prod_type_id').val();

            $('#product_id').parent('div').parent('div').hide();
            productG['selected_value'] = $('#prod_type_id').val();
        }
    }

    // fnShowHideProdTypeElements();

</script>
