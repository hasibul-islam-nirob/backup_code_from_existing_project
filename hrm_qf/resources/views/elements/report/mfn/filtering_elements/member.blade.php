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
        <select class="form-control clsSelect2" name="memberId" id="memberId"
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
    
    @if(array_key_exists('member', $allElements)) 
        // if(branchId > 1)
        // populateSamityDropDown(branchId);
    @endif
 
    function populateMemberDropDown(samityId){
        $(`#memberId option:gt(0)`).remove();
        if(!samityId){
            return;
        }
        $.ajax({
            type: "POST",
            url: " {{route('getMemberOfSamity')}}",
            data: {
                samityId: samityId,
            },
            dataType: "json",
            success: function (response) {
                $.each(response.data, function (index, member) { 
                    $('#memberId').append(`<option value='${member.id}'>${member.name} [${member.memberCode}]</option>`);
                });
            },
            error: function(){
                alert('error!');
            }
        });
    }

    


</script>