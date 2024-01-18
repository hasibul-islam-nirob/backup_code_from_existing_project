
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Provident Fund (PF)</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head">

            </thead>
            <tbody id="details_table_body">
                <tr>
                    <td> <b>Company :</b>  {{ $viewData->company['comp_name'] }}</td>
                </tr>
                <tr>
                    <td> <b>Project :</b>  {{ $viewData->project()->project_name }}</td>
                    <td> <b>Recruitmrnt Type :</b> {{ $viewData->recruitment_type() }}  </td>
                </tr>
                <tr>
                    <td> <b>Calculation Type:</b>  {{ $viewData->calculation_type }}</td>
                    <td> <b>Calculation Amount:</b>  {{ $viewData->calculation_amount }}</td>
                </tr></b>
                <tr>
                    <td> <b>Emp. Contribution After Month:</b>  {{ $viewData->emp_cont_after_m }}</td>
                    <td> <b>Org. Contribution After Month:</b>  {{ $viewData->org_cont_after_m }}</td>
                </tr>
                <tr>
                    <td> <b>Loan Withdraw Min Month: </b> {{ $viewData->loan_wit_min_m }}</td>
                    <td> <b>Loan Withdraw Percentage:</b>  {{ $viewData->loan_wit_percentage }}</td>
                </tr>
                <tr>
                    <td> <b>Loan Early Settlement Percentage:</b>  {{ $viewData->loan_early_sett_percentage }}</td>
                    <td> <b>Org. Withdraw Min Job Year:</b>  {{ $viewData->org_wit_min_job_y }}</td>
                </tr>
                <tr>
                    <td> <b>Emp. Withdraw Min Job Year:</b>  {{ $viewData->emp_wit_min_job_y }}</td>
                    <td> <b>Interest Rate :</b> {{ $viewData['interest_rate'] }}</td>
                </tr>
                
                <tr>
                    <td> <b>Method :</b> {{ ($viewData->method == 'decline' ? 'Decline' : 'Flat') }}</td>
                    <td> <b>Effective Date :</b> {{ (new DateTime($viewData->effective_date))->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td> <b>Status :</b> {{ ($viewData->is_active == 1) ? 'Active' : 'Inactive' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>

    showModal({
        titleContent: "View PF",
    });
</script>