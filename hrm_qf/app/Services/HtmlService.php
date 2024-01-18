<?php

namespace App\Services;

use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use App\Services\MfnService as MFN;
use App\Services\AccService as ACCS;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Services\CommonService as Common;

class HtmlService
{

    public static function getModuleId($id = null)
    {
        return $smoduleID = Session::get('ModuleID');
    }

    public static function forLedgerSelectFeild($value = null, $FeildName = 'code_arr[]', $FeildID = '', $DisableFeild = '', $group_head = 0)
    {
        ## deafult group head 0 ... load all leaf node
        ## if want all ledger group head would be false (false load all ledger)
        ## group head 1 would load only group head ledger options


        // dd($group_head);

        $html = '';

        $branchId      = Common::getBranchId();
        $projectId     = Common::getProjectId($branchId);
        $projectTypeId = Common::getProjectTypeId($branchId);

        $QuerryData = ACCS::getLedgerData([
            'branchId'      => $branchId,
            'projectId'     => $projectId,
            'projectTypeId' => $projectTypeId,
            'isActive'      => false,
            'groupHead'     => $group_head,
        ]);


        // dd($QuerryData->where('id',292));

        // dd($value);
        // $html .= '<div class="input-group" style="width:100%;">';
        $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '"  id="' . $FeildID . '" ' . $DisableFeild . '>';

        $html .= '<option value="">Select Code</option>';

        foreach ($QuerryData as $Row) {
            $selectTxt = '';
            if ($value != null) {
                if ($Row->id == $value) {
                    $selectTxt = "selected";
                }
            }
            $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' code="' . $Row->code . '">' . $Row->name . " [" . $Row->code . ']</option>';
        }

        $html .= '</select>';
        // $html .= '</div>';

        return $html;
    }

