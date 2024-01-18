@php
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use App\Services\HtmlService as HTML;
@endphp

<div class="row align-items-center pb-10 d-print-none">

    {{-- Zone Start --}}
    {{--
        asob filter element or option old tai aikhane new modification er proyojon nai. New Filter option a modification kora hocche.
        jesob jaygay akhono use kora hocche sekhane jate error ba region load hoy sejonno zone er under a dewa holo.

        new common filter option sob jaygay implement kora hole ai code gulo gurbage hoye jabe. tai new modification dewar dorkar nai.
    --}}

    @if (isset($zone) && $zone)
        {!! HTML::forZoneFeildSearch('all', 'zone_id', 'zone_id', 'Zone', null) !!}

        {!! HTML::forRegionFeildSearch('all', 'region_id', 'region_id', 'Region', null) !!}
    @endif

    @if (isset($zoneFrom) && $zoneFrom)
        {!! HTML::forZoneFeildSearch('all', 'zone_from', 'zone_from', 'Zone From', null) !!}

        {!! HTML::forRegionFeildSearch('all', 'region_from', 'region_from', 'Region From', null) !!}
    @endif

    @if (isset($zoneTo) && $zoneTo)
        {!! HTML::forZoneFeildSearch('all', 'zone_to', 'zone_to', 'Zone To', null) !!}

        {!! HTML::forRegionFeildSearch('all', 'region_to', 'region_to', 'Region To', null) !!}
    @endif
    {{-- Zone End --}}

    {{-- Area Start --}}
    @if (isset($area) && $area)
        {!! HTML::forAreaFeildSearch('all', 'area_id', 'area_id', 'Area', null) !!}
    @endif

    @if (isset($areaFrom) && $areaFrom)
        {!! HTML::forAreaFeildSearch('all', 'area_from', 'area_from', 'Area From', null) !!}
    @endif

    @if (isset($areaTo) && $areaTo)
        {!! HTML::forAreaFeildSearch('all', 'area_to', 'area_to', 'Area To', null) !!}
    @endif
    {{-- Area End --}}

    @if (isset($branch) && $branch)
        @php $withHeadOffice = (isset($withHeadOffice)) ? $withHeadOffice : false; @endphp
        {!! HTML::forBranchFeildSearch_new('all', 'branch_id', 'branch_id', 'Branch', null, null, $withHeadOffice) !!}
    @endif

    @if (isset($branchFrom) && $branchFrom)
        {!! HTML::forBranchFeildSearch_new('all', 'branch_from', 'branch_from', 'Branch From', null) !!}
    @endif

    @if (isset($branchTo) && $branchTo)
        {!! HTML::forBranchToFeildSearch_new('all', 'branch_to', 'branch_to', 'Branch To', null) !!}
    @endif

    @if (isset($payscale) && $payscale)
    <div class="col-lg-2">
        <label class="input-title">Payscale</label>
        <div class="input-group">
            {!! HTML::forPayscaleFieldHr('payscale_id', 'payscale_id') !!}
        </div>
    </div>
    @endif

    @if (isset($grade) && $grade)
    <div class="col-lg-2">
        <label class="input-title">Grade</label>
        <div class="input-group">
            {!! HTML::forGradeFieldHr() !!}
        </div>
    </div>
    @endif

    @if (isset($level) && $level)
    <div class="col-lg-2">
        <label class="input-title">Level</label>
        <div class="input-group">
            {!! HTML::forLevelFieldHr() !!}
        </div>
    </div>
    @endif




    @if (isset($recruitment_type) && $recruitment_type)
    <div class="col-lg-2">
        <label class="input-title">Recruitment Type</label>
        <div class="input-group">
            {!! HTML::forRecruitmentFieldHr() !!}
        </div>
    </div>
    @endif



    @if (isset($reqFrom) && $reqFrom)
        {!! HTML::forBranchFeildSearch_new('all', 'branch_id', 'branch_id', 'Requisition From', null) !!}
    @endif

    @if (isset($deliveryPlace) && $deliveryPlace)
        {!! HTML::forBranchFeildSearch_new('all', 'branch_id', 'branch_id', 'Delivery Place', null) !!}
    @endif

    @if (isset($designation) && $designation)
        {!! HTML::forDesignationFeildSearch('all') !!}
    @endif

    @if (isset($department) && $department)
        {!! HTML::forDepartmentFeildSearch('all') !!}
    @endif

    @if (isset($leaveCategory) && $leaveCategory)
    <div class="col-lg-2">
        <label class="input-title">Leave Category</label>
        <div class="input-group">
            {!! HTML::forLeaveCategoryHr('leave_cat_id','leave_cat_id') !!}
        </div>
    </div>
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

    @if(isset($textField))
    <div class="col-lg-2">
        <label class="input-title">{{ $textField['field_text'] }}</label>
        <div class="input-group">
            <input type="text" class="form-control" id="{{ $textField['field_id'] }}" name="{{ $textField['field_name'] }}" value="{{ ($textField['field_value'] != null) ?  $textField['field_value'] : ''}}">
        </div>
    </div>
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


    @if (isset($userRole) && $userRole)
        <div class="col-lg-2">
            <label class="input-title">
                User Role
            </label>

            <div class="input-group">
                @php
                    $roleData = DB::table('gnl_sys_user_roles')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $permitRoleIdArr)
                    ->orderBy('order_by', 'ASC')
                    ->select('id', 'role_name')
                    ->get();
                @endphp
                <select class="form-control clsSelect2" name="user_role_id" id="user_role_id">
                    <option value="">All</option>
                    @foreach ($roleData as $row)
                        <option value="{{ $row->id }}">{{ $row->role_name }} </option>
                    @endforeach
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

                    <option value="">Select All</option>
                    @php
                        $PGroupList = Common::ViewTableOrder('pos_p_groups', [['is_delete', 0]], ['id', 'group_name'], ['group_name', 'ASC']);
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
                <select class="form-control clsSelect2" name="category_id" id="category_id"
                    onchange="fnAjaxGetSubCat(); fnAjaxGetModel(); fnAjaxGetProduct();">
                    <option value="">Select All</option>
                    {{-- @php
                    $CategoryList = Common::ViewTableOrder('pos_p_categories',
                    [['is_delete', 0]],
                    ['id', 'cat_name'],
                    ['cat_name', 'ASC'])
                @endphp

                @foreach ($CategoryList as $Row)
                <option value="{{ $Row->id }}">{{ $Row->cat_name }}</option>
                @endforeach --}}
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <label class="input-title">Sub Category</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="sub_cat_id" id="sub_cat_id"
                    onchange="fnAjaxGetModel(); fnAjaxGetProduct();">
                    <option value="">Select All</option>
                    {{-- @php
                    $SubCatList = Common::ViewTableOrder('pos_p_subcategories',
                    [['is_delete', 0]],
                    ['id', 'sub_cat_name'],
                    ['sub_cat_name', 'ASC'])
                @endphp

                @foreach ($SubCatList as $Row)
                <option value="{{ $Row->id }}">{{ $Row->sub_cat_name }}</option>
                @endforeach --}}
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <label class="input-title">Brand</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="brand_id" id="brand_id" onchange="fnAjaxGetProduct();">
                    <option value="">Select All</option>
                    @php
                        $BrandList = Common::ViewTableOrder('pos_p_brands', [['is_delete', 0]], ['id', 'brand_name'], ['brand_name', 'ASC']);
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
                <select class="form-control clsSelect2" name="model_id" id="model_id" onchange="fnAjaxGetProduct();">
                    <option value="">Select All</option>
                    {{-- @php
                    $modelData = Common::ViewTableOrder('pos_p_models',
                    [['is_delete', 0]],
                    ['id', 'model_name'],
                    ['id', 'ASC'])
                @endphp
                @foreach ($modelData as $model)
                <option value="{{ $model->id }}">{{ $model->model_name }}</option>
                @endforeach --}}
                </select>
            </div>
        </div>
    @endif

    @if (isset($carat) && $carat)
        <div class="col-lg-2 carat" style="display: none">
            <label class="input-title">carat</label>
            <div class="input-group">
                <select class="form-control clsSelect2" id="carat_id" name="carat_id">
                    <option value="">Select All</option>
                    <option value="1">21</option>
                    <option value="2">22</option>
                    <option value="3">20</option>
                    <option value="4">19</option>
                    <option value="5">24</option>
                </select>
            </div>
        </div>
    @endif

    @if (isset($product) && $product)
        <div class="col-lg-2">
            <label class="input-title">{{isset($product[0]['title'])? $product[0]['title'] : "Name Or Code"}}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="product_id" id="productorcode" placeholder="Enter {{isset($product[0]['title'])? $product[0]['title'] : "Name Or Code"}}" >
            </div>
    </div>
    @endif

    {{-- backup Prv Product Filter  kaj sesh hoya gele delete kore dite hbe--}}
    {{-- @if (isset($product) && $product)
        <div class="col-lg-2">
            <label class="input-title">Product</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="product_id" id="product_id">
                    <option value="">Select All</option> --}}
                    {{-- @php //## Product Query
                    $productData = Common::ViewTableOrder('pos_products',
                    [['is_delete', 0]],
                    ['id', 'product_name', 'prod_barcode'],
                    ['product_name', 'ASC'])
                @endphp

                @foreach ($productData as $row)
                <option value="{{ $row->id }}">
                    {{ $row->product_name." (".$row->prod_barcode.")" }}
                </option>
                @endforeach --}}
                {{-- </select>
            </div>
        </div>
    @endif --}}

    @if (Common::getBranchId() == 1)
        @if (isset($supplier) && $supplier)
            <div class="col-lg-2">
                <label class="input-title">Supplier</label>
                <div class="input-group">
                    <select class="form-control clsSelect2" name="supplier_id" id="supplier_id">
                        <option value="">Select All</option>
                        @php
                            $supplierList = Common::ViewTableOrder('pos_suppliers', [['is_delete', 0]], ['id', 'sup_comp_name'], ['sup_name', 'ASC']);
                        @endphp

                        @foreach ($supplierList as $Row)
                            <option value="{{ $Row->id }}">{{ $Row->sup_comp_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    @endif

    @if (isset($customer) && $customer)
        <div class="col-lg-2">
            <label class="input-title">Customer</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="customer_id" id="customer_id">
                    <option value="">Select All</option>
                    @php
                        $customerData = Common::ViewTableOrderIn('pos_customers', [['is_delete', 0]], ['branch_id', HRS::getUserAccesableBranchIds()], ['id', 'customer_no', 'customer_name'], ['customer_name', 'ASC']);
                    @endphp
                    @foreach ($customerData as $row)
                        <option value="{{ $row->customer_no }}">
                            {{ $row->customer_name . " [" . $row->customer_no."]" }}
                        </option>
                    @endforeach

                </select>
            </div>
        </div>
    @endif

    @if ((isset($employee) && $employee) ||  (isset($paymentBy) && $paymentBy))
        <div class="col-lg-2">
            <label class="input-title">
                @if (isset($employeeFieldLabel))
                    {{ $employeeFieldLabel }}
                @elseif(isset($paymentBy) && $paymentBy)
                    Payment By
                @else
                    Sales By
                @endif
            </label>

            <div class="input-group">
                <select class="form-control clsSelect2" name="employee_id" id="employee_id">
                    <option value="">All</option>
                    @php
                        $employeeData = DB::table('hr_employees')
                            ->where([['is_delete', 0]])
                            ->whereIn('branch_id', HRS::getUserAccesableBranchIds())
                            ->orderBy('emp_code', 'ASC')
                            ->get();
                    @endphp

                    @if(Common::getModuleByRoute() == "pos")
                        @foreach ($employeeData as $row)
                            <option value="{{ $row->employee_no }}">
                                {{ $row->emp_name . ' [' . $row->emp_code . ']' }}
                            </option>
                        @endforeach
                    @else
                        @foreach ($employeeData as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->emp_name . ' [' . $row->emp_code . ']' }}
                            </option>
                        @endforeach
                    @endif

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

                            @if(Common::getModuleByRoute() == "pos")
                                @foreach ($employeeDataTramsfer as $row)
                                    <option value="{{ $row->employee_no }}">
                                        {{ $row->emp_name . ' [' . $row->emp_code . '] (Transfered)' }}
                                    </option>
                                @endforeach
                            @else
                                @foreach ($employeeDataTramsfer as $row)
                                    <option value="{{ $row->id }}">
                                        {{ $row->emp_name . ' [' . $row->emp_code . '] (Transfered)' }}
                                    </option>
                                @endforeach
                            @endif
                        @endif
                    @endif
                </select>
            </div>
        </div>
    @endif

    {{-- @if (isset($salesBillNo) && $salesBillNo)
        <div class="col-lg-2">
            <label class="input-title">Sales Bill No.</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="sales_bill_no" id="sales_bill_no">
                    <option value="">Select All</option>
                    @php
                        $saleMasterData = Common::ViewTableOrder('pos_sales_m', [['is_delete', 0], ['is_active', 1]], ['id', 'sales_bill_no'], ['sales_bill_no', 'ASC']);
                    @endphp
                    @foreach ($saleMasterData as $Row)
                        <option value="{{ $Row->sales_bill_no }}">{{ $Row->sales_bill_no }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif --}}

    {{-- @if (isset($purchaseNo) && $purchaseNo)
        <div class="col-lg-2">
            <label class="input-title">Purchase No</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="purchase_id" id="purchase_id">
                    <option value="">Select All</option>
                    @php
                        $PurchaseNoList = Common::ViewTableOrder('pos_purchases_m', [['is_delete', 0], ['is_active', 1], ['bill_no', '!=', '']], ['id', 'bill_no'], ['bill_no', 'ASC']);
                    @endphp
                    @foreach ($PurchaseNoList as $Row)
                        <option value="{{ $Row->bill_no }}">{{ $Row->bill_no }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif --}}

    {{-- @if (isset($paymentBillNo) && $paymentBillNo)
        <div class="col-lg-2">
            <label class="input-title">Payment Bill No</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="payment_no" id="payment_no">
                    <option value="">Select All</option>
                    @php
                        $supplierPaymentData = Common::ViewTableOrder('pos_supplier_payments', [['is_delete', 0], ['is_active', 1]], ['id', 'payment_no'], ['payment_no', 'ASC']);
                    @endphp
                    @foreach ($supplierPaymentData as $Row)
                        <option value="{{ $Row->payment_no }}">{{ $Row->payment_no }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif --}}



    @if (isset($IssueFromTo) && $IssueFromTo)
        <div class="col-lg-2">
            <label class="input-title">Issue From/To</label>
            <select class="form-control clsSelect2" name="t_type" id="t_type">
                <option value="">Select All</option>
                <option value="1">Issue From</option>
                <option value="2">Issue To</option>
            </select>
        </div>
    @endif

    @if (isset($ReturnFromTo) && $ReturnFromTo)
        <div class="col-lg-2">
            <label class="input-title">Return From/To</label>
            <select class="form-control clsSelect2" name="t_type" id="t_type">
                <option value="">Select All</option>
                <option value="1">Return From</option>
                <option value="2">Return To</option>
            </select>
        </div>
    @endif

    @if (isset($InstallmentType) && $InstallmentType)
        <div class="col-lg-2">
            <label class="input-title">Inst. Type</label>
            <select class="form-control clsSelect2" name="installment_type_id" id="installment_type_id">
                <option value="">Select one</option>
                @php
                    $InstallmentMonthData = Common::ViewTableOrder('gnl_installment_type', [['is_active', 1]], ['id', 'name'], ['id', 'ASC']);
                @endphp

                @foreach ($InstallmentMonthData as $Row)
                    <option value="{{ $Row->id }}">{{ $Row->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if (isset($InstallmentPackage) && $InstallmentPackage)
        <div class="col-lg-2">
            <label class="input-title">Inst. Package</label>
            <select class="form-control clsSelect2" name="installment_pack_id" id="installment_pack_id">
                <option value="">Select one</option>
                @php
                    $InstallmentPackData = Common::ViewTableOrder('pos_inst_packages', [['is_active', 1]], ['id', 'prod_inst_month'], ['id', 'ASC']);
                @endphp

                @foreach ($InstallmentPackData as $Row)
                    <option value="{{ $Row->id }}">{{ $Row->prod_inst_month }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if (isset($transactions) && $transactions)
        <div class="col-lg-2">
            <label class="input-title">Transaction Type</label>
            <select class="form-control clsSelect2" name="transaction_type" id="transaction_type">
                <option value="">All</option>
                <option value="collection">Collection</option>
                <option value="sales">Sales</option>
                <option value="salesreturn">Sales Return</option>
                <option value="transfer">Transfer</option>
                <option value="issue">Issue</option>
                <option value="issuereturn">Issue Return</option>
            </select>
        </div>
    @endif

    @if (isset($isActive) && $isActive)
        <div class="col-lg-2">
            <label class="input-title">Status</label>
            <select class="form-control clsSelect2" name="userStatus" id="userStatus">
                <option value="All">All</option>
                <option value="1">Active</option>
                <option value="0">In-Active</option>
            </select>
        </div>
    @endif

    @if (isset($startDate) && $startDate)
        <div class="col-lg-2">
            <label class="input-title">Start Date</label>
            <div class="input-group">
                <input type="text" class="form-control datepicker-custom" id="start_date" name="start_date"
                    placeholder="DD-MM-YYYY">
            </div>
        </div>
    @endif

    @if (isset($endDate) && $endDate)
        <div class="col-lg-2">
            <label class="input-title">End Date</label>
            <div class="input-group">
                <input type="text" class="form-control datepicker-custom" id="end_date" name="end_date"
                    placeholder="DD-MM-YYYY">
            </div>
        </div>
    @endif

    <!-- <div class="col-lg-2 pt-20 text-center">
        <a href="javascript:void(0)" class="btn btn-primary btn-round" id="searchFieldBtn">Search</a>
    </div> -->
    @include('elements.button.common_button', [
    'search' => [
    'action' => true,
    'title' => 'search',
    'id' => 'searchFieldBtn',
    'exClass' => 'float-right'
    ]
    ])

    {{-- @if (isset($submit) && $submit)
    <div class="col-lg-2 text-center">
        <div class="input-group">
            <button type='submit' class="btn btn-primary btn-round text-uppercase float-right mt-4">
                Show
            </button>
        </div>
    </div>
    @else
    <div class="col-lg-2 pt-20 text-center">
        <a href="javascript:void(0)" class="btn btn-primary btn-round" id="searchFieldBtn">Search</a>
    </div>
    @endif --}}
</div>

<script>
    var groupG = "{{ isset($group) && $group ? 1 : 0 }}";
    var modelG = "{{ isset($model) && $model ? 1 : 0 }}";
    var productG = "{{ isset($product) && $product ? 1 : 0 }}";

    $(document).ready(function() {
        if (groupG == 1) {
            fnAjaxGetCategory();
            fnAjaxGetSubCat();
        }

        if (modelG == 1) {
            fnAjaxGetModel();
        }

        if (productG == 1) {
            fnAjaxGetProduct();
        }
    });

    $('#group_id').change(function (){
        if($('#group_id').val() == 6) {
            $('.carat').show('slow');
        } else {
            $('.carat').hide('slow');
        }
    });

    function fnAjaxGetRegion() {
        var zoneId = $('#zone_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetRegion') }}",
            dataType: "json",
            async:false,
            data: {
                zoneId: zoneId,
                returnFor: 'search'
            },
            success: function(data) {
                // console.log(data);

                if (data) {
                    $('#region_id').empty().html(data);
                }
            }
        });
    }

    function fnAjaxGetArea() {
        var zoneId = $('#zone_id').val();
        var regionId = $('#region_id').val();

        // if (zoneId != null) {
            $.ajax({
                method: "GET",
                url: "{{ url('ajaxGetArea') }}",
                dataType: "text",
                data: {
                    zoneId: zoneId,
                    regionId: regionId,
                    returnFor: 'search'
                },
                success: function(data) {
                    // console.log(data);

                    if (data) {
                        $('#area_id').empty().html(data);
                    }
                }
            });
        // }
    }

    function fnAjaxGetBranch() {

        var zoneId = $('#zone_id').val();
        var regionId = $('#region_id').val();
        var areaId = $('#area_id').val();

        // if (areaId != null || zoneId != null) {
            $.ajax({
                method: "GET",
                url: "{{ url('ajaxGetBranch') }}",
                dataType: "text",
                data: {
                    areaId: areaId,
                    regionId:regionId,
                    zoneId: zoneId,
                    returnFor: 'search'
                },
                success: function(data) {
                    if (data) {
                        $('#branch_id').empty().html(data);
                    }
                }
            });
        // }
    }

    function fnAjaxGetCategory() {
        var groupId = $('#group_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetCategory') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#category_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#category_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetSubCat() {
        var groupId = $('#group_id').val();
        var categoryId = $('#category_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetSubCat') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#sub_cat_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#sub_cat_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetModel() {
        var groupId = $('#group_id').val();
        var categoryId = $('#category_id').val();
        var subCatId = $('#sub_cat_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetModel') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#model_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#model_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                }
            }
        });
    }

    function fnAjaxGetProduct() {
        var groupId = $('#group_id').val();
        var categoryId = $('#category_id').val();
        var subCatId = $('#sub_cat_id').val();
        var brandId = $('#brand_id').val();
        var modelId = $('#model_id').val();

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxGetProduct') }}",
            dataType: "json",
            data: {
                groupId: groupId,
                categoryId: categoryId,
                subCatId: subCatId,
                brandId: brandId,
                modelId: modelId,
                isActive: 1
            },
            success: function(response) {

                if (response['status'] == 'success') {

                    var result_data = response['result_data'];

                    $('#product_id').empty().append($('<option>', {
                        value: "",
                        text: "Select All"
                    }));

                    $.each(result_data, function(i, item) {

                        $('#product_id').append($('<option>', {
                            value: item.field_id,
                            text: item.field_name
                        }));

                    });

                }
            }
        });
    }
</script>
