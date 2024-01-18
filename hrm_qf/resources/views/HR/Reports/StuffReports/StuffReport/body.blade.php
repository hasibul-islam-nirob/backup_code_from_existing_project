<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">
    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>
                <th style="width:4%;">SL</th>
                <th>Employee Name [Code]</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Branch [Code]</th>
                <th>Area [Code]</th>
                <th>Gender</th>
                <th class="hideColumn">Status</th>
            </tr>
        </thead>
        @php
            $subTotalMale =0;
            $subTotalFemale =0;
        @endphp
        <tbody>
            
            @foreach ($data as $key => $emp)
                <tr>
                    @php
                        if(strtolower($emp['gender']) == "male")
                        {
                            $subTotalMale++;  
                        }else{
                            $subTotalFemale++;
                        }
                    @endphp
                    <td class="text-center">{{ $key+1 }}</td>
                    <td>{{ $emp['employee_name'] }}</td>
                    <td>{{ $emp['designation_name'] }}</td>
                    <td>{{ $emp['dept_name'] }}</td>
                    <td>{{ $emp['branch_name'] }}</td>
                    <td>{{ $emp['area_name'] }}</td>
                    <td>{{ $emp['gender'] }}</td>
                    <td class="text-center hideColumn">{!! $emp['status'] !!}</td>                
                </tr>
                @if(isset($data[$key + 1]) && $data[$key + 1]['branch_id'] != $emp['branch_id'])
                    <tr class="font-weight-bold p-0" style="background: #ccc;">
                        <td colspan="6"> {{ $emp['branch_name'] }} Sub Total ({{ $subTotalMale + $subTotalFemale }})</td>
                        <td colspan="1">
                            Male: {{ $subTotalMale }}
                            &nbsp;
                            Female: {{ $subTotalFemale }}
                        </td>
                        <td class="hideColumn"></td>
                    </tr>
                    @php
                        $subTotalMale =0;
                        $subTotalFemale =0;
                    @endphp
                @elseif(!isset($data[$key + 1]))
                    <tr class="font-weight-bold p-0" style="background: #ccc;">
                        <td colspan="6"> {{ $emp['branch_name'] }} Sub Total</td>
                        <td colspan="1">
                            Male: {{ $subTotalMale }}
                            &nbsp;
                            Female: {{ $subTotalFemale }}
                        </td>
                        <td class="hideColumn"></td>
                    </tr>
                    @php
                        $subTotalMale =0;
                        $subTotalFemale =0;
                    @endphp
                @endif
            @endforeach
            <tr style="font-weight:bold; background: #ccc;">
                <td colspan="6" style="text-align:left">Total</td>
                <td colspan="1">
                    Male: {{$male}}
                    &nbsp;
                    Female: {{$female}}
                </td>
                <td class="hideColumn"></td>
            </tr>
        </tbody>
    </table>
    @include('elements.signature.approvalSet')
</div>
<script>
    $(document).ready(function(event){
        // stuff status add into report title
        $('#beforeTitle').html($('#emp_status option:selected').text());

        if($('#emp_status option:selected').text() === "All") {
            $('.hideColumn').show();
        }
        else {
            $('.hideColumn').hide();
        }
    });
    
</script>