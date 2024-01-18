@php
use App\Services\HtmlService as HTML;
if(!isset($element['required'])){
    $element['required']= false;
}
@endphp
{!! HTML::forZoneFeildSearch('all','zoneId','zoneId', $element['label'], null, $element['required'])!!}

{!! HTML::forRegionFeildSearch('all','regionId','regionId', 'Region', null, $element['required'])!!}

<script>
    $('#zoneId').change(function(e){
        // $(`#areaId option:gt(0)`).remove();
        // $(`#branchId option:gt(0)`).remove();
        // dependecyClearForBrnach();
        // if(!$(this).val()){
        //     return;
        // }

        fnAjaxGetRegion();
        fnAjaxGetArea();

        // $.ajax({
        //     type: "POST",
        //     url: "{{ route('getAreasAndBranchOfZone') }}",
        //     data: {
        //         zoneId: $(this).val(),
        //     },
        //     dataType: "json",
        //     success: function (response) {
        //         $(`#areaId option:gt(0)`).remove();
        //         $.each(response.areas, function (index, value) {
        //             $('#areaId').append(`<option value='${value.id}'>${value.area_name} [${value.area_code}]</option>`);
        //         });

        //         $(`#branchId option:gt(0)`).remove();
        //         $.each(response.branchs, function (index, value) {
        //             $('#branchId').append(`<option value='${value.id}'>${value.branch_name} [${value.branch_code}]</option>`);
        //         });
        //         dependecyClearForBrnach();
        //     },
        //     error: function () {
        //         alert('error!');
        //     }
        // });
    });

    $('#regionId').change(function(e){

        fnAjaxGetArea();
    });
</script>
