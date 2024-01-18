<div class="col-lg-2 mt-1" id="loanTypeDiv">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    
    <div class="input-group">
        <select class="form-control clsSelect2" name="loanType" id="loanType"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            <option value="">All</option> 
            <option value="regular">Regular</option>
            <option value="onetime">Onetime</option>
        </select>
    </div>
</div>