<div class="col-lg-2 mt-1">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="options" id="options"
            @if (isset($element['required']) && $element['required'])
                required
            @endif    
        >
            <option value="1">Loan Product</option>
            <option value="2">Loan Product category</option>
        </select>
    </div>
</div>