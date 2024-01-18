@extends('Layouts.erp_master')

@section('content')
    <!-- Page -->
    <h3 class="text-center">Delete multiple DayEnd</h3>
    <div class="row">
        <div class="col-lg-12">
            {{-- <form action="#" method="POST" data-toggle="validator" novalidate="true" id="manageDayEndForm"> --}}
            {{-- @csrf --}}

            <div class="row align-items-center pb-10">

                <div class="col-lg-4">
                    <label class="input-title">Module</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="module" id="module" required>
                            <option value="">Select One</option>
                            @foreach ($moduleList as $module)
                                <option value="{{ $module->module_short_name }}">{{ $module->module_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="input-title">Branch</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="branchId" id="branchId">
                            <option value="">Select Branch</option>
                            @foreach ($branchList as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->branch_code . ' - ' . $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label class="input-title">From</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="fromDate" name="fromDate" placeholder="DD-MM-YYYY"
                            autocomplete="off">
                    </div>
                </div>

                {{-- <div class="col-lg-3">
              <label class="input-title">To</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="toDate" name="toDate"
                  placeholder="DD-MM-YYYY" autocomplete="off">
                </div>
            </div> --}}
            </div>

            <div class="row mt-4">
                <div class="col-lg-12 text-center" id="info"></div>
                <button onclick="deleteDayEnd()" class="btn btn-danger col-lg-4 ml-auto mr-auto">Delete Day End</button>
            </div>
            {{-- </form> --}}
        </div>
    </div>
    <!-- End Page -->

    <script>
        $('#branchId').change(function(e) {
            if ($(this).val() != '' && $('#module').val() != '') {
                getDayEndInfo();
            } else {
                $('#info').html("");
                swal({
                    icon: 'error',
                    title: 'Day End info error',
                    text: "Please select Module and Branch to get Day End info",
                });
            }
        });

        $('#module').change(function(e) {
            if ($('#branchId').val() != '' && $('#module').val() != '') {
                getDayEndInfo();
            } else {
                $('#info').html("");
            }
        });

        function getDayEndInfo() {
            $.ajax({
                type: "POST",
                url: "{{ url()->current() }}/getinfo",
                data: {
                    branchId: $('#branchId').val(),
                    module: $('#module').val(),
                },
                dataType: "json",
                success: function(response) {
                    $('#fromDate').val("");
                    if (response.minDate != '') {
                        $("#fromDate").datepicker("option", "minDate", response.minDate);
                    }
                    if (response.maxDate != '') {
                        $("#fromDate").datepicker("option", "maxDate", response.maxDate);
                    }

                    // $('#toDate').val(response.maxDate);

                    $('#info').hide();
                    $('#info').html(`<h5>${response.info}</h5>`);
                    $('#info').show('slow');
                },
                error: function() {
                    alert('error!');
                }
            });
        }

        function deleteDayEnd() {
            if (validateInput()) {
                $.ajax({
                    type: "POST",
                    url: "{{ url()->current() }}/delete",
                    data: {
                        branchId: $('#branchId').val(),
                        module: $('#module').val(),
                        fromDate: $('#fromDate').val(),
                        toDate: $('#toDate').val(),
                    },
                    dataType: "json",
                    success: function(response) {
                        swal({
                            icon: response['alert-type'],
                            title: response['alert-type'],
                            text: response['message'],
                        });

                        
                    },
                    error: function() {
                        alert('error!');
                    }
                });
            }
        }

        function validateInput() {
            let message = [];
            let passed = true;
            if ($('#branchId').val() == "") {
                message.push('Branch must be selected');
                passed = false;
            }
            if ($('#module').val() == "") {
                message.push('Module must be selected');
                passed = false;
            }
            if ($('#fromDate').val() == "") {
                message.push('From date cant not be empty');
                passed = false;
            }
            if ($('#toDate').val() == "") {
                message.push('To date cant not be empty');
                passed = false;
            }

            if (passed) {
                return passed;
            } else {
                swal({
                    icon: 'error',
                    title: 'Input Validation Failed',
                    text: message.join(',\n'),
                });
                return passed;
            }
        }

        $("#fromDate").datepicker({
            dateFormat: 'dd-mm-yy',
            orientation: 'bottom',
            autoclose: true,
            todayHighlight: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:+10',
            onClose: function(selectedDate) {
                $("#toDate").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#toDate").datepicker({
            dateFormat: 'dd-mm-yy',
            orientation: 'bottom',
            autoclose: true,
            todayHighlight: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:+10',
            disabled: true,
            onClose: function(selectedDate) {
                $("#fromDate").datepicker("option", "maxDate", selectedDate);
            }
        });
        $("#fromDate, #toDate").on('click', function() {
            this.value = '';
        });
    </script>
@endsection
