
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Bonus</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head">

            </thead>
            <tbody id="details_table_body">
               
                <tr>
                    <td><b>Name : </b> {{ $viewData->name }}</td>
                    <td><b>Company : </b> {{ $viewData->company['comp_name'] }}</td>
                </tr>
                <tr>
                    <td><b>Project : </b> {{ $viewData->project()->project_name }}</td>
                    <td><b>Recruitmrnt Type :</b> {{ $viewData->recruitment_type() }}  </td>
                </tr>
                <tr>
                    <td><b>Religion :</b> {{ ($viewData->religion_id != 0) ? $viewData->religion()->name : "All Religion"}}</td>
                    <td><b>Calculation Percentage :</b> {{ ($viewData->calculation_percentage) }}</td>
                </tr>
                <tr>
                    <td><b>Based On :</b> {{ $viewData->based_on }}</td>
                    <td><b>Bonus Payable min job month :</b> {{ ($viewData->min_job_m) }}</td>
                </tr>
                <tr>
                    <td><b>Effective Date :</b> {{ (new DateTime($viewData->effective_date))->format('d-m-Y') }}</td>
                    <td><b>Status : </b>{{ ($viewData->is_active == 1) ? 'Active' : 'Inactive' }}</td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>

<script>

    showModal({
        titleContent: "View Bonus",
    });
</script>