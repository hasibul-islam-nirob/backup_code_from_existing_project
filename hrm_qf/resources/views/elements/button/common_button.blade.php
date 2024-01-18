
@if (isset($authorityBtn))
<div class="row text-right pb-10 d-print-none">
@endif

{{-- Search Button --}}
@if (isset($search) && isset($search['action']) && $search['action'])

    @if (isset($authorityBtn))
        <div class="col-sm-10">
    @else
        <div class="col-sm-2 pt-5 text-right d-print-none">
    @endif
        <a href="javascript:void(0)"
            class="btn btn-primary btn-round text-uppercase mt-4
            {{ isset($search['exClass']) ? $search['exClass'] : '' }}"
            @if (isset($search['id'])) id="{{ $search['id'] }}" @endif
            @if (isset($search['jsEvent'])) {{ $search['jsEvent'] }} @endif
        >
            <i class="fa fa-search" aria-hidden="true"></i>
            &nbsp; {{ isset($search['title']) ? $search['title'] : 'search' }}
        </a>
    </div>
@endif

{{-- Authorized & unauthorized button --}}
@if (isset($authorityBtn) && isset($authorityBtn['action']) && $authorityBtn['action'])
    <div class="col-sm-2">
        {{-- style="margin-left: -50px;" --}}
        <a href="javascript:void(0)"
            class="btn btn-round text-uppercase mt-4
            {{ isset($authorityBtn['exClass']) ? $authorityBtn['exClass'] : '' }}"
            @if (isset($authorityBtn['id'])) id="{{ $authorityBtn['id'] }}" @endif
            @if (isset($authorityBtn['jsEvent'])) {{ $authorityBtn['jsEvent'] }} @endif
        >
            {{ isset($authorityBtn['title']) ? $authorityBtn['title'] : ' ' }}
        </a>
    </div>
@endif

@if (isset($authorityBtn))
</div>
@endif

{{-- @if (isset($search) && (isset($authorityBtn)))

<div class="row text-right pb-10 d-print-none">
    <div class="col-sm-10">
        @if (isset($search) && isset($search['action']) && $search['action'])
            <a href="javascript:void(0)"
                class="btn btn-primary btn-round text-uppercase mt-4
                {{ isset($search['exClass']) ? $search['exClass'] : '' }}"
                    @if (isset($search['id'])) id="{{ $search['id'] }}" @endif
                    @if (isset($search['jsEvent'])) {{ $search['jsEvent'] }} @endif
            >
                <i class="fa fa-search" aria-hidden="true"></i>
                &nbsp; {{ isset($search['title']) ? $search['title'] : 'search' }}
            </a>
        @endif
    </div>

    <div class="col-sm-2">
        @if (isset($authorityBtn) && isset($authorityBtn['action']) && $authorityBtn['action'])
            <a href="javascript:void(0)"
                class="btn btn-round text-uppercase mt-4
                {{ isset($authorityBtn['exClass']) ? $authorityBtn['exClass'] : '' }}"
                @if (isset($authorityBtn['id'])) id="{{ $authorityBtn['id'] }}" @endif
                @if (isset($authorityBtn['jsEvent'])) {{ $authorityBtn['jsEvent'] }} @endif
            >
                {{ isset($authorityBtn['title']) ? $authorityBtn['title'] : ' ' }}
            </a>
        @endif
    </div>
</div>

@elseif (isset($search) && isset($search['action']) && $search['action'])
<div class="col-sm-2 pt-5 text-right d-print-none">
    <a href="javascript:void(0)"
        class="btn btn-primary btn-round text-uppercase mt-4
        {{ isset($search['exClass']) ? $search['exClass'] : '' }}"
        @if (isset($search['id'])) id="{{ $search['id'] }}" @endif
        @if (isset($search['jsEvent'])) {{ $search['jsEvent'] }} @endif
    >
        <i class="fa fa-search" aria-hidden="true"></i>
        &nbsp; {{ isset($search['title']) ? $search['title'] : 'search' }}
    </a>
</div>
@endif --}}

@if (isset($execute) && array_search(15, array_column($GlobalRole, 'set_status')) !== false)
<div class="col-sm-2 pt-5 d-print-none">
    <a class="btn btn-danger btn-round text-uppercase mt-4
        {{ isset($execute['exClass']) ? $execute['exClass'] : '' }}"
        @if (isset($execute['id'])) id="{{ $execute['id'] }}" @endif
        @if (isset($execute['jsEvent'])) {{ $execute['jsEvent'] }} @endif
        style="color:#000; font-weight:bold;"
    >
        {{ isset($execute['title']) ? $execute['title'] : '' }}
    </a>
</div>
@endif

