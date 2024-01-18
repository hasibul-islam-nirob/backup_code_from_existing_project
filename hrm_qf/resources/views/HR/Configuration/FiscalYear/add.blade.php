<?php 
use App\Services\HtmlService as HTML;
?>

<style>
    #ui-datepicker-div{
        z-index: 100000 !important;
    },
    
    .ui-datepicker table {
        display: none !important;
    }
</style>

<form id="fiscal_year_form" enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true" autocomplete="off">
    @csrf
    <div class="row">
        <div class="col-lg-10 offset-sm-1">

            <!-- Html View Load  -->
            {!! HTML::forCompanyFeild() !!}

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Name</label>
                    <div class="input-group flex-nowrap">
                        <input type="text" name="fy_name" id="fy_name" class="form-control w-100" 
                            placeholder="Enter Fiscal Year Name" required data-error="Please enter Fiscal year name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Fiscal For&nbsp;</label>
                    <div class="input-group flex-nowrap">
                        <div class="radio-custom radio-primary" style="margin-left: 20px!important; padding-right: 20px;">
                            <input type="radio" id="radio1" name="fy_for" value="FFY" class="fType" checked>
                            <label for="radio1">FINANCIAL</label>
                        </div>
                        <div class="radio-custom radio-primary" style="margin-left: 20px!important; padding-right: 20px;">
                            <input type="radio" id="radio2" name="fy_for" value="LFY" class="fType">
                            <label for="radio2">LEAVE</label>
                        </div>
                        <div class="radio-custom radio-primary" style="margin-left: 20px!important; padding-right: 20px;">
                            <input type="radio" id="radio3" name="fy_for" value="BOTH" class="fType">
                            <label for="radio3">BOTH</label>
                        </div>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div> 

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Start Date&nbsp;</label>
                    <div class="input-group flex-nowrap">
                        <input type="text" class="form-control date-picker-year common_effective_date" id="fy_start_date" name="fy_start_date" placeholder="DD-MM-YYYY">
                        {{-- <input type="text" class="form-control datepicker-custom" id="fy_start_date" name="fy_start_date" placeholder="DD-MM-YYYY"> --}}
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
        </div>
    </div>
</form>


<script type="text/javascript">

    // Remove Disabled Months, Only enable January and July 
    var removeDisabledMonths = function() {
        setTimeout(function() {

            var monthsToDisable = [1,2,3,4,5,7,8,9,10,11];

            $.each(monthsToDisable, function(k, month) {
                $('#ui-datepicker-div select.ui-datepicker-month').find('option[value="'+month+'"]').remove();
            });

        }, 100);
    };

    $(".date-picker-year").datepicker({
        dateFormat: 'MM yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: false,
        onChangeMonthYear: function(year, month, obj) {
                removeDisabledMonths();
        },

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
        }
    });

    $(".date-picker-year").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });

    $(document).click(function(){
        removeDisabledMonths();
    })

    showModal({
        titleContent: "Add Fiscal Year",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save'
            },
            'btnName': {
                0: 'Save'
            },
            'btnId': {
                0: 'save'
            }
        }),
    });

    
    $('#save').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#fiscal_year_form')[0]);
        let fy_for_value = $("input[name='fy_for']:checked").val();
        
        formData.append("fy_for", fy_for_value);

        callApi("{{ url()->current() }}/../insert/api", 'post', formData,
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                // hideModal();
                ajaxDataLoad();

                if(response.status == "error") {

                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                    });
                } else {
                    hideModal();
                }
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

</script>
