
@php
    // $isTime = 'yes';
@endphp
<div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12">
    <table class="table table-bordered sticky-table clsDataTable" id="tableID" >
        <thead class="text-center sticky-head" id="header_col">
            <tr>
                <th style="width:4%;">SL</th>
                <th width="30%">Title</th>
                <th width="15%">From Date</th>
                <th width="15%">To Date</th>
                <th width="15%">Status</th>
            </tr>
        </thead>

        <tbody>
            @php
            // dd($filteredHoliday);
                $i = 0;
            @endphp

            @foreach ($filteredHoliday as $key => $holiDay)
            @php
                // dd($holiDay);
            @endphp
                <tr>
                    <td class="text-center" > {{++$i}} </td>
                    <td  > {{$holiDay['title']}} </td>
                    <td class="text-center" > {{ date("d-m-Y", strtotime($holiDay['from_date'])) }} </td>
                    <td class="text-center" > {{ date("d-m-Y", strtotime($holiDay['to_date'])) }} </td>
                    <td class="text-center" > {{$holiDay['status']}} </td>
                </tr>
            @endforeach
        </tbody>
    </table>

 
    @include('../elements.signature.signatureSet', ['visible' => false])
</div>

<style>
    @media print {
        .d-print-text-dark {
            color: #000;
        }
    }
</style>


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