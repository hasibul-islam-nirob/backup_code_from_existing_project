<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="viewas" id="viewas"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
        <option value="">Select One</option>
        <option value="CO">Credit Officer</option>
        <option value="BM">Branch Manager</option>
        <option value="AM">Area Manager</option>
        <option value="ZM">Zone Manager</option>
        </select>
    </div>
</div>