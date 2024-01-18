
<style>
    .modal-lg {
        max-width: 50%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php
    use App\Services\HrService as HRS;

    $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
    $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
    $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();
    $id = $viewData->id;
@endphp

<div class="row">
    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Donation Sector</h4>
    </div>
</div>

<form id="payroll_donation_sector_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-12">

            <div class="row">
                
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Company</label>
                    <div class="input-group">
                        <select name="company_id" id="view_company_id" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select Company</option>
                            @foreach ($companies as $val)
                            <option value="{{ $val->id }}">{{ $val->comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Project</label>
                    <div class="input-group">
                        <select name="project_id" id="view_project_id" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select Project</option>
                            @foreach ($projects as $val)
                            <option value="{{ $val->id }}">{{ $val->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Donation Sector Name</label>
                    <div class="input-group">
                        <input id="view_sector_name" name="sector_name" style="z-index:99999 !important;" 
                            type="text" class="form-control" disabled>
                    </div>
                </div>
               
    
               
            </div>

        </div>

    </div>

</form>

<script>

    $(document).ready(function(){

        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

            function(response, textStatus, xhr) {
                
                $("#view_group_id").val(response.group_id).trigger('change');;
                $("#view_company_id").val(response.company_id).trigger('change');;
                $("#view_project_id").val(response.project_id).trigger('change');;
                $("#view_sector_name").val(response.sector_name);

                
            },
            function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
        );

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.clsSelect2').select2();

        
    });

    showModal({
        titleContent: "View Payroll Donation Sector",
    });

    $('#edit_updateBtn').click(function(event) {
        event.preventDefault();
        hideModal();
        
    });
</script>
