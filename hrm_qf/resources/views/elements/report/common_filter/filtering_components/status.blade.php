
@php

    if ($element['module'] == "HR") {

         $statusArray = [
            // 0 => 'Draft',
            1 => 'Approved',
            2 => 'Rejected',
            3 => 'Processing',
        ];
        
    }else{
        $statusArray = [];
    }

    
@endphp

<div class="col-sm-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">

        <select name="{{$element['name']}}" id="{{$element['id']}}"  class="form-control clsSelect2" @if (isset($element['required']) && $element['required']) required @endif >
            
            <option value="">All</option>
            @foreach ($statusArray as $key => $value)
                <option value="{{$key}}">{{$value}}</option>
            @endforeach

        </select>


    </div>
</div>