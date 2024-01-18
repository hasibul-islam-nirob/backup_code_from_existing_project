<div class="col-lg-2">

    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    
    <div class="input-group">
        <input type="text" class="form-control datepicker-custom" id="date" name="date"
            placeholder="DD-MM-YYYY" 
            @if (isset($element['required']) && $element['required'])
            required
            @endif
        >
    </div>
</div>
<script>
    $("#date").on('click', function () {
        this.value = '';
    });
</script>


