@extends('Layouts.erp_master')
@section('content')

@php 
    use App\Services\HtmlService as HTML;
@endphp

<!-- Page -->
<div class="row">
    <div class="col-lg-9 offset-3 mb-2">
        <!-- Html View Load  -->
        {!! HTML::forCompanyFeild($CompHolidayData->company_id,'disabled') !!}
    </div>
</div>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th colspan="4"  style="color: #000;">
                Company Holiday Information

            </th>
        </tr>
    </thead>
    <tbody  style="color: #000;">
        <tr>
            <td width="20%">Holiday Title</td>
            <td width="20%">{{ $CompHolidayData->ch_title }}</td>

            <td width="20%">Day</td>
            <td width="20%">
                @foreach($days as $key => $value)
                    @if($key == $CompHolidayData->ch_day)
                        {{ $value }}
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <td width="20%">Effective Date Start</td>
            <td width="20%">{{ (new DateTime($CompHolidayData->ch_eff_date))->format('d-m-Y') }}</td>

            <td width="20%">Effective Date End</td>
            <td width="20%">
                {{ $CompHolidayData->ch_eff_date_end ? (new DateTime($CompHolidayData->ch_eff_date_end))->format('d-m-Y') : '' }}
            </td>
        </tr>
        <tr>
            <td width="20%">Description</td>
            <td width="20%">
                {{ $CompHolidayData->ch_description }}
            </td>
        </tr>

    </tbody>
</table>

@include('elements.button.common_button', [
                    'back' => true,
                ])
<!-- End Page -->

@endsection
