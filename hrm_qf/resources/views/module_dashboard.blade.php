@extends('Layouts.erp_master')
@section('title', 'Modules')
@section('content')

@php
    use App\Services\CommonService as Common;
@endphp

<style>
    .panel-body {
        padding: 0;
    }

    .go-corner {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 40px;
        height: 40px;
        overflow: hidden;
        top: 0;
        right: 0;
        background-color: #F59359;
        border-radius: 0 4px 0 32px;
    }

    .datcard {
        color: #99b898;
        display: block;
        font-family: sans-serif;
        position: relative;
        background-color: #7B78B4;
        padding: 5px;
        z-index: 0;
        overflow: hidden;
        text-decoration: none !important;
    }

    .datcard:before {
        content: "";
        position: absolute;
        z-index: -1;
        top: -16px;
        right: -16px;
        background-color: #F59359;
        height: 1em;
        width: 1em;
        border-radius: 100%;
        transform: scale(1);
        transform-origin: 50% 50%;
        transition: transform 0.25s ease-out;
    }

    .datcard:hover:before {
        transform: scale(45);
    }

</style>

<div class="page app-projects">
    <div class="page-header pt-4">
        <br><br><br>
        <div class="page-header-actions">
            @if(Common::getDBConnection() == "sqlite"
                && (Common::isSuperUser() == true
                || Common::isDeveloperUser() == true
                || Common::getBranchId() > 1))

            <a href="javascript:void(0);" onclick="synchronizeData(event, {{Common::getBranchId()}});" style="margin-right: 20px;">
                <button type="button" class="btn btn-tagged btn-lg animation-scale btn-outline-danger">
                    <span class="btn-tag">
                        <i class="fa fa-refresh fa-4x" aria-hidden="true"></i>
                    </span>
                    Synchronize
                </button>
            </a>
            @endif

            @foreach($SysModules as $modules)
                @php $modules = (object) $modules; @endphp
                @if($modules->id == 1)
                <a href="javascript:void(0)"
                    onclick="setModuleID('{{$modules->id}}','{{ url($modules->module_link) }}','{{ $modules->module_link }}');">
                    <button type="button" class="btn btn-tagged btn-lg btn-outline-secondary animation-scale">
                        <span class="btn-tag">
                            <i class="icon wb-settings" aria-hidden="true"></i>
                        </span>
                        {{ $modules->name }}
                    </button>
                </a>
                @endif
            @endforeach
        </div>
    </div>

    <div class="page-content">
      <div class="row">
        @foreach($SysModules as $modules)
            @php $modules = (object) $modules; @endphp
            @if($modules->id != 1)
                <div class="col-md-4" style="padding-top: 1%;">
                    <a href="javascript:void(0)" class="datcard btn btn-lg btn-round text-uppercase"
                    onclick="setModuleID('{{$modules->id}}','{{ url($modules->module_link) }}','{{ $modules->module_link }}');">

                    <i class="fa {{ (!empty($modules->icon)) ? $modules->icon : 'fa-cart-arrow-down' }} font text-white"
                        style="font-size: 50px"></i>
                        <br>
                        <span style="color:white;" class="h4">{{ $modules->name }}</span>

                        <div class="go-corner"></div>
                    </a>
                </div>
            @endif
        @endforeach
        </div>
    </div>

</div>

<script type="text/javascript">
    function setModuleID(id, mname, module_link) {

        $.ajax({
            method: "GET",
            url: "{{url('/modules/ajaxModuleID')}}",
            dataType: "json",
            data: {
                ModuleID: id,
                ModuleLink: module_link
            },
            success: function(data) {
                if (data === 1) {
                    window.location.href = mname;
                }
            }
        });
    }

    function synchronizeData(event, branchId){
        event.preventDefault();

        // allowOutsideClick: false,
        // allowEscapeKey: false,

        window.swal({
              title: "Synchronizing...",
              text: "Please wait",
            //   image: "assets/images/load.gif",
            //   showConfirmButton: false,
            closeOnClickOutside: false,
              allowOutsideClick: false,
              allowEscapeKey: false,
              closeClick: false,
              buttons: false
            });


        $.ajax({
            method: "GET",
            url: "{{url('/dataSynchronization')}}",
            dataType: "json",
            data: {
                branchId: branchId
            },
            success: function(response) {

                console.log(response);
                const wrapper = document.createElement('div');
                var icon = response['status'];
                var title = response['title'];

                if(response['status'] == 'success'){
                    let html = "<p style='color:#000;'>"+ response['message'] +"</p>";
                    wrapper.innerHTML = html;
                }
                else if (response['status'] == 'error'){
                    let html = "<p style='color:#000;'>Please Contact with our Support Team.</p>";
                    // html += "<p style='color:#000;'>"+ response['message'] +"</p>";
                    console.log(response['message']);

                    wrapper.innerHTML = html;
                }
                else {
                    let html = "<p style='color:#000;'>Please Contact with our Support Team.</p>";
                        html += "<p style='color:#000;'>Connection Error!</p>";
                    wrapper.innerHTML = html;
                }

                swal({
                    icon: icon,
                    title: title,
                    content: wrapper,
                    // timer: 5000
                }).then(function() {
                    // window.location = "{{url('/acc')}}";
                });
            },
            error: function(response) {

                const wrapper = document.createElement('div');
                var icon = 'error';
                var title = 'Connection Error!';

                let html = "<p style='color:#000;'>Please Contact with our Support Team.</p>";
                wrapper.innerHTML = html;

                swal({
                    icon: icon,
                    title: title,
                    content: wrapper,
                    // timer: 5000
                }).then(function() {
                    // window.location = "{{url('/acc')}}";
                });
            }
        });

    }
</script>
@endsection
