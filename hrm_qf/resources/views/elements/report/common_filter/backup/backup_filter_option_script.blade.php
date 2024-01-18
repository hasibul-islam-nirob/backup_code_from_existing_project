@php
use App\Services\CommonService as Common;
$requestData = Request::all();

// dd($elements );

@endphp

<script type="text/javascript">


    var groupG = "{{ isset($elements) && isset($elements['posgroup']) ? 1 : 0 }}";
    var categoryG = "{{ isset($elements) && isset($elements['poscategory']) ? 1 : 0 }}";
    var subcategoryG = "{{ isset($elements) && isset($elements['possubcategory']) ? 1 : 0 }}";
    var brandG = "{{ isset($elements) && isset($elements['posbrand']) ? 1 : 0 }}";
    var modelG = "{{ isset($elements) && isset($elements['posmodel']) ? 1 : 0 }}";
    var productG = "{{ isset($elements) && isset($elements['posproduct']) ? 1 : 0 }}";
    var supplierG = "{{ isset($elements) && isset($elements['possupplier']) ? 1 : 0 }}";


    var stockG = "{{ isset($elements) && isset($elements['stock']) ? 1 : 0 }}";


    $(document).ready(function() {
        //##not dependent on ajax
        if (groupG == 1) {
            fnAjaxGetCategory();
            fnAjaxGetSubCat();

            let groupId = "{{ isset($requestData['group_id']) ? $requestData['group_id'] : '' }}";
            if (groupId != '' && $('#group_id option[value="'+groupId+'"]').length > 0) {
                $('#group_id').val(groupId);
                $('#group_id').trigger('change');
            }

        }

        if (modelG == 1) {
            fnAjaxGetModel();
        }
        if (productG == 1) {
            fnAjaxGetProduct();
        }


        //##not dependent on ajax
        if (brandG == 1 && $("#brand_id") != undefined) {
            let brandId = "{{ isset($requestData['brand_id']) ? $requestData['brand_id'] : '' }}";

            if (brandId != '' && $('#brand_id option[value="'+brandId+'"]').length > 0) {
                $('#brand_id').val(brandId);
            }
        }

        if (stockG == 1 && $("#stock") != undefined) {
            let stockId = "{{ isset($requestData['stock']) ? $requestData['stock'] : '' }}";

            if (stockId != '' && $('#stock option[value="'+stockId+'"]').length > 0) {
                $('#stock').val(stockId);
            }
        }


    });


 
    function FnselectedOptionCheck(){

        
        if (categoryG == 1 && $("#category_id") != undefined) {
            let categoryId = "{{ isset($requestData['category_id']) ? $requestData['category_id'] : '' }}";
          
            if (categoryId != '' && $('#category_id option[value="'+categoryId+'"]').length > 0) {
                console.log('in casssssssssssssssssssssst');
                $('#category_id').val(categoryId);
            }
        }
        if (subcategoryG == 1 && $("#sub_cat_id") != undefined) {
            let subcategoryId = "{{ isset($requestData['sub_cat_id']) ? $requestData['sub_cat_id'] : '' }}";
            if (subcategoryId != '' && $('#sub_cat_id option[value="'+subcategoryId+'"]').length > 0) {
                $('#sub_cat_id').val(subcategoryId);
            }
        }
        
        if (modelG == 1 && $("#model_id") != undefined) {
            let modelId = "{{ isset($requestData['model_id']) ? $requestData['model_id'] : '' }}";
            if (modelId != '' && $('#model_id option[value="'+modelId+'"]').length > 0) {
                $('#model_id').val(modelId);
            }
        }
        if (productG == 1 && $("#sub_cat_id") != undefined) {
            let productId = "{{ isset($requestData['product_id']) ? $requestData['product_id'] : '' }}";
            if (productId != '' && $('#product_id option[value="'+productId+'"]').length > 0) {
                $('#product_id').val(productId);
            }
        }


        if (supplierG == 1 && $("#supplier_id") != undefined) {
            let supplierId = "{{ isset($requestData['supplier_id']) ? $requestData['supplier_id'] : '' }}";
            if (supplierId != '' && $('#supplier_id option[value="'+supplierId+'"]').length > 0) {
                $('#supplier_id').val(supplierId);
            }
        }
        console.log('second fn end');
    }



    

    function fnAjaxGetArea() {
        var zoneId = $('#zoneId').val();

        if (zoneId != null) {
            $.ajax({
                method: "GET",
                url: "{{ url('ajaxGetArea') }}",
                dataType: "text",
                data: {
                    zoneId: zoneId,
                },
                success: function(data) {
                    // console.log(data);

                    if (data) {
                        $('#areaId').empty().html(data);
                    }
                }
            });
        }
    }

    function fnAjaxGetBranch() {

        var zoneId = $('#zoneId').val();
        var areaId = $('#areaId').val();

        if (areaId != null || zoneId != null) {
            $.ajax({
                method: "GET",
                url: "{{ url('ajaxGetBranch') }}",
                dataType: "text",
                data: {
                    areaId: areaId,
                    zoneId: zoneId,
                },
                success: function(data) {
                    if (data) {
                        $('#branchId').empty().html(data);
                    }
                }
            });
        }
    }

    function fnAjaxGetCategory() {

        
        var groupId = $('#group_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetCategory') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var deferred = $.Deferred();

                    var result_data = response['result_data'];

                    $('#category_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#category_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });
                    FnselectedOptionCheck();
                }
                
            }
        });

    }

    function fnAjaxGetSubCat() {



        var groupId = $('#group_id').val();
        var categoryId = $('#category_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetSubCat') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#sub_cat_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#sub_cat_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });
                    FnselectedOptionCheck();
                }
            }
        });

    }

    function fnAjaxGetModel() {
        var groupId = $('#group_id').val();
        var categoryId = $('#category_id').val();
        var subCatId = $('#sub_cat_id').val();
      
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetModel') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#model_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#model_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });
                    FnselectedOptionCheck();
                }
            }
        });
    }

    function fnAjaxGetProduct() {
        var groupId = $('#group_id').val();
        var categoryId = $('#category_id').val();
        var subCatId = $('#sub_cat_id').val();
        var brandId = $('#brand_id').val();
        var modelId = $('#model_id').val();

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
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#product_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#product_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });
                    FnselectedOptionCheck();
                }
            }
        });
    }


</script>