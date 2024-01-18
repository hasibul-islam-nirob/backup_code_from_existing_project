@extends('Layouts.erp_master')
@section('content')

    @php
    // dd($moduleId, $events, $moduleName);
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
            width: 15px;
            height: 15px;
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

    <h5 class="text-center" style="font-size: 16px;">{{$moduleName}} Module</h5>

    <div class="row" style="background-color: #f1f4f5; padding-top: 40px;">

        @foreach ($events as $event)
            <div class="col-md-2" style="padding-bottom: 25px;">
                <a class="datcard btn btn-sm btn-round text-uppercase" href="{{ url()->current() }}/{{ $event->id }}">
                    <span style="color:white;" class="h6">{{ $event->event_title }}</span>
                    <div class="go-corner">
                    </div>
                </a>
            </div>
        @endforeach

    </div>
@endsection