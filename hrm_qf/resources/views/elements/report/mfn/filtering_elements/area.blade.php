@php
use App\Services\HtmlService as HTML;
if(!isset($element['required'])){
    $element['required']= false;
}
@endphp

{!! HTML::forAreaFeildSearch('all','areaId','areaId', $element['label'], null, $element['required'])!!}

<script>
    $('#areaId').change(function(e){


        fnAjaxGetBranch();
        // $(`#branchId option:gt(0)`).remove();
        // dependecyClearForBrnach();
        // if(!$(this).val()){
        //     return;
        // }

        // $.ajax({
        //     type: "POST",
        //     url: " {{route('getBranchOfArea')}}",
        //     data: {
        //         areaId: $(this).val(),
        //     },
        //     dataType: "json",
        //     success: function (response) {
                
        //         $.each(response.data, function (index, value) { 
        //             $('#branchId').append(`<option value='${value.id}'>${value.branch_name} [${value.branch_code}]</option>`);
        //         });
        //         dependecyClearForBrnach();
        //     },
        //     error: function () {
        //         alert('error!');
        //     }
        // });
    });
</script>