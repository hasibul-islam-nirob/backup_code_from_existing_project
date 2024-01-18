<div class="col-sm-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        @php
            $elements = array(
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December'
            );
        @endphp
        <select class="form-control clsSelect2" name="month" id="month"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            <option value="">Select Month</option>
            @foreach ($elements as $key => $element)
            <option value="{{ $key }}">{{ $element }}</option>
            @endforeach
        </select>
    </div>
</div>
