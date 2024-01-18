<?php
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
?>
<div class="row">
    <div class="col-sm-12" style="padding-left: 60px; padding-top: 30px">
        <div id="application_header_date">

        </div>
        <div id="application_header">
            To<br>ED<br>{{ Common::getCompanyInfo()->comp_name }} <br>{{ Common::getCompanyInfo()->comp_addr }}
        </div>

        <div style="padding-top: 10px; padding-bottom: 10px;">
            <strong id="application_subject"></strong>
        </div>

        <div>
            <p>
                Respected Sir / Madam,
            </p>

            <p id="application_body_common"></p>
            <p id="application_body"></p>
        </div>

        <div id="application_footer"></div>

        {{-- <div id="application_process_info" class="mt-5">
            <div class="d-flex justify-content-around">
                <div class="col-4"><b>Created At</b></div>
                <div class="col-4"><b>Created By</b></div>
                <div class="col-4"><b>Approved By</b></div>
            </div>
        </div> --}}

        <table class="table table-hover table-bordered table-striped clsDataTable" style="border-collapse: collapse; width: 90%; margin:0 auto; margin-top:10px">
            <thead>
                <tr style="border-bottom: 1px solid #ddd;">
                    <th style="border: 1px solid #ddd; padding:0px 8px;">Created At</th>
                    <th style="border: 1px solid #ddd; padding:0px 8px;">Created By</th>
                    <th style="border: 1px solid #ddd; padding:0px 8px;">Approved By</th>
                </tr>
            </thead>
            <tbody id="application_process_info">
               
            </tbody>
            
        </table>
        
        
        <br>
        <br>
    </div>
</div>
