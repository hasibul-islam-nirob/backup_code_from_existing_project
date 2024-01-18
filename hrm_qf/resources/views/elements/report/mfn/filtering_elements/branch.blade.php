@php
use App\Services\HtmlService as HTML;
use App\Services\HrService as HRS;

if(!isset($element['required'])){
    $element['required']= false;
}

if(!isset($element['withHeadOffice'])){
    $element['withHeadOffice']= false;
}
@endphp

@if(isset($element['required']) && $element['required']==true)
{!! HTML::forBranchFeildSearch_new('one','branchId','branchId', $element['label'], null, $element['required'], $element['withHeadOffice'])!!}
@else
{!! HTML::forBranchFeildSearch_new('all','branchId','branchId', $element['label'], null, $element['required'], $element['withHeadOffice'])!!}
@endif

<script>
    $('#branchId').change(function(e){
        // console.log('got hit');
        branchId = $('#branchId').val();
        @if(array_key_exists('samity', $allElements)) 
            populateSamityDropDown(branchId);
        @endif

        @if(array_key_exists('product', $allElements))
            populateProductDropDown(branchId);
        @endif

        @if(array_key_exists('fieldofficerdropdown', $allElements)) 
            populateFieldOfficerDropDown(branchId);
        @endif

        @if(array_key_exists('creditofficerdropdown', $allElements)) 
            populateCreditOfficerDropDown(branchId);
        @endif

        dependecyClearForBrnach();
    });

    function populateFieldOfficerDropDown(branchId){
        
        $('#fieldOfficerId option:gt(0)').remove();
        if(!branchId){
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "{{route('getFieldOfficerOfBranch')}}",
            data: {
                branchId: branchId,
            },
            dataType: "json",
            success: function (response) {
                
                $.each(response.data, function (index, value) { 
                    $('#fieldOfficerId').append(`<option value='${value.id}'>${value.name}</option>`);
                });
            },
            error: function () {
                alert('error!');
            }
        });
    }

    function populateCreditOfficerDropDown(branchId){
        
        $('#creditOfficerId option:gt(0)').remove();
        if(!branchId){
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "{{route('getCreditOfficerOfBranch')}}",
            data: {
                branchId: branchId,
            },
            dataType: "json",
            success: function (response) {
                
                $.each(response.data, function (index, value) { 
                    $('#creditOfficerId').append(`<option value='${value.id}'>${value.name}</option>`);
                });
            },
            error: function () {
                alert('error!');
            }
        });
    }

    function populateSamityDropDown(branchId){
        $(`#samityId option:gt(0)`).remove();
        if(!branchId){
            return;
        }
        $.ajax({
            type: "POST",
            url: " {{route('getSamityOfBranch')}}",
            data: {
                branchId: branchId,
            },
            dataType: "json",
            success: function (response) {
                $.each(response.data, function (index, samity) { 
                    $('#samityId').append(`<option value='${samity.id}'>${samity.name} [${samity.samityCode}]</option>`);
                });
            },
            error: function(){
                alert('error!');
            }
        });
    }

    function populateProductDropDown(branchId=null){
        $(`#product option:gt(0)`).remove();
        // if(!branchId){
        //     return;
        // }
        if(branchId == null){
            branchId = $('#branchId').val();
        }
        $.ajax({
            type: "POST",
            url: "{{ route('getLoanProducts') }}",
            data: {
                
                branchId: branchId,
                @if(isset($fundingOrg))// if funding org exist, need to consider it in filtering
                    fundingOrgId : $('#fundingOrg').val(),
                @endif
                @if(isset($productCategory)) // if product Category org exist, need to consider it in filtering
                    productCategory : $('#productCategory').val(),
                @endif
            },
            dataType: "json",
            success: function (response) {
                // console.log(response.data)
                $.each(response.data, function (index, product) { 
                    $('#product,#productFrom').append(`<option value='${product.id}'>${product.name}</option>`);
                    
                });
            },
            error: function(){
                alert('error!');
            }
        });
    }

    
    //if dependent element exists
    function dependecyClearForBrnach(){
        if($('#product').val() != undefined){
            $(`#product option:gt(0)`).remove();
        }
        if($('#productFrom').val() != undefined){
            $(`#productFrom option:gt(0)`).remove();
        }
        if($('#samityId').val() != undefined){
            $(`#samityId option:gt(0)`).remove();
        }
    }
    
    @php
        $branch_arr = HRS::getUserAccesableBranchIds();                            
    @endphp 
    @if(count($branch_arr) == 1)
        $('#branchId').change();
    @endif

</script>