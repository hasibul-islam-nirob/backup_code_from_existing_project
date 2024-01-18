<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="division" id="division"
            @if (isset($element['required']) && $element['required'])
                required 
            @endif >
            
            @if (isset($element['required']) && $element['required'])
                <option value="">Select One</option>
            @else
                <option value="">All</option>
            @endif
        </select>
    </div>
</div>

<script>
    $.ajax({
        type: "POST",
        url: "./getDivisions",
        data: { },
        dataType: "json",
        success: function (response) {
            $(`#division option:gt(0)`).remove();
            $.each(response, function (index, value) { 
                $('#division').append(`<option value='${value.id}'>${value.label}</option>`);
            });
        },
        error: function () {
            alert('error!');
        }
    });

    $('#division').change(function(e){
        $(`#district option:gt(0)`).remove();
        $(`#upozila option:gt(0)`).remove();
        $(`#union option:gt(0)`).remove();
        $(`#village option:gt(0)`).remove();

        if(!$(this).val()){
            return;
        }

        $.ajax({
            type: "POST",
            url: "./getDistricts",
            data: {
                divisionId: $(this).val(),
            },
            dataType: "json",
            success: function (response) {
                $.each(response, function (index, value) { 
                    $('#district').append(`<option value='${index}'>${value}</option>`);
                });
            },
            error: function () {
                alert('error!');
            }
        });
    });
</script>