<div class="col-sm-2">
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        <input type="text" class="form-control" id="toMonthYear" name="toMonthYear"
            placeholder="MM-YYYY" autocomplete="off"
            @if (isset($element['required']) && $element['required'])
            required
            @endif
        >
    </div>
</div>
<script>
    // $('#toMonthYear').datepicker({
    //     dateFormat: 'MM-yy',
    //     changeMonth: true,
    //     changeYear: true,
    //     showButtonPanel: true,
    //     todayButton: false,
    //     onClose: function (dateText, inst) {
    //         var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
    //         var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
    //         $(this).val($.datepicker.formatDate('MM-yy', new Date(year, month, 1)));
    //     }
    // });

    $('#toMonthYear').datepicker({
        dateFormat: 'MM-yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        // dateFormat: 'MM-yy',
        // startView: "months",
        // minViewMode: "months",
        // orientation: 'bottom',
        // autoclose: true,
        // todayHighlight: true,
        // changeMonth: true,
        // changeYear: true,
        yearRange: '1900:+10',

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('MM-yy', new Date(year, month, 1)));
        },
        
        beforeShow: function() {
            if ((selDate = $(this).val()).length > 0){

                year = selDate.substring(selDate.length - 4, selDate.length);
                month = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                $(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
                $(this).datepicker('setDate', new Date(year, month, 1));
                
            }
        }
    });

    $("#toMonthYear").click(function () {
        $(".ui-datepicker-calendar").hide();
    });

    $("#toMonthYear").focus(function () {
        $(".ui-datepicker-calendar").hide();
    });

    $("#toMonthYear").on('click', function () {
        this.value = '';
    });
</script>
