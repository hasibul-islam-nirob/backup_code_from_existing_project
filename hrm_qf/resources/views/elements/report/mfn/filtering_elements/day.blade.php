<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    
    <div class="input-group">
        <select class="form-control clsSelect2" name="day" id="day"
            @if (isset($element['required']) && $element['required'])
                required
            @endif
        >
            @if (isset($element['dafaultOption']) && $element['dafaultOption'])
                <option value="">{{ $element['dafaultOption'] }}</option>
            @else
                <option value="">Select Day</option>
            @endif
            <option value="Saturday">Saturday</option>
            <option value="Sunday">Sunday</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>

        </select>
    </div>
</div>