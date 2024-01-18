@extends('Layouts.erp_master_full_width')
@section('content')
    <style>
        /* RESET STYLES & HELPER CLASSES
–––––––––––––––––––––––––––––––––––––––––––––––––– */
        :root {
            --level-1: #8dccad;
            --level-2: #f5cc7f;
            --level-3: #7b9fe0;
            --level-4: #f27c8d;
            --level-5: #6cd492;
            --level-6: #6cd4cb;
            --level-7: #6c86d4;
            --level-8: #816cd4;
            --level-9: #cf6cd4;
            --level-10: #d4c66c;
            --level-11: #f27c8d;
            --black: black;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        ol {
            list-style: none;
        }

        /*body {
            margin: 50px 0 100px;
            text-align: center;
            font-family: "Inter", sans-serif;
        }*/

        .container {
            max-width: 100%;
            padding: 0 10px;
            margin: 0 auto;
            text-align: center;
        }

        .rectangle {
            position: relative;
            padding: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }


        /* LEVEL-1 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        .level-1 {
            width: 250px;
            margin: 0 auto 40px;
            background: var(--level-1);
        }

        /*.level-1::before {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 20px;
            background: var(--black);
        }*/


        /* LEVEL-2 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        .level-2-wrapper {
            /*position: relative;*/
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        .test-1-wrapper {
            /*position: relative;*/
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        .test-2-wrapper {
            /*position: relative;*/
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        /*.level-2-wrapper::before {
            content: "";
            position: absolute;
            top: -20px;
            left: 25%;
            width: 50%;
            height: 2px;
            background: var(--black);
        }

        .level-2-wrapper::after {
            display: none;
            content: "";
            position: absolute;
            left: -20px;
            bottom: -20px;
            width: calc(100% + 20px);
            height: 2px;
            background: var(--black);
        }

        .level-2-wrapper li {
            position: relative;
        }

        .level-2-wrapper > li::before {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 20px;
            background: var(--black);
        }*/

        .level-2 {
            width: 220px;
            margin: 30px auto 40px auto;
            background: var(--level-2);
            align-content: center;
        }

        /*.level-2::before {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 20px;
            background: var(--black);
        }

        .level-2::after {
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


        /* LEVEL-4 STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        /*.level-4-wrapper {
            position: relative;
            width: 80%;
            margin-left: auto;
        }

        .level-4-wrapper::before {
            content: "";
            position: absolute;
            top: -20px;
            left: -20px;
            width: 2px;
            height: calc(100% + 20px);
            background: var(--black);
        }

        .level-4-wrapper li + li {
            margin-top: 20px;
        }

        .level-4 {
            font-weight: normal;
            background: var(--level-4);
        }

        .level-4::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0%;
            transform: translate(-100%, -50%);
            width: 20px;
            height: 2px;
            background: var(--black);
        }*/


        /* MQ STYLES
        –––––––––––––––––––––––––––––––––––––––––––––––––– */
        @media screen and (max-width: 700px) {
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
        }

        #svg{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 200vh;
            pointer-events: none;
        }

        .tbb{
            border-style: solid;
            border-color: red;
        }

    </style>


    <svg id="svg">
        <line stroke-width="2px" stroke="#000000" id="mySVG"/>
    </svg>

    <form id="my-form">
        <div class="container">
            <ol id="level-1">
                <li id="n-1">
                    <div class="level-1 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">

                        <select name="n-1" class="clsSelect2" style="width: 100%;">
                            <option value="">Select</option>
                            @foreach($designations as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>

                        <div>
                            <button onclick="addBtnClicked(this)" data-level-no = '1' class="btn" style="background-color: var(--level-1); color: #0a0a0a">
                                <i class="fas fa-plus-circle fa-3x"></i>
                            </button>
                        </div>

                    </div>
                </li>
            </ol>

            {{--<ol class="level-2-wrapper">
                <li>
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
            </ol>

            <ol class="level-2-wrapper">
                <li>
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
            </ol>--}}

            {{--<ol class="level-2-wrapper">

                <li>
                    <ol class="test-1-wrapper">

                        <li>
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

                    </ol>
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

            </ol>--}}

        </div>

        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="form-group d-flex justify-content-center">
                    <div class="example example-buttons">
                        <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                        <button class="btn btn-primary btn-round" id="saveBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Level</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Enter level</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input id="new_level" type="text" class="form-control round" name="new_level"
                                       placeholder="Enter new level">
                                <input hidden id="prev_level">
                                <input hidden id="node_id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="level_change_btn" type="button" class="btn btn-primary">Change</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('form').submit(false);

        function isAllFieldSelected(){
            var flag = 0;
            $('select.clsSelect2').each(function(){

                if($(this).val() === ''){
                    flag = 1;
                }

                //console.log($(this).val());
                console.log($(this)[0]);
            });
            return flag === 0;
        }

        $('#saveBtn').click(function (e){
            e.preventDefault();

            if (!isAllFieldSelected()){
                swal({
                    icon: 'error',
                    title: 'Error...',
                    text: "All the designation field should be selected!!",
                    timer: 4000,
                    showConfirmButton: false
                });
                return;
            }

            // Get form
            let form = $('#my-form')[0];

            // FormData object
            let data = new FormData(form);
            Object.keys(lcr).forEach(key => data.append('c-'+key, lcr[key]));
            $.ajax({
                type : 'post',
                dataType : 'json',
                url : "{{ url()->current() }}" + '/add',
                data : data,
                processData: false,
                contentType: false,
                cache: false,
                success : function (response){
                    if (response.status === 'success'){
                        swal({
                            icon: 'success',
                            title: 'Success...',
                            text: response.msg,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    }
                    else if(response.status === 'error'){
                        swal({
                            icon: 'error',
                            title: 'Error...',
                            text: response.msg,
                            timer: 2000,
                            showConfirmButton: false
                        })
                    }
                    else {
                        swal({
                            icon: 'error',
                            title: 'Error...',
                            text: 'Unknown error occurred. Try again!!',
                            timer: 2000,
                            showConfirmButton: false
                        })
                    }
                },
            });
        });

        let lc = [];
        let lcr = [];
        let designations = {!! json_encode($designations) !!};
        let desOptions = '';
        let hierarchyData = {!! json_encode($hierarchyData) !!};

        function editLevel(btn){
            let n = btn.parentElement.parentElement;
            let x = '', flag = 0, id = n.id;

            if (isChildExist(id)){
                swal({
                    icon: 'error',
                    title: 'Error..',
                    text: 'You can not change level whose have existing child.',
                    timer: 3000,
                });
                return;
            }
            $('#new_level').val(getLevel(id));
            $('#prev_level').val(getLevel(id));
            $('#node_id').val(id);
            $('#exampleModal').modal('show');
        }

        $('#level_change_btn').click(function (){
            let pre_level = $('#prev_level').val();
            let new_level = $('#new_level').val();
            let node_id = $('#node_id').val();
            if (new_level - pre_level <= 0){
                swal({
                    icon: 'error',
                    title: 'Error...',
                    text: 'New level can not be less than or equal to current level',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            let node = node_id;

            for (let i=pre_level; i<new_level; i++){
                if (i === pre_level){
                    node = changeLevel($('#' + node), parseInt(i), parseInt(i)+1);
                }
                else {
                    node = changeLevel(node, parseInt(i), parseInt(i)+1);
                }
            }

            $('#exampleModal').modal('hide');

        });

        function changeLevel(sourceNode, sourceLevel, destLevel){
            let snId = sourceNode.attr('id');
            sourceNode.find('div').hide();
            sourceNode.find('select').append('<option value="-1">DD</option>');
            sourceNode.find('select').val(-1);
            //sourceNode.html("");
            sourceNode.css({"padding-left" : "110px", "padding-right" : "110px"});
            //sourceNode.removeAttr('id');
            //$('#p-' + snId).append(cloneNode);
            /*$('[id^=svg-n]').remove();
            drawArrows('n-1');*/

            let node = addChild(sourceLevel, destLevel, snId);

            /*$('#level-'+(sourceLevel + 2)).append(
                '<li id="p-'+ snId + '-' + lcr[snId] +'">' +
                '<ol>' +

                '</ol>' +
                '</li>'
            );*/
            appendNode($('#level-'+(sourceLevel + 2)),
                '<li id="p-'+ snId + '-' + lcr[snId] +'">' +
                '<ol>' +

                '</ol>' +
                '</li>',
                'p-'+ snId + '-' + lcr[snId]
            );
            $('#level-'+ (sourceLevel + 2)).css({"display" : "grid", "grid-template-columns" : "repeat("+ (lc[sourceLevel]) +", 1fr)"});

            $('[id^=svg-n]').remove();
            drawArrows('n-1');
            return node;
        }

        $(window).on('load', function() {
            // drawTree('n-1');
            //let node = changeLevel($('#n-1-1'), 2, 3);
            //console.log(lc);
            //let node1 = changeLevel(node, 3, 4);
            //let node2 = changeLevel(node1, 4, 5);
            //console.log(node);
        });

        $(document).ready(function (){
            //create 100 level
            for (let i=2; i<100; i++){
                createLevel(i);
            }

            //Previous.....................
            $('#level-2').append(
                '<li id="p-n-1">' +
                '<ol>' +

                '</ol>' +
                '</li>'
            )

            //create lc
            for (let i=1; i<100; i++){
                lc.push(0);
            }

            //Create designations options
            for (let i=0; i<designations.length; i++){
                let x = '<option value="'+ designations[i].id +'">'+ designations[i].name +'</option>';
                desOptions += x;
            }

            if (hierarchyData.length !== 0){
                $('#n-1 select').val(hierarchyData[0].designation_id);
            }

            drawTree('n-1');
        });

        function getSelectedDesignation(path){
            for (let i=0; i<hierarchyData.length; i++){
                if (hierarchyData[i].path === path){
                    return hierarchyData[i].designation_id;
                }
            }
            return '';
        }

        function getLevel(path){
            let c = 0;
            for (let i=0; i<path.length; i++){
                if (path[i] === '-'){
                    c++;
                }
            }
            return c;
        }

        function isPathExist(path){
            for (let i=0; i<hierarchyData.length; i++){
                if (hierarchyData[i].path === path){
                    return true;
                }
            }
            return false;
        }

        let sId = 'n-1';

        function drawTree(){

            for (let i=0; i<hierarchyData.length; i++){
                let level = getLevel(hierarchyData[i].path);

                //New.............................................
                /*if (hierarchyData[i].no_of_child !== 0){
                    $('#level-'+(level + 1)).append(
                        '<li id="p-'+ hierarchyData[i].path+ '">' +
                        '<ol>' +

                        '</ol>' +
                        '</li>'
                    );
                }*/

                for (let j=1; j<=hierarchyData[i].no_of_child; j++){

                    let node = addChild(level, level+1, hierarchyData[i].path, getSelectedDesignation(hierarchyData[i].path + '-' + j));
                    if (getSelectedDesignation(hierarchyData[i].path + '-' + j) === -1){
                        node.find('div').hide();
                        node.find('select').append('<option value="-1">DD</option>');
                        node.find('select').val(-1);
                    }
                    //Previous.............................................
                    /*$('#level-'+(level + 2)).append(
                        '<li id="p-'+ hierarchyData[i].path + '-' + lcr[hierarchyData[i].path] +'">' +
                        '<ol>' +

                        '</ol>' +
                        '</li>'
                    );*/
                    appendNode($('#level-'+(level + 2)),
                        '<li id="p-'+ hierarchyData[i].path + '-' + lcr[hierarchyData[i].path] +'">' +
                        '<ol>' +

                        '</ol>' +
                        '</li>',
                        'p-'+ hierarchyData[i].path + '-' + lcr[hierarchyData[i].path]
                    )
                }
            }

        }

        $(window).resize(function() {
            $('[id^=svg-n]').remove();
            drawArrows('n-1');
        })

        function connectObject(svg, div1, div2){
            var x1 = div1.position().left + parseInt(div1.css('marginLeft'), 10) + (div1.outerWidth()/2);
            var y1 = div1.position().top + parseInt(div1.css('marginTop'), 10) + (div1.outerHeight()/2);

            var x2 = div2.position().left + parseInt(div2.css('marginLeft'), 10) + (div2.outerWidth()/2);
            var y2 = div2.position().top + parseInt(div2.css('marginTop'), 10) + (div2.outerHeight()/2);

            svg.attr('x1',x1).attr('y1',y1).attr('x2',x2).attr('y2',y2);
        }

        function drawLowerVertical(svg, source, len){
            var x1 = source.position().left + parseInt(source.css('marginLeft'), 10) + (source.outerWidth()/2);
            var y1 = source.position().top + parseInt(source.css('marginTop'), 10) + (source.outerHeight()/2);

            svg.attr('x1',x1).attr('y1',y1).attr('x2',x1).attr('y2',( source.position().top + parseInt(source.css('marginTop'), 10) + source.outerHeight() + len));
        }

        function addBtnClicked(btn){
            let sourceLevelNo = $(btn).data('levelNo');
            let targetLevelNo = parseInt(sourceLevelNo) + 1;
            let sourceId = btn.parentElement.parentElement.parentElement.id;

            let flag = 0, x = '';
            for (let i = sourceId.length-1; i>=0; i--){
                if (sourceId[i] === '-' && flag === 0){
                    flag = 1;
                    continue;
                }
                if (flag === 1){
                    x += sourceId[i];
                }
            }
            let parentId = x.split("").reverse().join("");
            //console.log(sourceId, parentId, lcr);
            //console.log(targetLevelNo);
            //console.log(typeof lcr[sourceId], lcr[sourceId]);

            //New...........................................................
            /*if (typeof lcr[sourceId] === 'undefined'){
                $('#level-'+(targetLevelNo)).append(
                    /!*'<li id="p-'+ parentId + '-' + ((typeof lcr[sourceId] === 'undefined') ? 1 : (lcr[sourceId] + 1)) +'">' +*!/
                    '<li id="p-'+ sourceId +'">' +
                    '<ol>' +

                    '</ol>' +
                    '</li>'
                )

                //console.log("Appended....");
            }*/

            addChild(sourceLevelNo, targetLevelNo, sourceId);

            //Previous............................................
            /*$('#level-'+(targetLevelNo + 1)).append(
                '<li id="p-'+ sourceId + '-' + lcr[sourceId] +'">' +
                '<ol>' +

                '</ol>' +
                '</li>'
            )*/
            appendNode($('#level-'+(targetLevelNo + 1)),
                '<li id="p-'+ sourceId + '-' + lcr[sourceId] +'">' +
                '<ol>' +

                '</ol>' +
                '</li>',
                'p-'+ sourceId + '-' + lcr[sourceId]
            );

            $('#level-'+ (targetLevelNo + 1)).css({"display" : "grid", "grid-template-columns" : "repeat("+ (lc[sourceLevelNo]) +", 1fr)"});
            $('[id^=svg-n]').remove();
            drawArrows('n-1');

        }

        function appendNode(parent, node, nId){
            //parent.append(node);
            //console.log(nId);
            let child = parent[0].children;
            for (let i=0; i<child.length; i++){
                //console.log(nId, child[i].id);
                if (nId < child[i].id){
                    //console.log(nId, child[i].id);
                    $(node).insertBefore(child[i]);
                    return;
                }
            }
            parent.append(node);
        }

        function addChild(sourceLevelNo, targetLevelNo, sourceId, selectVal){

            //Add css
            //console.log(lc, sourceLevelNo);

            if (targetLevelNo !== 2){
                $('#level-'+ targetLevelNo).css({"display" : "grid", "grid-template-columns" : "repeat("+ (lc[sourceLevelNo-1]) +", 1fr)"});
            }
            else {
                $('#level-'+ targetLevelNo).css({"display" : "grid", "grid-template-columns" : "repeat(1, 1fr)"});
            }
            lc[sourceLevelNo]++;

            if (lcr[sourceId] === undefined){
                lcr[sourceId] = 0;
            }

            lcr[sourceId]++;

            //console.log($('#p-' + sourceId + ' ol')[0]);
            let newNode = createNode(targetLevelNo, sourceId + '-' + lcr[sourceId]);

            $('#p-' + sourceId + ' ol').append(newNode);

            $('#' + sourceId + '-' + lcr[sourceId] + ' select').val(selectVal);
            $('#' + sourceId + '-' + lcr[sourceId] + ' div:first-child').css("background-color", "var(--level-"+ targetLevelNo +")");

            $('#p-' + sourceId + ' ol').css({"display" : "grid", "grid-template-columns" : "repeat("+ (lcr[sourceId]) +", 1fr)"});

            $('.clsSelect2').select2();

            $('[id^=svg-n]').remove();
            drawArrows('n-1');
            return $('#'+ sourceId + '-' + lcr[sourceId]);
        }

        function drawArrows(root){

            if (lcr[root] === undefined){
                return;
            }

            let noOfChild = lcr[root];

            if (noOfChild === 0){
                return;
            }
            for (let i=1; i<=noOfChild; i++){
                createSvg(root+'-'+i);

                let svg = document.querySelector('#svg-' + root + '-' + i);
                connectObject($(svg), $('#' + root), $('#' + root + '-' + i));

                drawArrows(root + '-' + i);
            }
        }

        function createSvg(svgId){
            $('#mySVG').clone().attr('id','svg-'+svgId).insertAfter($('#mySVG'));
        }

        function createLevel(levelNo){
            let html =
                '<ol id="level-'+ levelNo +'">' +

                '</ol>';
            $('.container').append(html);
        }

        function createNode(levelNo, childId){
            let html =

                '<li id="'+ childId +'" style="margin-right: 5px; margin-left: 5px">' +
                '<div class="level-2 rectangle" style="display:flex; flex-direction: row; justify-content: center; align-items: center">' +

                /* '<button onclick="editLevel(this)" class="btn" style="background-color: var(--level-'+ levelNo +'); padding: 0 10px 0 0; margin: 0; color: #0a0a0a">' +
                '<i class="fas fa-edit fa-3x"></i>' +
                '</button>' + */

                '<select name="'+ childId +'" class="clsSelect2" style="width: 100%">' +
                '<option value="">Select</option>' +
                desOptions +
                '</select>' +

                '<div>' +
                '<button onclick="removeChildNode(this)" data-level-no = "'+ levelNo +'" class="btn" style="background-color: var(--level-'+ levelNo +'); padding: 0 10px 0 5px; margin: 0; color: #fa0505">' +
                '<i class="fas fa-minus-circle fa-3x"></i>' +
                '</button>' +
                '<button onclick="addBtnClicked(this)" data-level-no = "'+ levelNo +'" class="btn" style="background-color: var(--level-'+ levelNo +'); padding: 0 10px 0 5px; margin: 0; color: #0a0a0a">' +
                '<i class="fas fa-plus-circle fa-3x"></i>' +
                '</button>' +
                '</div>' +

                '</div>' +
                '</li>';
            return html;
        }

        function isChildExist(parentId){
            return lcr[parentId] > 0;
        }

        function defineRepeat(){
            for (let i=1; i<50; i++){
                if (lc[i] > 0){
                    $('#level-' + (i+2)).css({"display" : "grid", "grid-template-columns" : "repeat("+ (lc[i]) +", 1fr)"});
                }
            }
        }

        function reassignId(){
            let nodes = Object.keys(lcr);
            for (let i=0; i<nodes.length; i++){
                if (lcr[nodes[i]] > 0){
                    let childes = $('#p-' + nodes[i]).first()[0].firstElementChild.children;
                    for (let j = 0, k = 1; j<childes.length; j++){
                        childes[j].id = nodes[i] + '-' + k;
                        k++;
                    }
                }
            }
        }

        function resetId(){
            lcr = [];
            let allParent = $('[id^=p-]');
            $.each(allParent, function (index, item){
                let id = item.id, flag = 0, x = '';

                for (let i = 0; i<id.length; i++){
                    if (id[i] === '-' && flag === 0){
                        flag = 1;
                        continue;
                    }
                    if (flag === 1){
                        x += id[i];
                    }
                }

                let parentId = x;
                //console.log(parentId);
                let child = item.querySelectorAll('li');

                lcr[parentId] = child.length;

                for (let i=0, j = 1; i<child.length; i++){
                    child[i].id = parentId + '-' + j;
                    child[i].querySelector('select').name = parentId + '-' + j;
                    j++;
                }
            });
        }

        function removeChildNode(node){

            let n = node.parentElement.parentElement.parentElement;
            let x = '', flag = 0, id = n.id;

            if (isChildExist(id)){
                swal({
                    icon: 'error',
                    title: 'Error..',
                    text: 'Please remove the child node first.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            for (let i = id.length-1; i>=0; i--){
                if (id[i] === '-' && flag === 0){
                    flag = 1;
                    continue;
                }
                if (flag === 1){
                    x += id[i];
                }
            }

            let parentId = x.split("").reverse().join("");

            let currLev = $(node).data('levelNo');
            lc[currLev-1]--;
            let lvRep = $('#' + (currLev-1)).children().length;

            //$('#level-'+ currLev).css({"display" : "grid", "grid-template-columns" : "repeat("+ (lc[currLev-2]) +", 1fr)"});
            defineRepeat();
            //$('#level-'+ currLev).css({"display" : "grid", "grid-template-columns" : "repeat("+ (lvRep) +", 1fr)"});
            //$('#' + n.parentElement.parentElement.id).remove();
            n.remove();
            $('#p-' + id).remove();

            for (let i=1, j=1; i<= lcr[parentId]; i++){
                let cn = document.querySelector('#'+parentId+'-'+i);
                //let pn = document.querySelector('#p-'+parentId+'-'+i);

                if (cn !== null){
                    /*cn.id = parentId + '-' + j;
                    cn.querySelector('select').name = parentId + '-' + j;*/
                    let pn = document.querySelector('#p-'+parentId+'-'+i);
                    pn.id = 'p-'+parentId+'-'+j;
                    j++;
                }
            }

            //reassignId();

            lcr[parentId]--;

            if (lcr[parentId] === 0){
                $('#p-' + parentId + ' ol').removeAttr('style');
            }
            else{
                $('#p-' + parentId + ' ol').css({"display" : "grid", "grid-template-columns" : "repeat("+ (lcr[parentId]) +", 1fr)"});
            }

            //console.log(lcr);

            /*$('[id^=svg-n]').remove();
            drawArrows('n-1');*/

            if($('#' + parentId).find('div:hidden').length > 0){
                //console.log($('#' + parentId).find('div:first-child').children().last().children().first()[0]);
                removeChildNode($('#' + parentId).find('div:first-child').children().last().children().first()[0]);
            }

            resetId();

            $('[id^=svg-n]').remove();
            drawArrows('n-1');

            console.log(lcr);
        }

    </script>
@endsection
