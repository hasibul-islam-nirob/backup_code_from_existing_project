<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <select class="form-control clsSelect2" name="union" id="union"
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
     $('#union').change(function(e){
        $(`#village option:gt(0)`).remove();
        if(!$(this).val()){
            return;
        }

        $.ajax({
            type: "POST",
            url: "./getVillages",
            data: {
                upazilaId: $(this).val(),
            },
            dataType: "json",
            success: function (response) {
                $.each(response, function (index, value) { 
                    $('#village').append(`<option value='${index}'>${value}</option>`);
                });
            },
            error: function () {
                alert('error!');
            }
        });
    });
</script>