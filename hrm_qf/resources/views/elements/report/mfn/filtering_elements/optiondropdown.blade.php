<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        
        <select class="form-control clsSelect2" name="{{$element['name']}}" id="{{$element['id']}}"
            @if (isset($element['required']) && $element['required'])
                required
            @endif 
        >
            @foreach ($element['options'] as $ind=> $item)
                <option value="{{$ind}}">{{$item}}</option>
            @endforeach
        </select>
    </div>
</div>