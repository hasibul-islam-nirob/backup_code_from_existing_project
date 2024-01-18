@extends('Layouts.erp_master')
@section('content')
<?php
use App\Services\RoleService as Role;
use App\Services\CommonService as Common;

$AllData = Role::moduleArray();
$MenuArray = Role::menuArray();
$PerArray = Role::permissionArray();

$getActionForCommonValue = DB::table('gnl_dynamic_form_value')->whereIn('value_field', [100,101,102,103,104,105,106,107,108,109,110])->get();


// dd($getActionForCommonValue, $PerArray);
$UpdateFlag = true;

if(Common::isSuperUser() == false){
    if(Common::getRoleId() == $rid){
        $UpdateFlag = false;
    }
}

?>

<div class="row">
    <div class="col-lg-12">

    <span style="font-size:18px; color:#000;"><b> Role Name:</b> {{ $role_name }}</span><br><br>
        <form method="post">
            @csrf
            <div class="example-wrap panel with-nav-tabs panel-primary">
                <div class="nav-tabs-horizontal" data-plugin="tabs" id="tabs">

                    @if($UpdateFlag == true)
                        <li class="list-unstyled">
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" name="" id="all-menus-per"
                                    onclick="showAllPermission(this.id)" />
                                <label for="all-menus-per">
                                    <b>Select All</b>
                                </label>
                            </div>
                        </li>
                    @endif
                    <!-- All Module Button & Checkbox  -->
                    <ul class="nav nav-tabs nav-tabs-reverse" role="tablist">
                        @foreach($AllData as $row)
                        <li class="nav-item mr-2" role="presentation">
                            <a id="module_arr_{{ $row['module_id'] }}"
                                class="nav-link nav-tabs btn btn-bg-color moduleCheckbox"
                                data-toggle="tab" href="#module_arr_{{ $row['module_id'] }}_check_tab"
                                role="tab" ondblclick="fnModuleCheck(this.id);"
                                title="Double click for Check this module">

                                <input class="moduleclass" type="checkbox"
                                    id="module_arr_{{ $row['module_id'] }}_check" name="module_arr[]"
                                    value="{{ $row['module_id'] }}"
                                    <?=(in_array($row['module_id'], $modules)) ? 'checked' : ''?>>

                                <label>{{ $row['module_name'] }} </label>
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    <!-- All Module wise Menu, Sub-Menu, Permission Load -->
                    <div class="tab-content pt-20">
                        @foreach($AllData as $row)
                        <!--  tab pane  -->
                        <div class="tab-pane" id="module_arr_{{ $row['module_id'] }}_check_tab" role="tabpanel">

                            <div class="row">
                                <div class="col-lg-12">

                                    <!-- Menu Section -->
                                    @php
                                        $row['Menus'] = (isset($MenuArray[$row['module_id']])) ? $MenuArray[$row['module_id']] : array();
                                    @endphp

                                    @foreach($row['Menus'] as $MenuData)


                                    <li class="list-unstyled menus">
                                        <div class="checkbox-custom checkbox-primary menuscheck">
                                            <input type="checkbox" class="menusCheckbox"
                                                name="menu_arr[]"
                                                id="menu_arr_{{ $row['module_id'] }}_{{ $MenuData['menu_id'] }}"
                                                value="{{ $MenuData['menu_id'] }}"
                                                <?=(in_array($MenuData['menu_id'], $menus)) ? 'checked' : ''?>
                                                onclick="fnPermissionLoad(this.id, 'module_arr_{{ $row['module_id'] }}_check')">

                                            <label
                                                for="menu_arr_{{ $row['module_id'] }}_{{ $MenuData['menu_id'] }}">
                                                <b>{{ $MenuData['menu_name'] }}</b>
                                            </label>
                                        </div>

                                        <!-- Sub Menu & Permission View calling  -->
                                        {!! Role::subMenuPermissionLoad($row['module_id'], $MenuData['menu_id'], $MenuData['sub_menu'], $PerArray, $menus, $permissions, $getActionForCommonValue) !!}

                                    </li>
                                    @endforeach

                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    @if($UpdateFlag == true)
                        @include('elements.button.common_button', ['back' => true, 'submit' => [
                                'action' => 'update',
                                'title' => 'update',
                                'id' => 'validateButton2',
                                'exClass' => 'float-right'
                            ]])
                    @else

                    @include('elements.button.common_button', ['back' => true ])

                    @endif

                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {

    @if($UpdateFlag == false)
        $("input:checkbox").prop( "disabled", true );
    @endif

    $('.nav-tabs a:first').tab('show');

    $('.menus input:checkbox').each(function() {
        var menu_id = $(this).attr('id');

        if ($(this).is(':checked')) {

            var module_id = menu_id.split("_")[2];

            $('#' + menu_id).parents('li.list-unstyled.menus').each(function() {

                var parent_menu_id = $($(this).children('div.checkbox-custom.checkbox-primary.menuscheck').find('input:checkbox:first')).attr('id');

                $('#' + parent_menu_id).prop('checked', true);
                $('#module_arr_'+ module_id +'_check').prop('checked', true);

                $('#' + parent_menu_id + '_sub_lvl').show();
                $('#' + parent_menu_id + '_per_lvl').show();
            });
        }

        // if ($(this).is(':checked')) {
            // $('#' + menu_id + '_sub_lvl').show();
            // $('#' + menu_id + '_per_lvl').show();
        // }
    });
});

