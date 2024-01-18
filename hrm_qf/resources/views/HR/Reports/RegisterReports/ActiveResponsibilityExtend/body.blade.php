<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">
    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>
                <th style="width:4%;">SL</th>
                <th>Resign Code</th>
                <th>Employee Name [Code]</th>
                <th>Branch [Code]</th>
                <th style="width: 8%;">Resign Date</th>
                <th style="width: 8%;">Effective Date</th>
                <th class="hideColumn">Status</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($data as $key => $emp)
            <tr>
                <td class="text-center">{{ $emp['id'] }}</td>
                <td class="text-center">{{ $emp['resign_code'] }}</td>
                <td>{{ $emp['employee_name'] }}</td>
                <td>{{ $emp['branch'] }}</td>
                <td class="text-center">{{ $emp['resign_date'] }}</td>
                <td class="text-center">{{ $emp['effective_date'] }}</td>
                <td class="text-center hideColumn">{!! $emp['status'] !!}</td>
            
            </tr>
            @endforeach

        </tbody>
    </table>
    @include('elements.signature.approvalSet')
</div>

<script>
    $(document).ready(function(event){

        if($('#appl_status option:selected').text() === "All") {
            $('.hideColumn').show();
        }
        else {
            $('.hideColumn').hide();
        }
    });
    
</script>