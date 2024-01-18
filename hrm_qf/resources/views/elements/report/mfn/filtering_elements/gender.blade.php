<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="gender" id="gender"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
        <option value="">All</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        </select>
    </div>
</div>