{{-- Education --}}
<div id="Education" class="tab-pane show">
    <div>
        <table class="table w-full table-hover table-bordered table-striped educationTable">
            <thead>
            <tr>
                <th width="13%">Exam Title</th>
                <th width="12%">Department</th>
                <th width="15%">Institute Name</th>
                <th width="12%">Board/University</th>
                <th width="10%">Result Type</th>
                <th width="10%">Result</th>
                <th width="10%">Out Of</th>
                <th width="10%">Passing Year</th>
                @if(!isset($data['viewPage']))
                    <th width="6%">Action</th>
                @endif
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            <input type="text" class="form-control round eduCls " name="edu_exam_title[]" placeholder="Enter Exam Title" style="width: 100%">
                        </div>
                    </div>
                </td>

                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            <input type="text" class="form-control round eduCls" name="edu_department[]" placeholder="Enter Department" style="width: 100%">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            <input type="text" class="form-control round eduCls" name="edu_institute_name[]" placeholder="Enter Institute Name" style="width: 100%">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            <input type="text" class="form-control round eduCls" name="edu_board[]" placeholder="Enter Board/University" style="width: 100%">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            {{--<input type="text" class="form-control round eduCls" name="edu_res_type[]" placeholder="Enter Result Type" style="width: 100%">--}}
                            <select class="form-control round clsSelect2 eduCls" name="edu_res_type[]" style="width: 100%">
                                <option value="">Select</option>
                                <option value="GPA">GPA</option>
                                <option value="Division">Division</option>
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            <input type="number" class="form-control round eduCls" name="edu_result[]" placeholder="Enter Result" style="width: 100%">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            <input type="number" class="form-control round eduCls" placeholder="4" name="edu_res_out_of[]" style="width: 100%">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="form-row align-items-center">
                        <div class="input-group">
                            <input type="number" class="form-control round eduCls" placeholder="Passing year" name="edu_passing_year[]" style="width: 100%">
                        </div>
                    </div>
                </td>
                @if(!isset($data['viewPage']))
                    <td>
                        <div class="form-row align-items-center">
                            <button onclick="addNewEducationRow()" type="button" class="btn btn-primary btn-round" style="margin-left: 28%"><i class="fas fa-plus"></i></button>
                        </div>
                    </td>
                @endif
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function (){
        let empEduData = {!! json_encode((isset($empData['empEduData'])) ? $empData['empEduData'] : null) !!};
        if (empEduData !== null){
            for (let i=0; i<empEduData.length; i++){
                if (i !== 0){
                    addNewEducationRow();
                }
                setEditData(document.querySelectorAll("[name='edu_exam_title[]']")[i], empEduData[i]['exam_title']);
                setEditData(document.querySelectorAll("[name='edu_department[]']")[i], empEduData[i]['department']);
                setEditData(document.querySelectorAll("[name='edu_institute_name[]']")[i], empEduData[i]['institute_name']);
                setEditData(document.querySelectorAll("[name='edu_board[]']")[i], empEduData[i]['board']);
                setEditData(document.querySelectorAll("[name='edu_res_type[]']")[i], empEduData[i]['res_type']);
                setEditData(document.querySelectorAll("[name='edu_result[]']")[i], empEduData[i]['result']);
                setEditData(document.querySelectorAll("[name='edu_res_out_of[]']")[i], empEduData[i]['res_out_of']);
                setEditData(document.querySelectorAll("[name='edu_passing_year[]']")[i], empEduData[i]['passing_year']);
            }
        }
    });
    function addNewEducationRow(){
        $('.clsSelect2').select2('destroy');
        let eduTable = document.querySelector('.educationTable');
        let lastRow = eduTable.rows[eduTable.rows.length-1];
        eduTable.tBodies[0].append(cleanCloneNode(lastRow.cloneNode(true)));

        if (!isViewPage){
            let actionBtn = lastRow.cells[lastRow.cells.length-1];
            const btnRem = document.createElement('td');
            btnRem.innerHTML = '<div class="form-row align-items-center"><button onclick="removeEducationRow(this.parentNode.parentNode.parentNode)" type="button" class="btn btn-danger btn-round" style="margin-left: 28%"><i class="fas fa-minus"></i></button></div>';
            actionBtn.parentNode.replaceChild(btnRem, actionBtn);
        }

        $('.clsSelect2').select2();
    }
    function removeEducationRow(element){
        element.remove();
    }


</script>
