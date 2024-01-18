
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: #4f4e4eb8; color:#fff; padding:10px 0 10px 10px;">Salary Structure</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head">

            </thead>
            <tbody id="details_table_body">
                <tr>
                    <td>Fiscal Year : {{ $viewData['fiscal_year'] }}</td>
                    <td>Company :  {{ $viewData['company'] }}</td>
                </tr>
                <tr>
                    <td>Grade : {{ $viewData['grade'] }}</td>
                    <td>Level :  {{ $viewData['level'] }}</td>
                </tr>
                <tr>
                    <td>Designations : {{ $viewData['designations'] }}</td>
                    <td>Recruitmrnt Type : {{ $viewData['recruitment_type'] }}  </td>
                </tr>
                <tr>
                    <td>Basic Salary : {{ $viewData['basic'] }}</td>
                    <td>Acting Benefit Amount :  {{ $viewData['acting_benefit_amount'] }}</td>
                </tr>
                <tr>
                    <td>Welfare Amount : {{ $viewData['wf_amount'] }}</td>
                    <td>Project :  {{ $viewData['project'] }}</td>
                </tr>
                <tr>
                    <td>Status : {{ $viewData['status'] }}</td>
                    
                </tr>
            </tbody>
        </table>
    </div>
</div>

@php
    $detailsData = $viewData['salary_structure_details'];
    $detailsData = $detailsData->groupBy('data_type');
@endphp

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head" style="background-color: #4f4e4eb8;">
                <th>Increment Percentage</th>
                <th>Increment Amount</th>
                <th>No. Of Year</th>
            </thead>
            <tbody id="details_table_body">
                @foreach ($detailsData['increment'] as $key => $inc)
                <tr>
                    <td>{{ $inc->inc_percentage }}</td>
                    <td>{{ $inc->amount }}</td>
                    <td>{{ $inc->no_of_inc }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{-- @dd($allowance) --}}
{{-- @dd($allowance->where('id', 7)->pluck('name')[0]) --}}

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head" style="background-color: #4f4e4eb8;">
                <th>Allowance</th>
                <th>Benefit Type</th>
                <th>Calculation Type</th>
                <th>Amount</th>
            </thead>
            <tbody id="details_table_body">
                {{-- @dd($detailsData['allowance']) --}}
                @foreach ($detailsData['allowance'] as $key => $alw)
                {{-- @dd($allowance->where('id', $alw->allowance_type_id)[0]) --}}
                <tr>
                    <td>{{ $allowance->where('id', $alw->allowance_type_id)->pluck('name')[0] }}</td>
                    <td>{{ $allowance->where('id', $alw->allowance_type_id)->pluck('benifit_name')[0] }}</td>
                    <td>{{ ($alw->calculation_type == 1 ? 'Percentage' : 'Fixed Amount') }}</td>
                    <td>{{ $alw->amount }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head" style="background-color: #4f4e4eb8;">
                <th>Povidient Fund (%)</th>
                <th>No. Of Year</th>
                <th>Povidient Scheme (%)</th>
                <th>No. Of Year</th>
            </thead>
            <tbody id="details_table_body">
                @php
                    
                @endphp
                @foreach ($detailsData['ps'] as $key => $pf)
                <tr>
                    <td>{{ isset($detailsData['pf'][$key]->amount) ? $detailsData['pf'][$key]->amount : '' }}</td>
                    <td>{{ isset($detailsData['pf'][$key]->no_of_inc) ? $detailsData['pf'][$key]->no_of_inc : '' }}</td>
                    <td>{{ isset($detailsData['ps'][$key]->amount) ? $detailsData['ps'][$key]->amount : '' }}</td>
                    <td>{{ isset($detailsData['ps'][$key]->no_of_inc) ? $detailsData['ps'][$key]->no_of_inc : '' }}</td>
                </tr>
                @endforeach
                {{-- @foreach ($detailsData['ps'] as $key => $ps)
                    <td>{{ $ps->amount }}</td>
                    <td>{{ $ps->no_of_inc }}</td>
                @endforeach --}}
            </tbody>
        </table>
    </div>
</div>

{{-- <div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head" style="background-color: #4f4e4eb8;">
                <th>Povidient Scheme (%)</th>
                <th>No. Of Year</th>
            </thead>
            <tbody id="details_table_body">
                @foreach ($detailsData['ps'] as $key => $ps)
                <tr>
                    <td>{{ $ps->amount }}</td>
                    <td>{{ $ps->no_of_inc }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}

<script>

    showModal({
        titleContent: "View Salary Structure",
    });
</script>