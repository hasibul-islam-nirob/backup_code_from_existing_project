<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <input type="text" class="form-control" id="startDate" name="startDate"
            placeholder="DD-MM-YYYY" autocomplete="off"
            @if (isset($element['required']) && $element['required']) required @endif
            @if (isset($element['value'])) value="{{ $element['value'] }}" @endif
        >
    </div>
</div>
<script>

    $('#startDate').datepicker({
        dateFormat: 'dd-mm-yy',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
        // minDate: new Date(y,m,d),
        // maxDate : 'now',
        maxDate: new Date(),
        onClose: function (selectedDate) {
            $("#endDate").datepicker("option", "minDate", selectedDate);
        }
    });
    
    $("#startDate").on('click', function () {
        this.value = '';
    });
</script>