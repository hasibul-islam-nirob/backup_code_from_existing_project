
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Welfare Fund (WF)</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head">

            </thead>
            <tbody id="details_table_body">
                <tr>
                    <td width="50%"> <b>Company :</b>  {{ $viewData->company['comp_name'] }}</td>
                    <td> <b>Project :</b>  {{ $viewData->project()->project_name }}</td>
                </tr>
                <tr>
                    <td> <b>Recruitmrnt Type :</b> {{ $viewData->recruitment_type() }}  </td>
                    <td> <b>Method :</b> {{ ($viewData->method == 'decline' ? 'Decline' : 'Flat') }}</td>
                </tr>
                <tr>
                    <td> <b>Effective Date :</b> {{ (new DateTime($viewData->effective_date))->format('d-m-Y') }}</td>
                    <td> <b>Status :</b> {{ ($viewData->is_active == 1) ? 'Active' : 'Inactive' }}</td>
                </tr>
                
                
            </tbody>
        </table>
    </div>
</div>

@php

    use Illuminate\Support\Facades\DB;

    $detailsData = $viewData->wf_details;
    $detailsData = $detailsData->groupBy('data_type');

    $donSectorData = DB::table('hr_payroll_settings_wf_details')->where('wf_id', $viewData->id)->select('don_sector', 'amount')->get();

    $donationSector = DB::table('hr_payroll_settings_donation')->where([['is_active', 1],['is_delete', 0]])->get(); // Donation Sector

@endphp

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head" style="background-color: #4f4e4eb8;">
                <th>Type</th>
                <th>Grade</th>
                <th>Level</th>
                <th>Calculation Type</th>
                <th>Amount</th>
            </thead>
            <tbody id="details_table_body">
                @foreach ($detailsData['calculation'] as $key => $val)
                <tr>
                    <td>{{ $val->type }}</td>
                    <td>{{ ($val->grade != 0) ? $val->grade : 'All'}}</td>
                    <td>{{ ($val->level != 0) ? $val->level : 'All'}}</td>
                    <td>{{ $val->calculation_type }}</td>
                    <td>{{ $val->amount }}</td>
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
                <th>Donation Sector</th>
                <th>Amount</th>
            </thead>
            <tbody id="details_table_body">

                @foreach ($donationSector as $donSecData)
                    @foreach ($donSectorData as $key => $val)
                        @if ($val->don_sector == $donSecData->id && $val != null)
                            <tr>
                                <td>{{$donSecData->sector_name }}</td>
                                <td>{{ $val->amount }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach

            </tbody>
        </table>
    </div>
</div>

<script>

    showModal({
        titleContent: "View WF",
    });
</script>