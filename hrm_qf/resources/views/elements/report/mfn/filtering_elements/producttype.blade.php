<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="ProductType" id="ProductTypeId"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            @php
                $ProductTypeData = DB::table('mfn_savings_product_type')->get();
            @endphp 
            <option value="">All</option>
            @foreach ($ProductTypeData as $Row)
            <option value="{{ $Row->id }}">{{ $Row->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<script>
    $('#ProductTypeId').change(function(e){
        $('#savingsProduct option:gt(0)').remove();

        $.ajax({
            type: "POST",
            url: "{{route('getSavingsProducts')}}",
            data: {
                ProductTypeId : $('#ProductTypeId').val(),
            },
            dataType: "json",
            success: function (response) {
                $.each(response.data, function (index, value) { 
                    $('#savingsProduct').append(`<option value='${value.id}'>${value.name}</option>`);
                });
            },
            error: function () {
                alert('error!');
            }
        });
    });
</script>