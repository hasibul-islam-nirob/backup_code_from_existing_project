
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Insurance</h4>
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
                    <td> <b>Project :</b>  {{ $viewData->project()->project_name }}</td>
                </tr>
                <tr>
                    <td> <b>Recruitmrnt Type :</b> {{ $viewData->recruitment_type() }}  </td>
                    <td> <b>Calculation Type :</b>  {{ $viewData->calculation_type }}</td>
                </tr>
                <tr>
                    <td> <b>Calculation Amount :</b>  {{ $viewData->calculation_amount }}</td>
                    <td> <b>Employee Contribution After (month) :</b>  {{ $viewData->emp_cont_after_m }}</td>
                </tr>
                <tr>
                    <td> <b>Org. Contribution After (month) :</b>  {{ $viewData->org_cont_after_m }}</td>
                    <td> <b>First Maturity Year :</b>  {{ $viewData->first_maturity_y }}</td>
                </tr>
                <tr>
                    <td> <b>First Maturity Interest Rate :</b>  {{ $viewData->first_maturity_interest_rate }}</td>
                    <td> <b>Second Maturity Year :</b>  {{ $viewData->second_maturity_y }}</td>
                </tr>
                <tr>
                    <td> <b>Second Maturity Interest Rate :</b>  {{ $viewData->second_maturity_interest_rate }}</td>
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

<script>

    showModal({
        titleContent: "View Insurance",
    });
</script>