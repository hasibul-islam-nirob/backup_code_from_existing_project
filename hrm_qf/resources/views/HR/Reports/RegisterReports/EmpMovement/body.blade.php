<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12"> 
    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>
                <th style="width:4%;">SL</th>
                <th>Movement Date</th>
                <th>Movement Code</th>
                <th style="width: 8%;">Start Time</th>
                <th style="width: 8%;">End Time</th>
                <th style="width: 8%;">Movement To</th>
                <th>Employee Name [Code]</th>
                <th>Branch [Code]</th>
                <th>Reason</th>
                <th>Application Date</th>
                {{-- <th>Application For</th> --}}
                <th>Gender</th>
                <th class="hideColumn">Status</th>
            </tr>
        </thead>
        <tbody>
        
            @foreach ($data as $key => $emp)
            <tr>
                <td class="text-center">{{ $emp['id'] }}</td>
                <td class="text-center">{{ $emp['movement_date'] }}</td>
                <td class="text-center">{{ $emp['movement_code'] }}</td>
                <td class="text-center">{{ $emp['start_time'] }}</td>
                
                <td class="text-center">{{ $emp['end_time'] }}</td>
                <td class="">{{ $emp['location_to_branch'] }}</td>
                <td>{{ $emp['employee_name'] }}</td>
                <td>{{ $emp['branch'] }}</td>
                <td>{{ $emp['reason'] }}</td>
                <td class="text-center">{{ $emp['appl_date'] }}</td>
                {{-- <td>{{ $emp['application_for'] }}</td> --}}
                <td>{{ $emp['gender'] }}</td>
                <td class="text-center hideColumn">{!! $emp['status'] !!}</td>
            
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
                // $('.hideColumn').hide();
            }
        });
        
    </script>
    @include('elements.signature.approvalSet')
</div>