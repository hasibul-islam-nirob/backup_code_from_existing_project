<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    
    <div class="input-group">
        <select class="form-control clsSelect2" name="loanStatus" id="loanStatus"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            <option value="">All</option>
            @php
                $frequency = DB::table('mfn_loan_status')
                    ->where([['is_delete', 0]])
                    ->get();
            @endphp 
            @foreach ($frequency as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>