@if (isset($back) || isset($submit) || isset($print) || isset($refresh) || isset($backBtn) || isset($printBtn))

    <div class="row align-items-center d-print-none">
        @if (isset($acc_div) && $acc_div)
        <label class="col-sm-2 input-title"></label>
        {{-- Starting Div --}}
        <div class="col-sm-4">
        @elseif (isset($sales_div) && $sales_div)
        {{-- Starting Div --}}
        <div class="col-sm-12">
        @else
        {{-- <div class="col-sm-2"></div> --}}
        {{-- <div class="col-sm-8"> --}}
        {{-- Starting Div --}}
        <div class="col-sm-12">
        @endif

            <div class="form-group d-flex justify-content-center">
                {{-- <div class="example example-buttons"> --}}

                    {{-- new version --}}
                    @if (isset($closeBtn) && $closeBtn)
                        <a href="javascript:void(0)"
                            class="btn btn-dark btn-round text-uppercase mt-4 {{ isset($closeBtn['exClass']) ? $closeBtn['exClass'] : '' }}"
                            @if (isset($closeBtn['id'])) id="{{ $closeBtn['id'] }}" @endif
                            @if (isset($closeBtn['jsEvent'])) {{ $closeBtn['jsEvent'] }}
                            @else onclick="$('#modal').modal('hide');"
                            @endif
                        >
                            @if (isset($closeBtn['icon'])) {{ $closeBtn['icon'] }}
                            @else <i class="fa fa-window-close" aria-hidden="true"></i>&nbsp;
                            @endif

                            {{ isset($closeBtn['title']) ? $closeBtn['title'] : 'Close' }}
                        </a>
                    @endif

                    @if (isset($backBtn) && $backBtn)
                        <a href="javascript:void(0)"
                            class="btn btn-dark btn-round text-uppercase mt-4 {{ isset($backBtn['exClass']) ? $backBtn['exClass'] : '' }}"
                            @if (isset($backBtn['id'])) id="{{ $backBtn['id'] }}" @endif
                            @if (isset($backBtn['jsEvent'])) {{ $backBtn['jsEvent'] }}
                            @else onclick="goBack();"
                            @endif
                        >
                            @if (isset($backBtn['icon'])) {{ $backBtn['icon'] }}
                            @else <i class="fa fa-step-backward" aria-hidden="true"></i>&nbsp;
                            @endif

                            {{ isset($backBtn['title']) ? $backBtn['title'] : 'Back' }}
                        </a>
                    @endif

                    @if (isset($billBtn) && $billBtn)
                        <a href="javascript:void(0)"
                            class="btn btn-primary btn-round clsPrint text-uppercase  mt-4 {{ isset($billBtn['exClass']) ? $billBtn['exClass'] : '' }}"
                            @if (isset($billBtn['id'])) id="{{ $billBtn['id'] }}" @endif
                            @if (isset($billBtn['jsEvent'])) {{ $billBtn['jsEvent'] }}
                            @else onclick="window.print();"
                            @endif
                        >
                            @if (isset($billBtn['icon'])) <i class="{{ $billBtn['icon'] }}" aria-hidden="true"></i>&nbsp;
                            @else <i class="fa fa-print" aria-hidden="true"></i>&nbsp;
                            @endif

                            {{ isset($billBtn['title']) ? $billBtn['title'] : 'Print' }}
                        </a>
                    @endif

                    @if (isset($invoiceBtn) && $invoiceBtn)
                        <a href="javascript:void(0)"
                            class="btn btn-primary btn-round clsPrint text-uppercase  mt-4 {{ isset($invoiceBtn['exClass']) ? $invoiceBtn['exClass'] : '' }}"
                            @if (isset($invoiceBtn['id'])) id="{{ $invoiceBtn['id'] }}" @endif
                            @if (isset($invoiceBtn['jsEvent'])) {{ $invoiceBtn['jsEvent'] }}
                            @else onclick="window.print();"
                            @endif
                        >
                            @if (isset($invoiceBtn['icon'])) <i class="{{ $invoiceBtn['icon'] }}" aria-hidden="true"></i>&nbsp;
                            @else <i class="fa fa-print" aria-hidden="true"></i>&nbsp;
                            @endif

                            {{ isset($invoiceBtn['title']) ? $invoiceBtn['title'] : 'Print' }}
                        </a>
                    @endif

                    {{-- new version --}}
                    @if (isset($printBtn) && $printBtn)
                        <a href="javascript:void(0)"
                            class="btn btn-primary btn-round clsPrint text-uppercase  mt-4 {{ isset($printBtn['exClass']) ? $printBtn['exClass'] : '' }}"
                            @if (isset($printBtn['id'])) id="{{ $printBtn['id'] }}" @endif
                            @if (isset($printBtn['jsEvent'])) {{ $printBtn['jsEvent'] }}
                            @else onclick="window.print();"
                            @endif
                        >
                            @if (isset($printBtn['icon'])) <i class="{{ $printBtn['icon'] }}" aria-hidden="true"></i>&nbsp;
                            @else <i class="fa fa-print" aria-hidden="true"></i>&nbsp;
                            @endif

                            {{ isset($printBtn['title']) ? $printBtn['title'] : 'Print' }}
                        </a>
                    @endif

                    @if (isset($submitBtn))
                        <button type="submit" class="btn btn-primary btn-round text-uppercase mt-4
                            {{ isset($submitBtn['exClass']) ? $submitBtn['exClass'] : '' }}"
                            @if (isset($submitBtn['id'])) id="{{ $submitBtn['id'] }}" @endif
                            @if (isset($submitBtn['name'])) name="{{ $submitBtn['name'] }}" @endif
                            @if (isset($submitBtn['value'])) value="{{ $submitBtn['value'] }}" @endif
                            @if (isset($submitBtn['jsEvent'])) {{ $submitBtn['jsEvent'] }} @endif
                        >

                            @if (isset($submitBtn['icon']) && $submitBtn['icon'] == 'save')
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            @elseif (isset($submitBtn['icon']) && $submitBtn['icon'] == 'update')
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            @elseif (isset($submitBtn['icon']) && $submitBtn['icon'] == 'barcode')
                                <i class="fa fa-search" aria-hidden="true"></i>
                            @elseif (isset($submitBtn['icon']))
                            <i class="{{ $submitBtn['icon'] }}" aria-hidden="true"></i>&nbsp;
                            @endif

                            &nbsp; {{ isset($submitBtn['title']) ? $submitBtn['title'] : 'Submit' }}
                        </button>
                    @endif

                    {{-- old version --}}
                    @if (isset($back) && $back)
                        <a href="javascript:void(0)" onclick="goBack();"
                            class="btn btn-dark btn-round text-uppercase mt-4" >
                            <i class="fa fa-step-backward" aria-hidden="true"></i>
                            &nbsp; Back
                        </a>
                    @endif

                    @if (isset($refresh))
                        <a class="btn {{(isset($refresh['colorcls']))? $refresh['colorcls'] : 'btn-warning'}} btn-round d-print-none text-uppercase mt-4 {{ isset($refresh['exClass']) ? $refresh['exClass'] : '' }}"
                            @if (isset($refresh['id'])) id="{{ $refresh['id'] }}" @endif
                            @if (isset($refresh['href'])) href="{{ $refresh['href'] }}" @endif
                            @if (isset($refresh['jsEvent'])) {{ $refresh['jsEvent'] }} @endif
                            @if (isset($refresh['target'])) target="{{ $refresh['target'] }}" @endif
                        >
                            @if (isset($refresh['action']) && $refresh['action'] == 'refresh')
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                            @endif
                            @if (isset($refresh['icon']))
                            <i class="{{ $refresh['icon'] }}" aria-hidden="true"></i>
                            @endif
                            &nbsp; {{ isset($refresh['title']) ? $refresh['title'] : 'Refresh' }}
                        </a>
                    @endif

                    {{-- old version --}}
                    @if (isset($submit))
                        <button type="submit" class="btn btn-primary btn-round text-uppercase mt-4
                            {{ isset($submit['exClass']) ? $submit['exClass'] : '' }}"
                            @if (isset($submit['id'])) id="{{ $submit['id'] }}" @endif
                            @if (isset($submit['name'])) name="{{ $submit['name'] }}" @endif
                            @if (isset($submit['value'])) value="{{ $submit['value'] }}" @endif
                            @if (isset($submit['jsEvent'])) {{ $submit['jsEvent'] }} @endif
                        >

                            @if (isset($submit['action']) && $submit['action'] == 'save')
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            @elseif (isset($submit['action']) && $submit['action'] == 'update')
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            @elseif (isset($submit['action']) && $submit['action'] == 'barcode')
                                <i class="fa fa-search" aria-hidden="true"></i>
                            @endif

                            &nbsp; {{ isset($submit['title']) ? $submit['title'] : 'Submit' }}
                        </button>
                    @endif

                    {{-- old version --}}
                    @if (isset($print) && $print)
                        <a href="javascript:void(0)"
                            {{-- onclick="window.print();" --}}
                            class="btn btn-primary btn-round clsPrint text-uppercase  mt-4
                            {{ isset($print['exClass']) ? $print['exClass'] : '' }}"
                            @if (isset($print['id'])) id="{{ $print['id'] }}" @endif
                            @if (isset($print['jsEvent'])) {{ $print['jsEvent'] }} @endif
                        >
                            @if (isset($print['action']) && $print['action'] == 'print')
                                <i class="fa fa-print" aria-hidden="true"></i>
                            @endif
                            &nbsp; {{ isset($print['title']) ? $print['title'] : 'Print' }}
                        </a>
                    @endif

                    {{-- @if (isset($invoice) && $invoice) <a href="javascript:void(0)" onclick="fnModalPrint();"
                            class="btn btn-primary btn-round clsPrint text-uppercase mt-4">
                            <i class="fa fa-print" aria-hidden="true"></i>&nbsp; Invoice</a> @endif --}}

                {{-- </div> --}}
            </div>
        {{-- Ending Div ## Do not remove this--}}
        </div>
    </div>

{{-- Ending Div 4 ta ## Do not remove this--}}
@endif

