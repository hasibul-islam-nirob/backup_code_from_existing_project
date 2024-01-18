<div class="col-lg-2 mt-1">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" 
        @if (isset($element['id'])) id="{{ $element['id'] }}" @else id="category_id" @endif
        @if (isset($element['name'])) name="{{ $element['name'] }}" @else name="category_id" @endif
        @if (isset($element['required']) && $element['required']) required @endif
        onchange="fnAjaxGetSubCat(); fnAjaxGetModel(); fnAjaxGetProduct();"
        >
            @if (isset($element['required']) && $element['required'])
            <option value="">Select One</option>
            @else
            <option value="">All</option>
            @endif
            
        </select>
    </div>
</div>