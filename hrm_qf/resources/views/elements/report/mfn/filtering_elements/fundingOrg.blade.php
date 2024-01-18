<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="fundingOrg" id="fundingOrg"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
        
            <option value="">All</option>
            @php //## Product Query
                $element = DB::table('mfn_funding_orgs as forg')
                                ->where('is_delete', 0)
                                ->select('id', 'name', DB::raw("CONCAT(forg.name, ' [', forg.id, ']') as fundingOrg"))
                                ->get();
            @endphp
            @foreach ($element as $org)
            <option value="{{ $org->id }}">{{ $org->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<script>
     $('#fundingOrg').change(function(e){
        
        // populateProductDropDown();
        
        $('#product option:gt(0)').remove();
        $.ajax({
            type: "POST",
            url: "{{route('getLoanProducts')}}",
            data: {
                fundingOrgId : $('#fundingOrg').val(),
                branchId : $('#branchId').val(),
                productCategory : $('#productCategory').val(),
            },
            dataType: "json",
            success: function (response) {
                $.each(response.data, function (index, value) { 
                    $('#product').append(`<option value='${value.id}'>${value.name}</option>`);
                });
            },
            error: function () {
                alert('error!');
            }
        });
    });
</script>