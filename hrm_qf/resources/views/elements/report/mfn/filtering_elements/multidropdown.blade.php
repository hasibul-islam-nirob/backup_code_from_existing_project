@foreach ($element as $drowdown)
    <div class="col-lg-2">
        @if (isset($element['required']) && $element['required'])
            <label class="input-title RequiredStar">{{$element['label']}}</label>
        @else 
            <label class="input-title">{{$element['label']}}</label>
        @endif
        <div class="input-group">
            <select class="form-control clsSelect2" name="{{ $drowdown['name'] }}" id="{{ $drowdown['id'] }}" @if (isset($drowdown['required']) && $drowdown['required']) required @endif>
                @foreach ($drowdown['options'] as $indx => $item)
                    <option value="{{ $indx }}">{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endforeach
