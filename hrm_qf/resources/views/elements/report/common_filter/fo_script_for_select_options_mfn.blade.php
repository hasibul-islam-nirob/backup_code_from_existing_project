@php
use App\Services\CommonService as CS;
$loggedInBranch = CS::getBranchId();
if(count(Request::all())>0){
    $requestData = Request::all();
}else{
    $requestData = null;
}

@endphp

<script type="text/javascript">

        var requestDataM            = {!! json_encode($requestData) !!};
        requestDataM                = (requestDataM != null)?  requestDataM : [];
        var productCategoryG        = {'exist': false};
        var loanRepaymentFrequencyG = {'exist': false};
        var loanProductG            = {'exist': false};
        var samityG                 = {'exist': false};
        var memberG                 = {'exist': false};
        var savingsproductG         = {'exist': false};
        var producttypeG            = {'exist': false};
        var fundingOrgG             = {'exist': false};
        var fieldofficerdropdownG   = {'exist': false};
        var creditofficerdropdownG  = {'exist': false};
        var monthYearG              = {'exist': false};
        var dayG                    = {'exist': false};
        var loanStatusG             = {'exist': false};
        var transactionTypeG        = {'exist': false};

        //##productCategoryG check by assumed id
        @if(isset($elements['productCategory']))
            productCategoryG['exist'] = true;
            productCategoryG['id'] = "{{$elements['productCategory']['id']}}";
            productCategoryG['name'] = "{{$elements['productCategory']['name']}}";
            productCategoryG['type'] = "{{$elements['productCategory']['type']}}";
            productCategoryG['onload'] = "{{isset($elements['productCategory']['onload'])? $elements['productCategory']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(productCategoryG['name'])) {
                productCategoryG['selected'] = requestDataM[productCategoryG['name']];
            }
        @endif

        //##loanRepaymentFrequencyG check by assumed id
        @if(isset($elements['loanRepaymentFrequency']))
            loanRepaymentFrequencyG['exist'] = true;
            loanRepaymentFrequencyG['id'] = "{{$elements['loanRepaymentFrequency']['id']}}";
            loanRepaymentFrequencyG['name'] = "{{$elements['loanRepaymentFrequency']['name']}}";
            loanRepaymentFrequencyG['type'] = "{{$elements['loanRepaymentFrequency']['type']}}";
            loanRepaymentFrequencyG['onload'] = "{{isset($elements['loanRepaymentFrequency']['onload'])? $elements['loanRepaymentFrequency']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(loanRepaymentFrequencyG['name'])) {
                loanRepaymentFrequencyG['selected'] = requestDataM[loanRepaymentFrequencyG['name']];
            }
        @endif

        //##loanProductG check by assumed id
        @if(isset($elements['loanProduct']))
            loanProductG['exist'] = true;
            loanProductG['id'] = "{{$elements['loanProduct']['id']}}";
            loanProductG['name'] = "{{$elements['loanProduct']['name']}}";
            loanProductG['type'] = "{{$elements['loanProduct']['type']}}";
            loanProductG['onload'] = "{{isset($elements['loanProduct']['onload'])? $elements['loanProduct']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(loanProductG['name'])) {
                loanProductG['selected'] = requestDataM[loanProductG['name']];
            }
        @endif

        //##samityG check by assumed id
        @if(isset($elements['samity']))
            samityG['exist'] = true;
            samityG['id'] = "{{$elements['samity']['id']}}";
            samityG['name'] = "{{$elements['samity']['name']}}";
            samityG['type'] = "{{$elements['samity']['type']}}";
            samityG['onload'] = "{{isset($elements['samity']['onload'])? $elements['samity']['onload'] : 0}}";
            samityG['required'] = "{{isset($elements['samity']['required'])? $elements['samity']['required'] : 0}}";
            if (requestDataM.hasOwnProperty(samityG['name'])) {
                samityG['selected'] = requestDataM[samityG['name']];
            }
        @endif

        //##memberG check by assumed id
        @if(isset($elements['member']))
            memberG['exist'] = true;
            memberG['id'] = "{{$elements['member']['id']}}";
            memberG['name'] = "{{$elements['member']['name']}}";
            memberG['type'] = "{{$elements['member']['type']}}";
            memberG['onload'] = "{{isset($elements['member']['onload'])? $elements['member']['onload'] : 0}}";
            memberG['required'] = "{{isset($elements['member']['required'])? $elements['member']['required'] : 0}}";
            if (requestDataM.hasOwnProperty(memberG['name'])) {
                memberG['selected'] = requestDataM[memberG['name']];
            }
        @endif

        //##savingsproductG check by assumed id
        @if(isset($elements['savingsproduct']))
            savingsproductG['exist'] = true;
            savingsproductG['id'] = "{{$elements['savingsproduct']['id']}}";
            savingsproductG['name'] = "{{$elements['savingsproduct']['name']}}";
            savingsproductG['type'] = "{{$elements['savingsproduct']['type']}}";
            savingsproductG['onload'] = "{{isset($elements['savingsproduct']['onload'])? $elements['savingsproduct']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(savingsproductG['name'])) {
                savingsproductG['selected'] = requestDataM[savingsproductG['name']];
            }
        @endif

        //##producttypeG check by assumed id
        @if(isset($elements['producttype']))
            producttypeG['exist'] = true;
            producttypeG['id'] = "{{$elements['producttype']['id']}}";
            producttypeG['name'] = "{{$elements['producttype']['name']}}";
            producttypeG['type'] = "{{$elements['producttype']['type']}}";
            producttypeG['onload'] = "{{isset($elements['producttype']['onload'])? $elements['producttype']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(producttypeG['name'])) {
                producttypeG['selected'] = requestDataM[producttypeG['name']];
            }
        @endif

        //##fundingOrgG check by assumed id
        @if(isset($elements['fundingOrg']))
            fundingOrgG['exist'] = true;
            fundingOrgG['id'] = "{{$elements['fundingOrg']['id']}}";
            fundingOrgG['name'] = "{{$elements['fundingOrg']['name']}}";
            fundingOrgG['type'] = "{{$elements['fundingOrg']['type']}}";
            fundingOrgG['onload'] = "{{isset($elements['fundingOrg']['onload'])? $elements['fundingOrg']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(fundingOrgG['name'])) {
                fundingOrgG['selected'] = requestDataM[fundingOrgG['name']];
            }
        @endif

        //##fieldofficerdropdownG check by assumed id
        @if(isset($elements['fieldofficerdropdown']))
            fieldofficerdropdownG['exist'] = true;
            fieldofficerdropdownG['id'] = "{{$elements['fieldofficerdropdown']['id']}}";
            fieldofficerdropdownG['name'] = "{{$elements['fieldofficerdropdown']['name']}}";
            fieldofficerdropdownG['type'] = "{{$elements['fieldofficerdropdown']['type']}}";
            fieldofficerdropdownG['onload'] = "{{isset($elements['fieldofficerdropdown']['onload'])? $elements['fieldofficerdropdown']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(fieldofficerdropdownG['name'])) {
                fieldofficerdropdownG['selected'] = requestDataM[fieldofficerdropdownG['name']];
            }
        @endif

        //##creditofficerdropdownG check by assumed id
        @if(isset($elements['creditofficerdropdown']))
            creditofficerdropdownG['exist'] = true;
            creditofficerdropdownG['id'] = "{{$elements['creditofficerdropdown']['id']}}";
            creditofficerdropdownG['name'] = "{{$elements['creditofficerdropdown']['name']}}";
            creditofficerdropdownG['type'] = "{{$elements['creditofficerdropdown']['type']}}";
            creditofficerdropdownG['onload'] = "{{isset($elements['creditofficerdropdown']['onload'])? $elements['creditofficerdropdown']['onload'] : 0}}";
            creditofficerdropdownG['required'] = "{{isset($elements['creditofficerdropdown']['required'])? $elements['creditofficerdropdown']['required'] : 0}}";
            if (requestDataM.hasOwnProperty(creditofficerdropdownG['name'])) {
                creditofficerdropdownG['selected'] = requestDataM[creditofficerdropdownG['name']];
            }
        @endif

        //##monthYearG check by assumed id
        @if(isset($elements['monthYear']))
            monthYearG['exist'] = true;
            monthYearG['id'] = "{{$elements['monthYear']['id']}}";
            monthYearG['name'] = "{{$elements['monthYear']['name']}}";
            monthYearG['type'] = "{{$elements['monthYear']['type']}}";
            monthYearG['onload'] = "{{isset($elements['monthYear']['onload'])? $elements['monthYear']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(monthYearG['name'])) {
                monthYearG['selected'] = requestDataM[monthYearG['name']];
            }
        @endif

        //##dayG check by assumed id
        @if(isset($elements['day']))
            dayG['exist'] = true;
            dayG['id'] = "{{$elements['day']['id']}}";
            dayG['name'] = "{{$elements['day']['name']}}";
            dayG['type'] = "{{$elements['day']['type']}}";
            dayG['onload'] = "{{isset($elements['day']['onload'])? $elements['day']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(dayG['name'])) {
                dayG['selected'] = requestDataM[dayG['name']];
            }
        @endif

        //##loanStatusG check by assumed id
        @if(isset($elements['loanStatus']))
            loanStatusG['exist'] = true;
            loanStatusG['id'] = "{{$elements['loanStatus']['id']}}";
            loanStatusG['name'] = "{{$elements['loanStatus']['name']}}";
            loanStatusG['type'] = "{{$elements['loanStatus']['type']}}";
            loanStatusG['onload'] = "{{isset($elements['loanStatus']['onload'])? $elements['loanStatus']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(loanStatusG['name'])) {
                loanStatusG['selected'] = requestDataM[loanStatusG['name']];
            }
        @endif

        //##transactionTypeG check by assumed id
        @if(isset($elements['transactionType']))
            transactionTypeG['exist'] = true;
            transactionTypeG['id'] = "{{$elements['transactionType']['id']}}";
            transactionTypeG['name'] = "{{$elements['transactionType']['name']}}";
            transactionTypeG['type'] = "{{$elements['transactionType']['type']}}";
            transactionTypeG['onload'] = "{{isset($elements['transactionType']['onload'])? $elements['transactionType']['onload'] : 0}}";
            if (requestDataM.hasOwnProperty(transactionTypeG['name'])) {
                transactionTypeG['selected'] = requestDataM[transactionTypeG['name']];
            }
        @endif

