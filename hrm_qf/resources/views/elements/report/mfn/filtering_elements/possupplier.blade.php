<div class="col-lg-2 mt-1">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" 
        @if (isset($element['id'])) id="{{ $element['id'] }}" @else id="supplier_id" @endif
        @if (isset($element['name'])) name="{{ $element['name'] }}" @else name="supplier_id" @endif
        @if (isset($element['required']) && $element['required']) required @endif
        onchange="fnAjaxGetProduct();"
        >
            @if (isset($element['required']) && $element['required'])
            <option value="">Select One</option>
            @else
            <option value="">All</option>
            @endif
            
            @php    
                $supplierList = DB::table('pos_suppliers')
                    ->where([['is_delete', 0]])
                    ->select('id', 'sup_comp_name')
                    ->orderBy('sup_comp_name', 'ASC')
                    ->get();
            @endphp
            @foreach ($supplierList as $Row)
                <option value="{{ $Row->id }}">{{ $Row->sup_comp_name }}</option>
            @endforeach
        </select>
    </div>
</div>