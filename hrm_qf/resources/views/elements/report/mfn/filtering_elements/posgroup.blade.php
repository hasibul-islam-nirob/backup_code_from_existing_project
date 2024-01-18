<div class="col-lg-2 mt-1">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" 
        @if (isset($element['id'])) id="{{ $element['id'] }}" @else id="group_id" @endif
        @if (isset($element['name'])) name="{{ $element['name'] }}" @else name="group_id" @endif
        @if (isset($element['required']) && $element['required']) required @endif
        onchange="fnAjaxGetCategory(); fnAjaxGetSubCat(); fnAjaxGetModel(); fnAjaxGetProduct();"
        >
            @if (isset($element['required']) && $element['required'])
            <option value="">Select One</option>
            @else
            <option value="">All</option>
            @endif
            
            @php
                $PGroupList = DB::table('pos_p_groups')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->select('id', 'group_name')
                    ->orderBy('group_name', 'ASC')
                    ->get();
            @endphp
            @foreach ($PGroupList as $Row)
                <option value="{{ $Row->id }}">{{ $Row->group_name }}</option>
            @endforeach
        </select>
    </div>
</div>