<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif

    <div class="input-group">
        <input type="text" class="form-control" 
            name="{{isset($element['name']) ? $element['name'] : 'nameOrCode'}}" 
            id="{{isset($element['id']) ? $element['id'] : 'nameOrCode'}}"
            value="" autocomplete="off"
            @if (isset($element['required']) && $element['required'])
                required
            @endif  
        >
    </div>
</div>