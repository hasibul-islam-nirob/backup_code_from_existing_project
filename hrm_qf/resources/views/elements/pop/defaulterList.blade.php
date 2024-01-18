<?php 
    use App\Services\CommonService as Common;
    $sl = 0;
?>

<div class="modal fade" id="modalDefaulterList" tabindex="-1" role="dialog" aria-labelledby="defaulterLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">

                <h4 class="modal-title font-weight-bold text-center">
                    Defaulter List
                </h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body mx-3">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <td>SL</td>
                            <td>Name</td>
                            <td>Loan Code</td>
                            <td>Disbursement Amount</td>
                            <td>Default Amount</td>
                        </tr>
                    </thead>
                    <tbody id="defaulters">
                    </tbody>
                </table>
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