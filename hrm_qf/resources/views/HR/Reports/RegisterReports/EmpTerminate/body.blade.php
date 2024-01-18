
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12"> 
    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>
                <th style="width:4%;">SL</th>
                <th style="width: 8%;">Application Date</th>
                <th style="width: 8%;">Terminate Code</th>
                <th style="width: 8%;">Effective Date</th>
                <th style="width: 9%;">EXP Effective Date</th>
                <th>Employee Name [Code]</th>
                <th>Branch [Code]</th>
                <th>Reason</th>
                <th>Gender</th>
                <th class="">Status</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($data as $key => $emp)
            <tr>
                <td class="text-center">{{ $emp['id'] }}</td>
                <td class="text-center">{{ $emp['terminate_date'] }}</td>
                <td class="text-center">{{ $emp['terminate_code'] }}</td>
                <td class="text-center">{{ $emp['effective_date'] }}</td>
                <td class="text-center">{{ $emp['exp_effective_date'] }}</td>
                <td>{{ $emp['employee_name'] }}</td>
                <td>{{ $emp['branch'] }}</td>
                <td>{{ $emp['reason'] }}</td>
                <td>{{ $emp['gender'] }}</td>
                <td class="text-center ">{!! $emp['status'] !!}</td>
            
            </tr>
            @endforeach
            <tr style="font-weight:bold;">
                <td colspan="3" style="text-align:right">Total</td>
                <td colspan="2">
                    Male: {{$male}}
                    &nbsp;
                    Female: {{$female}}
                </td>
                {{-- <td colspan="6">

                </td> --}}
            </tr>
        </tbody>
    </table>

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
    @include('elements.signature.approvalSet')
</div>