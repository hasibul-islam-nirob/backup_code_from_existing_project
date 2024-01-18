

<div id="smsViewModal" data-backdrop="static" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">VIEW SMS</h5>
                <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 0 15px 0 15px;">

                <div class="row">
                    <div class="col-lg-12">
                        <table class="table w-full table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:30%;">Sms Title</th>
                                    <th style="width:40%;">Sms Body</th>
                                    <th style="width:30%;">Send To</th>
                                </tr>
                            </thead>
                            <tbody id="root_view">
                                <tr>
                                    <td id="view_sms_title"></td>
                                    <td id="view_sms_body"></td>
                                    <td id="view_sms_send_to"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