</script>

<script type="text/javascript">

    $(document).ready(function() {
        //##not dependent on ajax
        var branchId = "{{ $loggedInBranch }}";

        if (productCategoryG['exist'] == true && productCategoryG['onload'] != 0) {
            fnAjaxGetLoanProductCategory();
        }

        if (loanRepaymentFrequencyG['exist'] == true && loanRepaymentFrequencyG['onload'] != 0) {
            fnAjaxGetLoanRepaymentFrequency();
        }

        if (loanProductG['exist'] == true && branchId > 1) {
            fnAjaxGetLoanProduct(branchId);
        }

        if (samityG['exist'] == true && branchId > 1) {
            fnAjaxGetSamity(branchId);
        }

        if (savingsproductG['exist'] == true && savingsproductG['onload'] != 0) {
            fnAjaxGetSavingsProduct();
        }

        if (producttypeG['exist'] == true && producttypeG['onload'] != 0) {
            fnAjaxGetProductType();
        }

        if (fundingOrgG['exist'] == true && fundingOrgG['onload'] != 0) {
            fnAjaxGetFundingOrg();
        }

        if (loanStatusG['exist'] == true && loanStatusG['onload'] != 0) {
            fnAjaxGetLoanStatus();
        }

        if (fieldofficerdropdownG['exist'] == true && branchId > 1) {
            fnAjaxGetFieldoFficerdropdown(branchId);
        }

        if (creditofficerdropdownG['exist'] == true && branchId > 1) {
            fnAjaxGetCriditOfficerdropdown(branchId);
        }

        if (transactionTypeG['exist'] == true && transactionTypeG['onload'] != 0) {
            fnAjaxGetTransactionType();
        }

    });

    //This part for on change element
        if (samityG['exist'] == true){
            $("#"+samityG['id']).change(function(e){
                if(memberG['exist'] == true){
                    fnAjaxGetMember();
                }
            });
        }
        if (productCategoryG['exist'] == true){
            $("#"+productCategoryG['id']).change(function(e){
                if(loanProductG['exist'] == true){
                    fnAjaxGetLoanProduct();
                }
            });
        }

        if (producttypeG['exist'] == true){
            $("#"+producttypeG['id']).change(function(e){
                if(savingsproductG['exist'] == true){
                    fnAjaxGetSavingsProduct();
                }
            });
        }

        if (fundingOrgG['exist'] == true){
            $("#"+fundingOrgG['id']).change(function(e){
                if(loanProductG['exist'] == true){
                    fnAjaxGetLoanProduct();
                }
            });
        }

        if (fieldofficerdropdownG['exist'] == true){
            $("#"+fieldofficerdropdownG['id']).change(function(e){
                if(samityG['exist'] == true){
                    fnAjaxGetSamity();
                }
            });
        }

        if (creditofficerdropdownG['exist'] == true){
            $("#"+creditofficerdropdownG['id']).change(function(e){
                if(samityG['exist'] == true){
                    fnAjaxGetSamity();
                }
            });
        }

