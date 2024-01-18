@php
    use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;
    use App\Services\CommonService as Common;

    if(!isset($element['multiple'])){
        $element['multiple']= false;
    }

    if(!isset($element['withHeadOffice'])){
        $element['withHeadOffice']= true;
    }

    if($key == 'zone' || $key == 'region' || $key == 'area' || $key == 'branch' || $key == 'branchFrom' || $key == 'branchTo'){
        if(!isset($element['required'])){
            $element['required']= false;
        }

        if(isset($element['default_option']) == false){
            if($element['required'] == true){
                $element['default_option']= "One";
            }
            else {
                $element['default_option']= "All";
            }
        }
    }

@endphp

@if($key == 'zone')

    {!! HTML::forZoneFeildSearch($element['default_option'], $element['name'], $element['id'], $element['label'], null, $element['required'])!!}

@elseif($key == 'region')
    {!! HTML::forRegionFeildSearch($element['default_option'], $element['name'], $element['id'], $element['label'], null, $element['required'])!!}

@elseif($key == 'area')

    {!! HTML::forAreaFeildSearch($element['default_option'], $element['name'], $element['id'], $element['label'], null, $element['required'], $element['multiple'])!!}

@elseif($key == 'branch' || $key == 'branchFrom' || $key == 'branchTo')

    {!! HTML::forBranchFeildSearch_new($element['default_option'], $element['name'], $element['id'], $element['label'], null, $element['required'], $element['withHeadOffice'])!!}

{{-- @elseif($key == 'fiscalYearHr') --}}
@elseif ($key == 'payscale')

    <div class="col-lg-2 col-md-2">
        <label class="input-title">Payscale</label>
        <div class="input-group">
            {!! HTML::forPayscaleFieldHr('filter_payscale_id', 'payscale_id') !!}
        </div>
    </div>

@elseif ($key == 'grade')
    <div class="col-lg-2 col-md-2">
        <label class="input-title">Grade</label>
        <div class="input-group">
            {!! HTML::forGradeFieldHr('filter_grade') !!}
        </div>
    </div>

@elseif ($key == 'level')
    <div class="col-lg-2 col-md-2">
        <label class="input-title">Level</label>
        <div class="input-group">
            {!! HTML::forLevelFieldHr('filter_level') !!}
        </div>
    </div>

@elseif ($key == 'recruitment_type')
    <div class="col-lg-2 col-md-2">
        <label class="input-title">Recruitment Type</label>
        <div class="input-group">
            {!! HTML::forRecruitmentFieldHr('filter_recruitment_type_id') !!}
        </div>
    </div>

@elseif($key == 'leaveCategory')
    {!! HTML::forLeaveCatFeildSearch('all') !!}

@elseif($key == 'leaveType')
    {!! HTML::forLeaveTypeFeildSearch('all') !!}

@else

    <div
        @if (isset($element['col']))
            class="{{ $element['col'] }} {{ isset($element['divExClass']) ? $element['divExClass'] : '' }}"
        @else
            class="col-md-2 {{ isset($element['divExClass']) ? $element['divExClass'] : '' }}"
        @endif
    >

        @if (isset($element['required']) && $element['required'])
            <label class="input-title RequiredStar">{{$element['label']}}</label>
        @else
            <label class="input-title">{{$element['label']}}</label>
        @endif

        <div class="input-group">
            <select style="width: 100%" class="form-control
                {{ isset($element['exClass']) ? $element['exClass'] : 'clsSelect2 ' }}"
                name="{{$element['name']}}" id="{{$element['id']}}"

                @if (isset($element['multiple']) && $element['multiple']) multiple @endif
                @if (isset($element['jsEvent'])) {{ $element['jsEvent'] }} @endif
                @if (isset($element['required']) && $element['required'])  required @endif
                style="width: 100%"
            >
                @if (isset($element['default_option']))
                    <option value="{{ isset($element['default_option_value']) ? $element['default_option_value'] : '' }}">

                        @if ($element['default_option'] == 'one' || $element['default_option'] == 'One' || $element['default_option'] == 'ONE')
                            Select One
                        @elseif ($element['default_option'] == 'multiple' || $element['default_option'] == 'Multiple' || $element['default_option'] == 'MULTIPLE')
                            Select One/Multiple
                        @else
                            {{ $element['default_option'] }}
                        @endif
                    </option>
                @endif

                @if(isset($element['options']))
                    @foreach ($element['options'] as $key => $item)
                        <option value="{{$key}}" <?= (isset($element['selected_value']) && $element['selected_value'] == $key) ? "selected" : "" ?> >
                            {{$item}}
                        </option>
                    @endforeach
                @endif

            </select>
        </div>
    </div>
@endif

