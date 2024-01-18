<div class="col-lg-2 mt-1">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="productCategory" id="productCategory"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            @if (isset($element['required']) && $element['required'])
            <option value="">Select One</option>
            @else
            <option value="">All</option>
            @endif
            
            @php
                $prodCat = DB::table('mfn_loan_product_category as mlpc')
                    ->where('is_delete', 0)
                    ->select('id', 'name', DB::raw("CONCAT(mlpc.name, ' [', mlpc.id, ']') as prodCat"))
                    ->get();
            @endphp 
            @foreach ($prodCat as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<script>
    $('#productCategory').change(function(e){
        
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