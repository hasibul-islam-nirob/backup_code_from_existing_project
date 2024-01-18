@extends('Layouts.erp_master')
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
            width: 30px;
            height: 30px;
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
            padding: 1em;
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

    <div class="row" style="background-color: #f1f4f5; padding-top: 40px;">

        @foreach ($module as $modules)
            @php $modules = (object) $modules; @endphp
            @if ($modules->id != 1)

                <div class="col-md-3" style="padding-top: 20px">
                    <a class="datcard btn btn-lg btn-round text-uppercase" href="{{ url()->current() }}/m/{{ $modules->id }}">
                        <span style="color:white;" class="h4">{{ $modules->module_name }}</span>
                        <div class="go-corner">
                        </div>
                    </a>
                </div>

            @endif
        @endforeach

    </div>

    <script>
        $('.page-header-actions').hide();
    </script>
@endsection
