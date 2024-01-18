@extends('Layouts.erp_master')
@section('content')

    <form method="post" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row">

            <div class="col-lg-9 offset-lg-3">

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="ParentName">Module</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="module_id" id="selModuleId" required data-error="Please select module">
                                <option value="">Select One</option>
                                <?php
                                foreach ($module as $row) {
                                    ?>
                                    <option value="<?= $row->id ?>" <?= ($row->id == $sumenus->module_id) ? 'selected' : ''?>>
                                        <?= $row->module_name ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title" for="ParentName">Parent Menu</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <select class="form-control round browser-default"  name="parent_menu_id" id="select_parent_menu_id">
                                <option value="0">Select One</option>
                                
                                <?php
                                foreach ($parent_menu as $row) {
                                    ?>
                                    <option value="<?= $row->id ?>" <?= ($row->id == $sumenus->parent_menu_id) ? 'selected' : ''?>>
                                        <?= $row->menu_name ?> (<small> <?= $row->route_link ?> </small>)
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="roleName">Menu Name</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Menu Name" name="menu_name" id="menu_name" required data-error="Please enter menu name." value="{{ $sumenus->menu_name }}">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Route Link</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <input type="text" name="route_link" id="checkDuplicateCode"
                                   class="form-control round"
                                   placeholder="Enter Route Link" required
                                   data-error="Please enter route link."
                                   value="{{ $sumenus->route_link }}"
                                   onblur="fnCheckDuplicate(
                                    '{{base64_encode('gnl_sys_menus')}}', 
                                    this.name+'&&is_delete', 
                                    this.value+'&&0',
                                    '{{url('/ajaxCheckDuplicate')}}',
                                    this.id,
                                    'txtCodeError', 
                                    'route link',
                                    '{{$sumenus->id}}');" >
                        </div>
                        <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                       
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Page Title</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round"
                                   placeholder="Enter Page Title"
                                   name="page_title" id="page_title"
                                   value="{{ $sumenus->page_title }}" >
                        </div>
                    </div>
                </div>

<!--                            <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="roleName">Controller Name</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Controller Name" name="controller" id="controller" required data-error="Please controller name." value="{{ $sumenus->controller }}">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                        @error('controller')
                            <div class="help-block with-errors is-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                </div>-->

<!--                            <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="roleName">Method Name</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Method Name" name="action" id="action" required data-error="Please enter method name." value="{{ $sumenus->action }}">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                        @error('controller')
                            <div class="help-block with-errors is-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                </div>-->

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title" for="roleName">Icon</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Icon Name" name="menu_icon" id="menu_icon" value="{{ $sumenus->menu_icon }}">
                        </div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="groupName">Order By</label>
                    <div class="col-lg-6 form-group">
                        <div class="input-group ">
                            <input type="text" class="form-control round" id="order_by" name="order_by" placeholder="Enter Order" required data-error="Please enter order by." value="{{ $sumenus->order_by }}">
                        </div>
                    </div>
                </div>

                @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'exClass' => 'float-right'
                        ]])

            </div>

        </div>
    </form>

<script type="text/javascript">
    $('#select_parent_menu_id').select2();

    $('#selModuleId').change(function(){
        var module_id = $(this).val();
        var SelectedVal = $('#select_parent_menu_id').val();


        $.ajax({
            method: "GET",
            url: "{{url('/ajaxMenuList')}}",
            dataType: "text",
            data: {
                module_id: module_id, SelectedVal: SelectedVal
            },
            success: function (data) {
                if (data) {
                    $('#select_parent_menu_id').empty().html(data);
                }
            }
        });

    });
    
    $('form').submit(function (event) {
        $(this).find(':submit').attr('disabled', 'disabled');
    });
</script>

@endsection
