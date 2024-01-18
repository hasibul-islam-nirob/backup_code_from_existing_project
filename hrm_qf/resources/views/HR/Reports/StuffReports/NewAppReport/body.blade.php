    
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">
    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>
                <th style="width:4%;">SL</th>
                <th>Employee Name [Code]</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Gender</th>
                <th>Branch [Code]</th>
                <th>Joining Date</th>
                <th class="hideColumn">Status</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($data as $key => $emp)
            <tr>
                <td class="text-center">{{ ++$key }}</td>
                <td>{{ $emp['employee_name'] }}</td>
                <td>{{ $emp['desig_name'] }}</td>
                <td>{{ $emp['dept_name'] }}</td>
                <td>{{ $emp['gender'] }}</td>
                <td>{{ $emp['branch'] }}</td>
                <td class="text-center">{{ $emp['join_date'] }}</td>
                <td class="text-center hideColumn">{!! $emp['status'] !!}</td>
            
            </tr>
            @endforeach
            <tr style="font-weight:bold;">
                <td colspan="4" style="text-align:right">Total</td>
                <td colspan="2">
                    Male: {{$male}}
                    &nbsp;
                    Female: {{$female}}
                </td>
                <td colspan="2">

                </td>
            </tr>

        </tbody>
        
    </table>
    @include('elements.signature.approvalSet')
</div>
<script>
    $(document).ready(function(event){

        if($('#emp_status option:selected').text() === "All") {
            $('.hideColumn').show();
        }
        else {
            $('.hideColumn').hide();
        }
    });
    
</script>