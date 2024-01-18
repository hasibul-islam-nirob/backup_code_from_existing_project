
@php
    use App\Services\CommonService as Common;
    use App\Services\HtmlService as HTML;

    if (!empty(Session::get('LoginBy.user_role.role_module'))) {
       $SysModules = Session::get('LoginBy.user_role.role_module');
    }else {
       $SysModules = array();
    }

    $dateFlag = true;
    $routes = explode('/', $current_route_name);

    if(isset($routes[0]) && $routes[0] == 'gnl'){
        $dateFlag = false;
    }

    $productLogo = DB::table('gnl_dynamic_form_value')
        ->where([['is_delete', 0], ['is_active', 1], ['type_id', 5], ['form_id', 'GCONF.3']])
        ->first();

    $productTitle = DB::table('gnl_dynamic_form_value')
        ->where([['is_delete', 0], ['is_active', 1], ['type_id', 5], ['form_id', 'GCONF.4']])
        ->first();

    $productTitle = isset($productTitle->name) && !empty($productTitle->name) ? $productTitle->name : null;

@endphp

<style>
    .customNav{
        text-align: left;
        padding:5%;
        /* background-color:transparent; */
        background-color:#fff;
        border-radius: 10%;
    }

    .customNav a:hover {
        background-color: #589ffc;
        color: #fff;
    }
</style>

<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega navbar-inverse d-print-none" role="navigation">

    <div class="navbar-header">
        <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
            data-toggle="menubar">
            <span class="sr-only">Toggle navigation</span>
            <span class="hamburger-bar"></span>
        </button>

        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
            data-toggle="collapse">
            <i class="icon wb-more-horizontal" aria-hidden="true"></i>
        </button>

        <a class="navbar-brand navbar-brand-center" href="{{url('/')}}">

            @if(isset($productLogo->name) && !empty($productLogo->name) && file_exists($productLogo->name))
                <img class="navbar-brand-logo" src="{{ asset($productLogo->name) }}" title="Logo" style="height: 38px;">
            @endif

            @if(!empty($productTitle))
                <span title="{{ $productTitle }}">
                    {{ $productTitle }}
                </span>
            @endif
        </a>

        <div class="dropdown-content customNav">
            @foreach ($SysModules as $module)
                {{-- @if(Common::getDBConnection() != "sqlite" || (Common::getDBConnection() == "sqlite" && $module['id'] <= 2)) --}}
                    <a href="{{ url($module['module_link']) }}">{{ $module['name'] }}</a>
                {{-- @endif --}}
            @endforeach
        </div>
    </div>

    <div class="navbar-container container-fluid">

        <div class="site-menubar site-menubar-light">
            <div class="site-menubar-body">
                <!-- Html View Load -->
                {!! HTML::makeMenus() !!}

            </div>
        </div>

        <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
            <div class="row">
                <div class="col-xl-11 col-lg-11 col-sm-11 col-md-11 col-11 top-heading-branch-data">
                    @if($dateFlag == true)
                    <span>
                        <b style="font-family: sans-serif;">
                            Date:
                        </b>
                        <span id="systemDate">{!! Common::systemCurrentDate() !!} </span>
                    </span>
                    &nbsp;
                    @endif
                    <span>
                        <b style="font-family: sans-serif;">
                            Branch:
                        </b>
                        <?php
                            $BranchName = Common::ViewTableLast('gnl_branchs',['id'=> Common::getBranchId(), 'is_approve' => 1], ['branch_name']);

                            if($BranchName){
                                echo $BranchName->branch_name;
                            }
                        ?>
                    </span>
                </div>

                <div class="col-xl-1 col-lg-1 col-sm-1 col-md-1 col-1">
                    <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
                        <li class="nav-item dropdown">
                            <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
                                data-animation="scale-up" role="button">
                                <span class="avatar avatar-online">
                                    <img src="{{asset('assets/images/portraits/5.jpg')}}" alt="...">
                                    <i></i>
                                </span>
                            </a>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i
                                        class="icon wb-user" aria-hidden="true"></i>{{ Auth::user()->full_name }}</a>
                                <div class="dropdown-divider" role="presentation"></div>
                                <a class="dropdown-item" href="{{url('logout')}}" role="menuitem">
                                    <i class="icon wb-power" aria-hidden="true"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</nav>
