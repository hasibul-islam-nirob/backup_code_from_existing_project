<div class="col-lg-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else 
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <input type="text" class="form-control" id="monthYear" name="monthYear"
            placeholder="MM-YYYY" autocomplete="off"
            @if (isset($element['required']) && $element['required'])
            required
            @endif
        >
    </div>
</div>
<script>
    $('#monthYear').datepicker({
        dateFormat: 'MM-yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        todayButton: false,
        onClose: function (dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('MM-yy', new Date(year, month, 1)));
        }
    });

    $("#monthYear").click(function () {
        $(".ui-datepicker-calendar").hide();
    });

    $("#monthYear").focus(function () {
        $(".ui-datepicker-calendar").hide();
    });
    
    $("#monthYear").on('click', function () {
        this.value = '';
    });
</script>