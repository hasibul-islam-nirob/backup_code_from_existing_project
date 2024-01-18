<div class="col-lg-2 mt-1">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" 
        @if (isset($element['id'])) id="{{ $element['id'] }}" @else id="brand_id" @endif
        @if (isset($element['name'])) name="{{ $element['name'] }}" @else name="brand_id" @endif
        @if (isset($element['required']) && $element['required']) required @endif
        onchange="fnAjaxGetProduct();"
        >
            @if (isset($element['required']) && $element['required'])
            <option value="">Select One</option>
            @else
            <option value="">All</option>
            @endif
            
            @php
                $BrandList = DB::table('pos_p_brands')
                    ->where([['is_delete', 0]])
                    ->select('id', 'brand_name')
                    ->orderBy('brand_name', 'ASC')
                    ->get();
            @endphp
            @foreach ($BrandList as $Row)
                <option value="{{ $Row->id }}">{{ $Row->brand_name }}</option>
            @endforeach
        </select>
    </div>
</div>