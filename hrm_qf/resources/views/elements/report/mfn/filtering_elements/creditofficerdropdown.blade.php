<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="creditOfficerId" id="creditOfficerId"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
        @if (isset($element['required']) && $element['required'])
        <option value="">Select One</option>
        @else
        <option value="">All</option>
        @endif
        </select>
    </div>
</div>