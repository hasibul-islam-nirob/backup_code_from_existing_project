<div @if (isset($element['col'])) class="{{ $element['col'] }}" @else class="col-sm-2" @endif >
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else
        <label class="input-title">{{$element['label']}}</label>
    @endif

    <div class="input-group">
        <input type="text" class="form-control endDate"
            placeholder="DD-MM-YYYY" autocomplete="off"
            {{-- id="endDate" name="endDate" --}}
            name="{{ isset($element['name']) ? $element['name'] : 'endDate' }}"
            id="{{ isset($element['id']) ? $element['id'] : 'endDate' }}"
            @if (isset($element['required']) && $element['required']) required @endif
            @if (isset($element['readonly']) && $element['readonly']) readonly @endif
            @if (isset($element['value'])) value="{{ $element['value'] }}" @endif
        >
    </div>
</div>

<script>
    $(".endDate").datepicker({
        dateFormat: 'dd-mm-yy',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
        minDate: new Date("{{ (new Datetime(App\Services\CommonService::getBranchSoftwareStartDate()))->format('Y-m-d') }}"),
        // minDate: new Date(y,m,d),
        // maxDate: new Date(),
        onClose: function (selectedDate) {
            $(".startDate").datepicker("option", "maxDate", selectedDate);
        }
    });


    $(".endDate").on('click', function () {
        this.value = '';
    });
</script>
