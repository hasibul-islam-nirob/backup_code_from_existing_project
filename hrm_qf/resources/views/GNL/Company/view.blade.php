@extends('Layouts.erp_master')

@section('content')

    @php
        use App\Services\CommonService as Common;

        $startTimeTx = $CompanyData->tx_start_time;
        $endTimeTx = $CompanyData->tx_end_time;

        $flag = false;

        if (!empty($startTimeTx) || $startTimeTx != '00:00:00') {
            $startTimeTx = (new DateTime($startTimeTx))->format('H:i');
        }

        if (!empty($endTimeTx) || $endTimeTx == '00:00:00') {
            $endTimeTx = (new DateTime($endTimeTx))->format('H:i');
        }

        $checked1 = $checked2 = '';

        if ($CompanyData->applicable_for == 2) {
            $checked2 = 'checked';
        } else {
            $checked1 = 'checked';
        }

        if ($CompanyData->schedule_flag == 1) {
            $flag = true;
        }

    @endphp

    <!-- Page -->
    <form id="comForm" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf

        {{-- Section --}}
        <div class="panel panel-default" style="box-shadow:0 0px 0px rgb(0 0 0 / 0%);">

            <div class="row">
                <div class="col-sm-6">
                    <div class="panel-heading p-2 mb-4">Basic Information</div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title RequiredStar">Group</label>
                        <div class="col-sm-9 form-group">

                            <div class="input-group">
                                <select class="form-control" disabled>
                                    @foreach ($GroupData as $Row)
                                        <option value="{{ $Row->id }}"
                                            {{ $CompanyData->group_id == $Row->id ? 'selected="selected"' : '' }}>
                                            {{ $Row->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title RequiredStar">Company Name</label>
                        <div class="col-sm-9 form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $CompanyData->comp_name }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title RequiredStar">Company Code</label>
                        <div class="col-sm-9 form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $CompanyData->comp_code }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title">Company Phone</label>
                        <div class="col-sm-9 form-group">
                            <div class="input-group">
                                <input type="text" value="{{ $CompanyData->comp_phone }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title">Email</label>
                        <div class="col-sm-9 form-group">
                            <div class="input-group">
                                <input type="email" class="form-control" value="{{ $CompanyData->comp_email }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title">Address</label>
                        <div class="col-sm-9 form-group">
                            <div class="input-group">
                                <textarea class="form-control" rows="4" disabled>{{ $CompanyData->comp_addr }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title">Website</label>
                        <div class="col-sm-9 form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $CompanyData->comp_web_add }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title">Company logo</label>
                        <div class="col-sm-2 text-right image-preview">
                            @if (!empty($CompanyData->comp_logo))

                                @if (file_exists($CompanyData->comp_logo))
                                    <img src="{{ asset($CompanyData->comp_logo) }}" width="100%" alt="Preview" onclick="imagePreview(this);">
                                @else
                                    <p style="color:red;">File not Found!</p>
                                @endif
                            @else
                                <img src="{{ url('assets/images/placeholder.png')}}" width="100%" alt="Preview" onclick="imagePreview(this);">
                            @endif
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title  pt-10">Invoice / Bill logo <br><small style="font-size: 0.7em">( Prefer Black & White logo )</small></label>
                        <div class="col-sm-2 text-right image-preview">
                            @if (!empty($CompanyData->bill_logo))

                                @if (file_exists($CompanyData->bill_logo))
                                    <img src="{{ asset($CompanyData->bill_logo) }}" width="100%" alt="Preview" onclick="imagePreview(this);">
                                @else
                                    <p style="color:red;">File not Found!</p>
                                @endif
                            @else
                                <img src="{{ url('assets/images/placeholder.png')}}" width="100%" alt="Preview" onclick="imagePreview(this);">
                            @endif
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-3 input-title">Login Cover Image</label>
                        <div class="col-sm-2 text-right image-preview">
                            @if (!empty($CompanyData->cover_image_lp))

                                @if (file_exists($CompanyData->cover_image_lp))
                                    <img src="{{ asset($CompanyData->cover_image_lp) }}" width="100%" alt="Preview" onclick="imagePreview(this);">
                                @else
                                    <p style="color:red;">File not Found!</p>
                                @endif
                            @else
                                <img src="{{ url('assets/images/placeholder.png')}}" width="100%" alt="Preview" onclick="imagePreview(this);">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="panel-heading p-2 mb-4">Configuration</div>

                    <div class="row align-items-center">
                        <label class="col-md-12 input-title">Company Logo Display</label>

                        <div class="offset-md-1 col-md-11">

                            <div class="form-row align-items-center">
                                <label class="col-sm-5 text-dark">In login page ?</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" class="checkbox_class" {{ $CompanyData->logo_view_lp == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="col-sm-5" style="display:{{ $CompanyData->logo_view_lp == 1 ? 'block' : 'none' }}" id="logoLPDiv">
                                    <div class="row">
                                        <label class="col-sm-4 text-dark" style="font-size:80%;">Width</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" value="{{$CompanyData->logo_lp_width}}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row align-items-center">
                                <label class="col-sm-5 text-dark">In Reports ?</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" class="checkbox_class" {{ $CompanyData->logo_view_report == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="col-sm-5" style="display:{{ $CompanyData->logo_view_report == 1 ? 'block' : 'none' }}" id="logoReportDiv">
                                    <hr>
                                    <div class="row">
                                        <label class="col-sm-4 text-dark" style="font-size:80%;">Width(A4)</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" value="{{$CompanyData->logo_report_width}}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-4 text-dark" style="font-size:80%;">Width(POS Printer)</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" value="{{$CompanyData->logo_bill_width_pos}}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row align-items-center">
                                <label class="col-sm-5 text-dark">In Invoice / Bill ?</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" class="checkbox_class" {{ $CompanyData->logo_view_bill == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="col-sm-5" style="display:{{ $CompanyData->logo_view_bill == 1 ? 'block' : 'none' }}" id="logoBillDiv">
                                    <hr>
                                    <div class="row">
                                        <label class="col-sm-4 text-dark" style="font-size:80%;">Width(A4)</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" value="{{$CompanyData->logo_bill_width}}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-4 text-dark" style="font-size:80%;">Width(POS Printer)</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" value="{{$CompanyData->logo_bill_width_pos}}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-12 input-title">Company Name Display</label>

                        <div class="offset-md-1 col-md-11">

                            <div class="form-row align-items-center">
                                <label class="col-sm-5 text-dark">In Login Page ?</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" class="checkbox_class" {{ $CompanyData->name_view_lp == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-row align-items-center">
                                <label class="col-sm-5 text-dark">In Reports ?</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" class="checkbox_class" {{ $CompanyData->name_view_report == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-row align-items-center">
                                <label class="col-sm-5 text-dark">In Bill / Invoice ?</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" class="checkbox_class" {{ $CompanyData->name_view_bill == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <label class="col-sm-6 text-dark">Branch / Outlet address display into Bill / Invoice / Reports ?</label>
                        <div class="col-sm-2">
                            <label class="switch">
                                <input type="checkbox" name="br_add_view_bill" class="checkbox_class" {{ $CompanyData->br_add_view_bill == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <hr>
                    <div class="row align-items-center">
                        <label class="col-sm-6 input-title">Transaction Shedule</label>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <label class="switch">
                                    <input type="checkbox" name="timer_option" id="timer_option" class="checkbox_class" {{ $flag ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display:{{ $flag ? 'block' : 'none' }}" id="txDiv">

                        <div class="form-row align-items-center">
                            <label class="offset-md-1 col-md-11 input-title">Time Range</label>
                            <div class="offset-md-1 col-sm-5">
                                <input type="text" class="form-control" id="tx_start_time" name="tx_start_time"
                                    autocomplete="off" placeholder="Start Time" value="{{ $flag ? $startTimeTx : '' }}">
                            </div>

                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="tx_end_time" name="tx_end_time"
                                    autocomplete="off" placeholder="End Time" value="{{ $flag ? $endTimeTx : '' }}">
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="offset-md-1 col-sm-3 input-title">Applicable Branch</label>
                            <div class="col-sm-8 form-group">
                                <div class="input-group">
                                    <div class="radio-custom radio-primary">
                                        <input type="radio" id="radio1" name="applicable_for" value="1" {{ $checked1 }}>
                                        <label for="radio1">All Without HO</label>
                                    </div>
                                    <div class="radio-custom radio-primary" style="margin-left: 20px!important;">
                                        <input type="radio" id="radio2" value="2" name="applicable_for" {{ $checked2 }}>
                                        <label for="radio2">All With HO</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            @if (Common::isSuperUser() == true || Common::isDeveloperUser() == true)
                <div class="panel-heading p-2 mb-4">Developer Section</div>
                <div class="row">
                    <div class="col-sm-9 offset-lg-3">

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title">Company Type</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="company_type" id="company_type"
                                        required style="width: 100%;">
                                        <option value="">Select One</option>

                                        @foreach ($companyTypeList as $Row)
                                            <option value="{{ $Row->uid }}"
                                                {{ $CompanyData->company_type == $Row->uid ? 'selected="selected"' : '' }}
                                            >
                                                {{ $Row->name }} [{{ $Row->uid }}]
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-12 input-title">Module Selection</label>
                            <div class="col-sm-12">
                                <div class="row">
                                    @php
                                        $sysModules = DB::table('gnl_sys_modules')
                                            ->where([['is_delete', 0], ['is_active', 1]])
                                            ->select('id', 'module_name', 'module_short_name')
                                            ->orderBy('id', 'ASC')
                                            ->get();

                                        $i = 0;
                                        $selecetedModule = explode(',', $CompanyData->module_arr);
                                    @endphp

                                    @foreach ($sysModules as $module)
                                        @php
                                            $i++;
                                            if (in_array($module->id, $selecetedModule)) {
                                                $CheckText = 'checked';
                                            } else {
                                                $CheckText = '';
                                            }
                                        @endphp

                                        <div class="col-sm-4">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" class="checkboxs" name="module_arr[]" {{ $CheckText }}
                                                    id="module_arr_{{ $i }}" value="{{ $module->id }}" />
                                                <label for="module_arr_{{ $i }}"
                                                    style="color:#000;">{{ $module->module_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">DB Name</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="db_name" value="{{ $CompanyData->db_name }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Host Name</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="host" value="{{ $CompanyData->host }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">User Name</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="username" value="{{ $CompanyData->username }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Password</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="password" value="{{ $CompanyData->password }}" >
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Port</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="port" value="{{ $CompanyData->port }}">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @else
                <input type="hidden" name="company_type" value="{{ $CompanyData->company_type }}">
            @endif


        </div>

        @include('elements.button.common_button', [
            'back' => true,
        ])
    </form>
    <!-- End Page -->

    <script type="text/javascript">
        $(document).ready(function() {
            $('.ajaxRequest').hide();
            $('.httpRequest').hide(); // new entry button hide
        });
    </script>
@endsection
