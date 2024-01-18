{{-- @php
dd($data);
@endphp --}}
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">    
    <table class="table w-full table-hover table-bordered table-striped">
        <thead class="text-center sticky-head">
            <tr>
                <th style="width:4%;">SL</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Gender</th>
                <th>Branch</th>
                <th>Department</th>
                <th>Mobile</th>
                <th>Marital Status</th>
                <th>Blood Group</th>
                <th>Religion</th>
                <th>Joining Date</th>

                <th>DOB</th>
                <th>District</th>
                <th>Upazila </th>

                <th class="">Status</th>
                {{-- <th>Login Username</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $emp)
            
            <tr>
                <td>{{ $emp['id'] }}</td>
                <td>{{ $emp['emp_name'] }}</td>
                <td>{{ $emp['designation'] }}</td>
                <td>{{ $emp['gender'] }}</td>
                <td>{{ $emp['branch'] }}</td>
                <td>{{ $emp['department'] }}</td> 
                <td>{{ $emp['phone_number'] }}</td>
                <td>{{ $emp['marital_status'] }}</td>
                <td>{{ $emp['blood_group'] }}</td>
                <td>{{ $emp['religion'] }}</td>
                <td>{{ $emp['join_date'] }}</td>

                <td>{{ $emp['dateofbirth'] }}</td>
                <td>{{ $emp['district'] }}</td>
                <td>{{ $emp['thana'] }}</td>

                <td class="">{!! $emp['status'] !!}</td>
                {{-- <td>{{ $emp['username'] }}</td> --}}
            </tr>
            @endforeach

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