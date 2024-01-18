@extends('Layouts.erp_master')
@section('content')
    <style>
        /* RESET STYLES & HELPER CLASSES
–––––––––––––––––––––––––––––––––––––––––––––––––– */
        :root {
            --level-1: #8dccad;
            --level-2: #f5cc7f;
            --level-3: #7b9fe0;
            --level-4: #f27c8d;
            --black: black;
        }

        /*ol {
            list-style: none;
        }*/

        .container {
            /*max-width: 1000px;
            padding: 0 10px;
            margin: 0 auto;
            text-align: center;*/
           /* height: 1000px;
            width: auto;*/
            /*background: blue;*/
            /*position: relative;*/
        }

        .rectangle {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* LEVEL-1 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        .level-1 {
            width: 25%;
            padding: 10px 0 10px 25px;
            background: var(--level-1);
        }

        /*.level-1::before {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 23px;
            background: var(--black);
        }*/


        /* LEVEL-2 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */

        /*Horizontal line before a list*/
        /*.level-2-wrapper::before {
            content: "";
            position: absolute;
            top: -20px;
            !*left: 16.6666%;*!
            !*width: 66.66666%;*!
            width: 83.333333%;
            height: 2px;
            background: var(--black);
        }*/

        /*.level-2-wrapper::after {
            display: none;
            content: "";
            position: absolute;
            left: -20px;
            bottom: -20px;
            width: calc(100% + 20px);
            height: 2px;
            background: var(--black);
        }*/

        /*Upper vertical line*/
        /*.level-2-wrapper > li::before {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 20px;
            background: var(--black);
        }*/

        /*For background color*/
        .level-2 {
            /*width: 85%;
            margin: 0 auto 40px;*/
            margin-top: 50px;
            position: relative;
            background: var(--level-2);
        }
        /*.level-2-wrapper {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        .level-2-wrapper li {
            position: relative;
        }*/

        /*Lower vertical line*/
        /*.level-2.test::before {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 40px;
            background: var(--black);
        }*/

        /*.level-2::after {
            display: none;
            content: "";
            position: absolute;
            top: 50%;
            left: 0%;
            transform: translate(-100%, -50%);
            width: 20px;
            height: 2px;
            background: var(--black);
        }*/


        /* LEVEL-3 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        /*.level-3-wrapper {
            position: relative;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 20px;
            width: 90%;
            margin: 0 auto;
        }

        .level-3-wrapper::before {
            content: "";
            position: absolute;
            top: -20px;
            left: calc(25% - 5px);
            width: calc(50% + 10px);
            height: 2px;
            background: var(--black);
        }

        .level-3-wrapper > li::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translate(-50%, -100%);
            width: 2px;
            height: 20px;
            background: var(--black);
        }

        .level-3 {
            margin-bottom: 20px;
            background: var(--level-3);
        }*/

        /* MQ STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        /*@media screen and (max-width: 700px) {
            .rectangle {
                padding: 20px 10px;
            }

            .level-1,
            .level-2 {
                width: 100%;
            }

            .level-1 {
                margin-bottom: 20px;
            }

            .level-1::before,
            .level-2-wrapper > li::before {
                display: none;
            }

            .level-2-wrapper,
            .level-2-wrapper::after,
            .level-2::after {
                display: block;
            }

            .level-2-wrapper {
                width: 90%;
                margin-left: 10%;
            }

            .level-2-wrapper::before {
                left: -20px;
                width: 2px;
                height: calc(100% + 40px);
            }

            .level-2-wrapper > li:not(:first-child) {
                margin-top: 50px;
            }
        }*/

        /*#div1 {
            position: relative;
            left: 50%;
            top: 10%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%)
        }

        #div2 {
            position: relative;
            left: 50%;
            top: 150%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%)
        }*/

        #svg{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
        }

    </style>

    {{--<div class="row">

        <div class="col-sm-12">
            <div id="div1" class="level-2 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

                <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                    <i class="fas fa-edit fa-3x"></i>
                </button>

                <select class="clsSelect2" style="width: 100%">
                    <option>Select</option>
                </select>

                <div>
                    <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #fa0505">
                        <i class="fas fa-minus-circle fa-3x"></i>
                    </button>
                    <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">
                        <i class="fas fa-plus-circle fa-3x"></i>
                    </button>
                </div>

            </div>
        </div>
        --}}{{--<ol class="level-2-wrapper">

            <li class="">
                <div id="div1" class="level-2 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

                    <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                        <i class="fas fa-edit fa-3x"></i>
                    </button>

                    <select class="clsSelect2" style="width: 100%">
                        <option>Select</option>
                    </select>

                    <div>
                        <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #fa0505">
                            <i class="fas fa-minus-circle fa-3x"></i>
                        </button>
                        <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">
                            <i class="fas fa-plus-circle fa-3x"></i>
                        </button>
                    </div>

                </div>
            </li>

            <li class="">
                <div id="div2" class="level-2 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

                    <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                        <i class="fas fa-edit fa-3x"></i>
                    </button>

                    <select class="clsSelect2" style="width: 100%">
                        <option>Select</option>
                    </select>

                    <div>
                        <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #fa0505">
                            <i class="fas fa-minus-circle fa-3x"></i>
                        </button>
                        <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">
                            <i class="fas fa-plus-circle fa-3x"></i>
                        </button>
                    </div>
                </div>
            </li>

            <li>
                <div id="div3" class="level-2 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

                    <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                        <i class="fas fa-edit fa-3x"></i>
                    </button>

                    <select class="clsSelect2" style="width: 100%">
                        <option>Select</option>
                    </select>

                    <div>
                        <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #fa0505">
                            <i class="fas fa-minus-circle fa-3x"></i>
                        </button>
                        <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">
                            <i class="fas fa-plus-circle fa-3x"></i>
                        </button>
                    </div>

                </div>
            </li>
        </ol>--}}{{--
    </div>--}}
    {{--<div class="row">
        <div class="col-sm-4">
            <div class="level-1 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center; padding-top: 7px; padding-bottom: 7px">

                <button class="btn" style="background-color: var(--level-1); padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                    <i class="fas fa-edit fa-3x"></i>
                </button>

                <select class="clsSelect2" style="width: 100%;">
                    <option>Select</option>
                </select>

                <div>
                    <button class="btn" style="background-color: var(--level-1); padding: 0 14px 0 14px; color: #0a0a0a">
                        <i class="fas fa-plus-circle fa-3x"></i>
                    </button>
                </div>

            </div>
        </div>
        <div class="col-sm-4">
            <div class="level-1 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center; padding-top: 7px; padding-bottom: 7px">

                <button class="btn" style="background-color: var(--level-1); padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                    <i class="fas fa-edit fa-3x"></i>
                </button>

                <select class="clsSelect2" style="width: 100%;">
                    <option>Select</option>
                </select>

                <div>
                    <button class="btn" style="background-color: var(--level-1); padding: 0 14px 0 14px; color: #0a0a0a">
                        <i class="fas fa-plus-circle fa-3x"></i>
                    </button>
                </div>

            </div>
        </div>
        <div class="col-sm-4">
            <div class="level-1 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center; padding-top: 7px; padding-bottom: 7px">

                <button class="btn" style="background-color: var(--level-1); padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                    <i class="fas fa-edit fa-3x"></i>
                </button>

                <select class="clsSelect2" style="width: 100%;">
                    <option>Select</option>
                </select>

                <div>
                    <button class="btn" style="background-color: var(--level-1); padding: 0 14px 0 14px; color: #0a0a0a">
                        <i class="fas fa-plus-circle fa-3x"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>--}}

    {{--<div id="div1" class="level-2 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

        <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
            <i class="fas fa-edit fa-3x"></i>
        </button>

        <select class="clsSelect2" style="width: 100%">
            <option>Select</option>
        </select>

        <div>
            <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #fa0505">
                <i class="fas fa-minus-circle fa-3x"></i>
            </button>
            <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">
                <i class="fas fa-plus-circle fa-3x"></i>
            </button>
        </div>

    </div>--}}

    <div class="row">
        <div class="col-sm-12">
            <div id="div1" class="level-1 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

                <select class="clsSelect2" style="width: 100%; padding-left: 20px">
                    <option>Select</option>
                </select>

                <div>
                    <button class="btn" style="background-color: var(--level-1); padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">
                        <i class="fas fa-plus-circle fa-3x"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div id="div2" class="level-2 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

                <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 0; margin: 0; color: #0a0a0a">
                    <i class="fas fa-edit fa-3x"></i>
                </button>

                <select class="clsSelect2" style="width: 100%">
                    <option>Select</option>
                </select>

                <div>
                    <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #fa0505">
                        <i class="fas fa-minus-circle fa-3x"></i>
                    </button>
                    <button class="btn" style="background-color: #f5cc7f; padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">
                        <i class="fas fa-plus-circle fa-3x"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <svg id="svg">
        <line stroke-width="2px" stroke="#000000" id="mySVG"/>
    </svg>

    <script>
        const drawBetweenObjects = {
            drawSVG: function(target, div1, div2) {
                /*$("#mySVG").clone().addClass("clone").insertAfter(this);*/
                var x1 = div1.position().left + div1.outerWidth(true);
                var y1 = div1.position().top + (div1.height()/2);

                var x2 = div2.position().left;
                var y2 = div2.position().top + (div2.height()/2);

                console.log(div1.position().left, div1.position().top, div2.position().left, div2.position().top);

                //target.attr('x1',x1).attr('y1',y1).attr('x2',x2).attr('y2',y2);
                target.attr('x1',100).attr('y1',0).attr('x2',0).attr('y2',500);
                //target.attr('x1',left).attr('y1',top).attr('x2',800).attr('y2',20);
                //target.attr('x1',0).attr('y1',0).attr('x2',800).attr('y2',20);
            }
        }
        $(document).ready(function (){
            drawBetweenObjects.drawSVG($('#mySVG'),$('#div1'),$('#div2'));
        });

        $(window).resize(function() {
            //drawBetweenObjects.drawSVG($('#mySVG'),$('#div1'),$('#div2'));
        })

    </script>
@endsection
