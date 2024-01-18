@extends('Layouts.erp_master')
@section('content')

    @php 
        use App\Services\HtmlService as HTML;
    @endphp

    <form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row ">
            <div class="col-lg-9 offset-lg-3">
            {!! HTML::forCompanyFeild() !!}

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Terms Type</label>
                    <div class="col-lg-5">
                        <div class="input-group ">
                            <select class="form-control clsSelect2" name="type_id" 
                            id="type_id" required data-error="Please select Terms Type">
                                <option value="">Select Terms Type</option>
                                @foreach ($termTypes as $row)
                                <option value="{{$row->id}}" {{ $TCData->type_id == $row->id ? 'selected' : ''}}>{{$row->type_title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="tc_name">Terms & Conditions</label>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <textarea class="form-control round" id="tc_name"
                                name="tc_name" rows="2"  placeholder="Enter Terms & Conditions" 
                                required data-error="Please enter Terms & Conditions">{{$TCData->tc_name}}</textarea>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                @include('elements.button.common_button', ['back' => true, 'submit' => [
                                'action' => 'update',
                                'title' => 'update',
                                'id' => 'updateButton',
                                'exClass' => 'float-right'
                            ]])
            </div>
        </div>
    </form>

    <script type="text/javascript">
        $('form').submit(function (event) {
            $(this).find(':submit').attr('disabled', 'disabled');
        });

        CKEDITOR.replace( 'tc_name' );
    </script>
@endsection
