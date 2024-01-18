<?php

namespace App\Http\Controllers\GNL;

use App\Http\Controllers\Controller;
use App\Model\GNL\Company;

use App\Model\GNL\CompanyType;
use App\Model\GNL\CompanyConfig;

use App\Model\GNL\Group;
use App\Services\CommonService as Common;
use App\Services\RoleService as Role;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;

class CompanyController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Datatable Pagination Variable
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $masterQuery = Company::where('is_delete', 0)
                ->where(function ($masterQuery) use ($search) {
                    if (!empty($search)) {
                        $masterQuery->where('comp_name', 'LIKE', "%{$search}%")
                            ->orWhere('comp_email', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('comp_code', 'DESC');

            $masterQuery = $masterQuery->offset($start)->limit($limit)->get();
            $totalData = Company::count();

            $totalFiltered = $totalData;

            $DataSet = array();
            $i = $start;
            foreach ($masterQuery as $key => $Row) {
                $sysModules = "";
                if (Common::isSuperUser() == true) {
                    if (!empty($Row->module_arr)) {

                        $sysModules = DB::table('gnl_sys_modules')
                            ->where('is_delete', 0)
                            ->whereIn('id', explode(',', $Row->module_arr))
                            ->pluck('module_name');

                        if ($sysModules) {
                            $sysModules = implode(',<br>', $sysModules->toArray());
                        }
                    }
                }

                $companyType = DB::table('gnl_dynamic_form_value')
                    ->where([['type_id', 1], ['form_id', 2]])
                    ->where('uid', $Row->company_type)
                    ->when(true, function ($query) {
                        if (Common::getDBConnection() == "sqlite") {
                            $query->selectRaw('(name || "[" || uid || "]") as name');
                        } else {
                            $query->selectRaw('concat(name, "[", uid, "]") as name');
                        }
                    })
                    ->pluck('name')
                    ->first();


                $DataSet[] = [
                    'id' => ++$i,
                    'comp_name' => $Row->comp_name,
                    'comp_email' => $Row->comp_email,
                    'comp_code' => $Row->comp_code,
                    'company_type' => $companyType,
                    'comp_logo' => (!empty($Row->comp_logo) && file_exists($Row->comp_logo)) ? '<img src="' . asset($Row->comp_logo) . '" width="100%;" onclick="imagePreview(this);">' : '',
                    'bill_logo' => (!empty($Row->bill_logo) && file_exists($Row->bill_logo)) ? '<img src="' . asset($Row->bill_logo) . '" width="100%;" onclick="imagePreview(this);">' : '',
                    'cover_image_lp' => (!empty($Row->cover_image_lp) && file_exists($Row->cover_image_lp)) ? '<img src="' . asset($Row->cover_image_lp) . '" width="100%;" onclick="imagePreview(this);">' : '',
                    'group_name' => $Row->group['group_name'],
                    'module_name' => $sysModules,
                    'action' => Role::roleWiseArray($this->GlobalRole, $Row->id),
                ];
            }
            echo json_encode([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $DataSet,
            ]);
        } else {
            return view('GNL.Company.index');
        }
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {

            $RequestData = $request->all();

            $schedule_flag = isset($RequestData['timer_option']) && $RequestData['timer_option'] == 'on' ? 1 : 0;

            if ($schedule_flag == 0) {
                $start_time = null;
                $end_time = null;
            } else {
                $start_time = $RequestData['tx_start_time'];
                $end_time = $RequestData['tx_end_time'];
            }


            if (!empty($start_time)) {
                $start_time = (new DateTime($start_time))->format('H:i');
            }

            if (!empty($end_time)) {
                $end_time = (new DateTime($end_time))->format('H:i');
            }

            if (!empty($start_time) && empty($end_time)) {
                $end_time = (new DateTime($start_time))->format('H:i');
            }

            $logo_view_lp = isset($RequestData['logo_view_lp']) && $RequestData['logo_view_lp'] == 'on' ? 1 : 0;
            $logo_view_report = isset($RequestData['logo_view_report']) && $RequestData['logo_view_report'] == 'on' ? 1 : 0;
            $logo_view_bill = isset($RequestData['logo_view_bill']) && $RequestData['logo_view_bill'] == 'on' ? 1 : 0;
            $name_view_lp = isset($RequestData['name_view_lp']) && $RequestData['name_view_lp'] == 'on' ? 1 : 0;
            $name_view_report = isset($RequestData['name_view_report']) && $RequestData['name_view_report'] == 'on' ? 1 : 0;
            $name_view_bill = isset($RequestData['name_view_bill']) && $RequestData['name_view_bill'] == 'on' ? 1 : 0;
            $br_add_view_bill = isset($Data['br_add_view_bill']) && $Data['br_add_view_bill'] == 'on' ? 1 : 0;

            $tempData = array();
            $comBasicData = [
                'group_id'       => $RequestData['group_id'],
                'comp_name'      => $RequestData['comp_name'],
                'comp_code'      => $RequestData['comp_code'],
                'comp_phone'     => $RequestData['comp_phone'],
                'comp_email'     => $RequestData['comp_email'],
                'comp_addr'      => $RequestData['comp_addr'],
                'comp_web_add'   => $RequestData['comp_web_add'],
                'company_type'   => $RequestData['company_type'],
                'comp_logo'      => null,
                'bill_logo'      => null,
                'cover_image_lp' => null,

                'logo_view_lp'          => $logo_view_lp,
                'logo_lp_width'         => $RequestData['logo_lp_width'],
                'logo_view_report'      => $logo_view_report,
                'logo_report_width'     => $RequestData['logo_report_width'],
                'logo_view_bill'        => $logo_view_bill,
                'logo_bill_width'       => $RequestData['logo_bill_width'],
                'logo_bill_width_pos'   => $RequestData['logo_bill_width_pos'],
                'name_view_lp'          => $name_view_lp,
                'name_view_report'      => $name_view_report,
                'name_view_bill'        => $name_view_bill,
                'br_add_view_bill'      => $br_add_view_bill,

                'schedule_flag'  => $schedule_flag,
                'tx_start_time'  => $start_time,
                'tx_end_time'    => $end_time,
                'applicable_for' => $RequestData['applicable_for'],

            ];

            if (Common::isSuperUser() == true && isset($RequestData['db_name'])) {
                $tempData = [
                    'db_name' => $RequestData['db_name'],
                    'host' => $RequestData['host'],
                    'username' => $RequestData['username'],
                    'password' => $RequestData['password'],
                    'port' => $RequestData['port'],
                ];
            }
            $comBasicData += $tempData;

            /*if(isset($RequestData['module_arr']) && !empty($RequestData['module_arr'])){
                $RequestData['module_arr'] = implode(',', $RequestData['module_arr']);
            }*/

            if (isset($RequestData['module_arr']) && !empty($RequestData['module_arr'])) {
                $comBasicData['module_arr'] = implode(',', $RequestData['module_arr']);
            }

            // $RequestData['comp_logo'] = null;
            // $RequestData['cover_image_lp'] = null;

            DB::beginTransaction();

            try {
                $isInsert = Company::create($comBasicData);
                $SuccessFlag = false;

                $lastInsertQuery = Company::latest()->first();
                $tableName = $lastInsertQuery->getTable();
                $pid = $lastInsertQuery->id;

                if ($isInsert) {
                    $SuccessFlag = true;

                    if (!empty($request->file('comp_logo'))) {

                        ## ## Check File validation
                        $fileInfo = Common::upload_validation($_FILES['comp_logo'], 1, 'image');

                        $uploadFile = $request->file('comp_logo');
                        $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                        $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                        ## ## File Upload Function
                        $upload = Common::fileUpload($uploadFile, $tableName, $pid);

                        $lastInsertQuery->comp_logo = $upload;
                        $isSuccess = $lastInsertQuery->update();

                        if ($isSuccess) {
                            $SuccessFlag = true;
                        } else {
                            $SuccessFlag = false;
                        }
                    }

                    if (!empty($request->file('bill_logo'))) {

                        ## ## Check File validation
                        $fileInfo = Common::upload_validation($_FILES['bill_logo'], 1, 'image');

                        $uploadFile = $request->file('bill_logo');
                        $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                        $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                        ## ## File Upload Function
                        $upload = Common::fileUpload($uploadFile, $tableName, $pid);

                        $lastInsertQuery->bill_logo = $upload;
                        $isSuccess = $lastInsertQuery->update();

                        if ($isSuccess) {
                            $SuccessFlag = true;
                        } else {
                            $SuccessFlag = false;
                        }
                    }

                    if (!empty($request->file('cover_image_lp'))) {

                        ## ## Check File validation
                        $fileInfo = Common::upload_validation($_FILES['cover_image_lp'], 1, 'image');

                        $uploadFile = $request->file('cover_image_lp');
                        $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                        $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                        ## ## File Upload Function
                        $upload = Common::fileUpload($uploadFile, $tableName, $pid);

                        $lastInsertQuery->cover_image_lp = $upload;
                        $isSuccess = $lastInsertQuery->update();

                        if ($isSuccess) {
                            $SuccessFlag = true;
                        } else {
                            $SuccessFlag = false;
                        }
                    }
                }

                DB::commit();

                $notification = array(
                    'message' => 'Successfully Inserted Company Basic',
                    'alert_type' => 'success',
                    'comId' => $pid,
                    'addedTo' => 'basic',
                );
                //return Redirect::to('gnl/company')->with($notification);
                return response()->json($notification);
            } catch (\Exception $e) {
                dd($e);
                DB::rollBack();
                $notification = array(
                    'message' => 'Unsuccessful to insert data in basic',
                    'alert_type' => 'error',
                );
                //return Redirect()->back()->with($notification);
                return response()->json($notification);
            }
        } else {

            // $CompanyType = CompanyType::where([['is_active', 1], ['is_delete', 0]])->get();
            $companyTypeList = DB::table('gnl_dynamic_form_value')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where([['type_id', 1], ['form_id', 2]])
                ->get();

            $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();

            return view('GNL.Company.add', compact('GroupData', 'companyTypeList'));
        }
    }

    public function edit(Request $request, $id = null)
    {

        $CompanyData = Company::where('id', $id)->first();
        $tableName = $CompanyData->getTable();
        $pid = $id;

        $companyTypeList = DB::table('gnl_dynamic_form_value')
            ->where([['is_active', 1], ['is_delete', 0]])
            ->where([['type_id', 1], ['form_id', 2]])
            ->get();

        // dd($CompanyData, $companyTypeList);

        if ($request->isMethod('post')) {

            $Data = $request->all();

            if (isset($Data['timer_option']) && $Data['timer_option'] == 'on') {
                $schedule_flag = 1;
                $start_time = $Data['tx_start_time'];
                $end_time = $Data['tx_end_time'];
            } else {
                $schedule_flag = 0;
                $start_time = null;
                $end_time = null;
            }

            if (!empty($start_time)) {
                $start_time = (new DateTime($start_time))->format('H:i');
            }

            if (!empty($end_time)) {
                $end_time = (new DateTime($end_time))->format('H:i');
            }

            if (!empty($start_time) && empty($end_time)) {
                $end_time = (new DateTime($start_time))->format('H:i');
            }

            $logo_view_lp = isset($Data['logo_view_lp']) && $Data['logo_view_lp'] == 'on' ? 1 : 0;
            $logo_view_report = isset($Data['logo_view_report']) && $Data['logo_view_report'] == 'on' ? 1 : 0;
            $logo_view_bill = isset($Data['logo_view_bill']) && $Data['logo_view_bill'] == 'on' ? 1 : 0;
            $name_view_lp = isset($Data['name_view_lp']) && $Data['name_view_lp'] == 'on' ? 1 : 0;
            $name_view_report = isset($Data['name_view_report']) && $Data['name_view_report'] == 'on' ? 1 : 0;
            $name_view_bill = isset($Data['name_view_bill']) && $Data['name_view_bill'] == 'on' ? 1 : 0;
            $br_add_view_bill = isset($Data['br_add_view_bill']) && $Data['br_add_view_bill'] == 'on' ? 1 : 0;


            $tempData = array();
            $comBasicData = [
                'group_id'       => $Data['group_id'],
                'comp_name'      => $Data['comp_name'],
                'comp_code'      => $Data['comp_code'],
                'comp_phone'     => $Data['comp_phone'],
                'comp_email'     => $Data['comp_email'],
                'comp_addr'      => $Data['comp_addr'],
                'comp_web_add'   => $Data['comp_web_add'],
                'company_type'   => $Data['company_type'],

                'logo_view_lp'          => $logo_view_lp,
                'logo_lp_width'         => $Data['logo_lp_width'],
                'logo_view_report'      => $logo_view_report,
                'logo_report_width'     => $Data['logo_report_width'],
                'logo_bill_width_pos'   => $Data['logo_bill_width_pos'],
                'logo_view_bill'        => $logo_view_bill,
                'logo_bill_width'       => $Data['logo_bill_width'],
                'name_view_lp'          => $name_view_lp,
                'name_view_report'      => $name_view_report,
                'name_view_bill'        => $name_view_bill,
                'br_add_view_bill'      => $br_add_view_bill,

                'schedule_flag'  => $schedule_flag,
                'tx_start_time'  => $start_time,
                'tx_end_time'    => $end_time,
                'applicable_for' => $Data['applicable_for'],
            ];

            if (Common::isSuperUser() == true && isset($Data['db_name'])) {
                $tempData = [
                    'db_name' => $Data['db_name'],
                    'host' => $Data['host'],
                    'username' => $Data['username'],
                    'password' => $Data['password'],
                    'port' => $Data['port'],
                ];
            }
            $comBasicData += $tempData;

            if (isset($Data['module_arr']) && !empty($Data['module_arr'])) {
                $comBasicData['module_arr'] = implode(',', $Data['module_arr']);
            }

            if (!empty($request->file('comp_logo'))) {
                ## ## Check File validation

                $fileInfo = Common::upload_validation($_FILES['comp_logo'], 1, 'image');

                $uploadFile = $request->file('comp_logo');
                $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                ## ## File Upload Function
                $upload = Common::fileUpload($uploadFile, $tableName, $pid);
                $comBasicData['comp_logo'] = $upload;
            }

            if (!empty($request->file('bill_logo'))) {
                ## ## Check File validation

                $fileInfo = Common::upload_validation($_FILES['bill_logo'], 1, 'image');

                $uploadFile = $request->file('bill_logo');
                $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                ## ## File Upload Function
                $upload = Common::fileUpload($uploadFile, $tableName, $pid);
                $comBasicData['bill_logo'] = $upload;
            }

            if (!empty($request->file('cover_image_lp'))) {
                ## ## Check File validation

                $fileInfo = Common::upload_validation($_FILES['cover_image_lp'], 1, 'image');

                $uploadFile = $request->file('cover_image_lp');
                $FileType = (isset($fileInfo['filetype'])) ? $fileInfo['filetype'] : null;
                $FileSize = (isset($fileInfo['filesize'])) ? $fileInfo['filesize'] : 0;

                ## ## File Upload Function
                $upload = Common::fileUpload($uploadFile, $tableName, $pid);
                $comBasicData['cover_image_lp'] = $upload;
            }

            if(empty($Data['file_upload_edit']) && empty($request->file('comp_logo'))){
                $comBasicData['comp_logo'] = null;
            }

            if(empty($Data['file_upload_edit']) && empty($request->file('bill_logo'))){
                $comBasicData['bill_logo'] = null;
            }

            if(empty($Data['cover_image_edit']) && empty($request->file('cover_image_lp'))){
                $comBasicData['cover_image_lp'] = null;
            }

            try {
                $isUpdate = $CompanyData->update($comBasicData);


                $notification = array(
                    'message' => 'Successfully Updated Basic Data',
                    'alert_type' => 'success',
                    'comId' => $pid,
                    'addedTo' => 'basic',
                );
                //return Redirect::to('gnl/company')->with($notification);
                return response()->json($notification);
            } catch (\Exception $e) {
                $notification = array(
                    'message' => 'Unsuccessful to Update Configuration Data',
                    'alert-type' => 'error',
                );
                return response()->json($notification);
            }
        } else {

            $CompanyData = Company::where('id', $id)->first();
            $comConfigData = CompanyConfig::where('company_id', $id)->get();

            $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
            return view('GNL.Company.edit', compact('CompanyData', 'GroupData', 'companyTypeList'));
        }
    }

    public function view($id = null)
    {
        $companyTypeList = DB::table('gnl_dynamic_form_value')
            ->where([['is_active', 1], ['is_delete', 0]])
            ->where([['type_id', 1], ['form_id', 2]])
            ->get();

        $CompanyData = Company::where('id', $id)->first();
        $GroupData = Group::where('is_delete', 0)->orderBy('id', 'DESC')->get();
        return view('GNL.Company.view', compact('CompanyData', 'GroupData', 'companyTypeList'));
    }

    public function delete($id = null)
    {
        $CompanyData = Company::where('id', $id)->first();
        $CompanyData->is_delete = 1;

        $delete = $CompanyData->save();

        if ($delete) {
            $notification = array(
                'message' => 'Successfully Deleted',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Delete',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function isActive($id = null)
    {
        $CompanyData = Company::where('id', $id)->first();

        if ($CompanyData->is_active == 1) {
            $CompanyData->is_active = 0;
        } else {
            $CompanyData->is_active = 1;
        }

        $Status = $CompanyData->save();

        if ($Status) {
            $notification = array(
                'message' => 'Successfully Updated',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Update',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

    public function get_modules()
    {
        $module = DB::table('gnl_sys_modules')->where([['is_active', 1], ['is_delete', 0]])->get();
        return view('GNL.CompanyConfig.module', compact('module'));
    }


    public function get_form(Request $request, $moduleId)
    {
        $CompanyID = Common::getCompanyId();
        $pid = $CompanyID;
        $module = DB::table('gnl_sys_modules')->where([['is_active', 1], ['is_delete', 0]])->where('id', $moduleId)->first();
        if ($request->isMethod('post')) {

            $Data = $request->all();

            DB::beginTransaction();
            try {
                CompanyConfig::where('company_id', $pid)->where('module_id', $moduleId)->get()->each->delete();

                foreach ($Data as $key => $value) {
                    if (preg_match('/dynamic_form_/', $key)) {

                        $key = str_replace("_", ".", $key);
                        $formId = "";
                        for ($i = 13; $i < strlen($key); $i++) {
                            $formId .= $key[$i];
                        }

                        if (is_array($value)) {
                            foreach ($value as $index => $row) {
                                CompanyConfig::create(['company_id' => $pid, 'module_id' => $moduleId, 'form_id' => $formId, 'form_value' => $row]);
                            }
                        } else {
                            CompanyConfig::create(['company_id' => $pid, 'module_id' => $moduleId, 'form_id' => $formId, 'form_value' => $value]);
                        }
                    }
                }


                DB::commit();
                $notification = array(
                    'message' => 'Successfully Update Configuration Data',
                    'alert_type' => 'success',
                );
                return response()->json($notification);
            } catch (\Exception $e) {
                dd($e);
                DB::rollBack();
                $notification = array(
                    'message' => 'Unsuccessful to update data in company configuration',
                    'alert_type' => 'error',
                );
                return response()->json($notification);
            }
        }

        $CompanyData = Company::where('id', $CompanyID)->first();
        $comConfigData = CompanyConfig::where('company_id', $CompanyID)->where('module_id', $moduleId)->get();

        // $formId = $comConfigData->pluck('form_value' , 'form_id');

        $dFormRows = DB::table('gnl_dynamic_form')
            ->where([['type_id', 1], ['is_delete', 0], ['is_active', 1]])
            ->where('module_id', $moduleId)
            ->orderBy('order_by')
            ->get()
            ->toArray();

        $formRowValue = DB::table('gnl_dynamic_form_value')
            ->where([['type_id', 1], ['is_delete', 0], ['is_active', 1]])
            ->orderBy('order_by')->get();

        foreach ($dFormRows as $key => $row) {
            $formIdData = $comConfigData->where('form_id', $row->uid);

            $dFormRows[$key] = (array) $row;
            $dFormRows[$key]['form_values'] = $formRowValue->whereIn('form_id', $dFormRows[$key]['uid'])->toArray();

            if (!empty($formIdData)) {
                if ($row->input_type == "checkbox") {
                    $dFormRows[$key]['pre_values'] = $formIdData->pluck('form_value')->toArray();
                } else {
                    $dFormRows[$key]['pre_value'] = !empty($formIdData->first()) ? $formIdData->first()->form_value : '';
                }
            } else {
                $dFormRows[$key]['pre_value'] = "";
            }
        }

        return view('GNL.CompanyConfig.edit', compact('CompanyData', 'dFormRows', 'moduleId', 'module'));
    }
}
