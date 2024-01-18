@php
    use App\Services\CommonService as CS;
    $loggedInBranch = CS::getBranchId();
@endphp

<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="samityId" id="samityId"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            @if(isset($element['required']) && $element['required']==true)
            <option value="">Select One</option>
            @else
            <option value="">All</option>
            @endif
        </select>
    </div>
</div>

<script>
    var branchId = "{{ $loggedInBranch }}";
    
    @if(array_key_exists('samity', $allElements)) 
        if(branchId > 1)
        // populateSamityDropDown(branchId);
    @endif
 
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
    $('#samityId').change(function(e){
        // console.log('got hit');
        samityId = $('#samityId').val();
        @if(array_key_exists('member', $allElements)) 
            populateMemberDropDown(samityId);
        @endif
    });


</script>