
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Loan</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head">

            </thead>
            <tbody id="details_table_body">
                <tr>
                    <td><b>Vehicle Type : </b> {{ $viewData->vehicle_type }}</td>
                    <td><b>Maximum Installment :</b>  {{ $viewData->max_installment }}</td>
                </tr>
                <tr>
                    <td><b>Maximum Amount :</b>  {{ $viewData->max_amount }}</td>
                    <td><b>Settlement Fee (%) :</b>  {{ $viewData->settlement_fee }}</td>
                </tr>
                <tr>
                    <td><b>Intrest Rate (%) :</b> {{ $viewData->intrest_rate }}</td>
                    <td><b>Interest Method:</b> {{ $viewData->intrest_method }}</td>
                </tr>
                <tr>
                    <td><b>Effective Date :</b> {{ (new DateTime($viewData->effective_date))->format('d-m-Y') }}</td>
                    <td><b>Status :</b> {{ ($viewData->is_active == 1) ? 'Active' : 'Inactive' }}</td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>

<script>

    showModal({
        titleContent: "View Loan",
    });
</script>