function showAllPermission(menusPer) {

    if ($('#' + menusPer).is(':checked')) {

        $("label.permissions").each(function() {
            $(this).show();
            $('input:checkbox').each(function() {
                $(this).prop('checked', true);
            });
        });

        $("label.submenus").each(function() {
            $(this).show();
            $('input:checkbox').each(function() {
                $(this).prop('checked', true);
            });
        });

    } else {
        $("label.permissions").each(function() {
            $(this).hide();
            $('input:checkbox').each(function() {
                $(this).prop('checked', false);
            });
        });

        $("label.submenus").each(function() {
            $(this).hide();
            $('input:checkbox').each(function() {
                $(this).prop('checked', false);
            });
        });
    }
}

function fnModuleCheck(anchor_id) {

    $('#' + anchor_id + ' input:checkbox').each(function() {

        if ($(this).is(':checked')) {
            $(this).prop('checked', false);

            $($('#' + anchor_id).attr('href') + " label.permissions").each(function() {
                $(this).hide();

                $($('#' + anchor_id).attr('href') + ' input:checkbox').each(function() {
                    $(this).prop('checked', false);
                });
            });

            $($('#' + anchor_id).attr('href') + " label.submenus").each(function() {
                $(this).hide();

                $($('#' + anchor_id).attr('href') + ' input:checkbox').each(function() {
                    $(this).prop('checked', false);
                });
            });
        } else {
            $(this).prop('checked', true);

            $($('#' + anchor_id).attr('href') + " label.permissions").each(function() {
                $(this).show();

                $($('#' + anchor_id).attr('href') + ' input:checkbox').each(function() {
                    $(this).prop('checked', true);
                });
            });

            $($('#' + anchor_id).attr('href') + " label.submenus").each(function() {
                $(this).show();

                $($('#' + anchor_id).attr('href') + ' input:checkbox').each(function() {
                    $(this).prop('checked', true);
                });
            });
        }
    });
}


function fnPermissionLoad(menu_id, module_id) {

    // console.log(module_id);

    if ($("#" + menu_id).is(':checked')) {

        $('#' + module_id).prop('checked', true);

        $('#' + menu_id + '_sub_lvl').show();
        $('#' + menu_id + '_per_lvl').show();
        $('#' + menu_id + '_sub_lvl label.submenus').show();
        $('#' + menu_id + '_sub_lvl label.permissions').show();

        $('#' + menu_id + '_sub_lvl input:checkbox').each(function() {
            $(this).prop('checked', true);
        });

        $('#' + menu_id + '_per_lvl input:checkbox').each(function() {
            $(this).prop('checked', true);
        });

    } else {

        $('#' + menu_id + '_per_lvl input:checkbox').each(function() {
            $(this).prop('checked', false);
        });

        $('#' + menu_id + '_sub_lvl input:checkbox').each(function() {
            $(this).prop('checked', false);
        });

        $('#' + menu_id + '_sub_lvl label.permissions').hide();
        $('#' + menu_id + '_sub_lvl label.submenus').hide();
        $('#' + menu_id + '_per_lvl').hide();
        $('#' + menu_id + '_sub_lvl').hide();

        var Uflag = true;
        $('#' + module_id + '_tab input:checkbox').each(function() {

            if ($(this).is(':checked')) {
                Uflag = false;
            }
        });

        if (Uflag === true) {
            $('#' + module_id).prop('checked', false);
        }

    }
}

$('form').submit(function (event) {
    $(this).find(':submit').attr('disabled', 'disabled');
});
</script>
@endsection
