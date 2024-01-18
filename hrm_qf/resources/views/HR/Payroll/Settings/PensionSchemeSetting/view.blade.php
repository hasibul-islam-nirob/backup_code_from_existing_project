
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Employee Pension Schema Settings</h4>
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
                    <td width="50%"> <b>Project :</b>  {{ $viewData->project()->project_name }}</td>
                    <td> <b>Recruitmrnt Type :</b> {{ $viewData->recruitment_type() }}  </td>
                </tr>
                <tr>
                    <td> <b>Grade : </b>{{ $viewData['grade'] }}</td>
                    <td> <b>Amount : </b>{{ $viewData->amount }}</td>
                </tr>
                <tr>
                    <td> <b>Effective Date :</b> {{ (new DateTime($viewData->effective_date))->format('d-m-Y') }}</td>
                    <td> <b>Status : </b>{{ ($viewData->is_active == 1) ? 'Active' : 'Inactive' }}</td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>

<script>

    showModal({
        titleContent: "View Pension Scheme Setting",
    });
</script>