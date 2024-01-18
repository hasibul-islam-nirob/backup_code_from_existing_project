<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="product" id="product"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            <option value="">All</option>
        </select>
    </div>
</div>