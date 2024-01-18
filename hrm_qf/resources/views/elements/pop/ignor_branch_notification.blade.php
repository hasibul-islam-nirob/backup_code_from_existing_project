

<div class="modal fade" id="incomplete_list_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" class="d-print-none">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">

                <h4 class="modal-title font-weight-bold text-center">
                    Data not Found !!
                </h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body mx-3">
                @if(isset($incompleteReason) && $incompleteReason == "year_not_found")
                <p style="color:red;"> ** Data not found in Year End. Please execute year end for those branches. **</p>
                @elseif(isset($incompleteReason) && $incompleteReason == "month_not_found")
                <p style="color:red;"> ** Data not found for all Month End. Please execute month End for those branches. **</p>
                @endif
                
                <div class="row border">
                    @if(isset($incompleteBranchList))
                    @foreach ($incompleteBranchList as $BRow)
                        <div class="col-sm-4 border">
                            {{ $BRow }}
                        </div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-center">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="form-group d-flex justify-content-center">
                            <div class="example example-buttons">
                                <a href="javascript:void(0)" class="btn btn-default btn-round" data-dismiss="modal">Close</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        var branches = "";
        var branches = <?= (isset($incompleteBranchList)) ? json_encode($incompleteBranchList) : json_encode(0) ?>;

        if (branches.length > 0) {
           $("#incomplete_list_modal").modal('show');
        }

    });
</script>
