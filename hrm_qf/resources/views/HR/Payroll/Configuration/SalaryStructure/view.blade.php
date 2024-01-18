<style>
    .modal-lg {
        max-width: 60%;
    }


</style>

<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Salary Structure</h4>
    </div>
</div>

<form id="salary_structure_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden name="edit_id" value="{{ $editData->id }}">

    <div class="row">

        <div class="col-sm-12 offset-sm-1">

            <div class="row">
                <div class="col-sm-10">
                    <table class="table w-full table-hover table-bordered table-striped">
                        <thead id="details_table_head">
            
                        </thead>
                        <tbody id="details_table_body">
                            <tr>
                                <td width='50%'><b>Company : </b> {{!empty($editData->company()->comp_name) ? $editData->company()->comp_name : ''}} </td>

                                <td width='50%'><b>Project : </b> {{!empty($editData->project()->project_name) ? $editData->project()->project_name : ''}} </td>
                            </tr>
                            <tr>
                                <td><b>Grade :</b>  {{ $editData->grade }}</td>
                                <td><b>Level :</b>  {{ $editData->level }}</td>
                            </tr>
                            <tr>
                                <td><b>Pay Scale :</b>  {{ !empty($editData->pay_scale()->name) ? $editData->pay_scale()->name : '' }}</td>
                                <td><b>Designations :</b>  {{ !empty($editData->designations()) ? $editData->designations() : '' }}</td>
                            </tr>

                            <tr>
                                <td><b>Recruitment Type :</b>  {{ !empty($editData->recruitmentType()->title) ? $editData->recruitmentType()->title : '' }}</td>
                                <td><b>Designations :</b>  {{ !empty($editData->designations()) ? $editData->designations() : '' }}</td>
                            </tr>

                            <tr>
                                <td><b>Basic Salary :</b>  {{ $editData->basic }}</td>
                                <td><b>Acting Benefit Amount :</b>  {{ $editData->acting_benefit_amount }}</td>
                            </tr>

                            <tr>
                                <td title="Provident Fund (PF)" ><b>PF Id :</b>  {{ $editData->pf_id }}</td>
                                <td title="Welfare Fund (WF)" ><b>WF Id :</b>  {{ $editData->wf_id }}</td>
                            </tr>
                            <tr>
                                <td title="Employee Pension Schema Settings" ><b>EPS Id :</b>  {{ $editData->eps_id }}</td>
                                <td title="OSF Settings" ><b>OSF Id :</b>  {{ $editData->osf_id }}</td>
                            </tr>
                            <tr>
                                <td title="Insurance" ><b>INC Id :</b>  {{ $editData->inc_id }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>


            @php
                $detailsData = $editData->salary_structure_details;
                $detailsData = $detailsData->groupBy('data_type');
            @endphp

            <br>

            <div class="row">

                <div class="col-sm-11" id="inc_div">

                    <div class="row">

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Increment Percentage</label>
                        </div>

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Increment Amount</label>
                        </div>

                        <div class="col-sm-3 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">No. Of Increment</label>
                        </div>
                        
                    </div>

                    @foreach ($detailsData['increment'] as $key => $inc)
                    <div class="row" {{-- {{ ($key == 0) ? 'id = inc_div_row' : '' }} --}}>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $inc->inc_percentage }}" type="number" name="inc_percentage[]" style="width: 100%;" disabled>
                            </div>
                        </div>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $inc->amount }}" type="number" name="inc_amount[]" style="width: 100%;" disabled>
                            </div>
                        </div>

                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $inc->no_of_inc }}" type="number" name="inc_number_of_inc[]" style="width: 100%;" disabled>
                            </div>
                        </div>
                        
                    </div>
                    @endforeach
                    
                </div>
                
            </div>

            <br>

            <div class="row">

                <div class="col-sm-11" id="">

                    <div class="row">

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Allowance</label>
                        </div>

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Calculation Nature</label>
                        </div>

                        <div class="col-sm-3 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Amount</label>
                        </div>

                    </div>

                    @foreach ($detailsData['allowance'] as $key => $alw)
                    <div class="row" {{-- {{ ($key == 0) ? 'id = benefit_div_row' : '' }} --}} style="">

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                
                                <select name="allowance_id[]" class="form-control clsSelect2" style="width: 100%;" disabled>
                                    <option value="">Select allowance</option>
                                    @foreach ($allowance as $al)
                                        <option {{ ($alw->allowance_type_id == $al->id) ? 'selected' : '' }} value="{{ $al->id }}">{{ $al->name . ' [' .  strtoupper($al->value_field) . ']' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="allowance_calculation_type[]" class="form-control clsSelect2" style="width: 100%;" disabled>
                                    <option value="">Select calculation nature</option>
                                    <option {{ ($alw->calculation_type == 1) ? 'selected' : '' }} value="1">Percentage</option>
                                    <option {{ ($alw->calculation_type == 2) ? 'selected' : '' }} value="2">Fixed Amount</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $alw->amount }}" type="number" name="allowance_amount[]" style="width: 100%;" disabled>
                            </div>
                        </div>

                    </div>
                    @endforeach


                </div>
                
            </div>

            <br>
            <br>

        </div>

    </div>

</form>


<script>

    
    showModal({
        titleContent: "View Salary Structure",
        
    });


</script>