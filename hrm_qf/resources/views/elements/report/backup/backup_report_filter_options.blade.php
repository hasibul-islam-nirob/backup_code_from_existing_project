@php
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use App\Services\HtmlService as HTML;
use App\Services\AccService as ACC;

// $route = Route::current()->uri();
// $instFreq = "all";
// if($route == "pos/report/monthly_collection_sheet" || $route == "pos/report/daily_collection_sheet"){
//     $instFreq = "month";
// }else if($route == "pos/report/weekly_collection_sheet"){
//     $instFreq = "week";
// }

$branchArr = array();
$branchID = isset(Request::all()['branch_id']) ? Request::all()['branch_id'] : null;

if (!empty($branchID) && $branchID > 0) {
    $branchArr[] = $branchID;

    $StartDate = $EndDate = Common::systemCurrentDate($branchID);
    $branchOpenDate = Common::getBranchSoftwareStartDate($branchID);
} else {
    $StartDate = $EndDate = Common::systemCurrentDate();
    // $EndDate = Common::systemCurrentDate();
    $branchOpenDate = Common::getBranchSoftwareStartDate();
}

@endphp


<div class="w-full d-print-none">
    <div class="panel filterDiv">

        <div class="panel-heading">
            <h3 class="panel-title"></h3>
            <div class="panel-actions">
                <a class="panel-action icon wb-minus" data-toggle="panel-collapse" aria-expanded="true"
                    aria-hidden="true"></a>
            </div>
        </div>

        <div class="panel-body panel-search">

            <div class="row align-items-center pb-10">

                @if (isset($supplierAtFirst) && $supplierAtFirst)
                <div class="col-lg-2">
                    <label class="input-title">Supplier</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="supplier_id" id="supplier_id">
                            <option value="">All</option>
                            @php
                                $supplierList = Common::ViewTableOrder('pos_suppliers', [['is_delete', 0], ['is_active', 1], ['sup_comp_name', '!=', '']], ['id', 'sup_comp_name'], ['sup_comp_name', 'ASC']);
                            @endphp

                            @foreach ($supplierList as $Row)
                                <option value="{{ $Row->id }}">{{ $Row->sup_comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif




                @if(isset($orderNoAtFirst) && $orderNoAtFirst)
                <div class="col-lg-2">
                    <label class="input-title">Order No</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="order_id" id="order_id">
                            <option value="">All</option>
                            @php
                            $OrderList = Common::ViewTableOrder('pos_orders_m',
                            [['is_delete', 0], ['is_active', 1]],
                            ['order_no'],
                            ['order_no', 'DESC'])
                            @endphp
                            @foreach ($OrderList as $Row)
                            <option value="{{ $Row->order_no }}">{{ $Row->order_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($areaAtFirst) && $areaAtFirst)
                <div class="col-lg-2">
                    <label class="input-title">Area</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" multiple name="area_arr[]" id="area_id" placeholder="Select" >
                            <option value="">Select one/multiple</option>
                            @php
                            $AreaList = Common::ViewTableOrder('gnl_areas',
                            [['is_delete', 0], ['is_active', 1]],
                            ['id','area_name','area_code'],
                            ['area_code', 'ASC'])
                            @endphp
                            @foreach ($AreaList as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->area_name." [".$Row->area_code."]" }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif



                @if (isset($zone) && $zone)
                    {!! HTML::forZoneFeildSearch('all', 'zone_id', 'zone_id', 'Zone', null) !!}
                @endif

                @if (isset($area) && $area)
                    {!! HTML::forAreaFeildSearch('all', 'area_id', 'area_id', 'Area', null) !!}
                @endif

                @if (isset($project) && $project)
                    <div class="col-lg-2">
                        <label class="input-title">Project</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="project_id" id="project_id">
                                @php
                                    $projectData = Common::ViewTableOrder('gnl_projects', [['is_delete', 0], ['is_active', 1], ['project_name', '!=', '']], ['id', 'project_name', 'project_code'], ['project_name', 'ASC']);
                                @endphp
                                @foreach ($projectData as $Row)
                                    <option value="{{ $Row->id }}">
                                        {{-- {{ sprintf("%03d",$Row->project_code) }} - {{ $Row->project_name }} --}}
                                        {{ $Row->project_name . ' [' . $Row->project_code . ']' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($projectType) && $projectType)
                    <div class="col-lg-2">
                        <label class="input-title">Project Type</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="project_type_id" id="project_type_id">
                                @php
                                    $projectTypeData = Common::ViewTableOrder('gnl_project_types', [['is_delete', 0], ['is_active', 1], ['project_type_name', '!=', '']], ['id', 'project_type_name', 'project_type_code'], ['project_type_name', 'ASC']);
                                @endphp

                                @foreach ($projectTypeData as $Row)
                                    <option value="{{ $Row->id }}">
                                        {{ $Row->project_type_name . ' [' . $Row->project_type_code . ']' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($branch) && $branch)
                    {!! HTML::forBranchFeildSearch_new('all', 'branch_id', 'branch_id', 'Branch', null) !!}
                @endif

                @if(isset($branchWithoutHO) && $branchWithoutHO)
                {!! HTML::forBranchFeildSearch_new('one','branch_id','branch_id', 'Branch', null, true,false)!!}
                @endif

                @if(isset($branchFrom) && $branchFrom)
                {!! HTML::forBranchFeildSearch_new('all','branch_from','branch_from', 'Branch From', null)!!}
                @endif

                @if (isset($branchTo) && $branchTo)
                    {!! HTML::forBranchFeildSearch_new('all', 'branch_to', 'branch_to', 'Branch To', null) !!}
                @endif

                @if (isset($reqFrom) && $reqFrom)
                    {!! HTML::forBranchFeildSearch_new('all', 'branch_id', 'branch_id', 'Requisition From', null) !!}
                @endif

                @if (isset($deliveryPlace) && $deliveryPlace)
                    {!! HTML::forBranchFeildSearch_new('all', 'branch_id', 'branch_id', 'Delivery Place', null) !!}
                @endif

                <!-- For Accounting Report -->
                @if (isset($branchAcc) && $branchAcc)
                    @if(isset($allWithWihoutHO) && $allWithWihoutHO)
                    {!! HTML::forBranchFeildSearch() !!}
                    @else
                    {!! HTML::forBranchFeildSearch('all') !!}
                    @endif
                @endif

                @if (isset($vTypeReceipt) && $vTypeReceipt)
                    <div class="col-lg-2">
                        <label class="input-title">Voucher Type</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="voucher_type" id="voucher_type">
                                <option value="1" selected>Cash</option>
                                <option value="2">All</option>
                                <option value="3">Non-Cash</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($depthLevel) && $depthLevel)
                    <div class="col-lg-2">
                        <label class="input-title">Depth Level</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="depth_level" id="depth_level">
                                <option value="">All</option>
                                @php
                                    $levelData = DB::table('acc_account_ledger')
                                        ->select('id', 'level')
                                        ->where([['is_delete', 0], ['is_active', 1]])
                                        ->groupBy('level')
                                        ->get();
                                @endphp

                                @foreach ($levelData as $Row)
                                    <option value="{{ $Row->level }}">Level-{{ $Row->level }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($roundUp) && $roundUp)
                    <div class="col-lg-2">
                        <label class="input-title">Round Up</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="round_up" id="round_up">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($zeroBalance) && $zeroBalance)
                    <div class="col-lg-2">
                        <label class="input-title">'0' Balance</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="zero_balance" id="zero_balance">
                                <option value="1">Yes</option>
                                <option value="2">No</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($group) && $group)
                    <div class="col-lg-2">
                        <label class="input-title">Group</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="group_id" id="group_id"
                                onchange="fnAjaxGetCategory(); fnAjaxGetSubCat(); fnAjaxGetModel(); fnAjaxGetProduct();">

                                <option value="">All</option>
                                @php
                                    $PGroupList = Common::ViewTableOrder('pos_p_groups', [['is_delete', 0], ['is_active', 1]], ['id', 'group_name'], ['group_name', 'ASC']);
                                @endphp

                                @foreach ($PGroupList as $Row)
                                    <option value="{{ $Row->id }}">{{ $Row->group_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Category</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="cat_id" id="cat_id"
                                onchange="fnAjaxGetSubCat(); fnAjaxGetModel(); fnAjaxGetProduct();">

                                <option value="">All</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Sub Category</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="sub_cat_id" id="sub_cat_id"
                                onchange="fnAjaxGetModel(); fnAjaxGetProduct();">

                                <option value="">All</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Brand</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="brand_id" id="brand_id"
                                onchange="fnAjaxGetProduct();">

                                <option value="">All</option>
                                @php
                                    $BrandList = Common::ViewTableOrder('pos_p_brands', [['is_delete', 0], ['is_active', 1]], ['id', 'brand_name'], ['brand_name', 'ASC']);
                                @endphp
                                @foreach ($BrandList as $Row)
                                    <option value="{{ $Row->id }}">{{ $Row->brand_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($model) && $model)
                    <div class="col-lg-2">
                        <label class="input-title">Model</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="model_id" id="model_id"
                                onchange="fnAjaxGetProduct();">

                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($product) && $product)
                    <div class="col-lg-2">
                        <label class="input-title">Product</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="product_id" id="product_id">
                                <option value="">All</option>

                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($supplier) && $supplier)
                    <div class="col-lg-2">
                        <label class="input-title">Supplier</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="supplier_id" id="supplier_id">
                                <option value="">All</option>
                                @php
                                    $supplierList = Common::ViewTableOrder('pos_suppliers', [['is_delete', 0], ['is_active', 1], ['sup_comp_name', '!=', '']], ['id', 'sup_comp_name'], ['sup_comp_name', 'ASC']);
                                @endphp

                                @foreach ($supplierList as $Row)
                                    <option value="{{ $Row->id }}">{{ $Row->sup_comp_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                



                <!-- Inventory Filter Options Start -->
                @if (isset($groupInv) && $groupInv)
                    <div class="col-lg-2">
                        <label class="input-title">Group</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="group_id" id="group_id"
                                onchange="fnAjaxGetCategory('inv'); fnAjaxGetSubCat('inv'); fnAjaxGetModel('inv'); fnAjaxGetProduct('inv');">

                                <option value="">All</option>
                                @php
                                    $PGroupList = Common::ViewTableOrder('inv_p_groups', [['is_delete', 0], ['is_active', 1]], ['id', 'group_name'], ['group_name', 'ASC']);
                                @endphp

                                @foreach ($PGroupList as $Row)
                                    <option value="{{ $Row->id }}">{{ $Row->group_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Category</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="cat_id" id="cat_id"
                                onchange="fnAjaxGetSubCat('inv'); fnAjaxGetModel('inv'); fnAjaxGetProduct('inv');">

                                <option value="">All</option>

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Sub Category</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="sub_cat_id" id="sub_cat_id"
                                onchange="fnAjaxGetModel('inv'); fnAjaxGetProduct('inv');">
                                <option value="">All</option>

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Brand</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="brand_id" id="brand_id"
                                onchange="fnAjaxGetProduct('inv');">

                                <option value="">All</option>
                                @php
                                    $BrandList = Common::ViewTableOrder('inv_p_brands', [['is_delete', 0], ['is_active', 1]], ['id', 'brand_name'], ['brand_name', 'ASC']);
                                @endphp
                                @foreach ($BrandList as $Row)
                                    <option value="{{ $Row->id }}">{{ $Row->brand_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif


                @if (isset($modelInv) && $modelInv)
                    <div class="col-lg-2">
                        <label class="input-title">Model</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="model_id" id="model_id"
                                onchange="fnAjaxGetProduct('inv');">

                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($productInv) && $productInv)
                    <div class="col-lg-2">
                        <label class="input-title">Product</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="product_id" id="product_id">
                                <option value="">All</option>

                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($supplierInv) && $supplierInv)
                    <div class="col-lg-2">
                        <label class="input-title">Supplier</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="supplier_id" id="supplier_id">
                                <option value="">All</option>
                                @php
                                    $supplierList = Common::ViewTableOrder('inv_suppliers', [['is_delete', 0], ['is_active', 1], ['sup_comp_name', '!=', '']], ['id', 'sup_comp_name'], ['sup_comp_name', 'ASC']);
                                @endphp

                                @foreach ($supplierList as $Row)
                                    <option value="{{ $Row->id }}">{{ $Row->sup_comp_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                {{-- fam filter --}}
                <!-- fam Filter Options Start -->
                @if (isset($groupFam) && $groupFam)
                    <div class="col-lg-2">
                        <label class="input-title">Group</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="group_id" id="group_id"
                                onchange="fnAjaxGetCategory('fam'); fnAjaxGetSubCat('fam'); fnAjaxGetModel('fam');fnAjaxGetProductType('fam');fnAjaxGetProductName('fam'); fnAjaxGetProduct('fam');">

                                <option value="">All</option>
                                @php
                                $PGroupList = Common::ViewTableOrder('fam_p_groups',
                                [['is_delete', 0], ['is_active', 1]],
                                ['id', 'group_name'],
                                ['group_name', 'ASC'])
                                @endphp

                                @foreach($PGroupList as $Row)
                                <option value="{{ $Row->id}}">{{ $Row->group_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Category</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="cat_id" id="cat_id"
                                onchange="fnAjaxGetSubCat('fam'); fnAjaxGetModel('fam');fnAjaxGetProductType('fam');fnAjaxGetProductName('fam'); fnAjaxGetProduct('fam');">

                                <option value="">All</option>

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Sub Category</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="sub_cat_id" id="sub_cat_id"
                                onchange="fnAjaxGetModel('fam');fnAjaxGetProductType('fam');fnAjaxGetProductName('fam'); fnAjaxGetProduct('fam');">
                                <option value="">All</option>

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label class="input-title">Brand</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="brand_id" id="brand_id"
                                onchange="fnAjaxGetProduct('fam');">

                                <option value="">Select All</option>
                                @php
                                    $BrandList = Common::ViewTableOrder('fam_p_brands', [['is_delete', 0], ['is_active', 1]], ['id', 'brand_name'], ['brand_name', 'ASC']);
                                @endphp
                                @foreach ($BrandList as $Row)
                                    <option value="{{ $Row->id }}">{{ $Row->brand_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                @if(isset($typeFam) && $typeFam)
                <div class="col-lg-2">
                    <label class="input-title">Product Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="type_id" id="type_id"
                            onchange="fnAjaxGetProductName('fam');">

                            <option value="">Select All</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($nameFam) && $nameFam)
                <div class="col-lg-2">
                    <label class="input-title">Product Name</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="name_id" id="name_id"
                            onchange="fnAjaxGetProduct('fam');">

                            <option value="">Select All</option>
                        </select>
                    </div>
                </div>
                @endif
                
                

                @if(isset($modelFam) && $modelFam)
                <div class="col-lg-2">
                    <label class="input-title">Model</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="model_id" id="model_id"
                            onchange="fnAjaxGetProduct('fam');">

                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($productFam) && $productFam)
                <div class="col-lg-2">
                    <label class="input-title">Product</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="product_id" id="product_id">
                            <option value="">All</option>

                        </select>
                    </div>
                </div>
                @endif


                {{-- fam --}}
                @if(isset($supplierFam) && $supplierFam)
                <div class="col-lg-2">
                    <label class="input-title">Supplier</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="supplier_id" id="supplier_id">
                            <option value="">All</option>
                            @php
                            $supplierList = Common::ViewTableOrder('fam_suppliers',
                            [['is_delete', 0], ['is_active', 1], ['sup_comp_name', '!=', '']],
                            ['id', 'sup_comp_name'],
                            ['sup_comp_name', 'ASC'])
                            @endphp

                            @foreach ($supplierList as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->sup_comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                
                {{-- fam --}}
                @if(isset($purchaseNoFam) && $purchaseNoFam)
                <div class="col-lg-2">
                    <label class="input-title">Purchase No</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="purchase_id" id="purchase_id">
                            <option value="">All</option>
                            @php
                            $PurchaseNoList = Common::ViewTableOrder('fam_purchases_m',
                            [['is_delete', 0], ['is_active', 1], ['bill_no', '!=', '']],
                            ['id', 'bill_no'],
                            ['bill_no', 'ASC'])
                            @endphp
                            @foreach ($PurchaseNoList as $Row)
                            <option value="{{ $Row->bill_no }}">{{ $Row->bill_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($reportFormattingFam) && $reportFormattingFam)
                <div class="col-lg-2">
                    <label class="input-title">Report Formatting</label>
                    <select class="form-control clsSelect2" name="report_formatting" id="report_formatting">
                        <option value="">Default</option>
                        <option value="date">Group By Date</option>
                        <option value="product_name">Group By Product Name</option>
                        <option value="product_type">Group By Product Type</option>
                        <option value="supplier">Group By Supplier</option>
        
                    </select>
                </div>
                @endif

               
               
                {{-- fam  filter code end --}}

                @if(isset($purchaseNo) && $purchaseNo)
                <div class="col-lg-2">
                    <label class="input-title">Purchase No</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="purchase_id" id="purchase_id">
                            <option value="">All</option>
                            @php
                            $PurchaseNoList = Common::ViewTableOrder('pos_purchases_m',
                            [['is_delete', 0], ['is_active', 1], ['bill_no', '!=', '']],
                            ['id', 'bill_no'],
                            ['bill_no', 'ASC'])
                            @endphp
                            @foreach ($PurchaseNoList as $Row)
                            <option value="{{ $Row->bill_no }}">{{ $Row->bill_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif


                @if(isset($first_collection) && $first_collection)
                <div class="col-lg-2">
                    <label class="input-title">First Collection</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="first_collection" id="first_collection">
                            <option value="">With</option>
                            <option value="2">Without</option>
                            <option value="1">Only</option>
                        </select>

                    </div>
                </div>
                @endif

                @if(isset($orderNo) && $orderNo)
                <div class="col-lg-2">
                    <label class="input-title">Order No</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="order_id" id="order_id">
                            <option value="">All</option>
                            @php
                            $OrderList = Common::ViewTableOrder('pos_orders_m',
                            [['is_delete', 0], ['is_active', 1], ['is_approve', 1]],
                            ['order_no'],
                            ['order_no', 'DESC'])
                            @endphp
                            @foreach ($OrderList as $Row)
                            <option value="{{ $Row->order_no }}">{{ $Row->order_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($invoiceNo) && $invoiceNo)
                <div class="col-lg-2">
                    <label class="input-title">Invoice No</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="invoice_id" id="invoice_id">
                            <option value="">All</option>
                            @php
                            $InvoiceNoList = Common::ViewTableOrder('pos_purchases_m',
                            [['is_delete', 0], ['is_active', 1], ['invoice_no', '!=', '']],
                            ['id', 'invoice_no'],
                            ['invoice_no', 'ASC'])
                            @endphp
                            @foreach ($InvoiceNoList as $Row)
                            <option value="{{ $Row->invoice_no }}">{{ $Row->invoice_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($customer) && $customer)
                <div class="col-lg-2">
                    <label class="input-title">Customer</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="customer_id" id="customer_id">
                            <option value="">All</option>
                            @php
                            $customerData = Common::ViewTableOrderIn('pos_customers',
                            [['is_delete', 0], ['is_active', 1]],
                            ['branch_id', HRS::getUserAccesableBranchIds()],
                            ['id', 'customer_no', 'customer_name'],
                            ['customer_name', 'ASC'])
                            @endphp
                            @foreach ($customerData as $row)
                            <option value="{{ $row->customer_no }}">{{ $row->customer_name . ' [' .$row->customer_no. ']' }}
                            </option>
                            @endforeach

                        </select>
                    </div>
                </div>
                @endif

                @if(isset($nameorcode) && $nameorcode)
                <div class="col-lg-2">
                    <label class="input-title">{{isset($nameorcode[0]['title'])? $nameorcode[0]['title'] : "Name Or Code"}}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="nameorcode" id="nameorcode" placeholder="Enter {{isset($nameorcode[0]['title'])? $nameorcode[0]['title'] : "Name Or Code"}}" >
                    </div>
                </div>
                @endif

                @if(isset($customertext) && $customertext)
                <div class="col-lg-2">
                    <label class="input-title">Customer</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="customer_tx" id="customer_tx" placeholder="Enter Customer Name Or Code" >
                    </div>
                </div>
                @endif

                {{-- @if(isset($employeetext) && $employeetext)
                <div class="col-lg-2">
                    <label class="input-title">Sales By</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="employee_tx" id="employee_tx" placeholder="Enter Employee Name Or Code" >
                    </div>
                </div>
                @endif --}}

                @if ((isset($employee) && $employee))

                    <div class="col-lg-2">
                       
                        <label class="input-title">Employee</label>

                        <div class="input-group">
                            <select class="form-control clsSelect2" name="employee_id" id="employee_id">
                                <option value="">All</option>

                                @php
                                    $employeeData = DB::table('hr_employees')
                                        ->where([['is_delete', 0]])
                                        ->whereIn('branch_id', count($branchArr) > 0 ? $branchArr : HRS::getUserAccesableBranchIds())
                                        ->orderBy('emp_code', 'ASC')
                                        ->get();
                                @endphp

                                    @foreach ($employeeData as $row)
                                        <option value="{{ $row->id }}">
                                            {{ $row->emp_name . ' [' . $row->emp_code . ']' }}
                                        </option>
                                    @endforeach
                                

                                @if (Common::getDBConnection() != 'sqlite')
                                    @if(Schema::hasTable('hr_employee_transfer'))
                                        @php
                                            $employeeDataTramsfer = DB::table('hr_employee_transfer as het')
                                                ->where(function($employeeDataTramsfer) use ($employeeData){
                                                    if(Common::getModuleByRoute() == "pos"){
                                                        $employeeDataTramsfer->whereNotIn('het.employee_no', array_unique($employeeData->pluck('employee_no')->toArray()));
                                                    }
                                                    else{
                                                        $employeeDataTramsfer->whereNotIn('het.emp_id', array_unique($employeeData->pluck('id')->toArray()));
                                                    }
                                                })
                                                ->where([['het.is_delete', 0], ['het.is_active', 1]])
                                                ->where([['het.branch_from', HRS::getUserAccesableBranchIds()]])
                                                ->leftJoin('hr_employees as he', 'het.emp_id', '=', 'he.id')
                                                ->get();
                                        @endphp

                                        @foreach ($employeeDataTramsfer as $row)
                                            <option value="{{ $row->id }}">
                                                {{ $row->emp_name . ' [' . $row->emp_code . '] (Transfered)' }}
                                            </option>
                                        @endforeach
                                        
                                    @endif
                                @endif
                            </select>
                        </div>
                    </div>
                @endif

                @if(isset($salesType) && $salesType)
                <div class="col-lg-2">
                    <label class="input-title">Sales Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="sales_type" id="sales_type">
                            <option value="">All</option>
                            <option value="1">Cash Sales</option>
                            <option value="2">Installment Sales</option>
                        </select>
                    </div>
                </div>
                @endif

                {{-- @if(isset($installmentType) && $installmentType)
                <div class="col-lg-2" style="display:{{$instFreq == 'month' || $instFreq == 'week' ? 'none' : 'block'}}">
                    <label class="input-title">Installment Frequency</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="installment_type" id="installment_type">
                            <option value="">All</option>
                            <option value="1" {{$instFreq == 'month' ? 'selected' : ''}}>Monthly</option>
                            <option value="2" {{$instFreq == 'week' ? 'selected' : ''}}>Weekly</option>
                        </select>
                    </div>
                </div>
                @endif --}}

                @if(isset($installmentType) && $installmentType)
                <div class="col-lg-2" id="installment_type_div">
                    <label class="input-title">Installment Frequency</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="installment_type" id="installment_type">
                            <option value="">All</option>
                            <option value="1">Monthly</option>
                            <option value="2">Weekly</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($salesBillNo) && $salesBillNo)
                {{-- <div class="col-lg-2">
                    <label class="input-title">Sales Bill No.</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="sales_bill_no" id="sales_bill_no">
                            <option value="">All</option>
                            @php
                            $saleMasterData = Common::ViewTableOrder('pos_sales_m',
                            [['is_delete', 0], ['is_active', 1]],
                            ['id', 'sales_bill_no'],
                            ['sales_bill_no', 'ASC'])
                            @endphp
                            @foreach ($saleMasterData as $Row)
                            <option value="{{ $Row->sales_bill_no }}">{{ $Row->sales_bill_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                @endif

                @if(isset($issueBillNo) && $issueBillNo)
                {{-- <div class="col-lg-2">
                    <label class="input-title">Issue Bill No</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="issue_bill_no" id="issue_bill_no">
                            <option value="">All</option>
                            @php
                            $issueData = Common::ViewTableOrder('pos_issues_m',
                            [['is_delete', 0], ['is_active', 1]],
                            ['id', 'bill_no'],
                            ['bill_no', 'ASC'])
                            @endphp

                            @foreach ($issueData as $row)
                            <option value="{{ $row->bill_no }}">{{ $row->bill_no}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                @endif

                @if(isset($issueRetBillNo) && $issueRetBillNo)
                {{-- <div class="col-lg-2">
                    <label class="input-title">Issue Return Bill No</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="issue_r_bill_no" id="issue_r_bill_no">
                            <option value="">All</option>
                            @php
                            $issueData = Common::ViewTableOrder('pos_issues_r_m',
                            [['is_delete', 0], ['is_active', 1]],
                            ['id', 'bill_no'],
                            ['bill_no', 'ASC'])
                            @endphp

                            @foreach ($issueData as $row)
                            <option value="{{ $row->bill_no }}">{{ $row->bill_no}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                @endif

                @if(isset($stock) && $stock)
                <div class="col-lg-2">
                    <label class="input-title">Stock</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="stockSearch" id="stockSearch">
                            <option value="0">With Zero</option>
                            <option value="1">Without Zero</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($waiverFor) && $waiverFor)
                <div class="col-lg-2">
                    <label class="input-title">Waiver For</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="waiver_for" id="waiver_for">
                            <option value="">Select All</option>
                            <option value="1">Gift</option>
                            <option value="2">Cancel Product</option>
                            <option value="3">Promotion</option>
                        </select>
                    </div>
                </div>
                @endif
                

                @if(isset($ledger) && $ledger)
                <div class="col-lg-2">
                    <label class="input-title">Ledger</label>
                    <div class="input-group">
                        @php
                        $ledgerData = ACC::getLedgerAccount(-1);
                        @endphp

                        <select class="form-control clsSelect2" name="ledger_id" id="ledger_id">
                            <option value="">Select</option>
                            @foreach ($ledgerData as $Row)
                            <option value="{{ $Row->id }}">{{  $Row->name . " [" . $Row->code . "]"  }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($ledgerCash) && $ledgerCash)
                <div class="col-lg-2">
                    <label class="input-title">Ledger</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="ledger_cash" id="ledger_cash">
                            <option value="">All Cash</option>
                            @php
                            $ledgerCashBook = ACC::getLedgerAccount(-1,null,null,4)
                            @endphp

                            @foreach ($ledgerCashBook as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->name . " [" . $Row->code . "]" }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($ledgerBank) && $ledgerBank)
                <div class="col-lg-2">
                    <label class="input-title">Ledger</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="ledger_bank" id="ledger_bank">
                            <option value="">All Bank</option>
                            @php
                            $ledgerBankBook = ACC::getLedgerAccount(-1,null,null,5)
                            @endphp
                            @foreach ($ledgerBankBook as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->name . " [" . $Row->code ."]" }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($ledgerCashAndBank) && $ledgerCashAndBank)
                <div class="col-lg-2">
                    <label class="input-title">Ledger</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="ledger_cash_bank" id="ledger_cash_bank">
                            <option value="">All</option>
                            @php
                            $ledgerCashBook = ACC::getLedgerAccount(-1,null,null,4);
                            $ledgerBankBook = ACC::getLedgerAccount(-1,null,null,5);
                            $ledgerCashAndBankBook = array_merge($ledgerCashBook->toarray(),$ledgerBankBook->toarray());
                            @endphp
                            @foreach ($ledgerCashAndBankBook as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->name . " [" . $Row->code ."]" }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($typeCashAndBank) && $typeCashAndBank)
                <div class="col-lg-2">
                    <label class="input-title">Report Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="type_cash_bank" id="type_cash_bank">
                            <option value="">Both</option>
                            <option value="1">Cash Book</option>
                            <option value="2">Bank Book</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($voucher) && $voucher)
                <div class="col-lg-2">
                    <label class="input-title">Voucher</label>
                    <select class="form-control clsSelect2" name="v_generate_type" id="v_generate_type">
                        <option value="">All</option>
                        <option value="2">Manual</option>
                        <option value="1">Auto</option>
                    </select>
                </div>
                @endif

                @if(isset($voucherType) && $voucherType)
                <div class="col-lg-2">
                    <label class="input-title">Voucher Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="voucher_type_id" id="voucher_type_id">
                            <option value="">All</option>
                            @php
                            $voucherTypeData = Common::ViewTableOrder('acc_voucher_type',
                            [['is_delete', 0],['is_active', 1]],
                            ['id', 'short_name','name'],
                            ['name', 'ASC'])
                            @endphp

                            @foreach ($voucherTypeData as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($voucherTypeWithoutFT) && $voucherTypeWithoutFT)
                <div class="col-lg-2">
                    <label class="input-title">Voucher Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="voucher_type_id" id="voucher_type_id">
                            <option value="">Select One</option>
                            @php
                            $voucherTypeData = Common::ViewTableOrder('acc_voucher_type',
                            [['is_delete', 0],['is_active', 1], ['id', '!=', 5]],
                            ['id', 'short_name','name'],
                            ['id', 'ASC'])
                            @endphp

                            @foreach ($voucherTypeData as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($accountType) && $accountType)
                <div class="col-lg-2" id="accTypeDiv" style="display:none">
                    <label class="input-title">Account Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="account_type" id="account_type"
                            style="width:100%">
                            <option value="" selected>Select</option>
                            <option value="1">Cash</option>
                            <option value="2">Bank</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($space) && $space)
                <div class="col-lg-2">
                </div>
                @endif

                @if(isset($startDate) && $startDate)
                <div class="col-lg-2">
                    <label class="input-title">Start Date</label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker-custom" id="start_date" name="StartDate"
                            placeholder="DD-MM-YYYY" value="{{$StartDate}}">
                    </div>
                </div>
                @else
                <input type="hidden" id="start_date" name="StartDate">
                @endif

                @if(isset($endDate) && $endDate)
                <div class="col-lg-2">
                    <label class="input-title">End Date</label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker-custom" id="end_date" name="EndDate"
                            placeholder="DD-MM-YYYY" value="{{$EndDate}}">
                    </div>
                </div>
                @else
                <input type="hidden" id="end_date" name="EndDate">
                @endif

                @if (isset($designation) && $designation)
                    {!! HTML::forDesignationFeildSearch('all') !!}
                @endif

                @if (isset($department) && $department)
                    {!! HTML::forDepartmentFeildSearch('all') !!}
                @endif

                @if (isset($gender) && $gender)
                <div class="col-lg-2">
                    <label class="input-title">Gender</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="emp_gender" name="emp_gender">
                            <option value="">Both</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                @endif
                
                @if(isset($applicationStatus) && $applicationStatus)
                    <div class="col-lg-2">
                        <label class="input-title">Status</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" id="appl_status" name="appl_status">
                                <option value="">All</option>
                                <option value="3">Processing</option>
                                <option value="0">Draft</option>
                                <option value="1">Approved</option>
                                <option value="2">Rejected</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if (isset($maritalStatus) && $maritalStatus)
                <div class="col-lg-2">
                    <label class="input-title">Marital Status</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="emp_marital_status">
                            <option value="">Select</option>
                            <option value="Married">Married</option>
                            <option value="Unmarried">Unmarried</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widow">Widow</option>
                        </select>
                    </div>
                </div>
                @endif

                @if (isset($religion) && $religion)
                <div class="col-lg-2">
                    <label class="input-title">Religion</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="emp_religion">
                            <option value="">Select Religion</option>
                            <option value="Islam">Islam</option>
                            <option value="Hinduism">Hinduism</option>
                            <option value="Buddhists">Buddhists</option>
                            <option value="Christians">Christians</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($dateFields))
                    @foreach ($dateFields as $d)
                    <div class="col-lg-2">
                        <label class="input-title">{{ $d['field_text'] }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control datepicker-custom" id="{{ $d['field_id'] }}" name="{{ $d['field_name'] }}"
                                placeholder="DD-MM-YYYY" value="{{ ($d['field_value'] != null) ?  $d['field_value'] : ''}}">
                        </div>
                    </div>
                    @endforeach
                @endif

                @if(isset($employeeStatus) && $employeeStatus)
                <div class="col-lg-2">
                    <label class="input-title">Status</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="emp_status" name="emp_status">
                            <option value="">All</option>
                            <option value="1">Present</option>
                            <option value="2">Resigned</option>
                            <option value="3">Dismissed</option>
                            <option value="4">Terminated</option>
                            <option value="5">Retired</option>
                        </select>
                    </div>
                </div>
                @endif

                @if(isset($textField))
                <div class="col-lg-2">
                    <label class="input-title">{{ $textField['field_text'] }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="{{ $textField['field_id'] }}" name="{{ $textField['field_name'] }}" value="{{ ($textField['field_value'] != null) ?  $textField['field_value'] : ''}}">
                    </div>
                </div>
                @endif


                @if(isset($monthYear) && $monthYear)
                <div class="col-lg-2">
                    <label class="input-title">Month</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="month_year" name="month_year" placeholder="MM-YYYY"
                            autocomplete="off">
                        {{-- monthPicker --}}
                    </div>
                </div>
                @endif

                @if (isset($leaveType) && $leaveType)
                    {!! HTML::forLeaveTypeFeildSearch('all') !!}
                @endif
                
                @if (isset($leaveCat) && $leaveCat)
                    {!! HTML::forLeaveCatFeildSearch('all') !!}
                @endif

                @if(isset($searchBy) && $searchBy)

                <div class="col-lg-2">
                    <label class="input-title">Search By</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="search_by" name="search_by">
                            <option value="">Select</option>
                            <option value="1">Fiscal Year</option>

                            @if(isset($fiscalYearDateRange) && $fiscalYearDateRange)
                            <option value="5">Fiscal Year until Date</option>
                            @endif

                            @if(isset($currentYear) && $currentYear)
                            <option value="2">Current Year</option>
                            @endif
                            @if(isset($dateRange) && $dateRange)
                            <option value="3">Date Range</option>
                            @endif
                            @if(isset($month) && $month)
                            <option value="4">Month Wise</option>
                            @endif

                            
                        </select>
                    </div>
                </div>

                <!-- Select box for fiscal Year [Option 1]-->
                <div class="col-lg-2" style="display: none" id="fyDiv">

                    <label class="input-title">Fiscal Year</label>
                    <div class="input-group">
                        <select class="form-control" name="fiscal_year" id="fiscal_year">
                            <option value="">Select</option>
                            @php
                            $fiscalYearData = Common::ViewTableOrder('gnl_fiscal_year',
                            [['is_delete', 0],['is_active', 1],['company_id', Common::getCompanyId()]],
                            ['id', 'fy_name','fy_start_date','fy_end_date'],
                            ['fy_name', 'ASC']);

                            @endphp
                            @foreach ($fiscalYearData as $Row)
                                @php
                                    $start_date_fy = new DateTime($Row->fy_start_date);
                                    $end_date_fy = new DateTime($Row->fy_end_date);

                                    $loginSystemDate = new DateTime($EndDate);
                                    $loginBranchOpenDate = new DateTime($branchOpenDate);

                                    if($loginBranchOpenDate >= $start_date_fy && $loginBranchOpenDate <= $end_date_fy){
                                        $start_date_fy=$loginBranchOpenDate; 
                                    } 
                                    
                                    if($loginSystemDate>= $start_date_fy && $loginSystemDate <= $end_date_fy){ 
                                        $end_date_fy=$loginSystemDate; 
                                    }
                                @endphp 
                                
                                <option value="{{ $Row->id }}" 
                                    data-startdate="{{ $start_date_fy->format('d-m-Y') }}"
                                    data-enddate="{{ $end_date_fy->format('d-m-Y') }}" 
                                    {{-- data-fy_name="{{ $Row->fy_name }}" --}}
                                    >
                                    {{ $Row->fy_name }}
                                </option>
                                    @endforeach
                        </select>

                        <input type="hidden" name="start_date_fy" id="start_date_fy">
                        <input type="hidden" name="end_date_fy" id="end_date_fy">
                    </div>
                </div>

                <!-- End Date Datepicker for current year [Option 2]--->
                <div class="col-lg-2" style="display: none" id="endDateDivCY">
                    <label class="input-title">Date To</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="end_date_cy" name="end_date_cy"
                            placeholder="DD-MM-YYYY" autocomplete="off">
                    </div>

                    <input type="hidden" name="start_date_cy" id="start_date_cy">
                    <input type="hidden" name="fy_name_cy" id="fy_name_cy">
                </div>

                <!-- Start Date Datepicker for Date Range [Option 3]--->
                <div class="col-lg-2" style="display: none" id="startDateDivDR">
                    <label class="input-title">Start Date</label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker-custom" id="start_date_dr"
                            name="start_date_dr" placeholder="DD-MM-YYYY" autocomplete="off" value="{{$StartDate}}">
                    </div>
                </div>

                <!-- End Date Datepicker for Date Range [Option 3]--->
                <div class="col-lg-2" style="display: none" id="endDateDivDR">
                    <label class="input-title">End Date</label>
                    <div class="input-group">
                        <input type="text" class="form-control datepicker-custom" id="end_date_dr" name="end_date_dr"
                            placeholder="DD-MM-YYYY" autocomplete="off" value="{{$EndDate}}">
                    </div>
                </div>

                <!-- month Datepicker for Date Range [Option 4]--->
                <div class="col-lg-2" style="display: none" id="monthDateDivDR">
                    <label class="input-title">Month</label>
                    <div class="input-group">
                        <input type="text" class="form-control monthPicker" id="month_yr" name="month_yr"
                            placeholder="MM-YYYY" autocomplete="off">
                    </div>
                </div>

                @endif

                @if(isset($reportViewOption) && $reportViewOption)
                    <div class="col-lg-2" id="reportViewOptionDiv">
                        <label class="input-title">Report Format</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="view_option" id="view_option">
                                <option value="1">Single Part</option>
                                <option value="2">Double Part</option>
                            </select>
                        </div>
                    </div>
                @endif

                @if(isset($reportFormatting) && count($reportFormatting)>0)
                    <div class="col-lg-2">
                        <label class="input-title">Report Formatting</label>
                        <select class="form-control clsSelect2" name="report_formatting" id="report_formatting">
                            <option value="">Default</option>
                            @foreach ($reportFormatting as $item)
                            <option value="{{$item['value']}}">{{$item['name']}}</option>
                            @endforeach
                            {{-- @if(isset($groupByProduct) && $groupByProduct)
                                <option value="{{ $item['value'] }}">{{ $item['name'] }}</option>
                            @endforeach
                            {{-- @if (isset($groupByProduct) && $groupByProduct)
                            <option value="product_id">Group By Product</option>
                            @else
                            <option value="employee_id">Group By Employee</option>
                            <option value="customer_id">Group By Customer</option>
                            @endif --}}
                        </select>
                    </div>
                @endif

                @if(isset($reportHeader) && $reportHeader)
                    <div class="col-lg-2">
                        <label class="input-title">Report Header</label>
                        <select class="form-control clsSelect2" name="report_header" id="report_header">
                            <option value="">With Header</option>
                            <option value="without" selected>Without Header</option>
                        </select>
                    </div>
                @endif

            </div>

            <!-- Inventory Filter Options End -->

            <div class="row text-center pt-10 pb-10">
                <div class="col-lg-12">
                    
                    @if (isset($submit) && $submit)
                    <button type="submit"class="btn btn-primary btn-round text-uppercase" 
                        style="width: 10%; font-size:16px;">
                        <i class="fa fa-search" aria-hidden="true"></i>&nbsp;Show
                    </button>
                    
                    @else
                    <a href="javascript:void(0);" id="searchButton" class="btn btn-primary btn-round text-uppercase"
                        style="width: 10%; font-size:16px;">
                        <i class="fa fa-search" aria-hidden="true"></i>&nbsp;Show
                    </a>
                    
                    @endif

                    @if (isset($refresh) && $refresh)
                        <a href="javascript:void(0);" id="refreshButton" class="btn btn-danger btn-round text-uppercase"
                            style="width: 10%; font-size:16px;">
                            <i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;Refresh
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('elements/report/report_script')
