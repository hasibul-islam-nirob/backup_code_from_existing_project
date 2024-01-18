<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="viewtype" id="viewtype"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
        <option value="">Select One</option>
        <option value="summery">Summery</option>
        <option value="details">Details</option>
        </select>
    </div>
</div>