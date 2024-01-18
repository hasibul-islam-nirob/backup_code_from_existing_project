<div class="row">
    <div class="col-lg-12">

        <div class="row">
            <div class="col-lg-12">
                <h4 style="background-color: #17b3a3; color:#fff; padding:10px 0 10px 10px">Application Approval</h4>
            </div>
        </div>

        <div class="row">

            <input hidden name="application_id" value="{{ $applId }}">
            <input hidden name="event_id" value="{{ $applType[4] }}">
            @if ($dmp == 1)
                <div class="col-sm-6 offset-sm-1 form-group">
                    <label class="input-title">{{ $applType[2] }} Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input name="effective_date" type="text" id="effective_date" style="z-index:99999 !important;"
                            class="form-control round datepicker" placeholder="DD-MM-YYYY">
                    </div>
                </div>
            @endif

        </div>

        <div class="row">

            <div class="col-sm-6 offset-sm-1 form-group">
                <label class="input-title">Comment</label>
                <div class="input-group">
                    <div class="input-group">
                        <textarea rows="5" id="appr_comment" name="comment" class="form-control"
                            style="width: 100%"></textarea>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<script>
    $(".datepicker").datepicker();

    // callApi("{{ url()->current() }}/../../../../get_appl_with_notes/{{ $applId }}/{{ $applType[1] }}/api",
    //     'post', '',
    //     function(response, textStatus, xhr) {
    //         const appName = ("{{ $applType[2] }}"+'_date').toLowerCase();

    //         // console.log('app name : '+appName);
    //         // console.log(response.result_data.application[appName]);
    //         $("#effective_date").val(convertDateFormatTwo(response.result_data.application[appName]));

    //     },
    //     function(response) {
    //         showApiResponse(response.status, JSON.parse(response.responseText).message);
    //     }
    // );
</script>
