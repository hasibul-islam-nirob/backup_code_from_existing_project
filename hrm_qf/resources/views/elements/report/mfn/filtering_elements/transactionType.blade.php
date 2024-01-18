@php
$transactionType = DB::table('mfn_savings_transaction_types')
    ->where([['status', 1]])
    ->get();
@endphp
<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="transactionType" id="transactionType"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
        @if (isset($element['required']) && $element['required'])
            <option value="">Select One</option> 
            @foreach ($transactionType as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        @else
            <option value="">All</option>
            @foreach ($transactionType as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        @endif
        </select>
    </div>
</div>