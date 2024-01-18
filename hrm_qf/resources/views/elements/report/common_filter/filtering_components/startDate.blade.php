
<div @if (isset($element['col'])) class="{{ $element['col'] }}" @else class="col-sm-2" @endif >
    @if (isset($element['required']) && $element['required'])
        <label class="input-title RequiredStar">{{$element['label']}}</label>
    @else
        <label class="input-title">{{$element['label']}}</label>
    @endif
    <div class="input-group">
        
        @if ($element['type'] == 'startDate')
            <input type="text" class="form-control startDate"
                placeholder="DD-MM-YYYY" autocomplete="off"
                {{-- id="startDate" name="startDate" --}}
                name="{{ isset($element['name']) ? $element['name'] : 'startDate' }}"
                id="{{ isset($element['id']) ? $element['id'] : 'startDate' }}"
                @if (isset($element['required']) && $element['required']) required @endif
                @if (isset($element['readonly']) && $element['readonly']) readonly @endif
                @if (isset($element['value'])) value="{{ $element['value'] }}" @endif
            >
        @else
            <input type="text" class="form-control datepickerNotRange"
                placeholder="DD-MM-YYYY" autocomplete="off"
                {{-- id="startDate" name="startDate" --}}
                name="{{ isset($element['name']) ? $element['name'] : '' }}"
                id="{{ isset($element['id']) ? $element['id'] : '' }}"
                @if (isset($element['required']) && $element['required']) required @endif
                @if (isset($element['readonly']) && $element['readonly']) readonly @endif
                @if (isset($element['value'])) value="{{ $element['value'] }}" @endif
            >
        @endif

        
    </div>
</div>

@php
// dd(App\Services\CommonService::getBranchSoftwareStartDate());
@endphp

<script>

    $('.startDate').datepicker({
        dateFormat: 'dd-mm-yy',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
        minDate: new Date("{{ (new Datetime(App\Services\CommonService::getBranchSoftwareStartDate()))->format('Y-m-d') }}"),
        // minDate: new Date(y,m,d),
        // maxDate : 'now',
        // maxDate: new Date(),
        onClose: function (selectedDate) {
            $(".endDate").datepicker("option", "minDate", selectedDate);
        }
    });

    $(".startDate").on('click', function () {
        this.value = '';
    });
</script>