    public static function forCompanyFeild($value = null, $disableText = '', $SelectBox = false, $FeildName = 'company_id', $FeildID = 'company_id')
    {

        $html      = '';
        $CompanyID = Common::getCompanyId();
        // $CompanyID = 0 ;

        if ($CompanyID == 0 && $SelectBox == true) {

            $CompanyModel = 'App\\Model\\GNL\\Company';
            $CompanyData  = $CompanyModel::where('is_delete', 0)->orderBy('comp_code', 'ASC')->get();

            $html .= '<div class="form-row align-items-center">';
            $html .= '<label class="col-md-3 input-title">Company</label>';
            $html .= '<div class="col-md-5 form-group">';
            $html .= '<div class="input-group">';
            $html .= '<select class="form-control selCompanyCls clsSelect2"  name="' . $FeildName . '"  id="' . $FeildID . '" ' . $disableText . '>';

            $html .= '<option value="">Select Company</option>';

            foreach ($CompanyData as $Row) {
                $selectTxt = '';
                if ($value != null) {
                    if ($Row->id == $value) {
                        $selectTxt = "selected";
                    }
                }
                // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' >' . sprintf("%04d", $Row->comp_code) . " - " . $Row->comp_name . '</option>';
                $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' >' . $Row->comp_name . ' [' . $Row->comp_code . ']</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        } else {
            if (!empty($value)) {
                $CompanyID = $value;
            }
            $html .= '<input type="hidden"  name="' . $FeildName . '"  id="' . $FeildID . '" value="' . $CompanyID . '">';
        }

        return $html;
    }

    /**
     * @param selectBoxShow // Select Option Show or Hide
     * @param elementTitle // Field Title for Select Box
     * @param elementName // elementName is name attribute of Select Box
     * @param elementId // elementId is id attribute of Select Box
     * @param elementValue // elementValue is value of Select Box
     * @param ignoreHO // ignoreHO is do Head Office ignore, yes or not of Select Box
     * @param allOption // allOption is All Option load and Selectable  of Select Box
     * @param isRequired // isRequired is required attribute of Select Box
     * @param isDisabled // isDisabled is required attribute of Select Box
     * @param divClass // divClass is Parant div class like 'col-sm-4 form-group' of Select Box
     * @param formStyle // formStyle is vertical(up & down) or horizontal (side by side) of Select Box
     * @param titleClass // titleClass this is class of Select Box title
     * @param innerDivClass // innerDivClass this is inner div class of Select Box title
     * @param forBranchFeildTTL // forBranchFeildTTL this function Create select box input field for Branch
     */

    public static function forBranchFeildTTL($parameter = [])
    {
        $selectBoxShow = isset($parameter['selectBoxShow']) ? $parameter['selectBoxShow'] : false;

        $elementTitle = isset($parameter['elementTitle']) ? $parameter['elementTitle'] : "Branch";
        $elementName = isset($parameter['elementName']) ? $parameter['elementName'] : 'branch_id';
        $elementId = isset($parameter['elementId']) ? $parameter['elementId'] : 'branch_id';
        $elementValue = isset($parameter['elementValue']) ? $parameter['elementValue'] : null;

        $ignoreHO = isset($parameter['ignoreHO']) ? $parameter['ignoreHO'] : false;
        $allOption = isset($parameter['allOption']) ? $parameter['allOption'] : false;
        $allBranchs = isset($parameter['allBranchs']) ? $parameter['allBranchs'] : false;

        $transferToLoadFromBranch = isset($parameter['transferToLoadFromBranch']) ? $parameter['transferToLoadFromBranch'] : false;

        $isRequired = isset($parameter['isRequired']) ? $parameter['isRequired'] : true;
        $isDisabled = isset($parameter['isDisabled']) ? $parameter['isDisabled'] : false;

        $divClass = isset($parameter['divClass']) ? $parameter['divClass'] : "";

        ## formStyle =  vertical(up & down) or horizontal (side by side)
        $formStyle = isset($parameter['formStyle']) ? $parameter['formStyle'] : "horizontal";
        $titleClass = isset($parameter['titleClass']) ? $parameter['titleClass'] : "col-md-3";
        $innerDivClass = isset($parameter['innerDivClass']) ? $parameter['innerDivClass'] : "col-md-5";

        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        $html       = '';
        $branchId   = Common::getBranchId();

        if ($transferToLoadFromBranch) {
            $branch_arr = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                ->where('id', '<>', $branchId)
                ->pluck('id')
                ->toArray();
        } else {
            $branch_arr = HRS::getUserAccesableBranchIds();
        }

        if($allBranchs == true){
            $branch_arr = Common::getBranchIdsForAllSection(['branchId'=> -3]);
        }

        // || $branchId == 1
        if ((count($branch_arr) > 1) && $selectBoxShow == true) {

            $branchData = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                ->whereIn('id', $branch_arr)
                ->where(function ($query) use ($ignoreHO) {
                    if ($ignoreHO) {
                        $query->where('id', '<>', 1);
                    }
                })
                ->selectRaw('id, branch_name, branch_code')
                ->orderBy('branch_code', 'ASC')
                ->get();



            if ($formStyle == "horizontal") {
                ## form-row align-items-center
                if (empty($divClass)) {
                    $divClass = "form-row align-items-center";
                }

                $html .= '<div class="' . $divClass . '">';

                ## col-md-3
                if ($isRequired == true) {
                    $html .= '<label class="' . $titleClass . ' input-title RequiredStar">' . $elementTitle . '</label>';
                } else {
                    $html .= '<label class="' . $titleClass . ' input-title">' . $elementTitle . '</label>';
                }

                ## col-md-5
                $html .= '<div class="' . $innerDivClass . ' form-group">';
                $html .= '<div class="input-group">';
            } else {
                ## form-row align-items-center
                $html .= '<div class="' . $divClass . '">';

                if ($isRequired == true) {
                    $html .= '<label class="input-title RequiredStar">' . $elementTitle . '</label>';
                } else {
                    $html .= '<label class="input-title">' . $elementTitle . '</label>';
                }

                $html .= '<div class="input-group">';
            }

            ######## Common Section ########

            ## select html tag start
            if ("select_html_tag") {
                $html .= '<select class="form-control clsSelect2" style="width: 100%" ';
                $html .= 'name="' . $elementName . '" ';
                $html .= 'id="' . $elementId . '" ';

                if ($isRequired == true) {
                    $html .= ' required ';
                }

                if ($isDisabled == true) {
                    $html .= ' disabled ';
                }
                $html .= '>';
            }
            ## select html tag end

            // dd($html);

            if ("select_option_default") {
                if ($allOption == true) {
                    $sField = "All";
                    $sValue = '0';
                } else {
                    $sField = "Select One";
                    $sValue = '';
                }
                $html .= '<option value="' . $sValue . '">' . $sField . '</option>';
            }

            if ("select_options_load") {
                foreach ($branchData as $row) {
                    $selectTxt = '';
                    if ($elementValue != null) {
                        if ($row->id == $elementValue) {
                            $selectTxt = "selected";
                        }
                    }

                    $html .= '<option value="' . $row->id . '" ' . $selectTxt . ' >';
                    $html .= $row->branch_name . ' [' . $row->branch_code . ']';
                    $html .= '</option>';
                }
            }
            $html .= '</select>';

            ######### End ###########

            if ($formStyle == "horizontal") {
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            } else {
                $html .= '</div>';
                $html .= '</div>';
            }
        } else {
            if (!empty($elementValue)) {
                $branchId = $elementValue;
            }
            $html .= '<input type="hidden" name="' . $elementName . '" id="' . $elementId . '" value="' . $branchId . '">';
        }

        return $html;
    }

    ## backup code delete in 13/03/2023. deleted functions are forBranchFeildNewRRRR(), BackupforBranchFeildNew()

    public static function forBranchFeildNew(
        $SelectBox = false,
        $FeildName = 'branch_id',
        $FeildID = 'branch_id',
        $SelectedValue = null,
        $DisableFeild = '',
        $Title = 'Branch',
        $IgnoreHO = false,
        $TransferToLoadFromBranch = false,
        $isRequired = true,
        $allOption = false
    ) {

        return self::forBranchFeildTTL([
            'selectBoxShow' => $SelectBox,
            'elementName' => $FeildName,
            'elementId' => $FeildID,
            'elementValue' => $SelectedValue,
            'isDisabled' => empty($DisableFeild) ? false : true,
            'elementTitle' => $Title,
            'ignoreHO' => $IgnoreHO,
            'transferToLoadFromBranch' => $TransferToLoadFromBranch,
            'isRequired' => $isRequired,
            'allOption' => $allOption,
            'formStyle' => 'vertical'
        ]);
    }

    public static function forBranchFeild(
        $SelectBox = false,
        $FeildName = 'branch_id',
        $FeildID = 'branch_id',
        $SelectedValue = null,
        $DisableFeild = '',
        $Title = 'Branch',
        $IgnoreHO = false
    ) {

        return self::forBranchFeildTTL([
            'selectBoxShow' => $SelectBox,
            'elementName' => $FeildName,
            'elementId' => $FeildID,
            'elementValue' => $SelectedValue,
            'isDisabled' => empty($DisableFeild) ? false : true,
            'elementTitle' => $Title,
            'ignoreHO' => $IgnoreHO
        ]);
    }

    public static function forBranchFeildNew_backup(
        $SelectBox = false,
        $FeildName = 'branch_id',
        $FeildID = 'branch_id',
        $SelectedValue = null,
        $DisableFeild = '',
        $Title = 'Branch',
        $IgnoreHO = false,
        $TransferToLoadFromBranch = false,
        $isRequired = true,
        $allOption = false
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        if ($allOption == true) {
            $sField = "All";
            $sValue = '0';
        } else {
            $sField = "Select Branch";
            $sValue = '';
        }

        $html       = '';
        $BranchID   = Common::getBranchId();

        if ($TransferToLoadFromBranch) {

            $branch_arr = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                ->where([['id', '<>', $BranchID], ['id', '<>', 1]])
                ->pluck('id')
                ->toArray();
        } else {
            $branch_arr = HRS::getUserAccesableBranchIds();
        }

        if ((count($branch_arr) > 1 || $BranchID == 1) && $SelectBox == true) {

            // if ($BranchID == 1 && $SelectBox == true) {
            if ($IgnoreHO) {
                $BranchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                    ->where('id', '<>', 1)
                    ->whereIn('id', $branch_arr)
                    ->orderBy('branch_code', 'ASC')->get();
            } else {
                $BranchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                    ->whereIn('id', $branch_arr)
                    ->orderBy('branch_code', 'ASC')->get();
            }

            if (isset($isRequired) && $isRequired == true) {
                $html .= '<label class="input-title RequiredStar">' . $Title . '</label><div class="input-group">';
                $html .= '<select required class="form-control clsSelect2" style="width: 100%" name="' . $FeildName . '" id="' . $FeildID . '" ' . $DisableFeild . ' >';
            } else {
                $html .= '<label class="input-title">' . $Title . '</label><div class="input-group">';
                $html .= '<select class="form-control clsSelect2" style="width: 100%" name="' . $FeildName . '" id="' . $FeildID . '" ' . $DisableFeild . '>';
            }

            // if ($IgnoreHO) {
            //     $html .= '<option value="">Select One</option>';
            // }
            $html .= '<option value="' . $sValue . '">' . $sField . '</option>';

            foreach ($BranchData as $Row) {
                $selectTxt = '';
                if ($SelectedValue != null) {
                    if ($Row->id == $SelectedValue) {
                        $selectTxt = "selected";
                    }
                }
                // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' >' . sprintf("%04d", $Row->branch_code) . " - " . $Row->branch_name . '</option>';
                $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' >' . $Row->branch_name . ' [' . $Row->branch_code . ']</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
            $html .= ' <div class="help-block with-errors is-invalid"></div>';
        } else {
            if (!empty($SelectedValue)) {
                $BranchID = $SelectedValue;
            }
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $BranchID . '">';
        }

        return $html;
    }

    public static function forBranchFeild_backup(
        $SelectBox = false,
        $FeildName = 'branch_id',
        $FeildID = 'branch_id',
        $SelectedValue = null,
        $DisableFeild = '',
        $Title = 'Branch',
        $IgnoreHO = false
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */

        $html       = '';
        $BranchID   = Common::getBranchId();
        $branch_arr = HRS::getUserAccesableBranchIds();

        if ((count($branch_arr) > 1 || $BranchID == 1) && $SelectBox == true) {

            // if ($BranchID == 1 && $SelectBox == true) {

            if ($IgnoreHO) {
                $BranchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                    ->where('id', '<>', 1)
                    ->whereIn('id', $branch_arr)
                    ->orderBy('branch_code', 'ASC')->get();
            } else {
                $BranchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                    ->whereIn('id', $branch_arr)
                    ->orderBy('branch_code', 'ASC')->get();
            }

            // dd($BranchData);
            $html .= '<div class="form-row align-items-center">';
            $html .= '<label class="col-md-3 input-title RequiredStar">' . $Title . '</label>';
            $html .= '<div class="col-md-5 form-group">';
            $html .= '<div class="input-group">';

            $html .= '<select class="form-control clsSelect2" required name="' . $FeildName . '"  id="' . $FeildID . '" ' . $DisableFeild . '>';

            if ($IgnoreHO) {
                $html .= '<option value="">Select One</option>';
            }

            foreach ($BranchData as $Row) {
                $selectTxt = '';
                if ($SelectedValue != null) {
                    if ($Row->id == $SelectedValue) {
                        $selectTxt = "selected";
                    }
                }
                // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' >' . sprintf("%04d", $Row->branch_code) . " - " . $Row->branch_name . '</option>';
                $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' >' . $Row->branch_name . ' [' . $Row->branch_code . ']</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        } else {
            if (!empty($SelectedValue)) {
                $BranchID = $SelectedValue;
            }
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $BranchID . '">';
        }

        return $html;
    }

    public static function forZoneFeildSearch(
        $option = null,
        $FeildName = 'zone_id',
        $FeildID = 'zone_id',
        $Title = 'Zone',
        $SelectedValue = null,
        $isRequired = null
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        $html       = '';
        // $BranchID   = Common::getBranchId();
        $branchId   = Common::getBranchId();
        $branch_arr = HRS::getUserAccesableBranchIds();

        $accessArr = DB::table('gnl_branchs')
            ->where([['is_delete', 0], ['is_approve', 1]])
            ->where(function ($query) use ($branchId, $branch_arr) {
                if (!empty($branchId) && $branchId != 1) {
                    $query->whereIn('id', [$branchId]);
                    $query->orWhereIn('id', $branch_arr);
                }
            })
            ->pluck('zone_id')
            ->unique()
            ->toArray();

        $zoneWiseQuery = DB::table('gnl_zones')
            ->where([['is_delete', 0]])
            ->whereIn('id', $accessArr)
            ->orderBy('zone_code', 'ASC')
            ->get();

        // dd($zoneWiseQuery);

        if (count($zoneWiseQuery->toArray()) > 0) {

            // || Common::getBranchId() == 1
            if (count($zoneWiseQuery->toArray()) > 1 || Common::getBranchId() == 1) {

                $html .= '<div class="col-md-2">';

                if ($isRequired) {
                    $html .= '<label class="input-title RequiredStar">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="fnAjaxGetRegion(); fnAjaxGetArea(); fnAjaxGetBranch();">';
                } else {
                    $html .= '<label class="input-title">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="fnAjaxGetRegion(); fnAjaxGetArea(); fnAjaxGetBranch();">';
                }

                if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                    $html .= '<option value="">All</option>';
                }
                if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                    $html .= '<option value="">Select One</option>';
                }

                foreach ($zoneWiseQuery as $Row) {
                    $selectTxt = '';
                    if ($SelectedValue != null) {
                        if ($Row->id == $SelectedValue) {
                            $selectTxt = "selected";
                        }
                    }
                    // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . sprintf("%04d", $Row->zone_code) . ' - ' . $Row->zone_name . '</option>';
                    $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->zone_name . ' [' . $Row->zone_code . ']</option>';
                }

                $html .= '</select>';
                $html .= '</div></div>';
            } else {
                $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $zoneWiseQuery->toArray()[0]->id . '">';
            }
        } else {
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="">';
        }

        return $html;
    }

    public static function forRegionFeildSearch(
        $option = null,
        $FeildName = 'region_id',
        $FeildID = 'region_id',
        $Title = 'Region',
        $SelectedValue = null,
        $isRequired = null
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        $html       = '';
        // $BranchID   = Common::getBranchId();
        $branchId   = Common::getBranchId();
        $branch_arr = HRS::getUserAccesableBranchIds();

        $accessArr = DB::table('gnl_branchs')
            ->where([['is_delete', 0], ['is_approve', 1]])
            ->where(function ($query) use ($branchId, $branch_arr) {
                if (!empty($branchId) && $branchId != 1) {
                    $query->whereIn('id', [$branchId]);
                    $query->orWhereIn('id', $branch_arr);
                }
            })
            ->pluck('region_id')
            ->unique()
            ->toArray();

        $regionWiseQuery = DB::table('gnl_regions')
            ->where([['is_delete', 0]])
            ->whereIn('id', $accessArr)
            ->orderBy('region_code', 'ASC')
            ->get();

        if (count($regionWiseQuery->toArray()) > 0) {

            // || Common::getBranchId() == 1
            if (count($regionWiseQuery->toArray()) > 1 || Common::getBranchId() == 1) {

                $html .= '<div class="col-md-2">';

                if ($isRequired) {
                    $html .= '<label class="input-title RequiredStar">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="fnAjaxGetArea(); fnAjaxGetBranch();">';
                } else {
                    $html .= '<label class="input-title">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="fnAjaxGetArea(); fnAjaxGetBranch();">';
                }

                if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                    $html .= '<option value="">All</option>';
                }
                if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                    $html .= '<option value="">Select One</option>';
                }

                foreach ($regionWiseQuery as $Row) {
                    $selectTxt = '';
                    if ($SelectedValue != null) {
                        if ($Row->id == $SelectedValue) {
                            $selectTxt = "selected";
                        }
                    }

                    $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->region_name . ' [' . $Row->region_code . ']</option>';
                }

                $html .= '</select>';
                $html .= '</div></div>';
            } else {
                $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $regionWiseQuery->toArray()[0]->id . '">';
            }
        } else {
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="">';
        }
        return $html;
    }

    public static function forAreaFeildSearch(
        $option = null,
        $FeildName = 'area_id',
        $FeildID = 'area_id',
        $Title = 'Area',
        $SelectedValue = null,
        $isRequired = null,
        $isMultiple = null
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        $html       = '';
        $branchId   = Common::getBranchId();
        $branch_arr = HRS::getUserAccesableBranchIds();

        $accessArr = DB::table('gnl_branchs')
            ->where([['is_delete', 0], ['is_approve', 1]])
            ->where(function ($query) use ($branchId, $branch_arr) {
                if (!empty($branchId) && $branchId != 1) {
                    $query->whereIn('id', [$branchId]);
                    $query->orWhereIn('id', $branch_arr);
                }
            })
            ->pluck('area_id')
            ->unique()
            ->toArray();


        $areaWiseQuery = DB::table('gnl_areas')
            ->where([['is_delete', 0]])
            ->whereIn('id', $accessArr)
            ->orderBy('area_code', 'ASC')
            ->get();

        if (count($areaWiseQuery->toArray()) > 0) {

            // || Common::getBranchId() == 1
            if (count($areaWiseQuery->toArray()) > 1 || Common::getBranchId() == 1) {

                $html .= '<div class="col-md-2">';

                if ($isRequired) {
                    $html .= '<label class="input-title RequiredStar">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select ' . ($isMultiple ? 'multiple' : '') . ' required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="fnAjaxGetBranch();">';
                } else {
                    $html .= '<label class="input-title">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select ' . ($isMultiple ? 'multiple' : '') . ' class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="fnAjaxGetBranch();">';
                }

                if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                    $html .= '<option value="">All</option>';
                }
                if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                    $html .= '<option value="">Select One</option>';
                }

                if ($option == 'multiple' || $option == 'Multiple' || $option == 'MULTIPLE') {
                    $html .= '<option value="">Select One/Multiple</option>';
                }

                foreach ($areaWiseQuery as $Row) {
                    $selectTxt = '';
                    if ($SelectedValue != null) {
                        if ($Row->id == $SelectedValue) {
                            $selectTxt = "selected";
                        }
                    }
                    // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->area_name . '</option>';
                    // $html .= '<option value="' . $Row->id . '">' . sprintf("%04d", $Row->area_code) . ' - ' . $Row->area_name . '</option>';
                    $html .= '<option value="' . $Row->id . '">' . $Row->area_name . ' [' . $Row->area_code . ']</option>';
                }

                $html .= '</select>';
                $html .= '</div></div>';
            } else {
                $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $areaWiseQuery->toArray()[0]->id . '">';
            }
        } else {
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="">';
        }
        return $html;
    }

    public static function forBranchFeildSearch(
        $option = null,
        $FeildName = 'branch_id',
        $FeildID = 'branch_id',
        $Title = 'Branch',
        $SelectedValue = null,
        $IgnoreHO = false
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        $html       = '';
        $BranchID   = Common::getBranchId();
        $branch_arr = HRS::getUserAccesableBranchIds();

        if (count($branch_arr) > 1 || $BranchID == 1) {

            $BranchModel = 'App\\Model\\GNL\\Branch';

            if ($IgnoreHO) {
                $BranchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_approve', 1]])
                    ->where('id', '<>', 1)
                    ->whereIn('id', $branch_arr)
                    ->orderBy('branch_code', 'ASC')->get();
            } else {
                $BranchData = DB::table('gnl_branchs')
                    ->where([['is_delete', 0], ['is_approve', 1]])
                    ->whereIn('id', $branch_arr)
                    ->orderBy('branch_code', 'ASC')->get();
            }
            // $BranchData = $BranchModel::where(['is_delete' => 0])
            //     ->whereIn('id', HRS::getUserAccesableBranchIds())
            //     ->orderBy('branch_code', 'ASC')->get();

            $html .= '<div class="col-md-2">';
            $html .= '<label class="input-title">' . $Title . '</label>';

            $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';

            if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                $html .= '<option value="">All</option>';
            }
            if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                $html .= '<option value="">Select One</option>';
            }

            foreach ($BranchData as $Row) {
                $selectTxt = '';
                if ($SelectedValue != null) {
                    if ($Row->id == $SelectedValue) {
                        $selectTxt = "selected";
                    }
                }
                // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . sprintf("%04d", $Row->branch_code) . " - " . $Row->branch_name . '</option>';
                $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->branch_name . ' [' . $Row->branch_code . ']</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
        } else {
            $html .= '<input type="hidden" name="branch_id" id="branch_id" value="' . $BranchID . '">';
        }

        return $html;
    }

    public static function forBranchFeildSearch_new(
        $option = null,
        $FeildName = 'branch_id',
        $FeildID = 'branch_id',
        $Title = 'Branch',
        $SelectedValue = null,
        $isRequired = null,
        $withHeadOffice = true
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        $html       = '';
        $BranchID   = Common::getBranchId();
        $branch_arr = HRS::getUserAccesableBranchIds();

        $accesableBranchIds = HRS::getUserAccesableBranchIds();

        if (count($accesableBranchIds) > 1) {

            $BranchModel = 'App\\Model\\GNL\\Branch';
            // $BranchData = $BranchModel::where([['is_delete', 0], ['id', '!=', 1]])
            //     ->whereIn('id', $accesableBranchIds)
            //     ->orderBy('branch_code', 'ASC')
            //     ->get();

            $BranchData = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_approve', 1]]);
            if ($withHeadOffice == false) {
                $BranchData->where('id', '!=', 1);
            }
            $BranchData = $BranchData->whereIn('id', $accesableBranchIds)
                ->orderBy('branch_code', 'ASC')
                ->get();

            $html .= '<div class="col-md-2">';
                if (isset($isRequired) && $isRequired == true) {
                    $html .= '<label class="input-title RequiredStar">' . $Title . '</label><div class="input-group">';
                    $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';
                } else {
                    $html .= '<label class="input-title">' . $Title . '</label><div class="input-group">';
                    $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';
                }

                if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                    $html .= '<option value="">All</option>';
                }

                if ($option == 'permitted branch' || $option == 'Permitted Branch') {
                    $html .= '<option value="">Permitted Branch</option>';
                }

                if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                    $html .= '<option value="">Select One</option>';
                }

                foreach ($BranchData as $Row) {
                    $selectTxt = '';
                    if ($SelectedValue != null) {
                        if ($Row->id == $SelectedValue) {
                            $selectTxt = "selected";
                        }
                    }
                    // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . sprintf("%04d", $Row->branch_code) . " - " . $Row->branch_name . '</option>';
                    $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->branch_name . ' [' . $Row->branch_code . ']</option>';
                }

                $html .= '</select>';
            $html .= '</div></div>';
            // $html .= '</div>';
        } else {
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $BranchID . '">';
        }

        return $html;
    }

    public static function forBranchToFeildSearch_new(
        $option = null,
        $FeildName = 'branch_id',
        $FeildID = 'branch_id',
        $Title = 'Branch',
        $SelectedValue = null
    ) {
        /**
         * Branch ID 1 = Head Office
         * When Head Office Login Feild is select form otherwise input hidden feild
         */
        $html       = '';
        $BranchID   = Common::getBranchId();
        $branch_arr = HRS::getUserAccesableBranchIds();

        if (count($branch_arr) > 1 || $BranchID == 1) {

            $BranchData  = DB::table('gnl_branchs')
                ->where([['is_delete', 0], ['is_approve', 1]])
                ->where([['id', '!=', 1]])
                ->whereIn('id', $branch_arr)
                ->orderBy('branch_code', 'ASC')
                ->get();

            $html .= '<div class="col-md-2">';
            $html .= '<label class="input-title">' . $Title . '</label>';

            $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';

            if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                $html .= '<option value="">All</option>';
            }
            if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                $html .= '<option value="">Select One</option>';
            }

            foreach ($BranchData as $Row) {
                $selectTxt = '';
                if ($SelectedValue != null) {
                    if ($Row->id == $SelectedValue) {
                        $selectTxt = "selected";
                    }
                }
                // $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . sprintf("%04d", $Row->branch_code) . " - " . $Row->branch_name . '</option>';
                $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->branch_name . ' [' . $Row->branch_code . ']</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
        } else {
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $BranchID . '">';
        }

        return $html;
    }

    public static function forBranchFieldHr($id = 'branch_id', $name = 'branch_id', $class = '', $selVal = '')
    {
        return self::forBranchFeildNew(true, $name, $id, $selVal);
    }


    public static function forLeaveCategoryNew(
        $SelectBox = false,
        $FeildName = 'leave_cat_id',
        $FeildID = 'leave_cat_id',
        $Title = 'Leave Category',
        $SelectedValue = null,
        $isRequired = false,
        $class = '',
        $IgnoreHO = true
    ) {


        $html       = '';
        $leaveCategory = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0]])->get();

        if ($SelectBox == true) {

            $html .= '<div class="col-md-2">';

            if ($isRequired == false) {
                $html .= '<label class="input-title">' . $Title . '</label>';
                $required = '';
            } else {
                $html .= '<label class="input-title RequiredStar">' . $Title . '</label>';
                $required = 'required';
            }

            $html .= '<div class="input-group">';
            $html .= '<div class="input-group">';

            $html .= '<select class="form-control clsSelect2" ' . $required . ' name="' . $FeildName . '"  id="' . $FeildID . '" ' . $isRequired . '>';

            if ($IgnoreHO) {
                $html .= '<option value="">All</option>';
            }

            foreach ($leaveCategory as $Row) {
                $selectTxt = '';
                if ($SelectedValue != null) {
                    if ($Row->id == $SelectedValue) {
                        $selectTxt = "selected";
                    }
                }

                $html .= '<option value="' . $Row->id . '" ' . $selectTxt . ' >' . $Row->name . '</option>';
            }



            $html .= '</select>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        } else {

            if (!empty($SelectedValue)) {
                $BranchID = $SelectedValue;
            }
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $BranchID . '">';
        }

        return $html;
    }

    public static function forLeaveCategoryHr($id = 'leave_cat_id', $name = 'leave_cat_id', $class = '', $selVal = '')
    {
        $leaveCategory = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0]])->get();

        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="">Select leave category</option>';

        foreach ($leaveCategory as $lc) {
            $html .= '<option ' . (($lc->id == $selVal) ? "selected" : "") . ' value="' . $lc->id . '">' . $lc->name . '</option>';
        }

        $html .= '</select>';
        return $html;
    }

    public static function forLevelFieldHr($id = 'level', $name = 'level', $class = '', $selVal = '')
    {
        $level = DB::table('hr_config')->where([['title', 'level']])->first()->content;

        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="">Select level</option>';

        for ($i = 1; $i <= $level; $i++) {
            $html .= '<option ' . (($i == $selVal) ? "selected" : "") . ' value="' . $i . '">' . $i . '</option>';
        }

        $html .= '</select>';
        return $html;
    }

    public static function forGradeFieldHr($id = 'grade', $name = 'grade', $class = '', $selVal = '')
    {
        $grade = DB::table('hr_config')->where([['title', 'grade']])->first()->content;

        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="">Select grade</option>';

        for ($i = 1; $i <= $grade; $i++) {
            $html .= '<option ' . (($i == $selVal) ? "selected" : "") . ' value="' . $i . '">' . $i . '</option>';
        }

        $html .= '</select>';
        return $html;
    }

    public static function forPayscaleFieldHr($id = 'payscale_id', $name = 'payscale_id', $class = '', $selVal = '', $activeStatus = false)
    {
        if ($activeStatus == false) {
            $payscale = DB::table('hr_payroll_payscale')->where([['is_active', 1], ['is_delete', 0]])->get();
        } else {
            $payscale = DB::table('hr_payroll_payscale')->where([['is_active', 1], ['is_delete', 0], ['active_status', 1]])->get();
        }


        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="">Select payscale</option>';

        foreach ($payscale as $p) {
            $html .= '<option ' . (($p->id == $selVal) ? "selected" : "") . ' value="' . $p->id . '">' . $p->name . '</option>';
        }

        $html .= '</select>';
        return $html;
    }

    public static function forLeavePayscaleFieldHr(
        $id = 'fiscal_year',
        $name = 'fiscal_year',
        $module = "HR",
        $company_id = 1,
        $class = '',
        $selVal = ''
    ) {
        if ($module == "HR") {
            $payscale = DB::table('gnl_fiscal_year')->where([['is_active', 1], ['is_delete', 0], ['fy_for', 'LFY'], ['company_id', $company_id],])->get();
        } else {
            $payscale = DB::table('gnl_fiscal_year')->where([['is_active', 1], ['is_delete', 0], ['active_status', 1]])->get();
        }


        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="">Select payscale</option>';

        foreach ($payscale as $p) {
            $html .= '<option ' . (($p->id == $selVal) ? "selected" : "") . ' value="' . $p->id . '">' . $p->fy_name . '</option>';
        }

        $html .= '</select>';
        return $html;
    }

    public static function forReasonFieldHr($event_id, $id = 'reason', $name = 'reason', $class = '', $selVal = '')
    {
        $reason = DB::table('hr_app_reasons')->where([['event_id', $event_id], ['is_delete', 0]])->get();

        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="">Select a reason</option>';

        foreach ($reason as $r) {
            $html .= '<option ' . (($r->id == $selVal) ? "selected" : "") . ' value="' . $r->id . '">' . $r->reason . '</option>';
        }

        $html .= '</select>';
        return $html;
    }

    public static function forAttachmentFieldHr($id = "add_attachment_hr", $onchange = "validate_fileupload(this.id,2);")
    {
        $html = '<div class="input-group-append" style="line-height: 15px;">' .
            '<div class="btn btn-bg btn-success btn-file">' .
            '<i class="icon wb-upload" aria-hidden="true"></i>' .
            '<i>Uploader</i>' .
            '<input multiple type="file" id="' . $id . '" onchange="' . $onchange . '">' .
            '</div>' .
            '</div>';

        return $html;
    }

    public static function forDesignationFieldHr($id = 'designation_id', $name = 'designation_id', $class = '', $selVal = '', $allOption = false)
    {
        $designations = DB::select(DB::raw("SELECT id, name FROM hr_designations WHERE is_active = 1 AND is_delete = 0"));

        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">';

        if($allOption == true){
            $html .= '<option value="0">All</option>';
        }else{
            $html .= '<option value="">Select designation</option>';
        }

        foreach ($designations as $designation) {
            $html .= '<option ' . (($designation->id == $selVal) ? "selected" : "") . ' value="' . $designation->id . '">' . $designation->name . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    public static function forDesignationFeildSearch(
        $option = null,
        $FeildName = 'designation_id',
        $FeildID = 'designation_id',
        $Title = 'Designation',
        $SelectedValue = null,
        $isRequired = null
    ) {

        $html       = '';
        $designations = DB::select(DB::raw("SELECT id, name FROM hr_designations WHERE is_active = 1 AND is_delete = 0"));

        $html .= '<div class="col-md-2">';
        if (isset($isRequired) && $isRequired == true) {
            $html .= '<label class="input-title RequiredStar">' . $Title . '</label>';
            $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';
        } else {
            $html .= '<label class="input-title">' . $Title . '</label>';
            $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';
        }

        if ($option == 'all') {
            $html .= '<option value="">All</option>';
        }
        if ($option == 'one') {
            $html .= '<option value="" disabled >Select One</option>';
        }

        foreach ($designations as $Row) {
            $selectTxt = '';
            if ($SelectedValue != null) {
                if ($Row->id == $SelectedValue) {
                    $selectTxt = "selected";
                }
            }
            $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->name . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }

    public static function forDepartmentFieldHr($id = 'department_id', $name = 'department_id', $class = '', $selVal = '', $allOption = false)
    {
        if ($allOption == true) {
            $sField = "All";
            $sValue = '0';
        } else {
            $sField = "Select department";
            $sValue = '';
        }

        $departments = DB::select(DB::raw("SELECT id, dept_name FROM hr_departments WHERE is_active = 1 AND is_delete = 0"));

        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="' . $sValue . '">' . $sField . '</option>';

        foreach ($departments as $department) {
            $html .= '<option ' . (($department->id == $selVal) ? "selected" : "") . ' value="' . $department->id . '">' . $department->dept_name . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    public static function forDepartmentFeildSearch(
        $option = null,
        $FeildName = 'department_id',
        $FeildID = 'department_id',
        $Title = 'Department',
        $SelectedValue = null,
        $isRequired = null
    ) {

        $html       = '';
        $departments = DB::select(DB::raw("SELECT id, dept_name FROM hr_departments WHERE is_active = 1 AND is_delete = 0"));

        $html .= '<div class="col-md-2">';
        if (isset($isRequired) && $isRequired == true) {
            $html .= '<label class="input-title RequiredStar">' . $Title . '</label>';
            $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';
        } else {
            $html .= '<label class="input-title">' . $Title . '</label>';
            $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '">';
        }

        if ($option == 'all') {
            $html .= '<option value="">All</option>';
        }
        if ($option == 'one') {
            $html .= '<option value="">Select One</option>';
        }

        foreach ($departments as $Row) {
            $selectTxt = '';
            if ($SelectedValue != null) {
                if ($Row->id == $SelectedValue) {
                    $selectTxt = "selected";
                }
            }
            $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->dept_name . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }

    public static function forLeaveTypeFeildSearch(
        $option = null,
        $FeildName = 'leave_type_id',
        $FeildID = 'leave_type_id',
        $Title = 'Leave Type',
        $SelectedValue = null,
        $isRequired = null
    ) {

        $html       = '';
        $leaveType = DB::table('gnl_dynamic_form_value')->where([['type_id', 3], ['form_id', 1]])->get();

        $html .= '<div class="col-md-2">';
        $html .= '<div class="input-group">';
        if (isset($isRequired) && $isRequired == true) {
            $html .= '<label class="input-title RequiredStar">' . $Title . '</label>';
            $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" style="width: 100%;">';
        } else {
            $html .= '<label class="input-title">' . $Title . '</label>';
            $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" style="width: 100%;">';
        }

        if ($option == 'all') {
            $html .= '<option value="">All</option>';
        }
        if ($option == 'one') {
            $html .= '<option value="">Select One</option>';
        }

        foreach ($leaveType as $Row) {
            $selectTxt = '';
            if ($SelectedValue != null) {
                if ($Row->uid == $SelectedValue) {
                    $selectTxt = "selected";
                }
            }
            $html .= '<option value="' . $Row->uid . '" ' . $selectTxt . '>' . $Row->name . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function forLeaveCatFeildSearch(
        $option = null,
        $FeildName = 'leave_cat_id',
        $FeildID = 'leave_cat_id',
        $Title = 'Leave Category',
        $SelectedValue = null,
        $isRequired = null
    ) {

        $html       = '';
        $leaveCat = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0]])->get();

        $html .= '<div class="col-md-2">';
        $html .= '<div class="input-group">';
        if (isset($isRequired) && $isRequired == true) {
            $html .= '<label class="input-title RequiredStar">' . $Title . '</label>';

            $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" style="width: 100%;">';
        } else {
            $html .= '<label class="input-title">' . $Title . '</label>';
            $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" style="width: 100%;">';
        }

        if ($option == 'all') {
            $html .= '<option value="">All</option>';
        }
        if ($option == 'one') {
            $html .= '<option value="">Select One</option>';
        }

        foreach ($leaveCat as $Row) {
            $selectTxt = '';
            if ($SelectedValue != null) {
                if ($Row->uid == $SelectedValue) {
                    $selectTxt = "selected";
                }
            }
            $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->name . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public static function makeMenus()
    {

        $route          = Route::current();
        $ActiveRouteURl = $route->uri();

        $curUriArr = explode('/', $ActiveRouteURl);

        $TitleText = '';

        if (count($curUriArr) >= 3) {
            $ActiveLink = $curUriArr[0] . "/" . $curUriArr[1];

            if ($curUriArr[2] == 'add') {
                $TitleText = ' Entry';
            } elseif ($curUriArr[2] == 'edit') {
                $TitleText = ' Update';
            } elseif ($curUriArr[2] == 'view') {
                $TitleText = ' Details';
            }
        } else {
            $ActiveLink = $ActiveRouteURl;
        }

        // dd($ActiveRouteURl);

        if (!empty(Session::get('ModuleID'))) {
            $ModuleLink = Session::get('ModuleID');
        } else {
            $route_array = explode('/', $ActiveRouteURl);
            $ModuleLink  = $route_array[0];
            Session::put('ModuleID', $ModuleLink);
        }

        $route_array = explode('/', $ActiveRouteURl);
        $ModuleLink  = $route_array[0];

        $MenuData = array();

        if (!empty(Session::get('LoginBy.user_role.role_menu.' . $ModuleLink))) {
            $MenuData = Session::get('LoginBy.user_role.role_menu.' . $ModuleLink);
        } elseif (!empty(Session::get('LoginBy.user_role.role_menu./' . $ModuleLink))) {
            $MenuData = Session::get('LoginBy.user_role.role_menu.' . $ModuleLink);
        }

        $mhtml = '<ul class="site-menu" data-plugin="menu">';

        // // ## Menu Hide for condition purpose
        $branchId = Common::getBranchId();

        $roleModules = Session::get('LoginBy.user_role.role_module');
        $isMFNModule = array_search('mfn', array_column($roleModules, 'module_link'));

        if ($isMFNModule && $ModuleLink == 'mfn') {
            $checkOpening = MFN::isOpening($branchId);
        } else {
            $checkOpening = false;
        }

        foreach ($MenuData as $RootMenu) {

            if ($RootMenu['menu_link'] == "mfn/savings_ob") {
                if ($checkOpening == false) {
                    continue;
                }
            }

            $ActiveClass = '';

            if ($ActiveRouteURl == $RootMenu['menu_link'] || $ActiveLink == $RootMenu['menu_link']) {
                $ActiveClass = 'active pageTitle';
            }

            $SubMenu = false;
            if (count($RootMenu['sub_menu']) > 0) {
                $SubMenu = true;
            }

            if ($SubMenu) {
                $mhtml .= '<li class="site-menu-item has-sub CustomClass ">';
                $mhtml .= '<a href="javascript:void(0)" data-dropdown-toggle="false">';
            } else {
                $mhtml .= '<li class="site-menu-item CustomClass ' . $ActiveClass . '" menu_name="' . $RootMenu['name'] . $TitleText . '" page_title="' . $RootMenu['page_title'] . $TitleText . '">';
                // menu_link
                $mhtml .= '<a class="animsition-link" href="' . url($RootMenu['menu_link']) . '">';
            }

            $mhtml .= '<i class="site-menu-icon ' . $RootMenu['icon'] . ' aria-hidden="true" "></i>';
            $mhtml .= '<span class="site-menu-title">' . $RootMenu['name'] . '</span>';

            if ($SubMenu) {
                $mhtml .= '<span class="site-menu-arrow "></span>';
                $mhtml .= '</a>';

                $mhtml .= self::makeSubMenus($RootMenu['sub_menu'], $checkOpening);
            } else {
                $mhtml .= '</a>';
            }

            $mhtml .= '</li>';
        }

        $mhtml .= '</ul>';

        return $mhtml;
    }

    public static function makeSubMenus($SubMenuData = [], $checkOpening)
    {

        $route = Route::current();
        // $ActiveRouteURl = "/" . $route->uri();
        $ActiveRouteURl = $route->uri();

        $curUriArr = explode('/', $ActiveRouteURl);

        $TitleText = '';

        if (count($curUriArr) >= 3) {
            $ActiveLink = $curUriArr[0] . "/" . $curUriArr[1];

            if ($curUriArr[2] == 'add') {
                $TitleText = ' Entry';
            } elseif ($curUriArr[2] == 'edit') {
                $TitleText = ' Update';
            } elseif ($curUriArr[2] == 'view') {
                $TitleText = ' Details';
            }
        } else {
            $ActiveLink = $ActiveRouteURl;
        }

        $shtml = '<ul class="site-menu-sub">';

        foreach ($SubMenuData as $RowSubMenu) {

            if ($RowSubMenu['menu_link'] == "mfn/savings_ob") {
                if ($checkOpening == false) {
                    continue;
                }
            }

            $ActiveClass = '';

            if ($ActiveRouteURl == $RowSubMenu['menu_link'] || $ActiveLink == $RowSubMenu['menu_link']) {
                // $ActiveClass = 'active';
                $ActiveClass = 'active pageTitle';
            }

            $SubChild = false;
            if (count($RowSubMenu['sub_menu']) > 0) {
                $SubChild = true;
            }

            if ($SubChild) {
                $shtml .= '<li class="site-menu-item has-sub">';
                $shtml .= '<a href="javascript:void(0)">';
            } else {
                $shtml .= '<li class="site-menu-item ' . $ActiveClass . '" menu_name="' . $RowSubMenu['name'] . $TitleText . '" page_title="' . $RowSubMenu['page_title'] . $TitleText . '">';
                $shtml .= '<a class="animsition-link" href="' . url($RowSubMenu['menu_link']) . '">';
            }

            $shtml .= '<span class="site-menu-title">' . $RowSubMenu['name'] . '</span>';

            if ($SubChild) {
                $shtml .= '<span class="site-menu-arrow "></span>';
                $shtml .= '</a>';
                $shtml .= self::makeSubMenus($RowSubMenu['sub_menu'], $checkOpening);
            } else {
                $shtml .= '</a>';
            }

            $shtml .= '</li>';
        }

        $shtml .= '</ul>';

        return $shtml;
    }

    public static function getOptionsForEmployee($parameter = [])
    {
        return HRS::getOptionsForEmployee($parameter);
    }

    public static function searchEmployeeAndGetOptions($branchId = null, $departmentId = null, $designationId = null, $empCode = null)
    {
        $emp = DB::table('hr_employees')
            ->where('is_delete', 0)
            ->where(function ($emp) use ($branchId, $departmentId, $designationId, $empCode) {
                if ($branchId != null) {
                    # code...
                    $emp->where('branch_id', $branchId);
                }
                if ($departmentId != null) {
                    # code...
                    $emp->where('department_id', $departmentId);
                }
                if ($designationId != null) {
                    # code...
                    $emp->where('designation_id', $designationId);
                }
                if ($empCode != null) {
                    # code...
                    $emp->where('emp_code', $empCode);
                }
            })
            ->get();

        $empArr            = [];
        $empArr[0]['id']   = '';
        $empArr[0]['text'] = '<div>Select</div>';

        foreach ($emp as $key => $item) {
            $empArr[$key + 1]['id']    = $item->id;
            $empArr[$key + 1]['text']  = '<div>' . $item->emp_name . '</div>';
            $empArr[$key + 1]['html']  = '<div>' . $item->emp_name . '</div><div><small>Code: ' . $item->emp_code . '</small></div>';
            $empArr[$key + 1]['title'] = $item->emp_name;
        }

        return $empArr;
    }

    public static function forRecruitmentFieldHr($id = 'recruitment_type_id', $name = 'recruitment_type_id', $class = '', $selVal = '')
    {
        $recruitmentData = DB::table('hr_recruitment_types')->where([['is_active', 1], ['is_delete', 0]])
            ->whereBetween('salary_method', ['auto', 'both'])->get();
        // dd($recruitmentData);

        $html = '<select id="' . $id . '" name="' . $name . '" class="form-control clsSelect2 ' . $class . '"  style="width: 100%">' . '<option value="">Select payscale</option>';

        foreach ($recruitmentData as $p) {
            $html .= '<option value="' . $p->id . '">' . $p->title . '</option>';
        }

        $html .= '</select>';
        return $html;
    }


    public static function forDistrictFeildSearch(
        $option = "All",
        $FeildName = 'district_id',
        $FeildID = 'district_id',
        $Title = 'District',
        $SelectedValue = null,
        $isRequired = null
    ) {

        $html       = '';

        $regionWiseQuery = DB::table('gnl_districts')
            ->where('is_delete', 0)
            ->orderBy('district_name', 'ASC')
            ->get();

        // ss($regionWiseQuery);

        if (count($regionWiseQuery->toArray()) > 0) {

            if (count($regionWiseQuery->toArray()) > 1 ) {

                $html .= '<div class="col-md-2">';

                if ($isRequired) {
                    $html .= '<label class="input-title RequiredStar">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="">';
                } else {
                    $html .= '<label class="input-title">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="">';
                }

                $html .= '<option value="">All</option>';

                if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                    $html .= '<option value="">All</option>';
                }
                if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                    $html .= '<option value="">Select One</option>';
                }

                foreach ($regionWiseQuery as $Row) {
                    $selectTxt = '';
                    if ($SelectedValue != null) {
                        if ($Row->id == $SelectedValue) {
                            $selectTxt = "selected";
                        }
                    }

                    $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->district_name .'</option>';
                }

                $html .= '</select>';
                $html .= '</div></div>';
            } else {
                $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $regionWiseQuery->toArray()[0]->id . '">';
            }
        } else {
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="">';
        }
        return $html;
    }

    public static function forUpazilaFeildSearch(
        $option = "All",
        $FeildName = 'upazila_id',
        $FeildID = 'upazila_id',
        $Title = 'Upazila',
        $SelectedValue = null,
        $isRequired = null
    ) {

        $html       = '';

        $regionWiseQuery = DB::table('gnl_upazilas')
            ->where('is_delete', 0)
            ->orderBy('upazila_name', 'ASC')
            ->get();

        // ss($regionWiseQuery);

        if (count($regionWiseQuery->toArray()) > 0) {

            if (count($regionWiseQuery->toArray()) > 1 ) {

                $html .= '<div class="col-md-2">';

                if ($isRequired) {
                    $html .= '<label class="input-title RequiredStar">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select required class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="">';
                } else {
                    $html .= '<label class="input-title">' . $Title . '</label> <div class="input-group">';
                    $html .= '<select class="form-control clsSelect2" name="' . $FeildName . '" id="' . $FeildID . '" onchange="">';
                }

                if ($option == 'all' || $option == 'All' || $option == 'ALL') {
                    $html .= '<option value="">All</option>';
                }
                if ($option == 'one' || $option == 'One' || $option == 'ONE') {
                    $html .= '<option value="">Select One</option>';
                }
                $html .= '<option value="">All</option>';
                foreach ($regionWiseQuery as $Row) {
                    $selectTxt = '';
                    if ($SelectedValue != null) {
                        if ($Row->id == $SelectedValue) {
                            $selectTxt = "selected";
                        }
                    }

                    $html .= '<option value="' . $Row->id . '" ' . $selectTxt . '>' . $Row->upazila_name .'</option>';
                }

                $html .= '</select>';
                $html .= '</div></div>';
            } else {
                $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="' . $regionWiseQuery->toArray()[0]->id . '">';
            }
        } else {
            $html .= '<input type="hidden" name="' . $FeildName . '" id="' . $FeildID . '" value="">';
        }
        return $html;
    }

}
