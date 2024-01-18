<div class="col-lg-2 mt-1">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="savingsProduct" id="savingsProduct"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            <option value="">All</option>
            @php
                $sProduct = DB::table('mfn_savings_product')
                    ->where('is_delete', 0)
                    ->get();
            @endphp 
            @foreach ($sProduct as $item)
            <option value="{{ $item->id }}">{{ $item->name }} [{{ $item->productCode }}]</option>
            @endforeach
        </select>
    </div>
</div>
