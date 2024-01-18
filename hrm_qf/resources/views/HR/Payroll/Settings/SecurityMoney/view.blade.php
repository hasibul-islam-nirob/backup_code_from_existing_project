
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Security Money</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 pl-4">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head">

            </thead>
            <tbody id="details_table_body">
                <tr>
                    <td><b>Grade : </b> {{ $viewData->grade_id }}</td>
                    <td><b>Level : </b> {{ $viewData->level_id }}</td>
                </tr>
                <tr>
                    <td><b>Amount :</b>  {{ $viewData->amount }}</td>
                    <td><b>Effective Date :</b> {{ (new DateTime($viewData->effective_date))->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td><b>Status :</b> {{ ($viewData->is_active == 1) ? 'Active' : 'Inactive' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<script>

    showModal({
        titleContent: "View Security Money",
    });
</script>