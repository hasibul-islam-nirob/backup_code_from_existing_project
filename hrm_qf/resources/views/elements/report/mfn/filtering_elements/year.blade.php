<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        @php
            $headOfficeOpeningDate = DB::table('gnl_branchs')->where('id', 1)->value('branch_opening_date');
            $startYear = (int)date('Y', strtotime($headOfficeOpeningDate));
            $endYear = (int)date('Y') + 1;
            $elements = [];
            while ($endYear >= $startYear) {
                array_push($elements, $endYear);
                $endYear--;
            }
        @endphp
        <select class="form-control clsSelect2" name="year" id="year"
            @if (isset($element['required']) && $element['required'])
                required
            @endif  
        >
            <option value="">Select Year</option>
            @foreach ($elements as $element)
            <option value="{{ $element }}">{{ $element }}</option>
            @endforeach

        </select>
    </div>
</div>