</script>

<script>
    function fnAjaxGetLoanProductCategory(){
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetLoanProductCategory') }}",
            dataType: "json",
            // data: {
            //     moduleName: moduleName
            // },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];


                    $('#'+productCategoryG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+productCategoryG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetLoanRepaymentFrequency(){
        let selectedValue = $('#'+loanRepaymentFrequencyG['id']).val();
        if (selectedValue == '' || selectedValue == null || typeof (selectedValue) == 'undefined'){
            if(loanRepaymentFrequencyG['exist'] == true && typeof loanRepaymentFrequencyG['selected'] != "undefined" && loanRepaymentFrequencyG['selected'] != ''){
                selectedValue = loanRepaymentFrequencyG['selected'];
            }
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetLoanRepaymentFrequency') }}",
            dataType: "json",
            // data: {
            //     moduleName: moduleName
            // },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];


                    $('#'+loanRepaymentFrequencyG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+loanRepaymentFrequencyG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                    // if (selectedValue != '' && typeof (selectedValue) != 'undefined'){
                    //     $('#'+categoryG['id']).val(selectedValue);

                    //     if (subcategoryG['exist'] == true){
                    //         $("#"+categoryG['id']).trigger("change");
                    //     }
                    // }

                }
            }
        });
    }

    function fnAjaxGetLoanProduct(branch_id=null){
        var productCategoryId = $('#'+productCategoryG['id']).val();
        var branchId = null;

        if(branch_id == null)
        {
            branchId = $('#'+branchG['id']).val();
        }
        else
        {
            branchId = branch_id;
        }
        var fundingOrgId = $('#'+fundingOrgG['id']).val();

        $.ajax({
            method: "GET",
            url: "{{route('getLoanProducts')}}",
            dataType: "json",
            data: {
                productCategory: productCategoryId,
                branchId: '',
                fundingOrgId: fundingOrgId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['data'];


                    $('#'+loanProductG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+loanProductG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetSamity(branch_id=null){
        var branchId = null;

        if(branch_id == null)
        {
            branchId = $('#'+branchG['id']).val();
        }
        else
        {
            branchId = branch_id;
        }

        var fieldOfficerId = null;

        if (fieldofficerdropdownG['exist'] == true){
            fieldOfficerId = $('#'+fieldofficerdropdownG['id']).val();
        }
        else if(creditofficerdropdownG['exist'] == true){
            fieldOfficerId = $('#'+creditofficerdropdownG['id']).val();
        }

        // var fieldOfficerId = $('#'+creditofficerdropdownG['id']).val();
        // var fieldOfficerId = $('#'+fieldofficerdropdownG['id']).val();
        var monthYear = $('#'+monthYearG['id']).val();
        var day = $('#'+dayG['id']).val();

        // console.log(fieldOfficerId);

        $.ajax({
            method: "GET",
            url: "{{route('getSamityOfBranch')}}",
            dataType: "json",
            data: {
                branchId: branchId,
                fieldOfficerId: fieldOfficerId,
                monthYear: monthYear,
                day: day
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['data'];

                    if(samityG['required'] == 1)
                    {
                        $('#'+samityG['id']).empty().append($('<option>', {
                            value: "",
                            text: "Select One"
                        }));
                    }
                    else
                    {
                        $('#'+samityG['id']).empty().append($('<option>', {
                            value: "",
                            text: "All"
                        }));
                    }

                    $.each(result_data, function (i, item) {

                        $('#'+samityG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name+' ['+item.samityCode+']'
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetMember(samity_id=null){
        var samityId = null;

        if(samity_id == null)
        {
            samityId = $('#'+samityG['id']).val();
        }
        else
        {
            samityId = samity_id;
        }
        // console.log(samityId);
        $.ajax({
            method: "GET",
            url: "{{route('getMemberOfSamity')}}",
            dataType: "json",
            data: {
                samityId: samityId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['data'];

                    if(memberG['required'] == 1)
                    {
                        $('#'+memberG['id']).empty().append($('<option>', {
                            value: "",
                            text: "Select One"
                        }));
                    }
                    else
                    {
                        $('#'+memberG['id']).empty().append($('<option>', {
                            value: "",
                            text: "All"
                        }));
                    }

                    $.each(result_data, function (i, item) {

                        $('#'+memberG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name+' ['+item.memberCode+']'
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetSavingsProduct(){
        var ProductTypeId = $('#'+producttypeG['id']).val();
        $.ajax({
            method: "GET",
            url: "{{ route('getSavingsProducts') }}",
            dataType: "json",
            data: {
                ProductTypeId: ProductTypeId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['data'];


                    $('#'+savingsproductG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+savingsproductG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name+'['+item.productCode+']'
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetProductType(){
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetMfnProductType') }}",
            dataType: "json",
            // data: {
            //     branchId: branchId
            // },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];


                    $('#'+producttypeG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+producttypeG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetFundingOrg(){
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetFundingOrg') }}",
            dataType: "json",
            // data: {
            //     branchId: branchId
            // },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];


                    $('#'+fundingOrgG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+fundingOrgG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetFieldoFficerdropdown(branch_id=null){
        var branchId = null;

        if(branch_id == null)
        {
            branchId = $('#'+branchG['id']).val();
        }
        else
        {
            branchId = branch_id;
        }
        $.ajax({
            method: "GET",
            url: "{{route('getFieldOfficerOfBranch')}}",
            dataType: "json",
            data: {
                branchId: branchId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['data'];


                    $('#'+fieldofficerdropdownG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+fieldofficerdropdownG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetCriditOfficerdropdown(branch_id=null){
        var branchId = null;

        if(branch_id == null)
        {
            branchId = $('#'+branchG['id']).val();
        }
        else
        {
            branchId = branch_id;
        }
        $.ajax({
            method: "GET",
            url: "{{route('getCreditOfficerOfBranch')}}",
            dataType: "json",
            data: {
                branchId: branchId
            },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['data'];


                    if(creditofficerdropdownG['required'] == 1)
                    {
                        $('#'+creditofficerdropdownG['id']).empty().append($('<option>', {
                            value: "",
                            text: "Select One"
                        }));
                    }
                    else
                    {
                        $('#'+creditofficerdropdownG['id']).empty().append($('<option>', {
                            value: "",
                            text: "All"
                        }));
                    }

                    $.each(result_data, function (i, item) {

                        $('#'+creditofficerdropdownG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetLoanStatus()
    {
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetLoanStatus') }}",
            dataType: "json",
            // data: {
            //     branchId: branchId
            // },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];


                    $('#'+loanStatusG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+loanStatusG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetTransactionType(){
        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetMfnTransactionType') }}",
            dataType: "json",
            // data: {
            //     branchId: branchId
            // },
            success: function (response) {

                if (response['status'] == 'success') {
                    let result_data = response['result_data'];


                    $('#'+transactionTypeG['id']).empty().append($('<option>', {
                        value: "",
                        text: "All"
                    }));

                    $.each(result_data, function (i, item) {

                        $('#'+transactionTypeG['id']).append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                    });

                }
            }
        });
    }
</script>
