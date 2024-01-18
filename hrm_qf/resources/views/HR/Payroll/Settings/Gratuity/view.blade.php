
<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Gratuity</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head">

            </thead>
            <tbody id="details_table_body">
                <tr>
                    <td><b>Company : </b> {{ $viewData->company['comp_name'] }}</td>
                </tr>
                <tr>
                    <td><b>Project : </b> {{ $viewData->project()->project_name }}</td>
                    <td><b>Effective Date :</b> {{ (new DateTime($viewData->effective_date))->format('d-m-Y') }}</td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>

@php
    $detailsData = $viewData->gratuity_details;
@endphp

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead id="details_table_head" style="background-color: #4f4e4eb8;">
                <th>Steps</th>
                <th>Year From</th>
                <th>Year To</th>
                <th>Gratuity</th>
            </thead>
            <tbody id="details_table_body">
                @foreach ($detailsData as $key => $val)
                <tr>
                    <td>{{ $val->steps }}</td>
                    <td>{{ $val->year_from }}</td>
                    <td>{{ $val->year_to }}</td>
                    <td>{{ $val->gratuity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>

    showModal({
        titleContent: "View Gratuity",
    });
</script>