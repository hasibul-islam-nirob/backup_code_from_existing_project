<?php

namespace App\Services;

use Picqer;
use DateTime;
// use App\Model\GNL\Branch;
// use App\Model\POS\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\Config;

class PosService
{
    public function __construct()
    {
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
    }

    /** Start function for report join query & search */
    public static function fnForBranchZoneAreaWise($branchId = null, $zoneId = null, $areaId = null, $companyID = null)
    {
        $selBranchArr = Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId, $companyID);

        return $selBranchArr;
    }

    public static function fnForBranchData($branchArr = [])
    {
        $branchData = Common::fnForBranchData($branchArr);

        return $branchData;
    }

    public static function fnForEmployeeData($employeeArr = [])
    {
        return HRS::fnForEmployeeData($employeeArr, $posModule = true);
    }

    public static function fnForProductSettingsWise(
        $productId = null,
        $groupId = null,
        $catId = null,
        $subCatId = null,
        $brandId = null,
        $modelId = null,
        $supplierId = null,
        $companyID = null,
        $productTxt = null
    ) {

        $selectProduct = array();

        if (!empty($productId)) {
            if (gettype($productId) == 'array') {
                $selectProduct = $productId;
            } else {
                $selectProduct = [$productId];
            }
        } else {
            $productQuery = DB::table('pos_products')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($productQuery) use ($companyID) {
                    if (!empty($companyID)) {
                        $productQuery->where('company_id', $companyID);
                    }
                })
                ->where(function ($productQuery) use ($groupId) {
                    if (!empty($groupId)) {
                        $productQuery->where('prod_group_id', $groupId);
                    }
                })
                ->where(function ($productQuery) use ($catId) {
                    if (!empty($catId)) {
                        $productQuery->where('prod_cat_id', $catId);
                    }
                })
                ->where(function ($productQuery) use ($subCatId) {
                    if (!empty($subCatId)) {
                        $productQuery->where('prod_sub_cat_id', $subCatId);
                    }
                })
                ->where(function ($productQuery) use ($brandId) {
                    if (!empty($brandId)) {
                        $productQuery->where('prod_brand_id', $brandId);
                    }
                })
                ->where(function ($productQuery) use ($modelId) {
                    if (!empty($modelId)) {
                        $productQuery->where('prod_model_id', $modelId);
                    }
                })
                ->where(function ($productQuery) use ($supplierId) {
                    if (!empty($supplierId)) {
                        $productQuery->where('supplier_id', $supplierId);
                    }
                })
                ->where(function ($productQuery) use ($productTxt) {
                    if (!empty($productTxt)) {

                        $productQuery->where('product_name', 'LIKE', "%{$productTxt}%")
                            ->orWhere('prod_barcode', 'LIKE', "%{$productTxt}%")
                            ->orWhere('sys_barcode', 'LIKE', "%{$productTxt}%")
                            ->orWhere('cost_price', 'LIKE', "%{$productTxt}%")
                            ->orWhere('sale_price', 'LIKE', "%{$productTxt}%")
                            ->orWhere('serial_barcode', 'LIKE', "%{$productTxt}%");
                    }
                })
                ->pluck('id')
                ->toArray();

            $selectProduct = (!empty($productQuery) && count($productQuery) > 0) ? $productQuery : array();
        }

        return $selectProduct;
    }

    public static function fnForModelData($modelId = null, $groupId = null, $catId = null, $subCatId = null)
    {
        $selectModelArray = array();

        $modelData = DB::table('pos_p_models')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where(function ($modelData) use ($groupId) {
                if (!empty($groupId)) {
                    $modelData->where('prod_group_id', $groupId);
                }
            })
            ->where(function ($modelData) use ($catId) {
                if (!empty($catId)) {
                    $modelData->where('prod_cat_id', $catId);
                }
            })
            ->where(function ($modelData) use ($subCatId) {
                if (!empty($subCatId)) {
                    $modelData->where('prod_sub_cat_id', $subCatId);
                }
            })
            ->where(function ($modelData) use ($modelId) {
                if (!empty($modelId)) {
                    $modelData->where('id', $modelId);
                }
            })
            ->selectRaw('model_name, id')
            ->pluck('model_name', 'id')
            ->toArray();

        $selectModelArray = (!empty($modelData) && count($modelData) > 0) ? $modelData : array();

        return $selectModelArray;
    }

    public static function fnForCustomerData($customerArr = [])
    {
        $customerData = array();
        if (count($customerArr) > 0) {

            if (Common::getDBConnection() == "sqlite") {
                $customerData = DB::table('pos_customers')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('customer_no', $customerArr)
                    ->selectRaw('(customer_name || " [" || customer_no || "]") AS customer_name, customer_no')
                    ->pluck('customer_name', 'customer_no')
                    ->toArray();
            } else {
                $customerData = DB::table('pos_customers')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('customer_no', $customerArr)
                    ->selectRaw('CONCAT(customer_name, " [", customer_no, "]") AS customer_name, customer_no')
                    ->pluck('customer_name', 'customer_no')
                    ->toArray();
            }
            // // This query is return array[key as customer_no] = value as a customer_name
        }

        return $customerData;
    }

    public static function getAllCustomer()
    {

        if (Common::getDBConnection() == "sqlite") {
            $customerData = DB::table('pos_customers')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->selectRaw('(customer_name || " [M:" || customer_mobile || " NID:" || customer_nid || "]") AS customer_name, id')
                ->pluck('customer_name', 'id')
                ->toArray();
        } else {
            $customerData = DB::table('pos_customers')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->selectRaw('CONCAT(customer_name, " [M:", customer_mobile, "- NID:", customer_nid, "]") AS customer_name, id')
                ->pluck('customer_name', 'id')
                ->toArray();
        }

        return $customerData;
    }

    public static function fnForCustomerAddress($customerAddressData = [])
    {
        $transformedArray = [];

        foreach ($customerAddressData as $key => $value) {
            $transformedArray[$key] = $value;
        }

        $divisionData = DB::table('gnl_divisions')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', $transformedArray['division'])
            ->pluck('division_name', 'id')
            ->toArray();

        $districtData = DB::table('gnl_districts')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', $transformedArray['district'])
            ->pluck('district_name', 'id')
            ->toArray();

        $upazillaData = DB::table('gnl_upazilas')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', $transformedArray['upazilla'])
            ->pluck('upazila_name', 'id')
            ->toArray();

        $unionData = DB::table('gnl_unions')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', $transformedArray['union'])
            ->pluck('union_name', 'id')
            ->toArray();

        $villData = DB::table('gnl_villages')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', $transformedArray['village'])
            ->pluck('village_name', 'id')
            ->toArray();

        $addressString = '';

        if (isset($transformedArray['village'])) {
            $villageId = $transformedArray['village'];
            if (isset($villData[$villageId])) {
                $addressString .= $villData[$villageId];
            }
        }

        if (isset($transformedArray['union'])) {
            $unionId = $transformedArray['union'];
            if (isset($unionData[$unionId])) {
                $addressString .= (!empty($addressString) ? ', ' : '') . $unionData[$unionId];
            }
        }

        if (isset($transformedArray['upazilla'])) {
            $upazillaId = $transformedArray['upazilla'];
            if (isset($upazillaData[$upazillaId])) {
                $addressString .= (!empty($addressString) ? ', ' : '') . $upazillaData[$upazillaId];
            }
        }

        if (isset($transformedArray['district'])) {
            $districtId = $transformedArray['district'];
            if (isset($districtData[$districtId])) {
                $addressString .= (!empty($addressString) ? ', ' : '') . $districtData[$districtId];
            }
        }

        if (isset($transformedArray['division'])) {
            $divisionId = $transformedArray['division'];
            if (isset($divisionData[$divisionId])) {
                $addressString .= (!empty($addressString) ? ', ' : '') . $divisionData[$divisionId];
            }
        }

        return $addressString;
    }

    public static function fnForCustomerDataWithMobile($customerArr = [])
    {
        $customerData = array();
        if (count($customerArr) > 0) {

            if (Common::getDBConnection() == "sqlite") {
                $customerData = DB::table('pos_customers')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('customer_no', $customerArr)
                    ->selectRaw('(customer_name || " [" || customer_mobile || "]") AS customer_name, customer_no')
                    ->pluck('customer_name', 'customer_no')
                    ->toArray();
            } else {
                $customerData = DB::table('pos_customers')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('customer_no', $customerArr)
                    ->selectRaw('CONCAT(customer_name, " [", customer_mobile, "]") AS customer_name, customer_no')
                    ->pluck('customer_name', 'customer_no')
                    ->toArray();
            }
            // // This query is return array[key as customer_no] = value as a customer_name
        }

        return $customerData;
    }

    public static function fnForProductData($productArr = [], $code = true)
    {
        $productData = array();
        if (count($productArr) > 0) {

            if (Common::getDBConnection() == "sqlite") {
                $productData = DB::table('pos_products')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $productArr)
                    ->when($code, function ($query) {
                        $query->selectRaw('(product_name || " [" || prod_barcode || "]") AS product_name, id');
                    })
                    ->pluck('product_name', 'id')
                    ->toArray();
            } else {
                $productData = DB::table('pos_products')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $productArr)
                    ->when($code, function ($query) {
                        $query->selectRaw('CONCAT(product_name, " [", prod_barcode, "]") AS product_name, id');
                    })
                    ->pluck('product_name', 'id')
                    ->toArray();
            }
        }

        return $productData;
    }

    public static function fnForCollectionData($salesBillArr = [])
    {
        $collectionData = array();
        if (count($salesBillArr) > 0) {
            $collectionData = DB::table('pos_collections')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('sales_bill_no', $salesBillArr)

                ->groupBy('sales_bill_no')
                ->selectRaw('IFNULL(SUM(collection_amount), 0) AS total_collection_amount, sales_bill_no')
                ->pluck('total_collection_amount', 'sales_bill_no')
                ->toArray();
        }

        return $collectionData;
    }

    public static function fnForSupplierData($supplierArr = [])
    {
        $supplierData = array();
        if (count($supplierArr) > 0) {
            $supplierData = DB::table('pos_suppliers')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $supplierArr)
                ->selectRaw('sup_comp_name, id')
                ->pluck('sup_comp_name', 'id')
                ->toArray();
        }

        return $supplierData;
    }

    public static function fnForProdGroup($groupArr = [])
    {
        $groupData = array();
        if (count($groupArr) > 0) {
            $groupData = DB::table('pos_p_groups')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $groupArr)
                ->selectRaw('group_name, id')
                ->pluck('group_name', 'id')
                ->toArray();
        }

        return $groupData;
    }

    public static function fnForProdCategory($catArr = [])
    {
        $catData = array();
        if (count($catArr) > 0) {
            $catData = DB::table('pos_p_categories')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $catArr)
                ->selectRaw('cat_name, id')
                ->pluck('cat_name', 'id')
                ->toArray();
        }

        return $catData;
    }

    public static function fnForProdSubCategory($subCatArr = [])
    {
        $subCatData = array();
        if (count($subCatArr) > 0) {
            $subCatData = DB::table('pos_p_subcategories')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $subCatArr)
                ->selectRaw('sub_cat_name, id')
                ->pluck('sub_cat_name', 'id')
                ->toArray();
        }

        return $subCatData;
    }

    public static function fnForProdBrand($brandArr = [])
    {
        $brandData = array();
        if (count($brandArr) > 0) {
            $brandData = DB::table('pos_p_brands')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $brandArr)
                ->selectRaw('brand_name, id')
                ->pluck('brand_name', 'id')
                ->toArray();
        }

        return $brandData;
    }

    public static function fnForProdModel($modelArr = [])
    {
        $modelData = array();
        if (count($modelArr) > 0) {
            $modelData = DB::table('pos_p_models')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('id', $modelArr)
                ->selectRaw('model_name, id')
                ->pluck('model_name', 'id')
                ->toArray();
        }

        return $modelData;
    }

    public static function fnGetProcessingFee($amount)
    {

        $masterQuery = DB::table('pos_processing_fee')
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->where(function ($masterQuery) use ($amount) {

                $masterQuery->where([['start_amt', '<=', $amount], ['end_amt', '>', $amount]])
                    ->orWhere([['start_amt', '<=', $amount]]);
            })
            ->orderBy('start_amt', 'DESC')
            ->first();

        $pro_fee = (!empty($masterQuery)) ? $masterQuery->amount : 0;

        return $pro_fee;
    }

    public static function fnGetDiscount($req)
    {

        $data = self::fnCalculateProductDiscount($req);

        if ($data === false) {
            $data = self::fnCalculateBillDiscount($req);
        }

        if ($data === false) {
            $data = self::fnCalculateRegularDiscount($req);
        }

        // dd($data);

        return $data;
    }

    public static function fnCalculateProductDiscount($req)
    {

        $amount          = (isset($req->amount) ? $req->amount : array());
        $product_id_arr  = (isset($req->Product) ? $req->Product : array());
        $product_qnt_arr = (isset($req->Qnt) ? $req->Qnt : array());

        $cal_amount = 0;
        $dis_flag   = false;
        $array_dis  = array();
        $dis_code   = array();

        $sales_date = new DateTime($req['sales_date']);
        $sales_date = $sales_date->format('Y-m-d');

        $branch_id = $req->branch_id;

        $detailsQueryRemake = array();

        $detailsQuery = DB::table('pos_discount_d as pdd')
            ->where(function ($detailsQuery) use ($branch_id) {
                if (!empty($branch_id)) {
                    $detailsQuery->where('pdd.branch_arr', 0)
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id}")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id}");
                }
            })
            ->join('pos_discount_m as pdm', function ($detailsQuery) {
                $detailsQuery->on('pdd.dis_code', 'pdm.dis_code')
                    ->where('pdm.is_delete', '=', 0)
                    ->where('pdm.is_active', '=', 1)
                    ->where('pdm.dis_apply', 'product');
            })
            ->where(function ($detailsQuery) use ($sales_date) {
                $detailsQuery->where([['pdm.start_date', '<=', $sales_date], ['pdm.end_date', '>=', $sales_date]])
                    ->orWhere([['pdm.start_date', '<=', $sales_date]]);
            })
            ->orderBy('pdm.start_date', 'DESC')
            ->orderBy('pdd.id', 'DESC')
            ->get();


        if (!empty($detailsQuery)) {

            foreach ($detailsQuery as $keyin => $value) {

                $value_Arr = explode(",", $value->product_arr);
                $based_on = !empty($value->based_on) ? $value->based_on : 'product';

                if ($based_on == 'group') {

                    $productArray = DB::table('pos_products')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->where(function ($productData) use ($value_Arr) {
                            if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                                $productData->whereIn('prod_group_id', $value_Arr);
                            }
                        })
                        ->pluck('id')->toArray();
                } else if ($based_on == 'category') {

                    $productArray = DB::table('pos_products')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->where(function ($productData) use ($value_Arr) {
                            if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                                $productData->whereIn('prod_cat_id', $value_Arr);
                            }
                        })
                        ->pluck('id')->toArray();
                } else if ($based_on == 'subcat') {

                    $productArray = DB::table('pos_products')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->where(function ($productData) use ($value_Arr) {
                            if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                                $productData->whereIn('prod_sub_cat_id', $value_Arr);
                            }
                        })
                        ->pluck('id')->toArray();
                } else {
                    $productArray = DB::table('pos_products')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->where(function ($productData) use ($value_Arr) {
                            if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                                $productData->whereIn('id', $value_Arr);
                            }
                        })
                        ->pluck('id')->toArray();
                }

                $resultcheck = array_intersect($productArray, $product_id_arr);

                if (count($resultcheck) > 0) {
                    $detailsQuery[$keyin]->product_arr = $productArray;

                    foreach ($resultcheck as $result_prod) {
                        $temp_obj = null;
                        $temp_obj['id'] = $keyin;
                        $temp_obj['product_id'] = $result_prod;
                        $temp_obj['dis_type'] = $detailsQuery[$keyin]->dis_type;
                        $temp_obj['dis_rate'] = $detailsQuery[$keyin]->dis_rate;
                        $temp_obj['dis_code'] = $detailsQuery[$keyin]->dis_code;
                        $temp_obj['based_on'] = $detailsQuery[$keyin]->based_on;
                        array_push($detailsQueryRemake, (object)$temp_obj);
                    }
                } else {
                    $detailsQuery[$keyin]->product_arr = null;
                }
            }

            $detailsQueryRemake = collect($detailsQueryRemake);

            foreach ($product_id_arr as $key => $product_id_sin) {
                if (!empty($product_id_sin)) {
                    // if (!empty($detailsQuery) && in_array($product_id_sin,$productArray)) {
                    $findDisQuery = $detailsQueryRemake->where('product_id', $product_id_sin)->first();
                    if (!empty($detailsQueryRemake) && !empty($findDisQuery)) {
                        $dis_flag = true;
                        if ($findDisQuery->dis_type == 'percentage') { # percentage calculation
                            $cal_amount += (($amount[$key] / 100) * $findDisQuery->dis_rate);
                            array_push($array_dis, (($amount[$key] / 100) * $findDisQuery->dis_rate));
                            array_push($dis_code, $findDisQuery->dis_code);
                        } else { # amount flat calculation by qnt
                            $cal_amount += ($findDisQuery->dis_rate * $product_qnt_arr[$key]);
                            array_push($array_dis, ($findDisQuery->dis_rate * $product_qnt_arr[$key]));
                            array_push($dis_code, $findDisQuery->dis_code);
                        }
                    } else {
                        array_push($array_dis, 0);
                    }
                }
            }
        }

        if ($dis_flag) {

            $data = array(
                'Amount'         => round($cal_amount),
                'discount_array' => $array_dis,
                'dis_code'       => $dis_code,
                'Discount'       => $dis_flag,
                'dis_type'       => 'Product',
            );

            return $data;
        } else {
            return false;
        }
    }

    public static function BACKfnCalculateProductDiscount($req)
    {

        $amount          = (isset($req->amount) ? $req->amount : array());
        $product_id_arr  = (isset($req->Product) ? $req->Product : array());
        $product_qnt_arr = (isset($req->Qnt) ? $req->Qnt : array());

        $cal_amount = 0;
        $dis_flag   = false;
        $array_dis  = array();
        $dis_code   = array();

        $sales_date = new DateTime($req['sales_date']);
        $sales_date = $sales_date->format('Y-m-d');

        $branch_id = $req->branch_id;

        $detailsQuery = DB::table('pos_discount_d as pdd')
            ->where(function ($detailsQuery) use ($branch_id) {
                if (!empty($branch_id)) {
                    $detailsQuery->where('pdd.branch_arr', 0)
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id}")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id}");
                }
            })
            ->join('pos_discount_m as pdm', function ($detailsQuery) {
                $detailsQuery->on('pdd.dis_code', 'pdm.dis_code')
                    ->where('pdm.is_delete', '=', 0)
                    ->where('pdm.is_active', '=', 1)
                    ->where('pdm.dis_apply', 'product');
            })
            ->where(function ($detailsQuery) use ($sales_date) {
                $detailsQuery->where([['pdm.start_date', '<=', $sales_date], ['pdm.end_date', '>=', $sales_date]])
                    ->orWhere([['pdm.start_date', '<=', $sales_date]]);
            })
            ->orderBy('pdm.start_date', 'DESC')
            ->orderBy('pdd.id', 'DESC')
            ->first();
        if (!empty($detailsQuery)) {

            $value_Arr = explode(",", $detailsQuery->product_arr);
            $based_on = !empty($detailsQuery->based_on) ? $detailsQuery->based_on : 'product';

            if ($based_on == 'group') {

                $productArray = DB::table('pos_products')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($value_Arr) {
                        if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                            $productData->whereIn('prod_group_id', $value_Arr);
                        }
                    })
                    ->pluck('id')->toArray();
            } else if ($based_on == 'category') {

                $productArray = DB::table('pos_products')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($value_Arr) {
                        if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                            $productData->whereIn('prod_cat_id', $value_Arr);
                        }
                    })
                    ->pluck('id')->toArray();
            } else if ($based_on == 'subcat') {

                $productArray = DB::table('pos_products')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($value_Arr) {
                        if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                            $productData->whereIn('prod_sub_cat_id', $value_Arr);
                        }
                    })
                    ->pluck('id')->toArray();
            } else {
                $productArray = DB::table('pos_products')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($value_Arr) {
                        if (!in_array("0", $value_Arr) && !empty($value_Arr)) {
                            $productData->whereIn('id', $value_Arr);
                        }
                    })
                    ->pluck('id')->toArray();
            }

            foreach ($product_id_arr as $key => $product_id_sin) {

                if (!empty($product_id_sin)) {

                    if (!empty($detailsQuery) && in_array($product_id_sin, $productArray)) {
                        $dis_flag = true;

                        if ($detailsQuery->dis_type == 'percentage') { # percentage calculation
                            $cal_amount += (($amount[$key] / 100) * $detailsQuery->dis_rate);
                            array_push($array_dis, (($amount[$key] / 100) * $detailsQuery->dis_rate));
                            array_push($dis_code, $detailsQuery->dis_code);
                        } else { # amount flat calculation by qnt
                            $cal_amount += ($detailsQuery->dis_rate * $product_qnt_arr[$key]);
                            array_push($array_dis, ($detailsQuery->dis_rate * $product_qnt_arr[$key]));
                            array_push($dis_code, $detailsQuery->dis_code);
                        }
                    } else {
                        array_push($array_dis, 0);
                    }
                }
            }
        }


        if ($dis_flag) {

            $data = array(
                'Amount'         => round($cal_amount),
                'discount_array' => $array_dis,
                'dis_code'       => $dis_code,
                'Discount'       => $dis_flag,
                'dis_type'       => 'Product',
            );

            return $data;
        } else {
            return false;
        }
    }

    public static function backupfnCalculateProductDiscount($req)
    {

        $amount          = (isset($req->amount) ? $req->amount : array());
        $product_id_arr  = (isset($req->Product) ? $req->Product : array());
        $product_qnt_arr = (isset($req->Qnt) ? $req->Qnt : array());

        $cal_amount = 0;
        $dis_flag   = false;
        $array_dis  = array();
        $dis_code   = array();

        $sales_date = new DateTime($req['sales_date']);
        $sales_date = $sales_date->format('Y-m-d');

        $branch_id = $req->branch_id;

        foreach ($product_id_arr as $key => $product_id_sin) {

            if (!empty($product_id_sin)) {

                $detailsQuery = DB::table('pos_discount_d as pdd')
                    ->where(function ($detailsQuery) use ($branch_id) {
                        if (!empty($branch_id)) {
                            $detailsQuery->where('pdd.branch_arr', 0)
                                ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id},%")
                                ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id},%")
                                ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id}")
                                ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id}");
                        }
                    })
                    ->where(function ($detailsQuery) use ($product_id_sin) {
                        if (!empty($product_id_sin)) {
                            $detailsQuery->where('pdd.product_arr', 0)
                                ->orWhere('pdd.product_arr', 'LIKE', "%,{$product_id_sin},%")
                                ->orWhere('pdd.product_arr', 'LIKE', "{$product_id_sin},%")
                                ->orWhere('pdd.product_arr', 'LIKE', "%,{$product_id_sin}")
                                ->orWhere('pdd.product_arr', 'LIKE', "{$product_id_sin}");
                        }
                    })
                    ->join('pos_discount_m as pdm', function ($detailsQuery) {
                        $detailsQuery->on('pdd.dis_code', 'pdm.dis_code')
                            ->where('pdm.is_delete', '=', 0)
                            ->where('pdm.is_active', '=', 1)
                            ->where('pdm.dis_apply', 'product');
                    })
                    ->where(function ($detailsQuery) use ($sales_date) {
                        $detailsQuery->where([['pdm.start_date', '<=', $sales_date], ['pdm.end_date', '>=', $sales_date]])
                            ->orWhere([['pdm.start_date', '<=', $sales_date]]);
                    })
                    ->orderBy('pdm.start_date', 'DESC')
                    ->orderBy('pdd.id', 'DESC')
                    ->first();

                if (!empty($detailsQuery)) {
                    $dis_flag = true;

                    if ($detailsQuery->dis_type == 'percentage') { # percentage calculation
                        $cal_amount += (($amount[$key] / 100) * $detailsQuery->dis_rate);
                        array_push($array_dis, (($amount[$key] / 100) * $detailsQuery->dis_rate));
                        array_push($dis_code, $detailsQuery->dis_code);
                    } else { # amount flat calculation by qnt
                        $cal_amount += ($detailsQuery->dis_rate * $product_qnt_arr[$key]);
                        array_push($array_dis, ($detailsQuery->dis_rate * $product_qnt_arr[$key]));
                        array_push($dis_code, $detailsQuery->dis_code);
                    }
                } else {
                    array_push($array_dis, 0);
                }
            }
        }

        if ($dis_flag) {

            $data = array(
                'Amount'         => round($cal_amount),
                'discount_array' => $array_dis,
                'dis_code'       => $dis_code,
                'Discount'       => $dis_flag,
                'dis_type'       => 'Product',
            );

            return $data;
        } else {
            return false;
        }
    }

    public static function fnCalculateBillDiscount($req)
    {

        $amount          = (isset($req->amount) ? $req->amount : array());
        $product_id_arr  = (isset($req->Product) ? $req->Product : array());
        $product_qnt_arr = (isset($req->Qnt) ? $req->Qnt : array());

        $cal_amount = 0;
        $dis_flag   = false;
        $array_dis  = array();
        $dis_code   = array();

        $sales_date = new DateTime($req['sales_date']);
        $sales_date = $sales_date->format('Y-m-d');

        $branch_id = $req->branch_id;

        $TOTAL_Amount = 0;

        foreach ($product_id_arr as $key => $product_id_sin) {

            if (!empty($product_id_sin)) {
                $TOTAL_Amount += $amount[$key];
            }
        }

        $detailsQuery = DB::table('pos_discount_d as pdd')
            ->where(function ($detailsQuery) use ($branch_id) {
                if (!empty($branch_id)) {
                    $detailsQuery->where('pdd.branch_arr', 0)
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id}")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id}");
                }
            })
            ->where(function ($detailsQuery) use ($TOTAL_Amount) {
                $detailsQuery->where([['pdd.start_bill_amount', '<=', $TOTAL_Amount], ['pdd.end_bill_amount', '>=', $TOTAL_Amount]])
                    ->orWhere([['pdd.start_bill_amount', '<=', $TOTAL_Amount]]);
            })
            ->join('pos_discount_m as pdm', function ($detailsQuery) {
                $detailsQuery->on('pdd.dis_code', 'pdm.dis_code')
                    ->where('pdm.is_delete', '=', 0)
                    ->where('pdm.is_active', '=', 1)
                    ->where('pdm.dis_apply', 'bill');
            })
            ->where(function ($detailsQuery) use ($sales_date) {
                $detailsQuery->where([['pdm.start_date', '<=', $sales_date], ['pdm.end_date', '>=', $sales_date]])
                    ->orWhere([['pdm.start_date', '<=', $sales_date]]);
            })
            ->orderBy('pdm.start_date', 'DESC')
            ->orderBy('pdd.id', 'DESC')
            ->first();

        if (!empty($detailsQuery)) {
            $dis_flag = true;

            if ($detailsQuery->dis_type == 'percentage') { # percentage calculation
                $calculatedAmt = (($TOTAL_Amount / 100) * $detailsQuery->dis_rate);
                if ($detailsQuery->dis_limit != null && $detailsQuery->dis_limit > 0) { # limit check and set limit
                    $cal_amount = ($calculatedAmt > $detailsQuery->dis_limit) ? $detailsQuery->dis_limit : $calculatedAmt;
                } else {
                    $cal_amount = $calculatedAmt;
                }
            } else { # amount flat

                $cal_amount = $detailsQuery->dis_limit;
            }

            array_push($array_dis, $cal_amount);
            array_push($dis_code, $detailsQuery->dis_code);
        }

        if ($dis_flag) {

            $data = array(
                'Amount'         => round($cal_amount),
                'discount_array' => $array_dis,
                'dis_code'       => $dis_code,
                'Discount'       => $dis_flag,
                'dis_type'       => 'Bill',
            );

            return $data;
        } else {
            return false;
        }
    }

    public static function fnCalculateRegularDiscount($req)
    {

        $amount          = (isset($req->amount) ? $req->amount : array());
        $product_id_arr  = (isset($req->Product) ? $req->Product : array());
        $product_qnt_arr = (isset($req->Qnt) ? $req->Qnt : array());

        $cal_amount = 0;
        $dis_flag   = false;
        $array_dis  = array();
        $dis_code   = array();

        $sales_date = new DateTime($req['sales_date']);
        $sales_date = $sales_date->format('Y-m-d');

        $branch_id = $req->branch_id;

        $detailsQuery = DB::table('pos_discount_d as pdd')
            // ->where('pdd.dis_code', $masterQuery->dis_code)
            ->where(function ($detailsQuery) use ($branch_id) {
                if (!empty($branch_id)) {
                    $detailsQuery->where('pdd.branch_arr', 0)
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id},%")
                        ->orWhere('pdd.branch_arr', 'LIKE', "%,{$branch_id}")
                        ->orWhere('pdd.branch_arr', 'LIKE', "{$branch_id}");
                }
            })
            ->join('pos_discount_m as pdm', function ($detailsQuery) {
                $detailsQuery->on('pdd.dis_code', 'pdm.dis_code')
                    ->where('pdm.is_delete', '=', 0)
                    ->where('pdm.is_active', '=', 1)
                    ->where('pdm.dis_apply', 'regular');
            })
            ->where(function ($detailsQuery) use ($sales_date) {
                $detailsQuery->where([['pdm.start_date', '<=', $sales_date], ['pdm.end_date', '>=', $sales_date]])
                    ->orWhere([['pdm.start_date', '<=', $sales_date]]);
            })
            ->orderBy('pdm.start_date', 'DESC')
            ->orderBy('pdd.id', 'DESC')
            ->first();


        if (!empty($detailsQuery)) {
            $dis_flag = true;

            if ($detailsQuery->dis_type == 'percentage') { # percentage calculation
                $cal_amount = $detailsQuery->dis_rate;
                array_push($array_dis, 'percentage');
            } else { # amount flat
                $cal_amount = $detailsQuery->dis_rate;
                array_push($array_dis, 'amount');
            }

            array_push($dis_code, $detailsQuery->dis_code);
        }

        $data = array(
            'Amount'         => round($cal_amount),
            'discount_array' => $array_dis,
            'dis_code'       => $dis_code,
            'Discount'       => $dis_flag,
            'dis_type'       => 'Regular',
        );

        return $data;
    }

    public static function fnGetDiscountBackup($req)
    {

        $amount          = (isset($req->amount) ? $req->amount : array());
        $product_id_arr  = (isset($req->Product) ? $req->Product : array());
        $product_qnt_arr = (isset($req->Qnt) ? $req->Qnt : array());

        $cal_amount = 0;

        $sales_date = new DateTime($req['sales_date']);
        $sales_date = $sales_date->format('Y-m-d');

        // dd( $sales_date );

        $branch_id = $req->branch_id;

        $masterQuery = DB::table('pos_discount_m')
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->where(function ($masterQuery) use ($sales_date) {

                $masterQuery->where([['start_date', '<=', $sales_date], ['end_date', '>=', $sales_date]])
                    ->orWhere([['start_date', '<=', $sales_date]]);
            })
            ->orderBy('id', 'DESC')
            ->first();
        $dis_flag  = false;
        $array_dis = array();

        if (!empty($masterQuery)) {
            $dis_flag = true;
            if ($masterQuery->dis_apply == "bill") { # bill type discount

                $TOAL_Amount = 0;
                foreach ($product_id_arr as $key => $product_id_sin) {

                    if (!empty($product_id_sin)) {
                        $TOAL_Amount += $amount[$key];
                    }
                }

                $detailsQuery = DB::table('pos_discount_d')
                    ->where('dis_code', $masterQuery->dis_code)
                    ->where(function ($detailsQuery) use ($branch_id) {
                        if (!empty($branch_id)) {
                            $detailsQuery->where('branch_arr', 0)
                                ->orWhere('branch_arr', 'LIKE', "%,{$branch_id},%")
                                ->orWhere('branch_arr', 'LIKE', "{$branch_id},%")
                                ->orWhere('branch_arr', 'LIKE', "%,{$branch_id}")
                                ->orWhere('branch_arr', 'LIKE', "{$branch_id}");
                        }
                    })
                    ->where(function ($detailsQuery) use ($TOAL_Amount) {

                        $detailsQuery->where([['start_bill_amount', '<=', $TOAL_Amount], ['end_bill_amount', '>=', $TOAL_Amount]])
                            ->orWhere([['start_bill_amount', '<=', $TOAL_Amount]]);
                    })
                    ->orderBy('id', 'DESC')
                    ->first();

                if (!empty($detailsQuery)) {
                    if ($detailsQuery->dis_type == 'percentage') { # percentage calculation
                        $calculatedAmt = (($TOAL_Amount / 100) * $detailsQuery->dis_rate);
                        if ($detailsQuery->dis_limit != null && $detailsQuery->dis_limit > 0) { # limit check and set limit
                            $cal_amount = ($calculatedAmt > $detailsQuery->dis_limit) ? $detailsQuery->dis_limit : $calculatedAmt;
                        } else {
                            $cal_amount = $calculatedAmt;
                        }
                    } else { # amount flat

                        $cal_amount = $detailsQuery->dis_limit;
                    }

                    array_push($array_dis, $cal_amount);
                }
            } else { # product wise discount

                foreach ($product_id_arr as $key => $product_id_sin) {

                    if (!empty($product_id_sin)) {
                        if (!empty($masterQuery)) {
                            $detailsQuery = DB::table('pos_discount_d')
                                ->where('dis_code', $masterQuery->dis_code)
                                ->where(function ($detailsQuery) use ($branch_id) {
                                    if (!empty($branch_id)) {
                                        $detailsQuery->where('branch_arr', 0)
                                            ->orWhere('branch_arr', 'LIKE', "%,{$branch_id},%")
                                            ->orWhere('branch_arr', 'LIKE', "{$branch_id},%")
                                            ->orWhere('branch_arr', 'LIKE', "%,{$branch_id}")
                                            ->orWhere('branch_arr', 'LIKE', "{$branch_id}");
                                    }
                                })
                                ->where(function ($detailsQuery) use ($product_id_sin) {
                                    if (!empty($product_id_sin)) {
                                        $detailsQuery->where('product_arr', 0)
                                            ->orWhere('product_arr', 'LIKE', "%,{$product_id_sin},%")
                                            ->orWhere('product_arr', 'LIKE', "{$product_id_sin},%")
                                            ->orWhere('product_arr', 'LIKE', "%,{$product_id_sin}")
                                            ->orWhere('product_arr', 'LIKE', "{$product_id_sin}");
                                    }
                                })
                                ->orderBy('id', 'DESC')
                                ->first();

                            if (!empty($detailsQuery)) {
                                if ($detailsQuery->dis_type == 'percentage') { # percentage calculation

                                    $cal_amount += (($amount[$key] / 100) * $detailsQuery->dis_rate);

                                    array_push($array_dis, (($amount[$key] / 100) * $detailsQuery->dis_rate));
                                } else { # amount flat calculation by qnt

                                    $cal_amount += ($detailsQuery->dis_rate * $product_qnt_arr[$key]);
                                    array_push($array_dis, ($detailsQuery->dis_rate * $product_qnt_arr[$key]));
                                }
                            }
                        }
                    }
                }
            }
        }

        $data = array(
            'Amount'         => round($cal_amount),
            'discount_array' => $array_dis,
            'Discount'       => $dis_flag,
        );

        return $data;
    }
    /** End report function */

    /** start Schedule function  */
    public static function installmentSchedule(
        $companyID = null,
        $branchID = null,
        $somityID = null,
        $salesDate = null,
        $instType = null,
        $instMonth = null,
        $instPackageId = null
    ) {

        $companyID = (!empty($companyID)) ? $companyID : Common::getCompanyId();
        $branchID  = (!empty($branchID)) ? $branchID : Common::getBranchId();
        $somityID  = (!empty($somityID)) ? $somityID : 1;
        $companyID = (!empty($companyID)) ? $companyID : 1;

        $fromDate     = null;
        $actualToDate = null;
        $toDate       = null;
        $instCount    = 0;
        $instMonth    = (int) $instMonth;
        $instWeek     = 0;

        $scheduleDays     = array();
        $tempScheduleDays = array();

        if (!empty($salesDate) && !empty($instMonth)) {

            $fromDate       = new DateTime($salesDate);
            $tempDate       = clone $fromDate;
            $actualTempDate = clone $fromDate;

            /*
             * Extra 3 month add kora hocche karon jodi
            kono date, week, month holiday te pore
            tahole add or remove kora jay
             */
            $actualToDate = $actualTempDate->modify('+' . ($instMonth) . ' month');
            $toDate       = $tempDate->modify('+' . (($instMonth - 1) + 3) . ' month');

            ## Week Count from Two Dates
            /*
             * 1 Week = 60*60*24*7 = 604800
             * by default week count by date wise
             */
            $dateDiff = strtotime($actualToDate->format('d-m-Y'), 0) - strtotime($fromDate->format('d-m-Y'), 0);
            $instWeek = (int) floor($dateDiff / 604800);

            ## Week count by company data wise
            if (!empty($instPackageId)) {
                $queryInstPackage = DB::table('pos_inst_packages')->where([['id', $instPackageId], ['is_delete', 0], ['is_active', 1]])->first();

                if ($queryInstPackage) {
                    if (!empty($queryInstPackage->prod_inst_week)) {
                        $instWeek = $queryInstPackage->prod_inst_week;
                    }
                }
            }

            // dd($fromDate, $actualToDate, $instWeek);

        }

        // ///// This is for query
        if (!empty($fromDate) && !empty($toDate)) {

            // Fixed Govt Holiday Query
            $fixedGovtHoliday = DB::table('hr_holidays_govt')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            // $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            // Company Holiday Query
            // $companyArr          = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];

            $companyHolidays = DB::table('hr_holidays_comp')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date', 'ch_eff_date_end')
                ->where(function ($companyHolidays) use ($companyID) {
                    if (!empty($companyID)) {
                        $companyHolidays->where('company_id', $companyID);
                    }
                })
                // ->where([$companyArr])
                ->get();

            // $companyHolidays = (count($companyHolidayQuery->toarray()) > 0) ? $companyHolidayQuery->toarray() : array();

            // Special Holiday for Organization Query
            $sHolidaysORG = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            // $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            // Special Holiday for Branch Query
            $sHolidaysBr = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchID)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            // $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();
        }

        if (!empty($fromDate) && !empty($toDate) && !empty($instType)) {

            ///////////////////////////////////// test ////////////////////////////
            // $instType = 2;
            ///////////////////////////////////// test ////////////////////////////
            // $week = $installmentDate->format("W");

            $firstInstallmentDay   = $fromDate->format('d');
            $firstInstallmentMonth = $fromDate->format('m');
            $firstInstallmentYear  = $fromDate->format('Y');

            if ($instType == 1) {
                // Month Type
                $installmentDate = clone $fromDate;
                array_push($tempScheduleDays, clone $installmentDate);

                while ($installmentDate <= $toDate) {

                    if ($firstInstallmentDay == '31' || $firstInstallmentDay == 31) {
                        $installmentDate = $installmentDate->modify('last day of next month');
                    } else if (
                        $firstInstallmentDay == '30' || $firstInstallmentDay == 30
                        || $firstInstallmentDay == '29' || $firstInstallmentDay == 29
                    ) {

                        $tempNextMonth = clone $installmentDate;
                        $tempNextMonth = $tempNextMonth->modify('last day of next month');

                        if (
                            $tempNextMonth->format('m') == '2' || $tempNextMonth->format('m') == '02'
                            || $tempNextMonth->format('m') == 2 || $tempNextMonth->format('m') == 02
                        ) {

                            $installmentDate = $installmentDate->modify('last day of next month');
                        } else {
                            $installmentDate = $installmentDate->modify('+1 month');
                        }
                    } else {
                        $installmentDate = $installmentDate->modify('+1 month');
                    }

                    array_push($tempScheduleDays, clone $installmentDate);
                }
            } elseif ($instType == 2) {
                // Week Type
                $installmentDate = clone $fromDate;
                while ($installmentDate <= $toDate) {
                    // array_push($tempScheduleDays, $installmentDate->format('Y-m-d'));
                    array_push($tempScheduleDays, clone $installmentDate);
                    $installmentDate = $installmentDate->modify('+1 week');
                }
            }

            foreach ($tempScheduleDays as $tempRow) {

                $holidayFlag  = true;
                $tempLoopDate = clone $tempRow;

                while ($holidayFlag == true) {

                    $holidayFlag = false;

                    // Fixed Govt Holiday Check
                    foreach ($fixedGovtHoliday as $RowFG) {
                        // $RowFG = (array) $RowFG;
                        if ($RowFG->gh_date == $tempLoopDate->format('d-m')) {
                            $holidayFlag = true;
                        }
                    }

                    // Company Holiday Check
                    if ($holidayFlag == false) {
                        foreach ($companyHolidays as $RowC) {
                            // $RowC = (array) $RowC;

                            $ch_day = $RowC->ch_day;

                            $ch_day_arr  = explode(',', $RowC->ch_day);
                            $ch_eff_date = new DateTime($RowC->ch_eff_date);

                            $ch_eff_date_end = (!empty($RowC->ch_eff_date_end)) ? new DateTime($RowC->ch_eff_date_end) : null;

                            // This is Full day name
                            $dayName = $tempLoopDate->format('l');

                            // if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                            //     $holidayFlag = true;
                            // }
                            if (
                                !empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) &&
                                ($tempLoopDate->format('Y-m-d') >= $ch_eff_date->format('Y-m-d')) &&
                                ($tempLoopDate->format('Y-m-d') <= $ch_eff_date_end->format('Y-m-d'))
                            ) {

                                $holidayFlag = true;
                            } else if (
                                $ch_eff_date_end == '' && in_array($dayName, $ch_day_arr) &&
                                ($ch_eff_date->format('Y-m-d') <= $tempLoopDate->format('Y-m-d'))
                            ) {
                                $holidayFlag = true;
                            }
                        }
                    }

                    // Special Holiday Org check
                    if ($holidayFlag == false) {
                        foreach ($sHolidaysORG as $RowO) {
                            // $RowO = (array) $RowO;

                            $sh_date_from = new DateTime($RowO->sh_date_from);
                            $sh_date_to   = new DateTime($RowO->sh_date_to);

                            if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                                $holidayFlag = true;
                            }
                        }
                    }

                    // Special Holiday Branch check
                    if ($holidayFlag == false) {
                        foreach ($sHolidaysBr as $RowB) {
                            // $RowB = (array) $RowB;

                            $sh_date_from_b = new DateTime($RowB->sh_date_from);
                            $sh_date_to_b   = new DateTime($RowB->sh_date_to);

                            if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                                $holidayFlag = true;
                            }
                        }
                    }

                    if ($holidayFlag == false) {
                        array_push($scheduleDays, $tempLoopDate->format('Y-m-d'));
                        // array_push($scheduleDays, clone $tempLoopDate);

                    } else {
                        if ($instType == 1) {
                            // $tempCurMonth = $tempRow->format('m');
                            $tempLoopDate = $tempLoopDate->modify('+1 day');
                        } else {
                            $holidayFlag = false;
                        }
                    }
                }
            }

            // dd($scheduleDays);

        }

        ///////////////////////////////////////////////////////////////
        // Incomplete function, check remain if full week holiday skip this week but schedule date count must be equal installment month or week
        // When month and week end and go to next week that case date modify minus day

        if ($instType == 1) {
            // if (count($scheduleDays) > $instMonth) {
            $countDiff = count($scheduleDays) - $instMonth;
            for ($r = 0; $r < $countDiff; $r++) {
                array_pop($scheduleDays);
            }
            // }
        } else if ($instType == 2) {

            // dd($instType, $instMonth, $instPackageId, $instWeek, $countDiff);

            // if (count($scheduleDays) > $instWeek) {
            $countDiff = count($scheduleDays) - $instWeek;

            for ($r = 0; $r < $countDiff; $r++) {
                array_pop($scheduleDays);
            }
            // }
        }

        // dd($scheduleDays);

        return $scheduleDays;
    }

    public static function installmentSchedule_multiple(
        $companyID = null,
        $branchArr = [],
        $branchDateTypeMonthArr = [],
        $somityArr = []
    ) {
        // ## integer, ## string, ## array
        // if (gettype($branchDateTypeMonthArr) == "string") {
        //     $branchDateTypeMonthArr = unserialize($branchDateTypeMonthArr);
        // }

        // if (gettype($branchArr) == "string") {
        //     $branchArr = unserialize($branchArr);
        // }

        $allScheduleData = array();

        // // ## Concat data by @ (branch@salesdate@installmentType@installmentMonth)
        $scheduleFlag = (count($branchDateTypeMonthArr) > 0) ? true : false;

        // ## if sales date, type, month empty then return initial with empty array
        if ($scheduleFlag == false) {
            // return serialize($allScheduleData);
            return $allScheduleData;
        }

        $weekDayArr = [
            1 => 'Saturday',
            2 => 'Sunday',
            3 => 'Monday',
            4 => 'Tuesday',
            5 => 'Wednesday',
            6 => 'Thursday',
            7 => 'Friday',
        ];

        $companyID = (!empty($companyID)) ? $companyID : Common::getCompanyId();
        $branchArr = (count($branchArr) > 0) ? $branchArr : [Common::getBranchId()];
        $somityArr = (count($somityArr) > 0) ? $somityArr : [1];

        // ## ----------------------------------- Holiday Query Start
        if ($scheduleFlag) {
            /**
             * Collection theke Array Faster,
             * @current due report - 24-09-2020 porjonto sales ache 1714 ta,
             * @collection diye korle page load hote time ney 15.72s file size 9.2 kB
             * but query er data array te convert kore check korle seta time ney 3.21s only file size 9.2 kB
             * @single data pass korle file size 9.2kB load hote time ney 4.80s but multiple function seikhane 3.21s a load hoy
             * @test korte hole ai same function er ekta copy rakha ache old a seta test kore dekha jabe
             *
             */

            // // ## Fixed Govt Holiday Query
            $fixedGovtHoliday = DB::table('hr_holidays_govt')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            // $fixedGovtHoliday = (count($fixedGovtHoliday->toarray()) > 0) ? $fixedGovtHoliday->toarray() : array();

            // // ## Company Holiday Query
            $companyHolidays = DB::table('hr_holidays_comp')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date', 'ch_eff_date_end')
                ->where(function ($companyHolidays) use ($companyID) {
                    if (!empty($companyID)) {
                        $companyHolidays->where('company_id', $companyID);
                    }
                })
                ->get();
            // $companyHolidays = (count($companyHolidays->toarray()) > 0) ? $companyHolidays->toarray() : array();

            // // ## Special Holiday for Organization Query
            $sHolidaysORG = DB::table('hr_holidays_special')
                ->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            // $sHolidaysORG = (count($sHolidaysORG->toarray()) > 0) ? $sHolidaysORG->toarray() : array();

            // // ## Special Holiday for Branch Query
            $sHolidaysBr = DB::table('hr_holidays_special')
                ->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where(function ($sHolidaysBr) use ($branchArr) {
                    if (!empty($branchArr)) {
                        $sHolidaysBr->whereIn('branch_id', $branchArr);
                    }
                })
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            // $sHolidaysBr = (count($sHolidaysBr->toarray()) > 0) ? $sHolidaysBr->toarray() : array();

        }
        // ## ----------------------------------- End Holiday Query

        // // ## Schedule Make Start
        foreach ($branchDateTypeMonthArr as $passingValue) {

            // ## explode concat value
            $passingArr    = explode('@', $passingValue);
            $branchID      = (isset($passingArr[0]) && !empty($passingArr[0])) ? $passingArr[0] : null;
            $salesDate     = (isset($passingArr[1]) && !empty($passingArr[1])) ? $passingArr[1] : null;
            $instType      = (isset($passingArr[2]) && !empty($passingArr[2])) ? $passingArr[2] : null;
            $instMonth     = (isset($passingArr[3]) && !empty($passingArr[3])) ? $passingArr[3] : null;
            $instPackageId = (isset($passingArr[4]) && !empty($passingArr[4])) ? $passingArr[4] : null;

            // ## end explode

            // dd($passingValue);

            // // ## Start Process Make Schedule
            $fromDate     = null;
            $actualToDate = null;
            $toDate       = null;
            $instCount    = 0;
            $instMonth    = (int) $instMonth;
            $instWeek     = 0;

            $scheduleDays     = array();
            $tempScheduleDays = array();

            if (!empty($salesDate) && !empty($instMonth)) {

                $fromDate       = new DateTime($salesDate);
                $tempDate       = clone $fromDate;
                $actualTempDate = clone $fromDate;

                /*
                 * Extra 3 month add kora hocche karon jodi
                kono date, week, month holiday te pore
                tahole add or remove kora jay
                 */
                $actualToDate = $actualTempDate->modify('+' . ($instMonth) . ' month');
                $toDate       = $tempDate->modify('+' . (($instMonth - 1) + 3) . ' month');

                ## Week Count from Two Dates
                /*
                 * 1 Week = 60*60*24*7 = 604800
                 */
                $dateDiff = strtotime($actualToDate->format('d-m-Y'), 0) - strtotime($fromDate->format('d-m-Y'), 0);
                $instWeek = (int) floor($dateDiff / 604800);

                ## Week count by company data wise
                if (!empty($instPackageId)) {
                    $queryInstPackage = DB::table('pos_inst_packages')->where([['id', $instPackageId], ['is_delete', 0], ['is_active', 1]])->first();

                    if ($queryInstPackage) {
                        if (!empty($queryInstPackage->prod_inst_week)) {
                            $instWeek = $queryInstPackage->prod_inst_week;
                        }
                    }
                }

                // dd($fromDate, $actualToDate, $instWeek);
            }

            if (!empty($fromDate) && !empty($toDate) && !empty($instType)) {

                ///////////////////////////////////// test ////////////////////////////
                // $instType = 2;
                ///////////////////////////////////// test ////////////////////////////
                // $week = $installmentDate->format("W");

                $firstInstallmentDay   = $fromDate->format('d');
                $firstInstallmentMonth = $fromDate->format('m');
                $firstInstallmentYear  = $fromDate->format('Y');

                if ($instType == 1) {
                    // Month Type
                    $installmentDate = clone $fromDate;
                    array_push($tempScheduleDays, clone $installmentDate);

                    while ($installmentDate <= $toDate) {

                        if ($firstInstallmentDay == '31' || $firstInstallmentDay == 31) {
                            $installmentDate = $installmentDate->modify('last day of next month');
                        } else if (
                            $firstInstallmentDay == '30' || $firstInstallmentDay == 30
                            || $firstInstallmentDay == '29' || $firstInstallmentDay == 29
                        ) {

                            $tempNextMonth = clone $installmentDate;
                            $tempNextMonth = $tempNextMonth->modify('last day of next month');

                            if (
                                $tempNextMonth->format('m') == '2' || $tempNextMonth->format('m') == '02'
                                || $tempNextMonth->format('m') == 2 || $tempNextMonth->format('m') == 02
                            ) {

                                $installmentDate = $installmentDate->modify('last day of next month');
                            } else {
                                $installmentDate = $installmentDate->modify('+1 month');
                            }
                        } else {
                            $installmentDate = $installmentDate->modify('+1 month');
                        }

                        array_push($tempScheduleDays, clone $installmentDate);
                    }
                } elseif ($instType == 2) {
                    // Week Type
                    $installmentDate = clone $fromDate;
                    while ($installmentDate <= $toDate) {
                        // array_push($tempScheduleDays, $installmentDate->format('Y-m-d'));
                        array_push($tempScheduleDays, clone $installmentDate);
                        $installmentDate = $installmentDate->modify('+1 week');
                    }
                }

                foreach ($tempScheduleDays as $tempRow) {

                    $holidayFlag  = true;
                    $tempLoopDate = clone $tempRow;

                    while ($holidayFlag == true) {

                        $holidayFlag = false;

                        // Fixed Govt Holiday Check
                        foreach ($fixedGovtHoliday as $RowFG) {

                            // $RowFG = (array) $RowFG;

                            if (($RowFG->gh_date == $tempLoopDate->format('d-m'))
                                && (empty($RowFG->efft_start_date) || ($RowFG->efft_start_date <= $tempLoopDate->format('Y-m-d')))
                                && (empty($RowFG->efft_end_date) || ($RowFG->efft_end_date >= $tempLoopDate->format('Y-m-d')))
                            ) {

                                $holidayFlag = true;
                            }
                        }

                        // Company Holiday Check
                        if ($holidayFlag == false) {
                            foreach ($companyHolidays as $RowC) {

                                // $RowC = (array) $RowC;

                                $ch_day      = $RowC->ch_day;
                                $ch_day_arr  = explode(',', $RowC->ch_day);
                                $ch_eff_date = (!empty($RowC->ch_eff_date)) ? new DateTime($RowC->ch_eff_date) : null;

                                $ch_eff_date_end = (!empty($RowC->ch_eff_date_end)) ? new DateTime($RowC->ch_eff_date_end) : null;

                                // ## This is Full day name
                                $dayName = $tempLoopDate->format('l');
                                // if (in_array($dayName, $ch_day_arr) && (empty($ch_eff_date) || ($ch_eff_date <= $tempLoopDate))) {
                                //     $holidayFlag = true;
                                // }

                                if (
                                    !empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) &&
                                    ($tempLoopDate->format('Y-m-d') >= $ch_eff_date->format('Y-m-d')) &&
                                    ($tempLoopDate->format('Y-m-d') <= $ch_eff_date_end->format('Y-m-d'))
                                ) {

                                    $holidayFlag = true;
                                } else if (
                                    empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) &&
                                    (empty($ch_eff_date) || $ch_eff_date->format('Y-m-d') <= $tempLoopDate->format('Y-m-d'))
                                ) {
                                    $holidayFlag = true;
                                }
                            }
                        }

                        // Special Holiday Org check
                        if ($holidayFlag == false) {
                            foreach ($sHolidaysORG as $RowO) {

                                // $RowO = (array) $RowO;

                                $sh_date_from = new DateTime($RowO->sh_date_from);
                                $sh_date_to   = new DateTime($RowO->sh_date_to);

                                if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                                    $holidayFlag = true;
                                }
                            }
                        }

                        // Special Holiday Branch check
                        if ($holidayFlag == false) {
                            foreach ($sHolidaysBr as $RowB) {

                                // $RowB = (array) $RowB;

                                $sh_date_from_b = new DateTime($RowB->sh_date_from);
                                $sh_date_to_b   = new DateTime($RowB->sh_date_to);

                                if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                                    $holidayFlag = true;
                                }
                            }
                        }

                        if ($holidayFlag == false) {
                            array_push($scheduleDays, $tempLoopDate->format('Y-m-d'));
                            // array_push($scheduleDays, clone $tempLoopDate);

                        } else {
                            if ($instType == 1) {
                                // $tempCurMonth = $tempRow->format('m');
                                $tempLoopDate = $tempLoopDate->modify('+1 day');
                            } else {
                                $holidayFlag = false;
                            }
                        }
                    }
                }
            }

            ///////////////////////////////////////////////////////////////
            // Incomplete function, check remain if full week holiday skip this week but schedule date count must be equal installment month or week
            // When month and week end and go to next week that case date modify minus day

            if ($instType == 1) {
                // if (count($scheduleDays) > $instMonth) {
                $countDiff = count($scheduleDays) - $instMonth;
                for ($r = 0; $r < $countDiff; $r++) {
                    array_pop($scheduleDays);
                }
                // }
            } else if ($instType == 2) {
                // if (count($scheduleDays) > $instWeek) {
                $countDiff = count($scheduleDays) - $instWeek;
                for ($r = 0; $r < $countDiff; $r++) {
                    array_pop($scheduleDays);
                }
                // }
            }

            // // ## ## Data merge with (branch@salesdate@installmentType@installmentMonth) key
            $allScheduleData[$passingValue] = $scheduleDays;

            // dd($allScheduleData);
        }
        // // ## Schedule Make End

        // return serialize($allScheduleData);
        return $allScheduleData;
    }

    /** End Schedule function */

    /** Start Due Calculation function */

    public static function installment_due($companyID = null, $selBranchArr = [], $endDate = null, $dueFor = 'current_and_over_due', $useFor = 'end_execution', $viewMethod = 'single', $startDate = null, $employeeId = null, $order = null, $dir = null)
    {
        /**
         * @dueFor = 'current_due' or 'over_due' or 'current_and_over_due' or 'regular_due'
         * @useFor = 'report' or 'end_execution'
         * @viewMethod is used for branch wise report or total due show
         * @viewMethod = 'single' or 'branch_wise'
         */

        if (empty($order)) {
            $order = 'sales_date';
            $dir   = 'ASC';
        }

        $dueForCheckArr = ['current_due', 'over_due', 'current_and_over_due', 'regular_due'];
        $useForCheckArr = ['report', 'end_execution'];

        if (count($selBranchArr) < 1 || empty($endDate) || in_array($dueFor, $dueForCheckArr) == false || in_array($useFor, $useForCheckArr) == false) {
            return false;
        }
        $companyID = (empty($companyID)) ? Common::getCompanyId() : $companyID;
        // dd($endDate);
        ///////// start work
        if (Common::getDBConnection() == "sqlite") {

            $dueQuery = DB::table('pos_sales_m')
                ->where([['is_delete', 0], ['is_active', 1], ['sales_type', 2]]) // ['is_opening', 0]
                ->whereIn('branch_id', $selBranchArr)
                ->selectRaw(
                    'branch_id, sales_bill_no, customer_id, sales_date, total_amount as sales_amount, installment_amount,employee_id,
                    installment_type, installment_month, installment_date as last_installment_date, inst_package_id,
                    IFNULL((paid_amount - vat_amount - service_charge),0) as first_instalment,
                    (branch_id || "@" || sales_date || "@" || installment_type || "@" || installment_month || "@" || inst_package_id) as branch_date_type_month'
                )
                ->where(function ($dueQuery) use ($employeeId) {
                    if (!empty($employeeId)) {
                        $dueQuery->where('employee_id', $employeeId);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        $dueQuery->where('sales_date', '<', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate, $dueFor) { // ## installment_date is instalment last date
                    if (!empty($endDate) && $dueFor == 'current_due') {
                        $dueQuery->where('installment_date', '>=', $endDate);
                    }
                    if (!empty($endDate) && $dueFor == 'over_due') {
                        $dueQuery->where('installment_date', '<', $endDate);
                    }
                    if (!empty($endDate) && $dueFor == 'regular_due') {
                        $dueQuery->where('installment_date', '>=', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        ## Complete Sales ignore
                        $dueQuery->whereNull('complete_date');
                        $dueQuery->orWhere('complete_date', '>=', $endDate);
                    }
                })
                // ->where(function ($dueQuery) use ($branchID) {
                //     if (!empty($branchID)) {
                //         $dueQuery->where('branch_id', $branchID);
                //     }
                // })
                ->groupBy('sales_bill_no')
                ->orderBy($order, $dir)
                // ->orderBy('sales_date', 'ASC')
                ->get();
        } else {
            $dueQuery = DB::table('pos_sales_m')
                ->where([['is_delete', 0], ['is_active', 1], ['sales_type', 2]]) // ['is_opening', 0]
                ->whereIn('branch_id', $selBranchArr)
                ->selectRaw(
                    'branch_id, sales_bill_no, customer_id, sales_date, total_amount as sales_amount, installment_amount, employee_id,
                    installment_type, installment_month, installment_date as last_installment_date, inst_package_id,
                    IFNULL((paid_amount - vat_amount - service_charge),0) as first_instalment,
                    CONCAT(branch_id, "@", sales_date, "@", installment_type, "@", installment_month, "@", inst_package_id) as branch_date_type_month'
                )
                // (CASE
                //         WHEN installment_date >= "' . $endDate . '" THEN CONCAT(branch_id, "@", sales_date, "@", installment_type, "@", installment_month, "@", inst_package_id)
                //         ELSE "NULL@NULL@NULL@NULL@NULL"
                //     END) as branch_date_type_month
                // (CASE
                //         WHEN installment_type = 1 THEN installment_month
                //         ELSE (FLOOR(DATEDIFF(DATE(DATE_FORMAT(DATE_ADD(sales_date, INTERVAL +installment_month MONTH), "%Y%m%d")), DATE(DATE_FORMAT(sales_date, "%Y%m%d")))/7))
                //     END) as ttl_installment,
                ->where(function ($dueQuery) use ($employeeId) {
                    if (!empty($employeeId)) {
                        $dueQuery->where('employee_id', $employeeId);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        $dueQuery->where('sales_date', '<', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate, $dueFor) { // ## installment_date is instalment last date
                    if (!empty($endDate) && $dueFor == 'current_due') {
                        $dueQuery->where('installment_date', '>=', $endDate);
                    }
                    if (!empty($endDate) && $dueFor == 'over_due') {
                        $dueQuery->where('installment_date', '<', $endDate);
                    }
                    if (!empty($endDate) && $dueFor == 'regular_due') {
                        $dueQuery->where('installment_date', '>=', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        ## Complete Sales ignore
                        $dueQuery->whereNull('complete_date');
                        $dueQuery->orWhere('complete_date', '>=', $endDate);
                    }
                })
                // ->where(function ($dueQuery) use ($branchID) {
                //     if (!empty($branchID)) {
                //         $dueQuery->where('branch_id', $branchID);
                //     }
                // })
                ->orderBy($order, $dir)
                // ->orderBy('sales_date', 'ASC')
                ->get();
        }

        // dd($dueQuery);

        // // ## this is for ignore join or sub query
        if ($useFor == 'report') {
            $customerArr  = (!empty($dueQuery)) ? array_values($dueQuery->pluck('customer_id')->unique()->all()) : array();
            $customerData = array();

            $employeeArr  = (!empty($dueQuery)) ? array_values($dueQuery->pluck('employee_id')->unique()->all()) : array();
            $employeeData = self::fnForEmployeeData($employeeArr);

            if (count($customerArr) > 0) {

                if (Common::getDBConnection() == "sqlite") {
                    $customerData = DB::table('pos_customers')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('customer_no', $customerArr)
                        ->selectRaw('(customer_name || " [" || customer_no || "]") AS customer_name, customer_no')
                        ->pluck('customer_name', 'customer_no')
                        ->toArray();
                } else {
                    $customerData = DB::table('pos_customers')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('customer_no', $customerArr)
                        ->selectRaw('CONCAT(customer_name, " [", customer_no, "]") AS customer_name, customer_no')
                        ->pluck('customer_name', 'customer_no')
                        ->toArray();
                }

                // // This query is return array[key as customer_no] = value as a customer_name
            }
        }

        $salesBillArr   = (!empty($dueQuery)) ? array_values($dueQuery->pluck('sales_bill_no')->unique()->all()) : array();
        $collectionData = array();
        if (count($salesBillArr) > 0) {
            $collectionData = DB::table('pos_collections')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('sales_bill_no', $salesBillArr)
                ->where(function ($collectionData) use ($endDate) {
                    if (!empty($endDate)) {
                        $collectionData->where('collection_date', '<=', $endDate);
                    }
                })
                // ->groupBy('sales_bill_no')
                ->selectRaw('collection_amount, collection_date, sales_bill_no')
                // ->pluck('total_collection_amount', 'sales_bill_no')
                // ->toArray();
                ->get();
        }

        $allScheduleData = array();

        // if ($dueFor == 'current_due' || $dueFor == 'current_and_over_due') {

        $branchArr = (!empty($dueQuery)) ? array_values($dueQuery->pluck('branch_id')->unique()->all()) : array();
        // // ## Concat data by @ (branch@salesdate@installmentType@installmentMonth)
        $branchDateTypeMonthArr = (!empty($dueQuery)) ? array_values($dueQuery->pluck('branch_date_type_month')->unique()->all()) : array();

        $allScheduleData = self::installmentSchedule_multiple($companyID, $branchArr, $branchDateTypeMonthArr);
        $allScheduleData = $allScheduleData;
        // }

        // dd($allScheduleData);

        $sl                     = 0;
        $ttl_sales_amount       = 0;
        $ttl_payable_amount     = 0;
        $ttl_paid_amount        = 0;
        $ttl_paid_amount_period = 0;
        $ttl_current_due        = 0;

        $ttl_over_due      = 0;
        $ttl_total_balance = 0;
        $ttl_total_due     = 0;

        $DataSet = array();

        foreach ($dueQuery as $row) {

            // // ## Pasing Installment due to End Date
            $varBranchDateTypeMonth = $row->branch_date_type_month;
            $scheduleDate           = array();

            if (isset($allScheduleData[$varBranchDateTypeMonth])) {
                $scheduleDate = $allScheduleData[$varBranchDateTypeMonth];
            }

            $sales_amount = $row->sales_amount;
            // $total_installment = $row->ttl_installment;
            $total_installment           = count($scheduleDate);
            $total_installment_in_period = 0;
            // ## first_instalment
            $first_instalment_amount    = $row->first_instalment;
            $regular_installment_amount = $row->installment_amount;
            /** total installment theke 2 (-) karon last & first installment bad diye calculation kora hocche */
            $last_instalment_amount = ($sales_amount - ($first_instalment_amount + ($regular_installment_amount * ($total_installment - 2))));

            /// ## Get Collection Amount
            // $collection_amount = (isset($collectionData[$row->sales_bill_no])) ? $collectionData[$row->sales_bill_no] : 0;
            $collection_amount        = $collectionData->where('sales_bill_no', $row->sales_bill_no)->sum('collection_amount');
            $collection_amount_period = 0; ## this is for regular due calculation

            $outstanding_balance_amount = ($sales_amount - $collection_amount);
            $over_due_amount            = ($sales_amount - $collection_amount);

            $advance_or_ob_due   = 0;
            $current_due         = 0;
            $payable_amount_till = 0;

            ## calculation For Current Due
            if ($dueFor == 'current_due' || $dueFor == 'current_and_over_due') {

                ## if running installment then calculate due
                if ($row->last_installment_date >= $endDate) {
                    $over_due_amount       = 0;
                    $payable_amount_till   = 0;
                    $first_instalment_flag = false;

                    // // ## Count having installment date
                    $passInstallment = 0;
                    foreach ($scheduleDate as $value) {
                        if ($value <= $endDate) {
                            if ($value == $scheduleDate[0]) {
                                $first_instalment_flag = true;
                            }
                            $passInstallment++;
                            ## count this installment
                        } else {
                            break; // if enddate gater break the loop
                        }
                    }

                    $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0

                    ## First installment
                    if ($first_instalment_flag) {
                        ## Ignore 1st instalment count
                        $passInstallment = $passInstallment - 1;
                        $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                        /** Payable amount a  plus first installemnt */
                        $payable_amount_till += $first_instalment_amount;
                        $total_installment_in_period += 1;
                    }

                    ## last installment
                    if ($endDate >= $row->last_installment_date) {
                        ## Ignore Last instalment count
                        $passInstallment = $passInstallment - 1;
                        $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                        /** Payable amount a  plus last installemnt */
                        $payable_amount_till += $last_instalment_amount;
                        $total_installment_in_period += 1;
                    }
                    ##add counted installment * regular_inst_amount to find total payable amount
                    $payable_amount_till += ($regular_installment_amount * $passInstallment);
                    $total_installment_in_period += $passInstallment;
                    $current_due = $payable_amount_till - $collection_amount;

                    if ($current_due <= 0) { # if current due less than 0 then NO due
                        $current_due = 0;
                    }
                }
            }

            if ($dueFor == 'regular_due') {
                ## collectin amount in this period =

                $onedateback = date('Y-m-d', (strtotime('-1 day', strtotime($startDate))));

                $collection_amount = $collectionData->where('sales_bill_no', $row->sales_bill_no)
                    ->where('collection_date', '<=', $onedateback)->sum('collection_amount');

                $collection_amount_period = $collectionData->where('sales_bill_no', $row->sales_bill_no)
                    ->where('collection_date', '>=', $startDate)->where('collection_date', '<=', $endDate)->sum('collection_amount');

                ## if running installment then calculate regular due
                if ($row->last_installment_date >= $endDate) {
                    $over_due_amount     = 0;
                    $payable_amount_till = 0;
                    $payable_amount_ob   = 0;

                    $first_instalment_flag    = false;
                    $first_instalment_flag_ob = false;

                    ## count having installment date during period
                    $passInstallment_ob = 0;
                    $passInstallment    = 0;

                    foreach ($scheduleDate as $value) {
                        if ($startDate <= $value && $endDate >= $value) {
                            if ($value == $scheduleDate[0]) {
                                $first_instalment_flag = true;
                            }
                            $passInstallment++;
                            ## count this installment
                        } else {
                            if ($startDate > $value) {
                                if ($value == $scheduleDate[0]) {
                                    $first_instalment_flag_ob = true;
                                }
                                $passInstallment_ob++;
                                ## count this installment ob
                            }

                            if ($value > $endDate) {
                                break; // break the loop
                            } else {
                                continue; // continue the loop
                            }
                        }
                    }

                    $passInstallment    = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                    $passInstallment_ob = ($passInstallment_ob < 0) ? 0 : $passInstallment_ob; // if 0 or less make it 0
                    ## First installment
                    if ($first_instalment_flag) {
                        ## Ignore 1st instalment count
                        $passInstallment = $passInstallment - 1;
                        $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                        /** Payable amount a  plus first installemnt */
                        $payable_amount_till += $first_instalment_amount;
                        $total_installment_in_period += 1;
                    }

                    if ($first_instalment_flag_ob) {
                        ## Ignore 1st instalment count ob
                        $passInstallment_ob = $passInstallment_ob - 1;
                        $passInstallment_ob = ($passInstallment_ob < 0) ? 0 : $passInstallment_ob; // if 0 or less make it 0
                        /** Payable amount ob a  plus first installemnt */
                        $payable_amount_ob += $first_instalment_amount;
                    }

                    ## last installment
                    if ($endDate == $row->last_installment_date) {
                        ## Ignore Last instalment count
                        $passInstallment = $passInstallment - 1;
                        $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                        /** Payable amount a  plus last installemnt */
                        $payable_amount_till += $last_instalment_amount;
                        $total_installment_in_period += 1;
                    }
                    ##add counted installment * regular_inst_amount to find total payable amount
                    $payable_amount_till += ($regular_installment_amount * $passInstallment);
                    $payable_amount_ob += ($regular_installment_amount * $passInstallment_ob);

                    $total_installment_in_period += $passInstallment;

                    $advance_or_ob_due = $payable_amount_ob - $collection_amount;

                    $advance = ($advance_or_ob_due < 0) ? abs($advance_or_ob_due) : 0;
                    $due     = ($advance_or_ob_due >= 0) ? $advance_or_ob_due : 0;

                    #$payable_amount_till 14 - ()
                    $regular_due = $payable_amount_till - ($collection_amount_period + $advance);

                    // if($row->sales_bill_no == 'SL00020000015'){
                    //     dd($advance_or_ob_due , $collection_amount , $payable_amount_ob,$regular_installment_amount,$passInstallment);
                    // }

                    $current_due = $regular_due;
                    ## current due define the regular due
                    if ($current_due <= 0) { ## if due less than 0 then NO due
                        $current_due = 0;
                    }
                }
            }

            if ($current_due > 0 || $over_due_amount > 0) {
                $TempSet       = array();
                $employee_info = "";
                $customer_info = "";
                if ($useFor == 'report') {
                    $customer_info = (isset($customerData[$row->customer_id])) ? $customerData[$row->customer_id] : "";
                    $employee_info = (isset($employeeData[$row->employee_id])) ? $employeeData[$row->employee_id] : "";
                }

                $TempSet = [
                    'sl'                    => ++$sl,
                    'customer_name'         => $customer_info,
                    'customer_id'           => $row->customer_id,
                    'employee_id'           => $row->employee_id,
                    'branch_id'             => $row->branch_id,
                    'employee_name'         => $employee_info,
                    'sales_bill_no'         => $row->sales_bill_no,
                    'sales_date'            => (new DateTime($row->sales_date))->format('d-m-Y'),
                    'sales_amount'          => $sales_amount,
                    'installment_type'      => $row->installment_type,
                    'installment_month'     => $row->installment_month,
                    'installment'           => $total_installment,
                    'installment_in_period' => $total_installment_in_period,
                    'first_installment'     => $first_instalment_amount,
                    'installment_amount'    => $regular_installment_amount,
                    'last_installment'      => $last_instalment_amount,
                    'last_installment_date' => (new DateTime($row->last_installment_date))->format('d-m-Y'),

                    'payable_amount'        => ($payable_amount_till > 0) ? $payable_amount_till : '-',
                    'paid_amount'           => $collection_amount,
                    'paid_amount_period'    => $collection_amount_period,
                    'current_due'           => ($current_due > 0) ? $current_due : '-',
                    'advance_or_ob_due'     => $advance_or_ob_due,

                    'over_due'              => ($over_due_amount > 0) ? $over_due_amount : '-',
                    'total_balance'         => $outstanding_balance_amount,
                ];

                $DataSet[] = $TempSet;

                $ttl_sales_amount += $sales_amount;
                $ttl_payable_amount += $payable_amount_till;
                $ttl_paid_amount += $collection_amount;
                $ttl_paid_amount_period += $collection_amount_period;
                $ttl_current_due += $current_due;
                $ttl_over_due += $over_due_amount;
                $ttl_total_balance += $outstanding_balance_amount;
            }
        }

        $ttl_total_due = $ttl_current_due + $ttl_over_due;

        if ($useFor == 'end_execution') {
            $result_set = [
                'ttl_current_due' => $ttl_current_due,
                'ttl_over_due'    => $ttl_over_due,
                'ttl_total_due'   => $ttl_total_due,
            ];
        } elseif ($useFor == 'report') {
            $result_set = [
                'ttl_sales_amount'       => $ttl_sales_amount,
                'ttl_payable_amount'     => $ttl_payable_amount,
                'ttl_paid_amount'        => $ttl_paid_amount,
                'ttl_paid_amount_period' => $ttl_paid_amount_period,
                'ttl_current_due'        => $ttl_current_due,

                'ttl_over_due'           => $ttl_over_due,
                'ttl_total_balance'      => $ttl_total_balance,
                'ttl_total_due'          => $ttl_total_due,
                'report_data'            => serialize($DataSet),
            ];
        }

        return $result_set;
    }

    /**
     * @dueFor = 'current_due' or 'over_due' or 'current_and_over_due' or 'regular_due'
     * @useFor = 'report' or 'end_execution'
     * @viewMethod is used for branch wise report or total due show
     * @viewMethod = 'single' or 'branch_wise'
     */
    public static function installmentStatus($bills, ...$dates)
    {
        $endDate   = null;
        $startDate = null;
        $companyID = Common::getCompanyId();

        if (isset($dates[1])) {
            $endDate   = date('Y-m-d', strtotime($dates[1]));
            $startDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            $endDate = date('Y-m-d', strtotime($dates[0]));
        }

        if ($startDate > $endDate) {
            return 'From date should be appear first';
        }

        if (!is_array($bills)) {
            $bills = array($bills);
        }

        $salesBillArr = $bills;
        $SalesQuery   = DB::table('pos_sales_m')
            ->where([['is_delete', 0], ['is_active', 1], ['sales_type', 2]])
            ->whereIn('sales_bill_no', $salesBillArr)
            ->selectRaw(
                'branch_id, sales_bill_no, customer_id, sales_date, total_amount as sales_amount, installment_amount, employee_id,
                installment_type, installment_month, installment_date as last_installment_date, inst_package_id,
                IFNULL((paid_amount - vat_amount - service_charge),0) as first_instalment,
                CONCAT(branch_id, "@", sales_date, "@", installment_type, "@", installment_month, "@", inst_package_id) as branch_date_type_month'
            )
            ->where(function ($SalesQuery) use ($endDate) {
                if (!empty($endDate)) {
                    $SalesQuery->where('sales_date', '<', $endDate);
                }
            })
            // ->where(function ($SalesQuery) use ($endDate) {
            //     if (!empty($endDate)) {
            //         ## Complete Sales ignore
            //         $SalesQuery->whereNull('complete_date');
            //         $SalesQuery->orWhere('complete_date', '>=', $endDate);
            //     }
            // })
            ->get();

        $collectionData = DB::table('pos_collections')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->whereIn('sales_bill_no', $salesBillArr)
            ->where(function ($collectionData) use ($endDate) {
                if (!empty($endDate)) {
                    $collectionData->where('collection_date', '<=', $endDate);
                }
            })
            // ->groupBy('sales_bill_no')
            ->selectRaw('collection_amount, collection_date, sales_bill_no')
            // ->pluck('total_collection_amount', 'sales_bill_no')
            // ->toArray();
            ->get();

        if ($startDate != null) {
            $collectionDataOnPeriod = clone $collectionData;
            $collectionDataOnPeriod = $collectionDataOnPeriod->where('collection_date', '>=', $startDate);
        }

        ## get all schedule
        $allScheduleData = array();
        $branchArr       = (!empty($SalesQuery)) ? array_values($SalesQuery->pluck('branch_id')->unique()->all()) : array();
        ## Concat data by @ (branch@salesdate@installmentType@installmentMonth)
        $branchDateTypeMonthArr = (!empty($SalesQuery)) ? array_values($SalesQuery->pluck('branch_date_type_month')->unique()->all()) : array();
        $allScheduleData        = self::installmentSchedule_multiple($companyID, $branchArr, $branchDateTypeMonthArr);
        $allScheduleData        = $allScheduleData;

        $SalesStatus = [];
        $DataSet     = array();

        foreach ($SalesQuery as $key => $row) {

            // // ## Pasing Installment due to End Date
            $varBranchDateTypeMonth = $row->branch_date_type_month;
            $scheduleDate           = array();

            if (isset($allScheduleData[$varBranchDateTypeMonth])) {
                $scheduleDate = $allScheduleData[$varBranchDateTypeMonth];
            }

            $sales_amount             = $row->sales_amount;
            $data['sales_amount']     = $sales_amount;
            $data['sales_bill_no']    = $row->sales_bill_no;
            $data['installment_type'] = ($row->installment_type == 1) ? "Monthly" : "Weekly";

            $total_installment         = count($scheduleDate);
            $data['total_installment'] = $total_installment;
            ## first_instalment
            $first_instalment_amount    = $row->first_instalment;
            $regular_installment_amount = $row->installment_amount;
            /** total installment theke 2 (-) karon last & first installment bad diye calculation kora hocche */
            $last_instalment_amount = ($sales_amount - ($first_instalment_amount + ($regular_installment_amount * ($total_installment - 2))));

            ## Get Collection Amount
            // $collection_amount = (isset($collectionData[$row->sales_bill_no])) ? $collectionData[$row->sales_bill_no] : 0;
            $collection_amount = $collectionData->where('sales_bill_no', $row->sales_bill_no)->sum('collection_amount');

            $outstanding_balance_amount = ($sales_amount - $collection_amount);
            // $over_due_amount            = ($sales_amount - $collection_amount);

            ##variables
            $payable_amount_till       = 0;
            $total_installment_counter = 0;
            $first_instalment_flag     = false;
            $passInstallment           = 0;

            foreach ($scheduleDate as $value) {
                if ($value <= $endDate) {
                    if ($value == $scheduleDate[0]) {
                        $first_instalment_flag = true;
                    }
                    $passInstallment++;
                    ## count this installment
                } else {
                    break; // if enddate gater break the loop
                }
            }

            // dd($scheduleDate,$endDate, $passInstallment);

            $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
            ## First installment
            if ($first_instalment_flag) {
                ## Ignore 1st instalment count
                $passInstallment = $passInstallment - 1;
                $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                /** Payable amount a  plus first installemnt */
                $payable_amount_till += $first_instalment_amount;
                $total_installment_counter += 1;
            }

            ## last installment
            if ($endDate >= $row->last_installment_date) {
                ## Ignore Last instalment count
                $passInstallment = $passInstallment - 1;
                $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                /** Payable amount a  plus last installemnt */
                $payable_amount_till += $last_instalment_amount;
                $total_installment_counter += 1;
            }
            ##add counted installment * regular_inst_amount to find total payable amount
            $payable_amount_till += ($regular_installment_amount * $passInstallment);
            $current_due = $payable_amount_till - $collection_amount;
            $advance     = $current_due < 0 ? $current_due : 0;
            $current_due = $current_due > 0 ? $current_due : 0;
            $total_installment_counter += $passInstallment;

            $data['payableAmount']     = $payable_amount_till;
            $data['paidAmount']        = $collection_amount;
            $data['dueAmount']         = $current_due;
            $data['advanceAmount']     = abs($advance);
            $data['instalmentNo']      = $total_installment_counter;
            $data['outstandingAmount'] = $outstanding_balance_amount;

            #opening  data
            $data['openingPayableAmount'] = 0;
            $data['openingPaidAmount']    = 0;
            $data['openingDueAmount']     = 0;
            $data['openingAdvanceAmount'] = 0;
            $data['openingInstalmentNo']  = 0;
            #on Period  data
            $data['onPeriodPayableAmount'] = 0;
            $data['onPeriodPaidAmount']    = 0;
            $data['onPeriodDueAmount']     = 0;
            $data['onPeriodAdvanceAmount'] = 0;
            $data['onPeriodInstalmentNo']  = 0;
            // $data['overdue'] = $over_due_amount;

            if ($startDate != null) {

                $onedateback = date('Y-m-d', (strtotime('-1 day', strtotime($startDate))));

                $collection_amount_opening = $collectionData->where('sales_bill_no', $row->sales_bill_no)
                    ->where('collection_date', '<=', $onedateback)->sum('collection_amount');

                $collection_amount_period = $collectionData->where('sales_bill_no', $row->sales_bill_no)
                    ->where('collection_date', '>=', $startDate)->where('collection_date', '<=', $endDate)->sum('collection_amount');

                $payable_amount_opening            = 0;
                $payable_amount_period             = 0;
                $total_installment_counter_period  = 0;
                $total_installment_counter_opening = 0;
                $first_instalment_flag             = false;
                $first_instalment_flag_opening     = false;

                $last_instalment_flag             = false;
                $last_instalment_flag_opening     = false;

                ## count having installment date during period
                $passInstallment         = 0;
                $passInstallment_opening = 0;
                foreach ($scheduleDate as $value) {
                    if ($startDate <= $value && $endDate >= $value) {
                        if ($value == $scheduleDate[0]) {
                            $first_instalment_flag_period = true;
                        }
                        $passInstallment++;
                        ## count this installment
                    } else {
                        if ($startDate > $value) {
                            if ($value == $scheduleDate[0]) {
                                $first_instalment_flag_opening = true;
                            }
                            $passInstallment_opening++;
                            ## count this installment ob
                        }

                        if ($value > $endDate) {
                            break; // break the loop
                        } else {
                            continue; // continue the loop
                        }
                    }
                }


                if ($passInstallment + $passInstallment_opening >= $total_installment) {
                    $last_instalment_flag             = true;
                    $last_instalment_flag_opening     = false;
                }
                if ($passInstallment_opening == $total_installment) {
                    $last_instalment_flag             = false;
                    $last_instalment_flag_opening     = true;
                }


                $passInstallment         = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                $passInstallment_opening = ($passInstallment_opening < 0) ? 0 : $passInstallment_opening; // if 0 or less make it 0
                ## First installment
                if ($first_instalment_flag) {
                    ## Ignore 1st instalment count
                    $passInstallment = $passInstallment - 1;
                    $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                    /** Payable amount a  plus first installemnt */
                    $payable_amount_period += $first_instalment_amount;
                    $total_installment_counter_period += 1;
                }

                #first installment in opening
                if ($first_instalment_flag_opening) {
                    ## Ignore 1st instalment count ob
                    $passInstallment_opening = $passInstallment_opening - 1;
                    $passInstallment_opening = ($passInstallment_opening < 0) ? 0 : $passInstallment_opening; // if 0 or less make it 0
                    /** Payable amount ob a  plus first installemnt */
                    $payable_amount_opening += $first_instalment_amount;
                    $total_installment_counter_opening += 1;
                }

                ## last installment
                // if () {
                if ($endDate >= $row->last_installment_date && $last_instalment_flag) {
                    ## Ignore Last instalment count
                    $passInstallment = $passInstallment - 1;
                    $passInstallment = ($passInstallment < 0) ? 0 : $passInstallment; // if 0 or less make it 0
                    /** Payable amount a  plus last installemnt */
                    $payable_amount_period += $last_instalment_amount;
                    $total_installment_counter_period += 1;
                }

                #Last installment in opening
                if ($endDate >= $row->last_installment_date && $last_instalment_flag_opening) {
                    ## Ignore 1st instalment count ob
                    $passInstallment_opening = $passInstallment_opening - 1;
                    $passInstallment_opening = ($passInstallment_opening < 0) ? 0 : $passInstallment_opening; // if 0 or less make it 0
                    /** Payable amount ob a  plus first installemnt */
                    $payable_amount_opening += $last_instalment_amount;
                    $total_installment_counter_opening += 1;
                }

                $total_installment_counter_period += $passInstallment;
                $total_installment_counter_opening += $passInstallment_opening;
                ##add counted installment * regular_inst_amount to find total payable amount
                $payable_amount_period += ($regular_installment_amount * $passInstallment);
                $payable_amount_opening += ($regular_installment_amount * $passInstallment_opening);

                #opening calculation
                $opening_due     = $payable_amount_opening - $collection_amount_opening;
                $opening_advance = $opening_due < 0 ? $opening_due : 0;
                $opening_due     = $opening_due > 0 ? $opening_due : 0;
                #on Period calculation
                $period_due     = $payable_amount_period - $collection_amount_period;
                $period_advance = $period_due < 0 ? $period_due : 0;
                $period_due     = $period_due > 0 ? $period_due : 0;
                #opening data
                $data['openingPayableAmount'] = $payable_amount_opening;
                $data['openingPaidAmount']    = $collection_amount_opening;
                $data['openingDueAmount']     = $opening_due;
                $data['openingAdvanceAmount'] = abs($opening_advance);
                $data['openingInstalmentNo']  = $total_installment_counter_opening;
                #on Period  data
                $data['onPeriodPayableAmount'] = $payable_amount_period;
                $data['onPeriodPaidAmount']    = $collection_amount_period;
                $data['onPeriodDueAmount']     = $period_due;
                $data['onPeriodAdvanceAmount'] = abs($period_advance);
                $data['onPeriodInstalmentNo']  = $total_installment_counter_period;
            }

            array_push($SalesStatus, $data);
        }

        return $SalesStatus;
    }

    public static function due_calculation($companyID = null, $selBranchArr = [], $endDate = null, $dueFor = 'current_and_over_due', $useFor = 'end_execution', $viewMethod = 'single', $startDate = null, $employeeId = null, $order = null, $dir = null, $salesBillOrCode = null)
    {
        /**
         * @dueFor = 'current_due' or 'over_due' or 'current_and_over_due'
         * @useFor = 'report' or 'end_execution'
         * @viewMethod is used for branch wise report or total due show
         * @viewMethod = 'single' or 'branch_wise'
         */

        if (empty($order)) {
            $order = 'sales_date';
            $dir   = 'ASC';
        }

        $dueForCheckArr = ['current_due', 'over_due', 'current_and_over_due'];
        $useForCheckArr = ['report', 'end_execution'];

        if (count($selBranchArr) < 1 || empty($endDate) || in_array($dueFor, $dueForCheckArr) == false || in_array($useFor, $useForCheckArr) == false) {
            return false;
        }
        $companyID = (empty($companyID)) ? Common::getCompanyId() : $companyID;

        ///////// start work
        if (Common::getDBConnection() == "sqlite") {

            $dueQuery = DB::table('pos_sales_m')
                ->where([['is_delete', 0], ['is_active', 1], ['sales_type', 2]]) // ['is_opening', 0]
                ->whereIn('branch_id', $selBranchArr)
                ->selectRaw(
                    'branch_id, sales_bill_no, customer_id, sales_date, total_amount as sales_amount, installment_amount,employee_id,
                    installment_type, installment_month, installment_date as last_installment_date, inst_package_id,
                    IFNULL((paid_amount - vat_amount - service_charge),0) as first_instalment,
                    (branch_id || "@" || sales_date || "@" || installment_type || "@" || installment_month || "@" || inst_package_id) as branch_date_type_month'
                )
                ->where(function ($dueQuery) use ($employeeId) {
                    if (!empty($employeeId)) {
                        $dueQuery->where('employee_id', $employeeId);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        $dueQuery->where('sales_date', '<', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate, $dueFor) { // ## installment_date is instalment last date
                    if (!empty($endDate) && $dueFor == 'current_due') {
                        $dueQuery->where('installment_date', '>=', $endDate);
                    }
                    if (!empty($endDate) && $dueFor == 'over_due') {
                        $dueQuery->where('installment_date', '<', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        ## Complete Sales ignore
                        $dueQuery->whereNull('complete_date');
                        $dueQuery->orWhere('complete_date', '>=', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($salesBillOrCode) {
                    if (!empty($salesBillOrCode)) {
                        $dueQuery->where('sales_bill_no', 'LIKE', "%$salesBillOrCode%");
                    }
                })
                // ->where(function ($dueQuery) use ($branchID) {
                //     if (!empty($branchID)) {
                //         $dueQuery->where('branch_id', $branchID);
                //     }
                // })
                ->groupBy('sales_bill_no')
                ->orderBy($order, $dir)
                ->orderBy('sales_date', 'ASC')
                ->get();
        } else {
            $dueQuery = DB::table('pos_sales_m')
                ->where([['is_delete', 0], ['is_active', 1], ['sales_type', 2]]) // ['is_opening', 0]
                ->whereIn('branch_id', $selBranchArr)
                ->selectRaw(
                    'branch_id, sales_bill_no, customer_id, sales_date, total_amount as sales_amount, installment_amount, employee_id,
                    installment_type, installment_month, installment_date as last_installment_date, inst_package_id,
                    IFNULL((paid_amount - vat_amount - service_charge),0) as first_instalment,
                    CONCAT(branch_id, "@", sales_date, "@", installment_type, "@", installment_month, "@", inst_package_id) as branch_date_type_month'
                )
                // (CASE
                //         WHEN installment_date >= "' . $endDate . '" THEN CONCAT(branch_id, "@", sales_date, "@", installment_type, "@", installment_month, "@", inst_package_id)
                //         ELSE "NULL@NULL@NULL@NULL@NULL"
                //     END) as branch_date_type_month
                // (CASE
                //         WHEN installment_type = 1 THEN installment_month
                //         ELSE (FLOOR(DATEDIFF(DATE(DATE_FORMAT(DATE_ADD(sales_date, INTERVAL +installment_month MONTH), "%Y%m%d")), DATE(DATE_FORMAT(sales_date, "%Y%m%d")))/7))
                //     END) as ttl_installment,
                ->where(function ($dueQuery) use ($employeeId) {
                    if (!empty($employeeId)) {
                        $dueQuery->where('employee_id', $employeeId);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        $dueQuery->where('sales_date', '<', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate, $dueFor) { // ## installment_date is instalment last date
                    if (!empty($endDate) && $dueFor == 'current_due') {
                        $dueQuery->where('installment_date', '>=', $endDate);
                    }
                    if (!empty($endDate) && $dueFor == 'over_due') {
                        $dueQuery->where('installment_date', '<', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($endDate) {
                    if (!empty($endDate)) {
                        ## Complete Sales ignore
                        $dueQuery->whereNull('complete_date');
                        $dueQuery->orWhere('complete_date', '>=', $endDate);
                    }
                })
                ->where(function ($dueQuery) use ($salesBillOrCode) {
                    if (!empty($salesBillOrCode)) {
                        $dueQuery->where('sales_bill_no', 'LIKE', "%$salesBillOrCode%");
                    }
                })
                // ->where(function ($dueQuery) use ($branchID) {
                //     if (!empty($branchID)) {
                //         $dueQuery->where('branch_id', $branchID);
                //     }
                // })
                ->orderBy($order, $dir)
                ->orderBy('sales_date', 'ASC')
                ->get();
        }

        // dd($dueQuery);

        // // ## this is for ignore join or sub query
        if ($useFor == 'report') {
            $customerArr  = (!empty($dueQuery)) ? array_values($dueQuery->pluck('customer_id')->unique()->all()) : array();
            $customerData = array();

            $employeeArr  = (!empty($dueQuery)) ? array_values($dueQuery->pluck('employee_id')->unique()->all()) : array();
            $employeeData = self::fnForEmployeeData($employeeArr);

            if (count($customerArr) > 0) {

                if (Common::getDBConnection() == "sqlite") {
                    $customerData = DB::table('pos_customers')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('customer_no', $customerArr)
                        ->selectRaw('(customer_name || " [" || customer_no || "]") AS customer_name, customer_no')
                        ->pluck('customer_name', 'customer_no')
                        ->toArray();
                } else {
                    $customerData = DB::table('pos_customers')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->whereIn('customer_no', $customerArr)
                        ->selectRaw('CONCAT(customer_name, " [", customer_no, "]") AS customer_name, customer_no')
                        ->pluck('customer_name', 'customer_no')
                        ->toArray();
                }

                // // This query is return array[key as customer_no] = value as a customer_name
            }
        }

        $salesBillArr   = (!empty($dueQuery)) ? array_values($dueQuery->pluck('sales_bill_no')->unique()->all()) : array();
        $collectionData = array();
        if (count($salesBillArr) > 0) {
            $collectionData = DB::table('pos_collections')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('sales_bill_no', $salesBillArr)
                ->where(function ($collectionData) use ($endDate) {
                    if (!empty($endDate)) {
                        $collectionData->where('collection_date', '<=', $endDate);
                    }
                })
                ->groupBy('sales_bill_no')
                ->selectRaw('IFNULL(SUM(collection_amount), 0) AS total_collection_amount, sales_bill_no')
                ->pluck('total_collection_amount', 'sales_bill_no')
                ->toArray();
        }

        $allScheduleData = array();

        // if ($dueFor == 'current_due' || $dueFor == 'current_and_over_due') {

        $branchArr = (!empty($dueQuery)) ? array_values($dueQuery->pluck('branch_id')->unique()->all()) : array();
        // // ## Concat data by @ (branch@salesdate@installmentType@installmentMonth)
        $branchDateTypeMonthArr = (!empty($dueQuery)) ? array_values($dueQuery->pluck('branch_date_type_month')->unique()->all()) : array();

        $allScheduleData = self::installmentSchedule_multiple($companyID, $branchArr, $branchDateTypeMonthArr);
        $allScheduleData = $allScheduleData;
        // }

        // dd($allScheduleData);

        $sl                 = 0;
        $ttl_sales_amount   = 0;
        $ttl_payable_amount = 0;
        $ttl_paid_amount    = 0;
        $ttl_current_due    = 0;
        $ttl_over_due       = 0;
        $ttl_total_balance  = 0;
        $ttl_total_due      = 0;

        $DataSet = array();

        foreach ($dueQuery as $row) {

            // // ## Pasing Installment due to End Date
            $varBranchDateTypeMonth = $row->branch_date_type_month;
            $scheduleDate           = array();

            if (isset($allScheduleData[$varBranchDateTypeMonth])) {
                $scheduleDate = $allScheduleData[$varBranchDateTypeMonth];
            }

            $sales_amount = $row->sales_amount;
            // $total_installment = $row->ttl_installment;
            $total_installment = count($scheduleDate);
            // ## first_instalment
            $first_instalment_amount    = $row->first_instalment;
            $regular_installment_amount = $row->installment_amount;
            /** total installment theke 2 (-) karon last & first installment bad diye calculation kora hocche */
            $last_instalment_amount = ($sales_amount - ($first_instalment_amount + ($regular_installment_amount * ($total_installment - 2))));

            /// ## Get Collection Amount
            $collection_amount = (isset($collectionData[$row->sales_bill_no])) ? $collectionData[$row->sales_bill_no] : 0;

            $outstanding_balance_amount = ($sales_amount - $collection_amount);
            $over_due_amount            = ($sales_amount - $collection_amount);

            $current_due         = 0;
            $payable_amount_till = 0;

            /// // ## calculation For Current Due
            if ($dueFor == 'current_due' || $dueFor == 'current_and_over_due') {
                if ($row->last_installment_date >= $endDate) {
                    $over_due_amount     = 0;
                    $payable_amount_till = 0;

                    // // // ## Pasing Installment due to End Date
                    // $varBranchDateTypeMonth = $row->branch_date_type_month;
                    // $scheduleDate = array();

                    // if (isset($allScheduleData[$varBranchDateTypeMonth])) {
                    //     $scheduleDate = $allScheduleData[$varBranchDateTypeMonth];
                    // }

                    // // ## find passing installment date
                    $passInstallment = 0;
                    foreach ($scheduleDate as $value) {
                        if ($value <= $endDate) {
                            $passInstallment++;
                        } else {
                            break;
                        }
                    }

                    // // ## Ignore 1st instalment count
                    $passInstallment = $passInstallment - 1;

                    /** Payable amount a  */
                    $payable_amount_till = $first_instalment_amount + ($regular_installment_amount * $passInstallment);

                    if ($endDate == $row->last_installment_date) {
                        // // ## Ignore Last instalment count
                        $passInstallment     = $passInstallment - 1;
                        $payable_amount_till = $first_instalment_amount + ($regular_installment_amount * $passInstallment) + $last_instalment_amount;
                    }

                    $current_due = $collection_amount - $payable_amount_till;

                    if ($current_due >= 0) {
                        $current_due = 0;
                    } else {
                        $current_due = abs($current_due);
                    }
                }
            }

            if ($current_due > 0 || $over_due_amount > 0) {
                $TempSet       = array();
                $employee_info = "";
                $customer_info = "";
                if ($useFor == 'report') {
                    $customer_info = (isset($customerData[$row->customer_id])) ? $customerData[$row->customer_id] : "";
                    $employee_info = (isset($employeeData[$row->employee_id])) ? $employeeData[$row->employee_id] : "";
                }

                $TempSet = [
                    'sl'                    => ++$sl,
                    'customer_name'         => $customer_info,
                    'employee_name'         => $employee_info,
                    'sales_bill_no'         => $row->sales_bill_no,
                    'sales_date'            => (new DateTime($row->sales_date))->format('d-m-Y'),
                    'sales_amount'          => number_format($sales_amount, 2),

                    'installment'           => $total_installment,
                    'first_installment'     => number_format($first_instalment_amount, 2),
                    'installment_amount'    => number_format($regular_installment_amount, 2),
                    'last_installment'      => number_format($last_instalment_amount, 2),
                    'last_installment_date' => (new DateTime($row->last_installment_date))->format('d-m-Y'),

                    'payable_amount'        => ($payable_amount_till > 0) ? number_format($payable_amount_till, 2) : '-',
                    'paid_amount'           => number_format($collection_amount, 2),
                    'current_due'           => ($current_due > 0) ? number_format($current_due, 2) : '-',
                    'over_due'              => ($over_due_amount > 0) ? number_format($over_due_amount, 2) : '-',
                    'total_balance'         => number_format($outstanding_balance_amount, 2),
                ];

                $DataSet[] = $TempSet;

                $ttl_sales_amount += $sales_amount;
                $ttl_payable_amount += $payable_amount_till;
                $ttl_paid_amount += $collection_amount;
                $ttl_current_due += $current_due;
                $ttl_over_due += $over_due_amount;
                $ttl_total_balance += $outstanding_balance_amount;
            }
        }

        $ttl_total_due = $ttl_current_due + $ttl_over_due;

        if ($useFor == 'end_execution') {
            $result_set = [
                'ttl_current_due' => $ttl_current_due,
                'ttl_over_due'    => $ttl_over_due,
                'ttl_total_due'   => $ttl_total_due,
            ];
        } elseif ($useFor == 'report') {
            $result_set = [
                'ttl_sales_amount'   => $ttl_sales_amount,
                'ttl_payable_amount' => $ttl_payable_amount,
                'ttl_paid_amount'    => $ttl_paid_amount,
                'ttl_current_due'    => $ttl_current_due,
                'ttl_over_due'       => $ttl_over_due,
                'ttl_total_balance'  => $ttl_total_balance,
                'ttl_total_due'      => $ttl_total_due,
                'report_data'        => serialize($DataSet),
            ];
        }

        return $result_set;
    }

    /** End Due Calculation function */

    /** start Stock function  */
    /**
     * Stock Count for Product
     */
    public static function stockQuantity($branchID, $ProductID, $returnArray = false, $startDate = null, $endDate = null)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
            // $fromDate = $fromDate->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
            // $toDate = $toDate->format('Y-m-d');
        } else {
            $toDate = (new DateTime(Common::systemCurrentDate($branchID, 'pos')))->format('Y-m-d');
            // $toDate = new DateTime();

            // $toDate = $toDate->format('Y-m-d');
        }

        // $productSearch = false;
        // if (count($ProductID) < 951) {
        //     $productSearch = true;
        // }

        if ($branchID >= 1 && !empty($ProductID)) {

            $Stock          = 0;
            $PreOB          = 0;
            $OpeningBalance = 0;
            $Purchase       = 0;
            $PurchaseReturn = 0;
            $Issue          = 0;
            $IssueReturn    = 0;
            $TransferIn     = 0;
            $TransferOut    = 0;
            $Sales          = 0;
            $SalesReturn    = 0;
            $Adjustment     = 0;
            $waiverProduct  = 0;

            /* Model Load */
            $POpeningBalance       = 'App\\Model\\POS\\POBStockDetails';
            $PurchaseDetails       = 'App\\Model\\POS\\PurchaseDetails';
            $PurchaseReturnDetails = 'App\\Model\\POS\\PurchaseReturnDetails';
            $IssueDetails          = 'App\\Model\\POS\\Issued';
            $IssueReturnDetails    = 'App\\Model\\POS\\IssueReturnd';
            $TransferDetails       = 'App\\Model\\POS\\TransferDetails';
            $SalesDetails          = 'App\\Model\\POS\\SalesDetails';
            $SaleReturnd           = 'App\\Model\\POS\\SaleReturnd';

            ## Opening Balance Count
            $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                ->where(function ($OpeningBalance) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                    $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
                        ->where('obd.product_id', $ProductID);
                })
                ->sum('obd.product_quantity');

            ## Purchase Balance Count
            $Purchase = DB::table('pos_purchases_m as pm')
                ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                ->where(function ($Purchase) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_d as pd', function ($Purchase) use ($ProductID) {
                    $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
                        ->where('pd.product_id', $ProductID);
                })
                ->sum('pd.product_quantity');

            ## Purchase Return Count

            $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                    $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
                        ->where('prd.product_id', $ProductID);
                })
                ->sum('prd.product_quantity');

            ## Waiver Product Balance Count
            $waiverProduct = DB::table('pos_waiver_product_m as psm')
                ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $waiverProduct->where('psm.date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->where('psm.date', '<=', $toDate);
                    }
                })
                ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                    $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no')
                        ->where('psd.product_id', $ProductID);
                })
                ->sum('psd.product_quantity');

            ## Adjustment Audit  Count
            $Adjustment = DB::table('pos_audit_m as am')
                ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                ->where(function ($Adjustment) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Adjustment->where('am.audit_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Adjustment->where('am.audit_date', '<=', $toDate);
                    }
                })
                ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID) {
                    $Adjustment->on('ad.audit_code', 'am.audit_code')
                        ->where('ad.product_id', $ProductID);
                })
                ->sum('ad.product_quantity');
            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                // Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    ->sum('isd.product_quantity');

                // dd($Issue);

                // Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    ->sum('ird.product_quantity');

                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockQuantity($branchID, $ProductID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;

                $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn - $Issue + $IssueReturn + $Adjustment - $waiverProduct);
            } else {
                ## Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_to', $branchID]])

                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    ->sum('isd.product_quantity');

                ## Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    ->sum('ird.product_quantity');

                ## TransferIn Balance Count
                $TransferIn = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_to', $branchID]])
                    ->where(function ($TransferIn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    ->sum('ptd.product_quantity');

                ## TransferOut Return Count
                $TransferOut = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_from', $branchID]])
                    ->where(function ($TransferOut) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    ->sum('ptd.product_quantity');

                if (Common::getCompanyType() == 2) { ## Fashion House
                    ## Sales Balance Count
                    $Sales = DB::table('pos_shop_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                        ->where(function ($Sales) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_shop_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no')
                                ->where('psd.product_id', $ProductID);
                        })
                        ->sum('psd.product_quantity');

                    ## SaleReturnd Return Count
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                        ->where(function ($SalesReturn) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
                                ->where('psrd.product_id', $ProductID);
                        })
                        ->sum('psrd.product_quantity');
                } else {
                    ## Sales Balance Count
                    $Sales = DB::table('pos_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                        ->where(function ($Sales) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no')
                                ->where('psd.product_id', $ProductID);
                        })
                        ->sum('psd.product_quantity');

                    ## SaleReturnd Return Count
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                        ->where(function ($SalesReturn) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
                                ->where('psrd.product_id', $ProductID);
                        })
                        ->sum('psrd.product_quantity');
                }

                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    // $tempDate = clone $fromDate;
                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockQuantity($branchID, $ProductID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;

                $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn + $Issue - $IssueReturn + $TransferIn - $TransferOut - $Sales + $SalesReturn + $Adjustment - $waiverProduct);
            }

            if ($returnArray) {
                $stockDetails = array();

                $stockDetails = [
                    'Stock'          => $Stock,
                    'PreOB'          => $PreOB,
                    'OpeningBalance' => $OpeningBalance + $PreOB,
                    'Purchase'       => $Purchase,
                    'PurchaseReturn' => $PurchaseReturn,
                    'Issue'          => $Issue,
                    'IssueReturn'    => $IssueReturn,
                    'TransferIn'     => $TransferIn,
                    'TransferOut'    => $TransferOut,
                    'Sales'          => $Sales,
                    'SalesReturn'    => $SalesReturn,
                    'Adjustment'     => $Adjustment,
                    'waiverProduct'  => $waiverProduct,
                ];

                return $stockDetails;
            } else {
                return $Stock;
            }
        } else {
            return "Error";
        }
    }

    public static function stockQuantity_Multiple($branchID, $ProductID = [], $startDate = null, $endDate = null, $checkToDate = true)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */

        if (count($ProductID) < 1) {
            return "Error";
        }

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            if ($checkToDate == false) {
                $toDate = null;
            } else {
                $toDate = (new DateTime(Common::systemCurrentDate($branchID, 'pos')))->format('Y-m-d');
            }
        }


        $StockC          = 0;
        $PreOBC          = 0;
        $OpeningBalanceC = 0;
        $PurchaseC       = 0;
        $PurchaseReturnC = 0;
        $IssueC          = 0;
        $IssueReturnC    = 0;
        $TransferInC     = 0;
        $TransferOutC    = 0;
        $SalesC          = 0;
        $SalesReturnC    = 0;
        $AdjustmentC     = 0;
        $waiverProductC  = 0;

        $StockA          = array();
        $PreOBA          = array();
        $OpeningBalanceA = array();
        $PurchaseA       = array();
        $PurchaseReturnA = array();
        $IssueA          = array();
        $IssueReturnA    = array();
        $TransferInA     = array();
        $TransferOutA    = array();
        $SalesA          = array();
        $SalesReturnA    = array();
        $AdjustmentA     = array();
        $waiverProductA  = array();

        $stockArr      = array();
        $AllStockArray = array();

        $productSearch = false;
        if (count($ProductID) < 951) {
            $productSearch = true;
        }

        /* Model Load */
        // $POpeningBalance = 'App\\Model\\POS\\POBStockDetails';
        // $PurchaseDetails = 'App\\Model\\POS\\PurchaseDetails';
        // $PurchaseReturnDetails = 'App\\Model\\POS\\PurchaseReturnDetails';
        // $IssueDetails = 'App\\Model\\POS\\Issued';
        // $IssueReturnDetails = 'App\\Model\\POS\\IssueReturnd';
        // $TransferDetails = 'App\\Model\\POS\\TransferDetails';
        // $SalesDetails = 'App\\Model\\POS\\SalesDetails';
        // $SaleReturnd = 'App\\Model\\POS\\SaleReturnd';

        /* Branch ID 1 for Head Office Branch */
        if ($branchID == 1) {
            // Opening Balance Count
            $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                ->where(function ($OpeningBalance) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                    $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }
                    } else {
                        $OpeningBalance->whereIn('obd.product_id', $ProductID);
                    }
                })
                ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                ->groupBy('obd.product_id')
                ->get();

            foreach ($OpeningBalance as $Row) {
                $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
            }

            // Purchase Balance Count
            $Purchase = DB::table('pos_purchases_m as pm')
                ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                ->where(function ($Purchase) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_d as pd', function ($Purchase) use ($ProductID, $productSearch) {
                    $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $Purchase->whereIn('pd.product_id', $ProductID);
                        }
                    } else {
                        $Purchase->whereIn('pd.product_id', $ProductID);
                    }
                })
                ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                ->groupBy('pd.product_id')
                ->get();
            foreach ($Purchase as $Row) {
                $PurchaseA[$Row->product_id] = $Row->Purchase;
            }

            // Purchase Return Count
            $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID, $productSearch) {
                    $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                        }
                    } else {
                        $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                    }
                })
                ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                ->groupBy('prd.product_id')
                ->get();

            foreach ($PurchaseReturn as $Row) {
                $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
            }

            // Issue Balance Count
            $Issue = DB::table('pos_issues_m as im')
                ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_from', $branchID]])
                ->where(function ($Issue) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Issue->where('im.issue_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Issue->where('im.issue_date', '<=', $toDate);
                    }
                })
                ->join('pos_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                    $Issue->on('isd.issue_bill_no', 'im.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }
                    } else {
                        $Issue->whereIn('isd.product_id', $ProductID);
                    }
                })
                ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                ->groupBy('isd.product_id')
                ->get();

            foreach ($Issue as $Row) {
                $IssueA[$Row->product_id] = $Row->Issue;
            }

            // Issue Return Count
            $IssueReturn = DB::table('pos_issues_r_m as irm')
                ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $IssueReturn->where('irm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $IssueReturn->where('irm.return_date', '<=', $toDate);
                    }
                })
                ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                    $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }
                    } else {
                        $IssueReturn->whereIn('ird.product_id', $ProductID);
                    }
                })
                ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                ->groupBy('ird.product_id')
                ->get();
            // dd($IssueReturn);

            foreach ($IssueReturn as $Row) {
                $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
            }

            ## Waiver Product Balance Count
            $waiverProduct = DB::table('pos_waiver_product_m as psm')
                ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $waiverProduct->where('psm.date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->where('psm.date', '<=', $toDate);
                    }
                })
                ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID, $productSearch) {
                    $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $waiverProduct->whereIn('psd.product_id', $ProductID);
                        }
                    } else {
                        $waiverProduct->whereIn('psd.product_id', $ProductID);
                    }
                })
                ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                ->groupBy('psd.product_id')
                ->get();

            foreach ($waiverProduct as $Row) {
                $waiverProductA[$Row->product_id] = $Row->waiverProduct;
            }

            // Adjustment Audit  Count
            $Adjustment = DB::table('pos_audit_m as am')
                ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                ->where(function ($Adjustment) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Adjustment->where('am.audit_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Adjustment->where('am.audit_date', '<=', $toDate);
                    }
                })
                ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                    $Adjustment->on('ad.audit_code', 'am.audit_code');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }
                    } else {
                        $Adjustment->whereIn('ad.product_id', $ProductID);
                    }
                })
                ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                ->groupBy('ad.product_id')
                ->get();

            foreach ($Adjustment as $Row) {
                $AdjustmentA[$Row->product_id] = $Row->Adjustment;
            }

            $productData = DB::table('pos_products')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($productData) use ($ProductID, $productSearch) {
                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $productData->whereIn('id', $ProductID);
                        }
                    } else {
                        $productData->whereIn('id', $ProductID);
                    }
                })
                ->get();

            foreach ($productData as $row) {

                $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC - $IssueC + $IssueReturnC + $AdjustmentC - $waiverProductC);

                $stockArr[$row->id] = [
                    'Stock'          => $StockC,
                    'PreOB'          => $PreOBC,
                    'OpeningBalance' => $OpeningBalanceC,
                    'Purchase'       => $PurchaseC,
                    'PurchaseReturn' => $PurchaseReturnC,
                    'Issue'          => $IssueC,
                    'IssueReturn'    => $IssueReturnC,
                    'Adjustment'     => $AdjustmentC,
                    'waiverProduct'  => $waiverProductC,
                ];
            }

            $PreOBArr      = array();
            $AllStockArray = $stockArr;

            if (!empty($fromDate) && !empty($toDate)) {

                $tempDate = clone (new DateTime($fromDate));
                $NewDate  = $tempDate->modify('-1 day');

                $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                foreach (array_keys($stockArr + $PreOBArr) as $key) {

                    // $AllStockArray[$key] = [
                    //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                    //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                    //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                    //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                    //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                    //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                    //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                    //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                    // ];

                    $AllStockArray[$key] = [
                        'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        'PreOB'          => $stockArr[$key]['PreOB'],
                        'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                        'Purchase'       => $stockArr[$key]['Purchase'],
                        'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                        'Issue'          => $stockArr[$key]['Issue'],
                        'IssueReturn'    => $stockArr[$key]['IssueReturn'],
                        'Adjustment'     => $stockArr[$key]['Adjustment'],
                        'waiverProduct'  => $stockArr[$key]['waiverProduct'],
                    ];
                }
            }
        } else {

            // ## Opening Balance Count
            $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', '<>', 1]])
                ->where(function ($OpeningBalance) use ($branchID, $fromDate, $toDate) {

                    if (!empty($branchID)) {
                        $OpeningBalance->where('obm.branch_id', $branchID);
                    }

                    if (!empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                    $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }
                    } else {
                        $OpeningBalance->whereIn('obd.product_id', $ProductID);
                    }
                })
                ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                ->groupBy('obd.product_id')
                ->get();

            foreach ($OpeningBalance as $Row) {
                $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
            }

            // Purchase Balance Count
            $Purchase = DB::table('pos_purchases_m as pm')
                ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                ->where(function ($Purchase) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_d as pd', function ($Purchase) use ($ProductID, $productSearch) {
                    $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $Purchase->whereIn('pd.product_id', $ProductID);
                        }
                    } else {
                        $Purchase->whereIn('pd.product_id', $ProductID);
                    }
                })
                ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                ->groupBy('pd.product_id')
                ->get();
            foreach ($Purchase as $Row) {
                $PurchaseA[$Row->product_id] = $Row->Purchase;
            }

            // Purchase Return Count
            $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID, $productSearch) {
                    $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                        }
                    } else {
                        $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                    }
                })
                ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                ->groupBy('prd.product_id')
                ->get();

            foreach ($PurchaseReturn as $Row) {
                $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
            }

            // ## Issue Balance Count
            $Issue = DB::table('pos_issues_m as im')
                ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_to', '<>', 1]])
                ->where(function ($Issue) use ($branchID, $fromDate, $toDate) {

                    if (!empty($branchID)) {
                        $Issue->where('im.branch_to', $branchID);
                    }

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Issue->where('im.issue_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Issue->where('im.issue_date', '<=', $toDate);
                    }
                })
                ->join('pos_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                    $Issue->on('isd.issue_bill_no', 'im.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }
                    } else {
                        $Issue->whereIn('isd.product_id', $ProductID);
                    }
                })
                ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                ->groupBy('isd.product_id')
                ->get();

            foreach ($Issue as $Row) {
                $IssueA[$Row->product_id] = $Row->Issue;
            }

            // ## Issue Return Count
            $IssueReturn = DB::table('pos_issues_r_m as irm')
                ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', '<>', 1]])
                ->where(function ($IssueReturn) use ($branchID, $fromDate, $toDate) {

                    if (!empty($branchID)) {
                        $IssueReturn->where('irm.branch_from', $branchID);
                    }

                    if (!empty($fromDate) && !empty($toDate)) {
                        $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $IssueReturn->where('irm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $IssueReturn->where('irm.return_date', '<=', $toDate);
                    }
                })
                ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                    $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }
                    } else {
                        $IssueReturn->whereIn('ird.product_id', $ProductID);
                    }
                })
                ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                ->groupBy('ird.product_id')
                ->get();

            foreach ($IssueReturn as $Row) {
                $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
            }

            // ## TransferIn Balance Count
            $TransferIn = DB::table('pos_transfers_m as ptm')
                ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_to', '<>', 1]])
                ->where(function ($TransferIn) use ($branchID, $fromDate, $toDate) {

                    if (!empty($branchID)) {
                        $TransferIn->where('ptm.branch_to', $branchID);
                    }

                    if (!empty($fromDate) && !empty($toDate)) {
                        $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                    }
                })
                ->join('pos_transfers_d as ptd', function ($TransferIn) use ($ProductID, $productSearch) {
                    $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $TransferIn->whereIn('ptd.product_id', $ProductID);
                        }
                    } else {
                        $TransferIn->whereIn('ptd.product_id', $ProductID);
                    }
                })
                ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferIn')
                ->groupBy('ptd.product_id')
                ->get();

            foreach ($TransferIn as $Row) {
                $TransferInA[$Row->product_id] = $Row->TransferIn;
            }

            // ## TransferOut Return Count
            $TransferOut = DB::table('pos_transfers_m as ptm')
                ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_from', '<>', 1]])
                ->where(function ($TransferOut) use ($branchID, $fromDate, $toDate) {

                    if (!empty($branchID)) {
                        $TransferOut->where('ptm.branch_from', $branchID);
                    }

                    if (!empty($fromDate) && !empty($toDate)) {
                        $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                    }
                })
                ->join('pos_transfers_d as ptd', function ($TransferOut) use ($ProductID, $productSearch) {
                    $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $TransferOut->whereIn('ptd.product_id', $ProductID);
                        }
                    } else {
                        $TransferOut->whereIn('ptd.product_id', $ProductID);
                    }
                })
                ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferOut')
                ->groupBy('ptd.product_id')
                ->get();

            foreach ($TransferOut as $Row) {
                $TransferOutA[$Row->product_id] = $Row->TransferOut;
            }

            ## Waiver Product Balance Count
            $waiverProduct = DB::table('pos_waiver_product_m as psm')
                ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $waiverProduct->where('psm.date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->where('psm.date', '<=', $toDate);
                    }
                })
                ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID, $productSearch) {
                    $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $waiverProduct->whereIn('psd.product_id', $ProductID);
                        }
                    } else {
                        $waiverProduct->whereIn('psd.product_id', $ProductID);
                    }
                })
                ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                ->groupBy('psd.product_id')
                ->get();

            foreach ($waiverProduct as $Row) {
                $waiverProductA[$Row->product_id] = $Row->waiverProduct;
            }

            // ## Sales Balance Count

            if (Common::getCompanyType() == 2) { ## Fashion House

                $Sales = DB::table('pos_shop_sales_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                    ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $Sales->where('psm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Sales->where('psm.sales_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Sales->where('psm.sales_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_shop_sales_d as psd', function ($Sales) use ($ProductID, $productSearch) {
                        $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Sales->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $Sales->whereIn('psd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                    ->groupBy('psd.product_id')
                    ->get();

                $SalesReturn = DB::table('pos_sales_return_m as psrm')
                    ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                    ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                        if (!empty($branchID)) {
                            $SalesReturn->where('psrm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $SalesReturn->where('psrm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID, $productSearch) {
                        $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $SalesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        } else {
                            $SalesReturn->whereIn('psrd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                    ->groupBy('psrd.product_id')
                    ->get();
            } else { ## other

                $Sales = DB::table('pos_sales_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                    ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $Sales->where('psm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Sales->where('psm.sales_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Sales->where('psm.sales_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_sales_d as psd', function ($Sales) use ($ProductID, $productSearch) {
                        $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Sales->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $Sales->whereIn('psd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                    ->groupBy('psd.product_id')
                    ->get();

                ## sales Return
                $SalesReturn = DB::table('pos_sales_return_m as psrm')
                    ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                    ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                        if (!empty($branchID)) {
                            $SalesReturn->where('psrm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $SalesReturn->where('psrm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID, $productSearch) {
                        $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $SalesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        } else {
                            $SalesReturn->whereIn('psrd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                    ->groupBy('psrd.product_id')
                    ->get();
            }

            ## Sales Count
            foreach ($Sales as $Row) {
                $SalesA[$Row->product_id] = $Row->Sales;
            }

            // ## SaleReturnd Return Count
            foreach ($SalesReturn as $Row) {
                $SalesReturnA[$Row->product_id] = $Row->SalesReturn;
            }

            // Adjustment Audit  Count
            $Adjustment = DB::table('pos_audit_m as am')
                ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                ->where(function ($Adjustment) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Adjustment->where('am.audit_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Adjustment->where('am.audit_date', '<=', $toDate);
                    }
                })
                ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                    $Adjustment->on('ad.audit_code', 'am.audit_code');

                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }
                    } else {
                        $Adjustment->whereIn('ad.product_id', $ProductID);
                    }
                })
                ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                ->groupBy('ad.product_id')
                ->get();

            foreach ($Adjustment as $Row) {
                $AdjustmentA[$Row->product_id] = $Row->Adjustment;
            }

            $productData = DB::table('pos_products')->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($productData) use ($ProductID, $productSearch) {
                    if (Common::getDBConnection() == "sqlite") {
                        if ($productSearch === true) {
                            $productData->whereIn('id', $ProductID);
                        }
                    } else {
                        $productData->whereIn('id', $ProductID);
                    }
                })
                ->get();

            foreach ($productData as $row) {

                $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                $TransferInC  = ((isset($TransferInA[$row->id]) ? $TransferInA[$row->id] : 0));
                $TransferOutC = ((isset($TransferOutA[$row->id]) ? $TransferOutA[$row->id] : 0));

                $SalesC       = ((isset($SalesA[$row->id]) ? $SalesA[$row->id] : 0));
                $SalesReturnC = ((isset($SalesReturnA[$row->id]) ? $SalesReturnA[$row->id] : 0));

                $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC + $IssueC - $IssueReturnC + $TransferInC - $TransferOutC - $SalesC + $SalesReturnC + $AdjustmentC - $waiverProductC);

                $stockArr[$row->id] = [
                    'Stock'          => $StockC,
                    'PreOB'          => $PreOBC,
                    'OpeningBalance' => $OpeningBalanceC,
                    'Purchase'       => $PurchaseC,
                    'PurchaseReturn' => $PurchaseReturnC,
                    'Issue'          => $IssueC,
                    'IssueReturn'    => $IssueReturnC,
                    'TransferIn'     => $TransferInC,
                    'TransferOut'    => $TransferOutC,
                    'Sales'          => $SalesC,
                    'SalesReturn'    => $SalesReturnC,
                    'Adjustment'     => $AdjustmentC,
                    'waiverProduct'  => $waiverProductC,
                ];
            }

            $PreOBArr      = array();
            $AllStockArray = $stockArr;

            if (!empty($fromDate) && !empty($toDate)) {

                $tempDate = clone (new DateTime($fromDate));
                $NewDate  = $tempDate->modify('-1 day');

                $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                // dd($stockArr, $PreOBArr);

                foreach (array_keys($stockArr + $PreOBArr) as $key) {

                    // $AllStockArray[$key] = [
                    //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                    //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                    //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                    //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                    //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                    //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                    //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                    //     'TransferIn' => $stockArr[$key]['TransferIn'] + $PreOBArr[$key]['TransferIn'],
                    //     'TransferOut' => $stockArr[$key]['TransferOut'] + $PreOBArr[$key]['TransferOut'],
                    //     'Sales' => $stockArr[$key]['Sales'] + $PreOBArr[$key]['Sales'],
                    //     'SalesReturn' => $stockArr[$key]['SalesReturn'] + $PreOBArr[$key]['SalesReturn'],
                    //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                    // ];

                    $AllStockArray[$key] = [
                        'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        'PreOB'          => $stockArr[$key]['PreOB'],
                        'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                        'Purchase'       => $stockArr[$key]['Purchase'],
                        'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                        'Issue'          => $stockArr[$key]['Issue'],
                        'IssueReturn'    => $stockArr[$key]['IssueReturn'],
                        'TransferIn'     => $stockArr[$key]['TransferIn'],
                        'TransferOut'    => $stockArr[$key]['TransferOut'],
                        'Sales'          => $stockArr[$key]['Sales'],
                        'SalesReturn'    => $stockArr[$key]['SalesReturn'],
                        'Adjustment'     => $stockArr[$key]['Adjustment'],
                        'waiverProduct'  => $stockArr[$key]['waiverProduct'],

                    ];
                }
            }
        }

        return $AllStockArray;
    }

    // -----------------------------------------------Cross check online stock START-------------------------------------------------------------------
    
    public static function stockQuantity_Multiple_online($branchID, $ProductID = [], $startDate = null, $endDate = null, $checkToDate = true)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */

        ## Online DB Connect
        // DB::disconnect();
        Config::set('database.connections.onlineDb.driver', 'mysql');
        Config::set('database.connections.onlineDb.url', null);

        Config::set('database.connections.onlineDb.unix_socket', '');
        Config::set('database.connections.onlineDb.charset', 'utf8mb4');
        Config::set('database.connections.onlineDb.collation', 'utf8mb4_unicode_ci');
        Config::set('database.connections.onlineDb.prefix', '');
        Config::set('database.connections.onlineDb.prefix_indexes', true);
        Config::set('database.connections.onlineDb.strict', false);
        Config::set('database.connections.onlineDb.engine', null);
        Config::set('database.connections.onlineDb.options', []);

        $onlineDBConfig = DB::table('gnl_companies')
            ->where([['id', Common::getCompanyId()], ['is_active', 1], ['is_delete', 0]])
            ->first();

        $db_host = $db_port = $db_name = $db_user = $db_pass = null;

        if ($onlineDBConfig) {
            $db_host = $onlineDBConfig->host;
            $db_port = $onlineDBConfig->port;
            $db_name = $onlineDBConfig->db_name;
            $db_user = $onlineDBConfig->username;
            $db_pass = $onlineDBConfig->password;
        }

        Config::set('database.connections.onlineDb.host', $db_host);
        Config::set('database.connections.onlineDb.port', $db_port);
        Config::set('database.connections.onlineDb.database', $db_name);
        Config::set('database.connections.onlineDb.username', $db_user);
        Config::set('database.connections.onlineDb.password', $db_pass);

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            if ($checkToDate == false) {
                $toDate = null;
            } else {
                $toDate = (new DateTime(Common::systemCurrentDate($branchID, 'pos')))->format('Y-m-d');
            }
        }

        if (!empty($ProductID)) {

            $StockC          = 0;
            $PreOBC          = 0;
            $OpeningBalanceC = 0;
            $PurchaseC       = 0;
            $PurchaseReturnC = 0;
            $IssueC          = 0;
            $IssueReturnC    = 0;
            $TransferInC     = 0;
            $TransferOutC    = 0;
            $SalesC          = 0;
            $SalesReturnC    = 0;
            $AdjustmentC     = 0;
            $waiverProductC  = 0;

            $StockA          = array();
            $PreOBA          = array();
            $OpeningBalanceA = array();
            $PurchaseA       = array();
            $PurchaseReturnA = array();
            $IssueA          = array();
            $IssueReturnA    = array();
            $TransferInA     = array();
            $TransferOutA    = array();
            $SalesA          = array();
            $SalesReturnA    = array();
            $AdjustmentA     = array();
            $waiverProductA  = array();

            $stockArr      = array();
            $AllStockArray = array();

            /* Model Load */
            // $POpeningBalance = 'App\\Model\\POS\\POBStockDetails';
            // $PurchaseDetails = 'App\\Model\\POS\\PurchaseDetails';
            // $PurchaseReturnDetails = 'App\\Model\\POS\\PurchaseReturnDetails';
            // $IssueDetails = 'App\\Model\\POS\\Issued';
            // $IssueReturnDetails = 'App\\Model\\POS\\IssueReturnd';
            // $TransferDetails = 'App\\Model\\POS\\TransferDetails';
            // $SalesDetails = 'App\\Model\\POS\\SalesDetails';
            // $SaleReturnd = 'App\\Model\\POS\\SaleReturnd';

            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {
                // Opening Balance Count
                $OpeningBalance = DB::connection('onlineDb')->table('pos_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                    ->where(function ($OpeningBalance) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        $OpeningBalance->whereIn('obd.product_id', $ProductID);
                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                // Purchase Balance Count
                $Purchase = DB::connection('onlineDb')->table('pos_purchases_m as pm')
                    ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                    ->where(function ($Purchase) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_purchases_d as pd', function ($Purchase) use ($ProductID) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                        $Purchase->whereIn('pd.product_id', $ProductID);
                    })
                    ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                    ->groupBy('pd.product_id')
                    ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id] = $Row->Purchase;
                }

                // Purchase Return Count
                $PurchaseReturn = DB::connection('onlineDb')->table('pos_purchases_r_m as prm')
                    ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                    ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                        $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                    })
                    ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                    ->groupBy('prd.product_id')
                    ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
                }

                // Issue Balance Count
                $Issue = DB::connection('onlineDb')->table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        $Issue->whereIn('isd.product_id', $ProductID);
                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                // Issue Return Count
                $IssueReturn = DB::connection('onlineDb')->table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        $IssueReturn->whereIn('ird.product_id', $ProductID);
                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();
                // dd($IssueReturn);

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::connection('onlineDb')->table('pos_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                        $waiverProduct->whereIn('psd.product_id', $ProductID);
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                    ->groupBy('psd.product_id')
                    ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id] = $Row->waiverProduct;
                }

                // Adjustment Audit  Count
                $Adjustment = DB::connection('onlineDb')->table('pos_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        $Adjustment->whereIn('ad.product_id', $ProductID);
                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = DB::connection('onlineDb')->table('pos_products')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($ProductID) {
                        $productData->whereIn('id', $ProductID);
                    })
                    ->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC - $IssueC + $IssueReturnC + $AdjustmentC - $waiverProductC);

                    $stockArr[$row->id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                    foreach (array_keys($stockArr + $PreOBArr) as $key) {

                        // $AllStockArray[$key] = [
                        //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                        //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                        //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                        //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                        //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                        //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                        //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                        // ];

                        $AllStockArray[$key] = [
                            'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                            'PreOB'          => $stockArr[$key]['PreOB'],
                            'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                            'Purchase'       => $stockArr[$key]['Purchase'],
                            'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                            'Issue'          => $stockArr[$key]['Issue'],
                            'IssueReturn'    => $stockArr[$key]['IssueReturn'],
                            'Adjustment'     => $stockArr[$key]['Adjustment'],
                            'waiverProduct'  => $stockArr[$key]['waiverProduct'],
                        ];
                    }
                }
            } else {

                // ## Opening Balance Count
                $OpeningBalance = DB::connection('onlineDb')->table('pos_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', '<>', 1]])
                    ->where(function ($OpeningBalance) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $OpeningBalance->where('obm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        $OpeningBalance->whereIn('obd.product_id', $ProductID);
                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                // Purchase Balance Count
                $Purchase = DB::connection('onlineDb')->table('pos_purchases_m as pm')
                    ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                    ->where(function ($Purchase) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_purchases_d as pd', function ($Purchase) use ($ProductID) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                        $Purchase->whereIn('pd.product_id', $ProductID);
                    })
                    ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                    ->groupBy('pd.product_id')
                    ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id] = $Row->Purchase;
                }

                // Purchase Return Count
                $PurchaseReturn = DB::connection('onlineDb')->table('pos_purchases_r_m as prm')
                    ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                    ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                        $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                    })
                    ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                    ->groupBy('prd.product_id')
                    ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
                }

                // ## Issue Balance Count
                $Issue = DB::connection('onlineDb')->table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_to', '<>', 1]])
                    ->where(function ($Issue) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $Issue->where('im.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        $Issue->whereIn('isd.product_id', $ProductID);
                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                // ## Issue Return Count
                $IssueReturn = DB::connection('onlineDb')->table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', '<>', 1]])
                    ->where(function ($IssueReturn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $IssueReturn->where('irm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        $IssueReturn->whereIn('ird.product_id', $ProductID);
                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }

                // ## TransferIn Balance Count
                $TransferIn = DB::connection('onlineDb')->table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_to', '<>', 1]])
                    ->where(function ($TransferIn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferIn->where('ptm.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        $TransferIn->whereIn('ptd.product_id', $ProductID);
                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferIn')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferIn as $Row) {
                    $TransferInA[$Row->product_id] = $Row->TransferIn;
                }

                // ## TransferOut Return Count
                $TransferOut = DB::connection('onlineDb')->table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_from', '<>', 1]])
                    ->where(function ($TransferOut) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferOut->where('ptm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        $TransferOut->whereIn('ptd.product_id', $ProductID);
                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferOut')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferOut as $Row) {
                    $TransferOutA[$Row->product_id] = $Row->TransferOut;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::connection('onlineDb')->table('pos_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                        $waiverProduct->whereIn('psd.product_id', $ProductID);
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                    ->groupBy('psd.product_id')
                    ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id] = $Row->waiverProduct;
                }

                // ## Sales Balance Count

                if (Common::getCompanyType() == 2) { ## Fashion House

                    $Sales = DB::connection('onlineDb')->table('pos_shop_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                        ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                            if (!empty($branchID)) {
                                $Sales->where('psm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_shop_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                            $Sales->whereIn('psd.product_id', $ProductID);
                        })
                        ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                        ->groupBy('psd.product_id')
                        ->get();

                    $SalesReturn = DB::connection('onlineDb')->table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                        ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                            if (!empty($branchID)) {
                                $SalesReturn->where('psrm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                            $SalesReturn->whereIn('psrd.product_id', $ProductID);
                        })
                        ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                        ->groupBy('psrd.product_id')
                        ->get();
                } else { ## other

                    $Sales = DB::connection('onlineDb')->table('pos_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                        ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                            if (!empty($branchID)) {
                                $Sales->where('psm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                            $Sales->whereIn('psd.product_id', $ProductID);
                        })
                        ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                        ->groupBy('psd.product_id')
                        ->get();

                    ## sales Return
                    $SalesReturn = DB::connection('onlineDb')->table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                        ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                            if (!empty($branchID)) {
                                $SalesReturn->where('psrm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                            $SalesReturn->whereIn('psrd.product_id', $ProductID);
                        })
                        ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                        ->groupBy('psrd.product_id')
                        ->get();
                }

                ## Sales Count
                foreach ($Sales as $Row) {
                    $SalesA[$Row->product_id] = $Row->Sales;
                }

                // ## SaleReturnd Return Count
                foreach ($SalesReturn as $Row) {
                    $SalesReturnA[$Row->product_id] = $Row->SalesReturn;
                }

                // Adjustment Audit  Count
                $Adjustment = DB::connection('onlineDb')->table('pos_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        $Adjustment->whereIn('ad.product_id', $ProductID);
                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = DB::connection('onlineDb')->table('pos_products')->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($ProductID) {
                        $productData->whereIn('id', $ProductID);
                    })
                    ->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                    $TransferInC  = ((isset($TransferInA[$row->id]) ? $TransferInA[$row->id] : 0));
                    $TransferOutC = ((isset($TransferOutA[$row->id]) ? $TransferOutA[$row->id] : 0));

                    $SalesC       = ((isset($SalesA[$row->id]) ? $SalesA[$row->id] : 0));
                    $SalesReturnC = ((isset($SalesReturnA[$row->id]) ? $SalesReturnA[$row->id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC + $IssueC - $IssueReturnC + $TransferInC - $TransferOutC - $SalesC + $SalesReturnC + $AdjustmentC - $waiverProductC);

                    $stockArr[$row->id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'TransferIn'     => $TransferInC,
                        'TransferOut'    => $TransferOutC,
                        'Sales'          => $SalesC,
                        'SalesReturn'    => $SalesReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockQuantity_Multiple_online($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                    // dd($stockArr, $PreOBArr);

                    foreach (array_keys($stockArr + $PreOBArr) as $key) {

                        // $AllStockArray[$key] = [
                        //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                        //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                        //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                        //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                        //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                        //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                        //     'TransferIn' => $stockArr[$key]['TransferIn'] + $PreOBArr[$key]['TransferIn'],
                        //     'TransferOut' => $stockArr[$key]['TransferOut'] + $PreOBArr[$key]['TransferOut'],
                        //     'Sales' => $stockArr[$key]['Sales'] + $PreOBArr[$key]['Sales'],
                        //     'SalesReturn' => $stockArr[$key]['SalesReturn'] + $PreOBArr[$key]['SalesReturn'],
                        //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                        // ];

                        $AllStockArray[$key] = [
                            'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                            'PreOB'          => $stockArr[$key]['PreOB'],
                            'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                            'Purchase'       => $stockArr[$key]['Purchase'],
                            'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                            'Issue'          => $stockArr[$key]['Issue'],
                            'IssueReturn'    => $stockArr[$key]['IssueReturn'],
                            'TransferIn'     => $stockArr[$key]['TransferIn'],
                            'TransferOut'    => $stockArr[$key]['TransferOut'],
                            'Sales'          => $stockArr[$key]['Sales'],
                            'SalesReturn'    => $stockArr[$key]['SalesReturn'],
                            'Adjustment'     => $stockArr[$key]['Adjustment'],
                            'waiverProduct'  => $stockArr[$key]['waiverProduct'],

                        ];
                    }
                }
            }

            return $AllStockArray;
        } else {
            return "Error";
        }
    }

    // -----------------------------------------------Cross check online stock END-------------------------------------------------------------------


    public static function stockQuantity_Multiple_Backup($branchID, $ProductID = [], $startDate = null, $endDate = null)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            $toDate = (new DateTime(Common::systemCurrentDate()))->format('Y-m-d');
        }

        // $branchID >= 1 &&

        if (!empty($ProductID)) {

            $StockC          = 0;
            $PreOBC          = 0;
            $OpeningBalanceC = 0;
            $PurchaseC       = 0;
            $PurchaseReturnC = 0;
            $IssueC          = 0;
            $IssueReturnC    = 0;
            $TransferInC     = 0;
            $TransferOutC    = 0;
            $SalesC          = 0;
            $SalesReturnC    = 0;
            $AdjustmentC     = 0;
            $waiverProductC  = 0;

            $StockA          = array();
            $PreOBA          = array();
            $OpeningBalanceA = array();
            $PurchaseA       = array();
            $PurchaseReturnA = array();
            $IssueA          = array();
            $IssueReturnA    = array();
            $TransferInA     = array();
            $TransferOutA    = array();
            $SalesA          = array();
            $SalesReturnA    = array();
            $AdjustmentA     = array();
            $waiverProductA  = array();

            $stockArr      = array();
            $AllStockArray = array();

            $productSearch = false;
            if (count($ProductID) < 951) {
                $productSearch = true;
            }

            /* Model Load */
            // $POpeningBalance = 'App\\Model\\POS\\POBStockDetails';
            // $PurchaseDetails = 'App\\Model\\POS\\PurchaseDetails';
            // $PurchaseReturnDetails = 'App\\Model\\POS\\PurchaseReturnDetails';
            // $IssueDetails = 'App\\Model\\POS\\Issued';
            // $IssueReturnDetails = 'App\\Model\\POS\\IssueReturnd';
            // $TransferDetails = 'App\\Model\\POS\\TransferDetails';
            // $SalesDetails = 'App\\Model\\POS\\SalesDetails';
            // $SaleReturnd = 'App\\Model\\POS\\SaleReturnd';

            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                // Opening Balance Count
                $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                    ->where(function ($OpeningBalance) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $OpeningBalance->whereIn('obd.product_id', $ProductID);
                            }
                        } else {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                // Purchase Balance Count
                $Purchase = DB::table('pos_purchases_m as pm')
                    ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                    ->where(function ($Purchase) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_purchases_d as pd', function ($Purchase) use ($ProductID, $productSearch) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Purchase->whereIn('pd.product_id', $ProductID);
                            }
                        } else {
                            $Purchase->whereIn('pd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                    ->groupBy('pd.product_id')
                    ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id] = $Row->Purchase;
                }

                // Purchase Return Count
                $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                    ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                    ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID, $productSearch) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                            }
                        } else {
                            $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                    ->groupBy('prd.product_id')
                    ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id] = $Row->PurchaseReturn;
                }

                // Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Issue->whereIn('isd.product_id', $ProductID);
                            }
                        } else {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                // Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $IssueReturn->whereIn('ird.product_id', $ProductID);
                            }
                        } else {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();
                // dd($IssueReturn);

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::table('pos_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID, $productSearch) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $waiverProduct->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $waiverProduct->whereIn('psd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                    ->groupBy('psd.product_id')
                    ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id] = $Row->waiverProduct;
                }

                // Adjustment Audit  Count
                $Adjustment = DB::table('pos_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Adjustment->whereIn('ad.product_id', $ProductID);
                            }
                        } else {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = DB::table('pos_products')->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($ProductID, $productSearch) {
                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $productData->whereIn('id', $ProductID);
                            }
                        } else {
                            $productData->whereIn('id', $ProductID);
                        }
                    })
                    ->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));
                    $PurchaseC       = ((isset($PurchaseA[$row->id]) ? $PurchaseA[$row->id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id]) ? $PurchaseReturnA[$row->id] : 0));
                    $IssueC          = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $AdjustmentC     = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    $IssueReturnC   = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));
                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC - $IssueC + $IssueReturnC + $AdjustmentC - $waiverProductC);

                    $stockArr[$row->id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                    foreach (array_keys($stockArr + $PreOBArr) as $key) {

                        // $AllStockArray[$key] = [
                        //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                        //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                        //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                        //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                        //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                        //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                        //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                        // ];

                        $AllStockArray[$key] = [
                            'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                            'PreOB'          => $stockArr[$key]['PreOB'],
                            'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                            'Purchase'       => $stockArr[$key]['Purchase'],
                            'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                            'Issue'          => $stockArr[$key]['Issue'],
                            'IssueReturn'    => $stockArr[$key]['IssueReturn'],
                            'Adjustment'     => $stockArr[$key]['Adjustment'],
                            'waiverProduct'  => $stockArr[$key]['waiverProduct'],
                        ];
                    }
                }
            } else {

                // ## Opening Balance Count
                $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', '<>', 1]])
                    ->where(function ($OpeningBalance) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $OpeningBalance->where('obm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $OpeningBalance->whereIn('obd.product_id', $ProductID);
                            }
                        } else {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                    ->groupBy('obd.product_id')
                    ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id] = $Row->OpeningBalance;
                }

                // ## Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.branch_to', '<>', 1]])
                    ->where(function ($Issue) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $Issue->where('im.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Issue->whereIn('isd.product_id', $ProductID);
                            }
                        } else {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                    ->groupBy('isd.product_id')
                    ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id] = $Row->Issue;
                }

                // ## Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', '<>', 1]])
                    ->where(function ($IssueReturn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $IssueReturn->where('irm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $IssueReturn->whereIn('ird.product_id', $ProductID);
                            }
                        } else {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                    ->groupBy('ird.product_id')
                    ->get();

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id] = $Row->IssueReturn;
                }

                // ## TransferIn Balance Count
                $TransferIn = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_to', '<>', 1]])
                    ->where(function ($TransferIn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferIn->where('ptm.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_transfers_d as ptd', function ($TransferIn) use ($ProductID, $productSearch) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $TransferIn->whereIn('ptd.product_id', $ProductID);
                            }
                        } else {
                            $TransferIn->whereIn('ptd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferIn')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferIn as $Row) {
                    $TransferInA[$Row->product_id] = $Row->TransferIn;
                }

                // ## TransferOut Return Count
                $TransferOut = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.branch_from', '<>', 1]])
                    ->where(function ($TransferOut) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferOut->where('ptm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_transfers_d as ptd', function ($TransferOut) use ($ProductID, $productSearch) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $TransferOut->whereIn('ptd.product_id', $ProductID);
                            }
                        } else {
                            $TransferOut->whereIn('ptd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferOut')
                    ->groupBy('ptd.product_id')
                    ->get();

                foreach ($TransferOut as $Row) {
                    $TransferOutA[$Row->product_id] = $Row->TransferOut;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::table('pos_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID, $productSearch) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $waiverProduct->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $waiverProduct->whereIn('psd.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                    ->groupBy('psd.product_id')
                    ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id] = $Row->waiverProduct;
                }

                // ## Sales Balance Count

                if (Common::getCompanyType() == 2) { ## Fashion House

                    $Sales = DB::table('pos_shop_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                        ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                            if (!empty($branchID)) {
                                $Sales->where('psm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->leftjoin('pos_shop_sales_d as psd', function ($Sales) use ($ProductID, $productSearch) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $Sales->whereIn('psd.product_id', $ProductID);
                                }
                            } else {
                                $Sales->whereIn('psd.product_id', $ProductID);
                            }
                        })
                        ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                        ->groupBy('psd.product_id')
                        ->get();

                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                        ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                            if (!empty($branchID)) {
                                $SalesReturn->where('psrm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->leftjoin('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID, $productSearch) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $SalesReturn->whereIn('psrd.product_id', $ProductID);
                                }
                            } else {
                                $SalesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        })
                        ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                        ->groupBy('psrd.product_id')
                        ->get();
                } else { ## other

                    $Sales = DB::table('pos_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                        ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                            if (!empty($branchID)) {
                                $Sales->where('psm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->leftjoin('pos_sales_d as psd', function ($Sales) use ($ProductID, $productSearch) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $Sales->whereIn('psd.product_id', $ProductID);
                                }
                            } else {
                                $Sales->whereIn('psd.product_id', $ProductID);
                            }
                        })
                        ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                        ->groupBy('psd.product_id')
                        ->get();

                    ## sales Return
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                        ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                            if (!empty($branchID)) {
                                $SalesReturn->where('psrm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->leftjoin('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID, $productSearch) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $SalesReturn->whereIn('psrd.product_id', $ProductID);
                                }
                            } else {
                                $SalesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        })
                        ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                        ->groupBy('psrd.product_id')
                        ->get();
                }

                ## Sales Count
                foreach ($Sales as $Row) {
                    $SalesA[$Row->product_id] = $Row->Sales;
                }

                // ## SaleReturnd Return Count
                foreach ($SalesReturn as $Row) {
                    $SalesReturnA[$Row->product_id] = $Row->SalesReturn;
                }

                // Adjustment Audit  Count
                $Adjustment = DB::table('pos_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->leftjoin('pos_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Adjustment->whereIn('ad.product_id', $ProductID);
                            }
                        } else {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }
                    })
                    ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                    ->groupBy('ad.product_id')
                    ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id] = $Row->Adjustment;
                }

                $productData = DB::table('pos_products')->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($productData) use ($ProductID, $productSearch) {
                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $productData->whereIn('id', $ProductID);
                            }
                        } else {
                            $productData->whereIn('id', $ProductID);
                        }
                    })
                    ->get();

                foreach ($productData as $row) {

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id]) ? $OpeningBalanceA[$row->id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id]) ? $IssueA[$row->id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id]) ? $IssueReturnA[$row->id] : 0));

                    $TransferInC  = ((isset($TransferInA[$row->id]) ? $TransferInA[$row->id] : 0));
                    $TransferOutC = ((isset($TransferOutA[$row->id]) ? $TransferOutA[$row->id] : 0));

                    $SalesC       = ((isset($SalesA[$row->id]) ? $SalesA[$row->id] : 0));
                    $SalesReturnC = ((isset($SalesReturnA[$row->id]) ? $SalesReturnA[$row->id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id]) ? $waiverProductA[$row->id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id]) ? $AdjustmentA[$row->id] : 0));

                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC + $IssueC - $IssueReturnC + $TransferInC - $TransferOutC - $SalesC + $SalesReturnC + $AdjustmentC - $waiverProductC);

                    $stockArr[$row->id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'TransferIn'     => $TransferInC,
                        'TransferOut'    => $TransferOutC,
                        'Sales'          => $SalesC,
                        'SalesReturn'    => $SalesReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockQuantity_Multiple($branchID, $ProductID, null, $NewDate->format('Y-m-d'));

                    // dd($stockArr, $PreOBArr);

                    foreach (array_keys($stockArr + $PreOBArr) as $key) {

                        // $AllStockArray[$key] = [
                        //     'Stock' => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                        //     'PreOB' => $stockArr[$key]['PreOB'] + $PreOBArr[$key]['PreOB'],
                        //     'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['OpeningBalance'],
                        //     'Purchase' => $stockArr[$key]['Purchase'] + $PreOBArr[$key]['Purchase'],
                        //     'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'] + $PreOBArr[$key]['PurchaseReturn'],
                        //     'Issue' => $stockArr[$key]['Issue'] + $PreOBArr[$key]['Issue'],
                        //     'IssueReturn' => $stockArr[$key]['IssueReturn'] + $PreOBArr[$key]['IssueReturn'],
                        //     'TransferIn' => $stockArr[$key]['TransferIn'] + $PreOBArr[$key]['TransferIn'],
                        //     'TransferOut' => $stockArr[$key]['TransferOut'] + $PreOBArr[$key]['TransferOut'],
                        //     'Sales' => $stockArr[$key]['Sales'] + $PreOBArr[$key]['Sales'],
                        //     'SalesReturn' => $stockArr[$key]['SalesReturn'] + $PreOBArr[$key]['SalesReturn'],
                        //     'Adjustment' => $stockArr[$key]['Adjustment'] + $PreOBArr[$key]['Adjustment'],
                        // ];

                        $AllStockArray[$key] = [
                            'Stock'          => $stockArr[$key]['Stock'] + $PreOBArr[$key]['Stock'],
                            'PreOB'          => $stockArr[$key]['PreOB'],
                            'OpeningBalance' => $stockArr[$key]['OpeningBalance'] + $PreOBArr[$key]['Stock'],
                            'Purchase'       => $stockArr[$key]['Purchase'],
                            'PurchaseReturn' => $stockArr[$key]['PurchaseReturn'],
                            'Issue'          => $stockArr[$key]['Issue'],
                            'IssueReturn'    => $stockArr[$key]['IssueReturn'],
                            'TransferIn'     => $stockArr[$key]['TransferIn'],
                            'TransferOut'    => $stockArr[$key]['TransferOut'],
                            'Sales'          => $stockArr[$key]['Sales'],
                            'SalesReturn'    => $stockArr[$key]['SalesReturn'],
                            'Adjustment'     => $stockArr[$key]['Adjustment'],
                            'waiverProduct'  => $stockArr[$key]['waiverProduct'],

                        ];
                    }
                }
            }

            return $AllStockArray;
        } else {
            return "Error";
        }
    }

    public static function stockSerialProductQuantity_Multiple($branchID, $ProductOBj, $startDate = null, $endDate = null, $checkToDate = true)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            if ($checkToDate == false) {
                $toDate = null;
            } else {
                $toDate = (new DateTime(Common::systemCurrentDate($branchID, 'pos')))->format('Y-m-d');
            }
        }

        $ProductID = $ProductOBj->pluck('id')->toArray();
        $sizeID = $ProductOBj->pluck('prod_size_id')->toArray();

        // $branchID >= 1 &&

        if (!empty($ProductID)) {

            $StockC          = 0;
            $PreOBC          = 0;
            $OpeningBalanceC = 0;
            $PurchaseC       = 0;
            $PurchaseReturnC = 0;
            $IssueC          = 0;
            $IssueReturnC    = 0;
            $TransferInC     = 0;
            $TransferOutC    = 0;
            $SalesC          = 0;
            $SalesReturnC    = 0;
            $AdjustmentC     = 0;
            $waiverProductC  = 0;

            $StockA          = array();
            $PreOBA          = array();
            $OpeningBalanceA = array();
            $PurchaseA       = array();
            $PurchaseReturnA = array();
            $IssueA          = array();
            $IssueReturnA    = array();
            $TransferInA     = array();
            $TransferOutA    = array();
            $SalesA          = array();
            $SalesReturnA    = array();
            $AdjustmentA     = array();
            $waiverProductA  = array();


            $StockCost          = array();
            $PreOBCost          = array();
            $OpeningBalanceCost = array();
            $PurchaseCost       = array();
            $PurchaseReturnCost = array();
            $IssueCost          = array();
            $IssueReturnCost    = array();
            $TransferInCost     = array();
            $TransferOutCost    = array();
            $SalesCost          = array();
            $SalesReturnCost    = array();
            $AdjustmentCost     = array();
            $waiverProductCost  = array();

            $stockArr      = array();
            $AllStockArray = array();

            $productSearch = false;
            if (count($ProductID) < 951) {
                $productSearch = true;
            }

            /* Model Load */
            // $POpeningBalance = 'App\\Model\\POS\\POBStockDetails';
            // $PurchaseDetails = 'App\\Model\\POS\\PurchaseDetails';
            // $PurchaseReturnDetails = 'App\\Model\\POS\\PurchaseReturnDetails';
            // $IssueDetails = 'App\\Model\\POS\\Issued';
            // $IssueReturnDetails = 'App\\Model\\POS\\IssueReturnd';
            // $TransferDetails = 'App\\Model\\POS\\TransferDetails';
            // $SalesDetails = 'App\\Model\\POS\\SalesDetails';
            // $SaleReturnd = 'App\\Model\\POS\\SaleReturnd';

            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                // Opening Balance Count
                $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                    ->where(function ($OpeningBalance) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $OpeningBalance->whereIn('obd.product_id', $ProductID);
                            }
                        } else {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($OpeningBalance) use ($ProductID) {
                        $OpeningBalance->on('p_d.id', 'obd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($OpeningBalance) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $OpeningBalance->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as OpeningBalance, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                // ->groupBy('obd.product_id')
                // ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id][$Row->prod_size_id] = $Row->OpeningBalance;
                    $OpeningBalanceCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // Purchase Balance Count
                $Purchase = DB::table('pos_purchases_m as pm')
                    ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                    ->where(function ($Purchase) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_product_details as pd', function ($Purchase) use ($ProductID, $productSearch) {
                        $Purchase->on('pd.purchase_bill_no', 'pm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Purchase->whereIn('pd.product_id', $ProductID);
                            }
                        } else {
                            $Purchase->whereIn('pd.product_id', $ProductID);
                        }
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->whereIn('pd.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('pd.product_id,pd.prod_size_id, IFNULL(COUNT(pd.id), 0) as Purchase , IFNULL(SUM(pd.unit_cost_price), 0) as Cost')
                    ->groupBy('pd.product_id')
                    ->groupBy('pd.prod_size_id')
                    ->get();
                // dd($Purchase);
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id][$Row->prod_size_id] = $Row->Purchase;
                    $PurchaseCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // dd($PurchaseA);

                // Purchase Return Count
                $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                    ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                    ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID, $productSearch) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                            }
                        } else {
                            $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($PurchaseReturn) use ($ProductID) {
                        $PurchaseReturn->on('p_d.id', 'prd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($PurchaseReturn) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $PurchaseReturn->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as PurchaseReturn, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id][$Row->prod_size_id] = $Row->PurchaseReturn;
                    $PurchaseReturnCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Issue->whereIn('isd.product_id', $ProductID);
                            }
                        } else {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Issue) use ($ProductID) {
                        $Issue->on('p_d.id', 'isd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Issue) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Issue->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as Issue, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id][$Row->prod_size_id] = $Row->Issue;
                    $IssueCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $IssueReturn->whereIn('ird.product_id', $ProductID);
                            }
                        } else {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('p_d.id', 'ird.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($IssueReturn) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $IssueReturn->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as IssueReturn, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */


                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id][$Row->prod_size_id] = $Row->IssueReturn;
                    $IssueReturnCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::table('pos_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID, $productSearch) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $waiverProduct->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $waiverProduct->whereIn('psd.product_id', $ProductID);
                        }
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($waiverProduct) use ($ProductID) {
                        $waiverProduct->on('p_d.id', 'psd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($waiverProduct) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $waiverProduct->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as waiverProduct, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                // ->groupBy('psd.product_id')
                // ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id][$Row->prod_size_id] = $Row->waiverProduct;
                    $waiverProductCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // Adjustment Audit  Count
                $Adjustment = DB::table('pos_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Adjustment->whereIn('ad.product_id', $ProductID);
                            }
                        } else {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Adjustment) use ($ProductID) {
                        $Adjustment->on('p_d.id', 'ad.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Adjustment) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Adjustment->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as Adjustment, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */

                // ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                // ->groupBy('ad.product_id')
                // ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id][$Row->prod_size_id] = $Row->Adjustment;
                    $AdjustmentCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }


                foreach ($ProductOBj as $row) {
                    // dd($row);

                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id][$row->prod_size_id]) ? $OpeningBalanceA[$row->id][$row->prod_size_id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id][$row->prod_size_id]) ? $PurchaseA[$row->id][$row->prod_size_id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id][$row->prod_size_id]) ? $PurchaseReturnA[$row->id][$row->prod_size_id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id][$row->prod_size_id]) ? $IssueA[$row->id][$row->prod_size_id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id][$row->prod_size_id]) ? $IssueReturnA[$row->id][$row->prod_size_id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id][$row->prod_size_id]) ? $waiverProductA[$row->id][$row->prod_size_id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id][$row->prod_size_id]) ? $AdjustmentA[$row->id][$row->prod_size_id] : 0));


                    ##Cost
                    $OpeningBalanceCostV = ((isset($OpeningBalanceCost[$row->id][$row->prod_size_id]) ? $OpeningBalanceCost[$row->id][$row->prod_size_id] : 0));

                    $PurchaseCostV      = ((isset($PurchaseCost[$row->id][$row->prod_size_id]) ? $PurchaseCost[$row->id][$row->prod_size_id] : 0));
                    $PurchaseReturnCostV = ((isset($PurchaseReturnCost[$row->id][$row->prod_size_id]) ? $PurchaseReturnCost[$row->id][$row->prod_size_id] : 0));

                    $IssueCostV      = ((isset($IssueCost[$row->id][$row->prod_size_id]) ? $IssueCost[$row->id][$row->prod_size_id] : 0));
                    $IssueReturnCostV = ((isset($IssueReturnCost[$row->id][$row->prod_size_id]) ? $IssueReturnCost[$row->id][$row->prod_size_id] : 0));

                    $waiverProductCostV = ((isset($waiverProductCost[$row->id][$row->prod_size_id]) ? $waiverProductCost[$row->id][$row->prod_size_id] : 0));
                    $AdjustmentCostV  = ((isset($AdjustmentCost[$row->id][$row->prod_size_id]) ? $AdjustmentCost[$row->id][$row->prod_size_id] : 0));




                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC - $IssueC + $IssueReturnC + $AdjustmentC - $waiverProductC);

                    $StockCostV = ($OpeningBalanceCostV + $PurchaseCostV - $PurchaseReturnCostV - $IssueCostV + $IssueReturnCostV + $AdjustmentCostV - $waiverProductCostV);


                    $stockArr[$row->id][$row->prod_size_id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,

                        'OpeningBalanceCost' => $OpeningBalanceCostV,
                        'PurchaseCost'       => $PurchaseCostV,
                        'PurchaseReturnCost' => $PurchaseReturnCostV,
                        'IssueCost'          => $IssueCostV,
                        'IssueReturnCost'    => $IssueReturnCostV,
                        'AdjustmentCost'     => $AdjustmentCostV,
                        'waiverProductCost'  => $waiverProductCostV,
                        'StockCost'          => $StockCostV,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockSerialProductQuantity_Multiple($branchID, $ProductOBj, null, $NewDate->format('Y-m-d'));


                    foreach ($ProductOBj as $row) {
                        //$OpeningBalanceC = ((isset($OpeningBalanceA[$row->id][$row->prod_size_id]) ? $OpeningBalanceA[$row->id][$row->prod_size_id] : 0));

                        if (isset($stockArr[$row->id][$row->prod_size_id]) && isset($PreOBArr[$row->id][$row->prod_size_id])) {
                            $AllStockArray[$row->id][$row->prod_size_id] = [
                                'Stock'          => $stockArr[$row->id][$row->prod_size_id]['Stock'] + $PreOBArr[$row->id][$row->prod_size_id]['Stock'],
                                'PreOB'          => $stockArr[$row->id][$row->prod_size_id]['PreOB'],
                                'OpeningBalance' => $stockArr[$row->id][$row->prod_size_id]['OpeningBalance'] + $PreOBArr[$row->id][$row->prod_size_id]['Stock'],
                                'Purchase'       => $stockArr[$row->id][$row->prod_size_id]['Purchase'],
                                'PurchaseReturn' => $stockArr[$row->id][$row->prod_size_id]['PurchaseReturn'],
                                'Issue'          => $stockArr[$row->id][$row->prod_size_id]['Issue'],
                                'IssueReturn'    => $stockArr[$row->id][$row->prod_size_id]['IssueReturn'],
                                'Adjustment'     => $stockArr[$row->id][$row->prod_size_id]['Adjustment'],
                                'waiverProduct'  => $stockArr[$row->id][$row->prod_size_id]['waiverProduct'],


                                'OpeningBalanceCost' => $stockArr[$row->id][$row->prod_size_id]['OpeningBalanceCost'] + $PreOBArr[$row->id][$row->prod_size_id]['StockCost'],
                                'PurchaseCost'       => $stockArr[$row->id][$row->prod_size_id]['PurchaseCost'],
                                'PurchaseReturnCost' => $stockArr[$row->id][$row->prod_size_id]['PurchaseReturnCost'],
                                'IssueCost'          => $stockArr[$row->id][$row->prod_size_id]['IssueCost'],
                                'IssueReturnCost'    => $stockArr[$row->id][$row->prod_size_id]['IssueReturnCost'],
                                'AdjustmentCost'     => $stockArr[$row->id][$row->prod_size_id]['AdjustmentCost'],
                                'waiverProductCost'  => $stockArr[$row->id][$row->prod_size_id]['waiverProductCost'],
                                'StockCost'          => $stockArr[$row->id][$row->prod_size_id]['StockCost'] + $PreOBArr[$row->id][$row->prod_size_id]['StockCost'],


                            ];
                        }
                    }
                }
            } else {

                ## Opening Balance Count
                $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                    ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', '<>', 1]])
                    ->where(function ($OpeningBalance) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $OpeningBalance->where('obm.branch_id', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID, $productSearch) {
                        $OpeningBalance->on('obd.ob_no', 'obm.ob_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $OpeningBalance->whereIn('obd.product_id', $ProductID);
                            }
                        } else {
                            $OpeningBalance->whereIn('obd.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($OpeningBalance) use ($ProductID) {
                        $OpeningBalance->on('p_d.id', 'obd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($OpeningBalance) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $OpeningBalance->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as OpeningBalance, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('obd.product_id, SUM(obd.product_quantity) as OpeningBalance')
                // ->groupBy('obd.product_id')
                // ->get();

                foreach ($OpeningBalance as $Row) {
                    $OpeningBalanceA[$Row->product_id][$Row->prod_size_id] = $Row->OpeningBalance;
                    $OpeningBalanceCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // Purchase Balance Count
                $Purchase = DB::table('pos_purchases_m as pm')
                    ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                    ->where(function ($Purchase) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Purchase->where('pm.purchase_date', '<=', $toDate);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID, $productSearch) {
                        $Purchase->on('p_d.purchase_bill_no', 'pm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Purchase->whereIn('p_d.product_id', $ProductID);
                            }
                        } else {
                            $Purchase->whereIn('p_d.product_id', $ProductID);
                        }
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as Purchase, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('pd.product_id, IFNULL(SUM(pd.product_quantity), 0) as Purchase')
                // ->groupBy('pd.product_id')
                // ->get();
                foreach ($Purchase as $Row) {
                    $PurchaseA[$Row->product_id][$Row->prod_size_id] = $Row->Purchase;
                    $PurchaseCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // Purchase Return Count
                $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                    ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                    ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                        if (!empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID, $productSearch) {
                        $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                            }
                        } else {
                            $PurchaseReturn->whereIn('prd.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($PurchaseReturn) use ($ProductID) {
                        $PurchaseReturn->on('p_d.id', 'prd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($PurchaseReturn) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $PurchaseReturn->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as PurchaseReturn, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('prd.product_id, SUM(prd.product_quantity) as PurchaseReturn')
                // ->groupBy('prd.product_id')
                // ->get();

                foreach ($PurchaseReturn as $Row) {
                    $PurchaseReturnA[$Row->product_id][$Row->prod_size_id] = $Row->PurchaseReturn;
                    $PurchaseReturnCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // ## Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_to', '<>', 1]])
                    ->where(function ($Issue) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $Issue->where('im.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID, $productSearch) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Issue->whereIn('isd.product_id', $ProductID);
                            }
                        } else {
                            $Issue->whereIn('isd.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Issue) use ($ProductID) {
                        $Issue->on('p_d.id', 'isd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Issue) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Issue->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id, p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as Issue, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('isd.product_id, SUM(isd.product_quantity) as Issue')
                // ->groupBy('isd.product_id')
                // ->get();

                foreach ($Issue as $Row) {
                    $IssueA[$Row->product_id][$Row->prod_size_id] = $Row->Issue;
                    $IssueCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // ## Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', '<>', 1]])
                    ->where(function ($IssueReturn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $IssueReturn->where('irm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID, $productSearch) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $IssueReturn->whereIn('ird.product_id', $ProductID);
                            }
                        } else {
                            $IssueReturn->whereIn('ird.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('p_d.id', 'ird.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($IssueReturn) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $IssueReturn->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as IssueReturn, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('ird.product_id, SUM(ird.product_quantity) as IssueReturn')
                // ->groupBy('ird.product_id')
                // ->get();

                foreach ($IssueReturn as $Row) {
                    $IssueReturnA[$Row->product_id][$Row->prod_size_id] = $Row->IssueReturn;
                    $IssueReturnCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // ## TransferIn Balance Count
                $TransferIn = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_to', '<>', 1]])
                    ->where(function ($TransferIn) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferIn->where('ptm.branch_to', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferIn) use ($ProductID, $productSearch) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $TransferIn->whereIn('ptd.product_id', $ProductID);
                            }
                        } else {
                            $TransferIn->whereIn('ptd.product_id', $ProductID);
                        }
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('p_d.id', 'ptd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($TransferIn) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $TransferIn->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as TransferIn, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferIn')
                // ->groupBy('ptd.product_id')
                // ->get();

                foreach ($TransferIn as $Row) {
                    $TransferInA[$Row->product_id][$Row->prod_size_id] = $Row->TransferIn;
                    $TransferInCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // ## TransferOut Return Count
                $TransferOut = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_from', '<>', 1]])
                    ->where(function ($TransferOut) use ($branchID, $fromDate, $toDate) {

                        if (!empty($branchID)) {
                            $TransferOut->where('ptm.branch_from', $branchID);
                        }

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferOut) use ($ProductID, $productSearch) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $TransferOut->whereIn('ptd.product_id', $ProductID);
                            }
                        } else {
                            $TransferOut->whereIn('ptd.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('p_d.id', 'ptd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($TransferOut) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $TransferOut->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as TransferOut, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('ptd.product_id, SUM(ptd.product_quantity) as TransferOut')
                // ->groupBy('ptd.product_id')
                // ->get();

                foreach ($TransferOut as $Row) {
                    $TransferOutA[$Row->product_id][$Row->prod_size_id] = $Row->TransferOut;
                    $TransferOutCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                ## Waiver Product Balance Count
                $waiverProduct = DB::table('pos_waiver_product_m as psm')
                    ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                    ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $waiverProduct->where('psm.date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $waiverProduct->where('psm.date', '<=', $toDate);
                        }
                    })
                    ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID, $productSearch) {
                        $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $waiverProduct->whereIn('psd.product_id', $ProductID);
                            }
                        } else {
                            $waiverProduct->whereIn('psd.product_id', $ProductID);
                        }
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($waiverProduct) use ($ProductID) {
                        $waiverProduct->on('p_d.id', 'psd.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($waiverProduct) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $waiverProduct->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as waiverProduct, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('psd.product_id, SUM(psd.product_quantity) as waiverProduct')
                // ->groupBy('psd.product_id')
                // ->get();

                foreach ($waiverProduct as $Row) {
                    $waiverProductA[$Row->product_id][$Row->prod_size_id] = $Row->waiverProduct;
                    $waiverProductCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // ## Sales Balance Count

                if (Common::getCompanyType() == 2) { ## Fashion House

                    $Sales = DB::table('pos_shop_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                        ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                            if (!empty($branchID)) {
                                $Sales->where('psm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_shop_sales_d as psd', function ($Sales) use ($ProductID, $productSearch) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $Sales->whereIn('psd.product_id', $ProductID);
                                }
                            } else {
                                $Sales->whereIn('psd.product_id', $ProductID);
                            }
                        })
                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($Sales) use ($ProductID) {
                            $Sales->on('p_d.id', 'psd.prod_details_id')
                                ->whereIn('p_d.product_id', $ProductID);
                        })
                        ->where(function ($Sales) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $Sales->whereIn('p_d.prod_size_id', $sizeID);
                            }
                        })
                        ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as Sales, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                        ->groupBy('p_d.product_id')
                        ->groupBy('p_d.prod_size_id')
                        ->get();
                    /* for serialbarcoe count code end */
                    // ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                    // ->groupBy('psd.product_id')
                    // ->get();

                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                        ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                            if (!empty($branchID)) {
                                $SalesReturn->where('psrm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID, $productSearch) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $SalesReturn->whereIn('psrd.product_id', $ProductID);
                                }
                            } else {
                                $SalesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        })
                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('p_d.id', 'psrd.prod_details_id')
                                ->whereIn('p_d.product_id', $ProductID);
                        })
                        ->where(function ($SalesReturn) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $SalesReturn->whereIn('p_d.prod_size_id', $sizeID);
                            }
                        })
                        ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as SalesReturn, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                        ->groupBy('p_d.product_id')
                        ->groupBy('p_d.prod_size_id')
                        ->get();
                    /* for serialbarcoe count code end */
                    // ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                    // ->groupBy('psrd.product_id')
                    // ->get();

                } else { ## other

                    $Sales = DB::table('pos_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', '<>', 1]])
                        ->where(function ($Sales) use ($branchID, $fromDate, $toDate) {

                            if (!empty($branchID)) {
                                $Sales->where('psm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_d as psd', function ($Sales) use ($ProductID, $productSearch) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $Sales->whereIn('psd.product_id', $ProductID);
                                }
                            } else {
                                $Sales->whereIn('psd.product_id', $ProductID);
                            }
                        })

                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($Sales) use ($ProductID) {
                            $Sales->on('p_d.id', 'psd.prod_details_id')
                                ->whereIn('p_d.product_id', $ProductID);
                        })
                        ->where(function ($Sales) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $Sales->whereIn('p_d.prod_size_id', $sizeID);
                            }
                        })
                        ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as Sales, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                        ->groupBy('p_d.product_id')
                        ->groupBy('p_d.prod_size_id')
                        ->get();
                    /* for serialbarcoe count code end */
                    // ->selectRaw('psd.product_id, SUM(psd.product_quantity) as Sales')
                    // ->groupBy('psd.product_id')
                    // ->get();

                    ## sales Return
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', '<>', 1]])
                        ->where(function ($SalesReturn) use ($branchID, $fromDate, $toDate) {
                            if (!empty($branchID)) {
                                $SalesReturn->where('psrm.branch_id', $branchID);
                            }

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID, $productSearch) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no');

                            if (Common::getDBConnection() == "sqlite") {
                                if ($productSearch === true) {
                                    $SalesReturn->whereIn('psrd.product_id', $ProductID);
                                }
                            } else {
                                $SalesReturn->whereIn('psrd.product_id', $ProductID);
                            }
                        })
                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('p_d.id', 'psrd.prod_details_id')
                                ->whereIn('p_d.product_id', $ProductID);
                        })
                        ->where(function ($SalesReturn) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $SalesReturn->whereIn('p_d.prod_size_id', $sizeID);
                            }
                        })
                        ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as SalesReturn, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                        ->groupBy('p_d.product_id')
                        ->groupBy('p_d.prod_size_id')
                        ->get();
                    /* for serialbarcoe count code end */
                    // ->selectRaw('psrd.product_id, IFNULL(SUM(psrd.product_quantity), 0) as SalesReturn')
                    // ->groupBy('psrd.product_id')
                    // ->get();
                }

                ## Sales Count
                foreach ($Sales as $Row) {
                    $SalesA[$Row->product_id][$Row->prod_size_id] = $Row->Sales;
                    $SalesCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // ## SaleReturnd Return Count
                foreach ($SalesReturn as $Row) {
                    $SalesReturnA[$Row->product_id][$Row->prod_size_id] = $Row->SalesReturn;
                    $SalesReturnCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                // Adjustment Audit  Count
                $Adjustment = DB::table('pos_audit_m as am')
                    ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                    ->where(function ($Adjustment) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Adjustment->where('am.audit_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Adjustment->where('am.audit_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID, $productSearch) {
                        $Adjustment->on('ad.audit_code', 'am.audit_code');

                        if (Common::getDBConnection() == "sqlite") {
                            if ($productSearch === true) {
                                $Adjustment->whereIn('ad.product_id', $ProductID);
                            }
                        } else {
                            $Adjustment->whereIn('ad.product_id', $ProductID);
                        }
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Adjustment) use ($ProductID) {
                        $Adjustment->on('p_d.id', 'ad.prod_details_id')
                            ->whereIn('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Adjustment) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Adjustment->whereIn('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->selectRaw('p_d.product_id,p_d.prod_size_id, IFNULL(COUNT(p_d.id), 0) as Adjustment, IFNULL(SUM(p_d.unit_cost_price), 0) as Cost')
                    ->groupBy('p_d.product_id')
                    ->groupBy('p_d.prod_size_id')
                    ->get();
                /* for serialbarcoe count code end */
                // ->selectRaw('ad.product_id, SUM(ad.product_quantity) as Adjustment')
                // ->groupBy('ad.product_id')
                // ->get();

                foreach ($Adjustment as $Row) {
                    $AdjustmentA[$Row->product_id][$Row->prod_size_id] = $Row->Adjustment;
                    $AdjustmentCost[$Row->product_id][$Row->prod_size_id] = $Row->Cost;
                }

                foreach ($ProductOBj as $row) {
                    $OpeningBalanceC = ((isset($OpeningBalanceA[$row->id][$row->prod_size_id]) ? $OpeningBalanceA[$row->id][$row->prod_size_id] : 0));

                    $PurchaseC       = ((isset($PurchaseA[$row->id][$row->prod_size_id]) ? $PurchaseA[$row->id][$row->prod_size_id] : 0));
                    $PurchaseReturnC = ((isset($PurchaseReturnA[$row->id][$row->prod_size_id]) ? $PurchaseReturnA[$row->id][$row->prod_size_id] : 0));

                    $IssueC       = ((isset($IssueA[$row->id][$row->prod_size_id]) ? $IssueA[$row->id][$row->prod_size_id] : 0));
                    $IssueReturnC = ((isset($IssueReturnA[$row->id][$row->prod_size_id]) ? $IssueReturnA[$row->id][$row->prod_size_id] : 0));

                    $TransferInC  = ((isset($TransferInA[$row->id][$row->prod_size_id]) ? $TransferInA[$row->id][$row->prod_size_id] : 0));
                    $TransferOutC = ((isset($TransferOutA[$row->id][$row->prod_size_id]) ? $TransferOutA[$row->id][$row->prod_size_id] : 0));

                    $SalesC       = ((isset($SalesA[$row->id][$row->prod_size_id]) ? $SalesA[$row->id][$row->prod_size_id] : 0));
                    $SalesReturnC = ((isset($SalesReturnA[$row->id][$row->prod_size_id]) ? $SalesReturnA[$row->id][$row->prod_size_id] : 0));

                    $waiverProductC = ((isset($waiverProductA[$row->id][$row->prod_size_id]) ? $waiverProductA[$row->id][$row->prod_size_id] : 0));
                    $AdjustmentC    = ((isset($AdjustmentA[$row->id][$row->prod_size_id]) ? $AdjustmentA[$row->id][$row->prod_size_id] : 0));

                    ##COst

                    $OpeningBalanceCostV = ((isset($OpeningBalanceCost[$row->id][$row->prod_size_id]) ? $OpeningBalanceCost[$row->id][$row->prod_size_id] : 0));

                    $PurchaseCostV       = ((isset($PurchaseCost[$row->id][$row->prod_size_id]) ? $PurchaseCost[$row->id][$row->prod_size_id] : 0));
                    $PurchaseReturnCostV = ((isset($PurchaseReturnCost[$row->id][$row->prod_size_id]) ? $PurchaseReturnCost[$row->id][$row->prod_size_id] : 0));

                    $IssueCostV      = ((isset($IssueCost[$row->id][$row->prod_size_id]) ? $IssueCost[$row->id][$row->prod_size_id] : 0));
                    $IssueReturnCostV = ((isset($IssueReturnCost[$row->id][$row->prod_size_id]) ? $IssueReturnCost[$row->id][$row->prod_size_id] : 0));

                    $TransferInCostV  = ((isset($TransferInCost[$row->id][$row->prod_size_id]) ? $TransferInCost[$row->id][$row->prod_size_id] : 0));
                    $TransferOutCostV = ((isset($TransferOutCost[$row->id][$row->prod_size_id]) ? $TransferOutCost[$row->id][$row->prod_size_id] : 0));

                    $SalesCostV       = ((isset($SalesCost[$row->id][$row->prod_size_id]) ? $SalesCost[$row->id][$row->prod_size_id] : 0));
                    $SalesReturnCostV = ((isset($SalesReturnCost[$row->id][$row->prod_size_id]) ? $SalesReturnCost[$row->id][$row->prod_size_id] : 0));

                    $waiverProductCostV = ((isset($waiverProductCost[$row->id][$row->prod_size_id]) ? $waiverProductCost[$row->id][$row->prod_size_id] : 0));
                    $AdjustmentCostV    = ((isset($AdjustmentCost[$row->id][$row->prod_size_id]) ? $AdjustmentCost[$row->id][$row->prod_size_id] : 0));


                    $StockC = ($OpeningBalanceC + $PurchaseC - $PurchaseReturnC + $IssueC - $IssueReturnC + $TransferInC - $TransferOutC - $SalesC + $SalesReturnC + $AdjustmentC - $waiverProductC);
                    $StockCostV = ($OpeningBalanceCostV + $PurchaseCostV - $PurchaseReturnCostV + $IssueCostV - $IssueReturnCostV + $TransferInCostV - $TransferOutCostV - $SalesCostV + $SalesReturnCostV + $AdjustmentCostV - $waiverProductCostV);

                    $stockArr[$row->id][$row->prod_size_id] = [
                        'Stock'          => $StockC,
                        'PreOB'          => $PreOBC,
                        'OpeningBalance' => $OpeningBalanceC,
                        'Purchase'       => $PurchaseC,
                        'PurchaseReturn' => $PurchaseReturnC,
                        'Issue'          => $IssueC,
                        'IssueReturn'    => $IssueReturnC,
                        'TransferIn'     => $TransferInC,
                        'TransferOut'    => $TransferOutC,
                        'Sales'          => $SalesC,
                        'SalesReturn'    => $SalesReturnC,
                        'Adjustment'     => $AdjustmentC,
                        'waiverProduct'  => $waiverProductC,

                        'OpeningBalanceCost' => $OpeningBalanceCostV,
                        'PurchaseCost'       => $PurchaseCostV,
                        'PurchaseReturnCost' => $PurchaseReturnCostV,
                        'IssueCost'          => $IssueCostV,
                        'IssueReturnCost'    => $IssueReturnCostV,
                        'TransferInCost'     => $TransferInCostV,
                        'TransferOutCost'    => $TransferOutCostV,
                        'SalesCost'          => $SalesCostV,
                        'SalesReturnCost'    => $SalesReturnCostV,
                        'AdjustmentCost'     => $AdjustmentCostV,
                        'waiverProductCost'  => $waiverProductCostV,
                        'StockCost'          => $StockCostV,
                    ];
                }

                $PreOBArr      = array();
                $AllStockArray = $stockArr;

                if (!empty($fromDate) && !empty($toDate)) {

                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');

                    $PreOBArr = self::stockSerialProductQuantity_Multiple($branchID, $ProductOBj, null, $NewDate->format('Y-m-d'));


                    foreach ($ProductOBj as $row) {
                        //$OpeningBalanceC = ((isset($OpeningBalanceA[$row->id][$row->prod_size_id]) ? $OpeningBalanceA[$row->id][$row->prod_size_id] : 0));

                        if (isset($stockArr[$row->id][$row->prod_size_id]) && isset($PreOBArr[$row->id][$row->prod_size_id])) {
                            $AllStockArray[$row->id][$row->prod_size_id] = [
                                'Stock'          => $stockArr[$row->id][$row->prod_size_id]['Stock'] + $PreOBArr[$row->id][$row->prod_size_id]['Stock'],
                                'PreOB'          => $stockArr[$row->id][$row->prod_size_id]['PreOB'],
                                'OpeningBalance' => $stockArr[$row->id][$row->prod_size_id]['OpeningBalance'] + $PreOBArr[$row->id][$row->prod_size_id]['Stock'],
                                'Purchase'       => $stockArr[$row->id][$row->prod_size_id]['Purchase'],
                                'PurchaseReturn' => $stockArr[$row->id][$row->prod_size_id]['PurchaseReturn'],
                                'Issue'          => $stockArr[$row->id][$row->prod_size_id]['Issue'],
                                'IssueReturn'    => $stockArr[$row->id][$row->prod_size_id]['IssueReturn'],

                                'TransferIn'    => $stockArr[$row->id][$row->prod_size_id]['TransferIn'],
                                'TransferOut'    => $stockArr[$row->id][$row->prod_size_id]['TransferOut'],
                                'Sales'    => $stockArr[$row->id][$row->prod_size_id]['Sales'],
                                'SalesReturn'    => $stockArr[$row->id][$row->prod_size_id]['SalesReturn'],


                                'Adjustment'     => $stockArr[$row->id][$row->prod_size_id]['Adjustment'],
                                'waiverProduct'  => $stockArr[$row->id][$row->prod_size_id]['waiverProduct'],


                                'OpeningBalanceCost' => $stockArr[$row->id][$row->prod_size_id]['OpeningBalanceCost'] + $PreOBArr[$row->id][$row->prod_size_id]['StockCost'],
                                'PurchaseCost'       => $stockArr[$row->id][$row->prod_size_id]['PurchaseCost'],
                                'PurchaseReturnCost' => $stockArr[$row->id][$row->prod_size_id]['PurchaseReturnCost'],
                                'IssueCost'          => $stockArr[$row->id][$row->prod_size_id]['IssueCost'],
                                'IssueReturnCost'    => $stockArr[$row->id][$row->prod_size_id]['IssueReturnCost'],

                                'TransferInCost'    => $stockArr[$row->id][$row->prod_size_id]['TransferInCost'],
                                'TransferOutCost'    => $stockArr[$row->id][$row->prod_size_id]['TransferOutCost'],
                                'SalesCost'    => $stockArr[$row->id][$row->prod_size_id]['SalesCost'],
                                'SalesReturnCost'    => $stockArr[$row->id][$row->prod_size_id]['SalesReturnCost'],

                                'AdjustmentCost'     => $stockArr[$row->id][$row->prod_size_id]['AdjustmentCost'],
                                'waiverProductCost'  => $stockArr[$row->id][$row->prod_size_id]['waiverProductCost'],
                                'StockCost'         => $stockArr[$row->id][$row->prod_size_id]['StockCost'] + $PreOBArr[$row->id][$row->prod_size_id]['StockCost'],


                            ];
                        }
                    }


                    // foreach (array_keys($stockArr + $PreOBArr) as $key) {
                    //     foreach(array_keys($stockArr[$key] + $PreOBArr[$key]) as $key2){

                    //     }
                    // }


                }
            }

            return $AllStockArray;
        } else {
            return "Error";
        }
    }

    public static function stockSerialProductQuantity($branchID, $ProductID, $sizeID = null,  $returnArray = false, $startDate = null, $endDate = null)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */
        /**
         * Ei function a Gold Product plus Serial Product er Opening Balance by default a zero boshano hoise, future a dorkar hoile boshaaya nibo
         */

        // config()->set('database.connections.mysql.strict', false);
        // DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            $toDate = (new DateTime(Common::systemCurrentDate($branchID, 'pos')))->format('Y-m-d');
        }



        if ($branchID >= 1 && !empty($ProductID)) {

            $Stock          = 0;
            $PreOB          = 0;
            $OpeningBalance = 0;
            $Purchase       = 0;
            $PurchaseReturn = 0;
            $Issue          = 0;
            $IssueReturn    = 0;
            $TransferIn     = 0;
            $TransferOut    = 0;
            $Sales          = 0;
            $SalesReturn    = 0;
            $Adjustment     = 0;
            $waiverProduct  = 0;

            /* Model Load */
            $POpeningBalance       = 'App\\Model\\POS\\POBStockDetails';
            $PurchaseDetails       = 'App\\Model\\POS\\PurchaseDetails';
            $PurchaseReturnDetails = 'App\\Model\\POS\\PurchaseReturnDetails';
            $IssueDetails          = 'App\\Model\\POS\\Issued';
            $IssueReturnDetails    = 'App\\Model\\POS\\IssueReturnd';
            $TransferDetails       = 'App\\Model\\POS\\TransferDetails';
            $SalesDetails          = 'App\\Model\\POS\\SalesDetails';
            $SaleReturnd           = 'App\\Model\\POS\\SaleReturnd';

            ## Opening Balance Count
            $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                ->where(function ($OpeningBalance) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                    $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
                        ->where('obd.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($OpeningBalance) use ($ProductID) {
                    $OpeningBalance->on('p_d.id', 'obd.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($OpeningBalance) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $OpeningBalance->where('p_d.prod_size_id', $sizeID);
                    }
                })
                ->count('p_d.product_id');

            // ->sum('obd.product_quantity');

            ## Purchase Balance Count
            $Purchase = DB::table('pos_purchases_m as pm')
                ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                ->where(function ($Purchase) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '<=', $toDate);
                    }
                })
                ->join('pos_product_details as pd', function ($Purchase) use ($ProductID) {
                    $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
                        ->where('pd.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('pd.prod_size_id', $sizeID);
                    }
                })
                ->count('pd.product_id');

            ## Purchase Return Count

            $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                    $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
                        ->where('prd.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                    $Purchase->on('p_d.id', 'prd.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('p_d.prod_size_id', $sizeID);
                    }
                })
                ->count('p_d.product_id');
            /* for serialbarcoe count code end */

            // ->sum('prd.product_quantity');

            ## Waiver Product Balance Count
            $waiverProduct = DB::table('pos_waiver_product_m as psm')
                ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $waiverProduct->where('psm.date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->where('psm.date', '<=', $toDate);
                    }
                })
                ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                    $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no')
                        ->where('psd.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                    $Purchase->on('p_d.id', 'psd.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('p_d.prod_size_id', $sizeID);
                    }
                })
                ->count('p_d.product_id');
            /* for serialbarcoe count code end */
            // ->sum('psd.product_quantity');

            ## Adjustment Audit  Count
            $Adjustment = DB::table('pos_audit_m as am')
                ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                ->where(function ($Adjustment) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Adjustment->where('am.audit_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Adjustment->where('am.audit_date', '<=', $toDate);
                    }
                })
                ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID) {
                    $Adjustment->on('ad.audit_code', 'am.audit_code')
                        ->where('ad.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                    $Purchase->on('p_d.id', 'ad.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('p_d.prod_size_id', $sizeID);
                    }
                })
                ->count('p_d.product_id');
            /* for serialbarcoe count code end */

            // ->sum('ad.product_quantity');
            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                ##Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'isd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->count('p_d.product_id');
                /* for serialbarcoe count code end */


                // ->sum('isd.product_quantity');

                ## Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ird.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->count('p_d.product_id');
                /* for serialbarcoe count code end */

                ## pob count
                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockSerialProductQuantity($branchID, $ProductID, $sizeID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;
                // $PreOB = $OpeningBalance = 0;

                $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn - $Issue + $IssueReturn + $Adjustment - $waiverProduct);
            } else {
                ## Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_to', $branchID]])

                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'isd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->count('p_d.product_id');
                /* for serialbarcoe count code end */

                // ->sum('isd.product_quantity');

                ## Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ird.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->count('p_d.product_id');
                /* for serialbarcoe count code end */

                ## TransferIn Balance Count
                $TransferIn = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_to', $branchID]])
                    ->where(function ($TransferIn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ptd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->count('p_d.product_id');
                /* for serialbarcoe count code end */

                ## TransferOut Return Count
                $TransferOut = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_from', $branchID]])
                    ->where(function ($TransferOut) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ptd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    ->count('p_d.product_id');
                /* for serialbarcoe count code end */

                if (Common::getCompanyType() == 2) { ## Fashion House
                    ## Sales Balance Count
                    $Sales = DB::table('pos_shop_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                        ->where(function ($Sales) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_shop_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no')
                                ->where('psd.product_id', $ProductID);
                        })
                        ->sum('psd.product_quantity');

                    ## SaleReturnd Return Count
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                        ->where(function ($SalesReturn) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
                                ->where('psrd.product_id', $ProductID);
                        })
                        ->sum('psrd.product_quantity');
                } else {
                    ## Sales Balance Count
                    $Sales = DB::table('pos_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                        ->where(function ($Sales) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no')
                                ->where('psd.product_id', $ProductID);
                        })
                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                            $Purchase->on('p_d.id', 'psd.prod_details_id')
                                ->where('p_d.product_id', $ProductID);
                        })
                        ->where(function ($Purchase) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $Purchase->where('p_d.prod_size_id', $sizeID);
                            }
                        })
                        ->count('p_d.product_id');
                    /* for serialbarcoe count code end */

                    ## SaleReturnd Return Count
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                        ->where(function ($SalesReturn) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
                                ->where('psrd.product_id', $ProductID);
                        })
                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                            $Purchase->on('p_d.id', 'psrd.prod_details_id')
                                ->where('p_d.product_id', $ProductID);
                        })
                        ->where(function ($Purchase) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $Purchase->where('p_d.prod_size_id', $sizeID);
                            }
                        })
                        ->count('p_d.product_id');
                    /* for serialbarcoe count code end */
                }

                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    // dd($fromDate);
                    $tempDate = clone $fromDate;
                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockSerialProductQuantity($branchID, $ProductID, $sizeID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;
                // $PreOB = $OpeningBalance = 0;

                $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn + $Issue - $IssueReturn + $TransferIn - $TransferOut - $Sales + $SalesReturn + $Adjustment - $waiverProduct);
            }

            if ($returnArray) {
                $stockDetails = array();

                $stockDetails = [
                    'Stock'          => $Stock,
                    'PreOB'          => $PreOB,
                    'OpeningBalance' => $OpeningBalance + $PreOB,
                    'Purchase'       => $Purchase,
                    'PurchaseReturn' => $PurchaseReturn,
                    'Issue'          => $Issue,
                    'IssueReturn'    => $IssueReturn,
                    'TransferIn'     => $TransferIn,
                    'TransferOut'    => $TransferOut,
                    'Sales'          => $Sales,
                    'SalesReturn'    => $SalesReturn,
                    'Adjustment'     => $Adjustment,
                    'waiverProduct'  => $waiverProduct,
                ];

                return $stockDetails;
            } else {
                return $Stock;
            }
        } else {
            return "Error";
        }
    }

    public static function stockSerialProductDetailsId($branchID, $ProductID, $sizeID = null,  $returnArray = false, $startDate = null, $endDate = null)
    {
        /**
         * Algorithm Stock Count For H/O
         * Stock = OpeningBalance + Purchase - PurchaseReturn - Issue + IssueReturn +- Adjustment
         */
        /**
         * Algorithm Stock Count For Branch
         * Stock = OpeningBalance + Issue - IssueReturn + TransferIn - TransferOut - Sales + SalesReturn +- Adjustment
         */
        /**
         * Ei function a Gold Product plus Serial Product er Opening Balance by default a zero boshano hoise, future a dorkar hoile boshaaya nibo
         */

        // config()->set('database.connections.mysql.strict', false);
        // DB::reconnect();

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            $toDate = (new DateTime(Common::systemCurrentDate($branchID, 'pos')))->format('Y-m-d');
        }

        if ($branchID >= 1 && !empty($ProductID)) {

            $Stock          = array();
            $PreOB          = array();
            $OpeningBalance = array();
            $Purchase       = array();
            $PurchaseReturn = array();
            $Issue          = array();
            $IssueReturn    = array();
            $TransferIn     = array();
            $TransferOut    = array();
            $Sales          = array();
            $SalesReturn    = array();
            $Adjustment     = array();
            $waiverProduct  = array();


            ## Opening Balance Count
            $OpeningBalance = DB::table('pos_ob_stock_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.branch_id', $branchID]])
                ->where(function ($OpeningBalance) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $OpeningBalance->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('pos_ob_stock_d as obd', function ($OpeningBalance) use ($ProductID) {
                    $OpeningBalance->on('obd.ob_no', 'obm.ob_no')
                        ->where('obd.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($OpeningBalance) use ($ProductID) {
                    $OpeningBalance->on('p_d.id', 'obd.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($OpeningBalance) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $OpeningBalance->where('p_d.prod_size_id', $sizeID);
                    }
                })
                // ->count('p_d.product_id');
                ->pluck('p_d.id')
                ->toArray();

            // ->sum('obd.product_quantity');

            ## Purchase Balance Count
            $Purchase = DB::table('pos_purchases_m as pm')
                ->where([['pm.is_delete', 0], ['pm.is_active', 1], ['pm.branch_id', $branchID]])
                ->where(function ($Purchase) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Purchase->whereBetween('pm.purchase_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Purchase->where('pm.purchase_date', '<=', $toDate);
                    }
                })
                ->join('pos_product_details as pd', function ($Purchase) use ($ProductID) {
                    $Purchase->on('pd.purchase_bill_no', 'pm.bill_no')
                        ->where('pd.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('pd.prod_size_id', $sizeID);
                    }
                })
                // ->count('pd.product_id');
                ->pluck('pd.id')
                ->toArray();

            ## Purchase Return Count

            $PurchaseReturn = DB::table('pos_purchases_r_m as prm')
                ->where([['prm.is_delete', 0], ['prm.is_active', 1], ['prm.branch_id', $branchID]])
                ->where(function ($PurchaseReturn) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->whereBetween('prm.return_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $PurchaseReturn->where('prm.return_date', '<=', $toDate);
                    }
                })
                ->join('pos_purchases_r_d as prd', function ($PurchaseReturn) use ($ProductID) {
                    $PurchaseReturn->on('prd.pr_bill_no', 'prm.bill_no')
                        ->where('prd.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                    $Purchase->on('p_d.id', 'prd.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('p_d.prod_size_id', $sizeID);
                    }
                })
                // ->count('p_d.product_id');
                ->pluck('p_d.id')
                ->toArray();
            /* for serialbarcoe count code end */

            // ->sum('prd.product_quantity');

            ## Waiver Product Balance Count
            $waiverProduct = DB::table('pos_waiver_product_m as psm')
                ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                ->where(function ($waiverProduct) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->whereBetween('psm.date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $waiverProduct->where('psm.date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $waiverProduct->where('psm.date', '<=', $toDate);
                    }
                })
                ->join('pos_waiver_product_d as psd', function ($waiverProduct) use ($ProductID) {
                    $waiverProduct->on('psd.waiver_product_no', 'psm.waiver_product_no')
                        ->where('psd.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                    $Purchase->on('p_d.id', 'psd.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('p_d.prod_size_id', $sizeID);
                    }
                })
                // ->count('p_d.product_id');
                ->pluck('p_d.id')
                ->toArray();

            /* for serialbarcoe count code end */
            // ->sum('psd.product_quantity');

            ## Adjustment Audit  Count
            $Adjustment = DB::table('pos_audit_m as am')
                ->where([['am.is_delete', 0], ['am.is_active', 1], ['am.is_completed', 1], ['am.branch_id', $branchID]])
                ->where(function ($Adjustment) use ($fromDate, $toDate) {

                    if (!empty($fromDate) && !empty($toDate)) {
                        $Adjustment->whereBetween('am.audit_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $Adjustment->where('am.audit_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $Adjustment->where('am.audit_date', '<=', $toDate);
                    }
                })
                ->join('pos_audit_d as ad', function ($Adjustment) use ($ProductID) {
                    $Adjustment->on('ad.audit_code', 'am.audit_code')
                        ->where('ad.product_id', $ProductID);
                })
                /* for serialbarcoe count code  start*/
                ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                    $Purchase->on('p_d.id', 'ad.prod_details_id')
                        ->where('p_d.product_id', $ProductID);
                })
                ->where(function ($Purchase) use ($sizeID) {
                    if (!empty($sizeID)) {
                        $Purchase->where('p_d.prod_size_id', $sizeID);
                    }
                })
                // ->count('p_d.product_id');
                ->pluck('p_d.id')
                ->toArray();


            /* for serialbarcoe count code end */

            // ->sum('ad.product_quantity');
            /* Branch ID 1 for Head Office Branch */
            if ($branchID == 1) {

                ##Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_from', $branchID]])
                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })

                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'isd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    // ->count('p_d.product_id');
                    ->pluck('p_d.id')
                    ->toArray();
                /* for serialbarcoe count code end */


                // ->sum('isd.product_quantity');

                ## Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_to', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ird.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    // ->count('p_d.product_id');
                    ->pluck('p_d.id')
                    ->toArray();

                /* for serialbarcoe count code end */

                ## pob count
                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockSerialProductDetailsId($branchID, $ProductID, $sizeID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;
                // $PreOB = $OpeningBalance = 0;

                // $Stock = ($PreOB + $OpeningBalance + $Purchase - $PurchaseReturn - $Issue + $IssueReturn + $Adjustment - $waiverProduct);

                $stockIn = array_merge($PreOB, $OpeningBalance, $Purchase, $IssueReturn, $Adjustment);
                $stockOut = array_merge($PurchaseReturn, $Issue, $waiverProduct);

                foreach ($stockOut as $key => $one) {
                    $result =  array_search($one, $stockIn);
                    if ($result !== false) {
                        unset($stockIn[$result]);
                        unset($stockOut[$key]);
                    }
                }
                $Stock = array_diff($stockIn, $stockOut);
            } else {
                ## Issue Balance Count
                $Issue = DB::table('pos_issues_m as im')
                    ->where([['im.is_delete', 0], ['im.is_active', 1], ['im.is_approve', 1], ['im.branch_to', $branchID]])

                    ->where(function ($Issue) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $Issue->whereBetween('im.issue_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $Issue->where('im.issue_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $Issue->where('im.issue_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_d as isd', function ($Issue) use ($ProductID) {
                        $Issue->on('isd.issue_bill_no', 'im.bill_no')
                            ->where('isd.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'isd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    // ->count('p_d.product_id');
                    ->pluck('p_d.id')
                    ->toArray();
                /* for serialbarcoe count code end */

                // ->sum('isd.product_quantity');

                ## Issue Return Count
                $IssueReturn = DB::table('pos_issues_r_m as irm')
                    ->where([['irm.is_delete', 0], ['irm.is_active', 1], ['irm.branch_from', $branchID]])
                    ->where(function ($IssueReturn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->whereBetween('irm.return_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $IssueReturn->where('irm.return_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_issues_r_d as ird', function ($IssueReturn) use ($ProductID) {
                        $IssueReturn->on('ird.ir_bill_no', 'irm.bill_no')
                            ->where('ird.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ird.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    // ->count('p_d.product_id');
                    ->pluck('p_d.id')
                    ->toArray();
                /* for serialbarcoe count code end */

                ## TransferIn Balance Count
                $TransferIn = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_to', $branchID]])
                    ->where(function ($TransferIn) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferIn->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferIn->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferIn) use ($ProductID) {
                        $TransferIn->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ptd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    // ->count('p_d.product_id');
                    ->pluck('p_d.id')
                    ->toArray();
                /* for serialbarcoe count code end */

                ## TransferOut Return Count
                $TransferOut = DB::table('pos_transfers_m as ptm')
                    ->where([['ptm.is_delete', 0], ['ptm.is_active', 1], ['ptm.is_approve', 1], ['ptm.branch_from', $branchID]])
                    ->where(function ($TransferOut) use ($fromDate, $toDate) {

                        if (!empty($fromDate) && !empty($toDate)) {
                            $TransferOut->whereBetween('ptm.transfer_date', [$fromDate, $toDate]);
                        }

                        if (!empty($fromDate) && empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '>=', $fromDate);
                        }

                        if (empty($fromDate) && !empty($toDate)) {
                            $TransferOut->where('ptm.transfer_date', '<=', $toDate);
                        }
                    })
                    ->join('pos_transfers_d as ptd', function ($TransferOut) use ($ProductID) {
                        $TransferOut->on('ptd.transfer_bill_no', 'ptm.bill_no')
                            ->where('ptd.product_id', $ProductID);
                    })
                    /* for serialbarcoe count code  start*/
                    ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                        $Purchase->on('p_d.id', 'ptd.prod_details_id')
                            ->where('p_d.product_id', $ProductID);
                    })
                    ->where(function ($Purchase) use ($sizeID) {
                        if (!empty($sizeID)) {
                            $Purchase->where('p_d.prod_size_id', $sizeID);
                        }
                    })
                    // ->count('p_d.product_id');
                    ->pluck('p_d.id')
                    ->toArray();
                /* for serialbarcoe count code end */

                if (Common::getCompanyType() == 2) { ## Fashion House
                    ## Sales Balance Count
                    $Sales = DB::table('pos_shop_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                        ->where(function ($Sales) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_shop_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no')
                                ->where('psd.product_id', $ProductID);
                        })
                        ->sum('psd.product_quantity');

                    ## SaleReturnd Return Count
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                        ->where(function ($SalesReturn) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
                                ->where('psrd.product_id', $ProductID);
                        })
                        ->sum('psrd.product_quantity');
                } else {
                    ## Sales Balance Count
                    $Sales = DB::table('pos_sales_m as psm')
                        ->where([['psm.is_delete', 0], ['psm.is_active', 1], ['psm.branch_id', $branchID]])
                        ->where(function ($Sales) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $Sales->whereBetween('psm.sales_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $Sales->where('psm.sales_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $Sales->where('psm.sales_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_d as psd', function ($Sales) use ($ProductID) {
                            $Sales->on('psd.sales_bill_no', 'psm.sales_bill_no')
                                ->where('psd.product_id', $ProductID);
                        })
                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                            $Purchase->on('p_d.id', 'psd.prod_details_id')
                                ->where('p_d.product_id', $ProductID);
                        })
                        ->where(function ($Purchase) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $Purchase->where('p_d.prod_size_id', $sizeID);
                            }
                        })
                        // ->count('p_d.product_id');
                        ->pluck('p_d.id')
                        ->toArray();
                    /* for serialbarcoe count code end */

                    ## SaleReturnd Return Count
                    $SalesReturn = DB::table('pos_sales_return_m as psrm')
                        ->where([['psrm.is_delete', 0], ['psrm.is_active', 1], ['psrm.branch_id', $branchID]])
                        ->where(function ($SalesReturn) use ($fromDate, $toDate) {

                            if (!empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->whereBetween('psrm.return_date', [$fromDate, $toDate]);
                            }

                            if (!empty($fromDate) && empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '>=', $fromDate);
                            }

                            if (empty($fromDate) && !empty($toDate)) {
                                $SalesReturn->where('psrm.return_date', '<=', $toDate);
                            }
                        })
                        ->join('pos_sales_return_d as psrd', function ($SalesReturn) use ($ProductID) {
                            $SalesReturn->on('psrd.return_bill_no', 'psrm.return_bill_no')
                                ->where('psrd.product_id', $ProductID);
                        })
                        /* for serialbarcoe count code  start*/
                        ->join('pos_product_details as p_d', function ($Purchase) use ($ProductID) {
                            $Purchase->on('p_d.id', 'psrd.prod_details_id')
                                ->where('p_d.product_id', $ProductID);
                        })
                        ->where(function ($Purchase) use ($sizeID) {
                            if (!empty($sizeID)) {
                                $Purchase->where('p_d.prod_size_id', $sizeID);
                            }
                        })
                        // ->count('p_d.product_id');
                        ->pluck('p_d.id')
                        ->toArray();
                    /* for serialbarcoe count code end */
                }

                // dd($branchID, $ProductID, $sizeID, $Issue, $IssueReturn, $TransferIn, $TransferOut);

                //////////////////////////////////////////////////////
                if (!empty($fromDate) && !empty($toDate)) {
                    $tempDate = clone $fromDate;
                    $tempDate = clone (new DateTime($fromDate));
                    $NewDate  = $tempDate->modify('-1 day');
                    $PreOB    = self::stockSerialProductDetailsId($branchID, $ProductID, $sizeID, false, null, $NewDate->format('Y-m-d'));
                }

                // $OpeningBalance = $OpeningBalance + $PreOB;
                // $PreOB = $OpeningBalance = 0;

                $stockIn = array_merge($PreOB, $OpeningBalance, $Purchase, $Issue, $TransferIn, $SalesReturn, $Adjustment);
                $stockOut = array_merge($PurchaseReturn, $IssueReturn, $TransferOut, $Sales, $waiverProduct);

                foreach ($stockOut as $key => $one) {
                    $result =  array_search($one, $stockIn);
                    if ($result !== false) {
                        unset($stockIn[$result]);
                        unset($stockOut[$key]);
                    }
                }

                $Stock = array_diff($stockIn, $stockOut);
            }

            if ($returnArray) {
                $stockDetails = array();

                $stockDetails = [
                    'Stock'          => $Stock,
                    'PreOB'          => $PreOB,
                    'OpeningBalance' => array_merge($OpeningBalance, $PreOB),
                    'Purchase'       => $Purchase,
                    'PurchaseReturn' => $PurchaseReturn,
                    'Issue'          => $Issue,
                    'IssueReturn'    => $IssueReturn,
                    'TransferIn'     => $TransferIn,
                    'TransferOut'    => $TransferOut,
                    'Sales'          => $Sales,
                    'SalesReturn'    => $SalesReturn,
                    'Adjustment'     => $Adjustment,
                    'waiverProduct'  => $waiverProduct,
                ];

                return $stockDetails;
            } else {
                return count($Stock);
            }
        } else {
            return "Error";
        }
    }

    public static function stockAvailableProductForSerialBarcode($branchID, $ProductID, $sizeID = null, $startDate = null, $endDate = null)
    {

        // $sizeID = 298;

        // $stockGet =  self::stockSerialProductQuantity($branchID, $ProductID,null,$returnArray = true,  null, $endDate);
        // $stockGet =  self::stockSerialProductDetailsId($branchID, $ProductID, $sizeID);
        // dd($branchID, $ProductID, $sizeID,  $returnArray = true, $startDate, $endDate);
        $stockGet =  self::stockSerialProductDetailsId($branchID, $ProductID, $sizeID,  $returnArray = true, $startDate, $endDate);

        // dd($stockGet, $branchID, $ProductID, $sizeID, $startDate, $endDate);
        if (isset($stockGet['Stock']) == false) {
            return 0;
        }

        $queryData = DB::table('pos_product_details')
            ->where('product_id', $ProductID)
            ->where(function ($queryData) use ($sizeID) {
                if (!empty($sizeID)) {
                    $queryData->where('prod_size_id', $sizeID);
                }
            })
            ->whereIn('id', $stockGet['Stock'])
            ->select('product_id', 'unit_cost_price', 'unit_other_cost', 'serial_barcode', 'id')
            ->orderBy('serial_barcode', 'DESC')
            ->limit(count($stockGet['Stock']))
            ->get();

        $queryData =  $queryData->sortBy("id");

        if (!empty($queryData)) {
            return $queryData;
        } else {
            return 0;
        }
    }

    public static function goldSalesPrice($branchID, $ProductID, $sizeID, $addOrEditFlag = null, $eff_date = null)
    {
        if ($branchID == null) {
            $branchID = Common::getBranchId();
        }

        if ($eff_date == null && $addOrEditFlag != "addSales") {

            $sales_date = Common::systemCurrentDate($branchID, 'pos');
        } else if ($eff_date == null && $addOrEditFlag == "addSales" ) {

            $sales_date = new DateTime();
            $sales_date = $sales_date->format('Y-m-d');
        } else if ($addOrEditFlag == "editSales" && $eff_date) {

            $sales_date = $eff_date;
        } else {
            $sales_date = $eff_date;
        }

        $sales_date = date('Y-m-d', strtotime($sales_date));

        $ProductData = DB::table('pos_products')
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->where('id', $ProductID)
            ->first();

        if (!empty($ProductData->carat_id)) {
            $carat_id = $ProductData->carat_id;
        } else {

            $carat_id = 1; ## 1 carart id N/A deafult carat will be select
        }

        $sizeData = DB::table('pos_p_sizes')
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->where('id', $sizeID)
            ->first();

        $total_point = self::fnGetTotalPointBySizeName($sizeData->size_name);

        $masterQuery = DB::table('pos_gold_price')
            ->where('is_delete', '=', 0)
            ->where('is_active', '=', 1)
            ->where('carat_id', $carat_id)
            ->where(function ($masterQuery) use ($sales_date) {
                $masterQuery->where([['start_date', '<=', $sales_date], ['end_date', '>=', $sales_date]])
                    ->orWhere([['start_date', '<=', $sales_date]]);
            })
            ->orderBy('id', 'DESC')
            ->first();

        ## per vori price = 960 point price = $masterQuery->sales_price
        $calprice =  $masterQuery != null ? $masterQuery->sales_price : null;

        if (!empty($masterQuery)) {
            ## per vori price = 960 point price = $masterQuery->sales_price
            $calprice =  $masterQuery->sales_price;

            ## 1 point ar price koto total point ar price koto ===
            $price   =   ($calprice / 960) * $total_point;
        } else {
            return "price not found";
        }



        return round($price);
    }

    public static function fnGetTotalPointBySizeName($size_name)
    {
        $SizeSplit = explode('/', $size_name);

        if (isset($SizeSplit[0]) == false) {
            return 0;
        }

        $sizeunit = explode('.', $SizeSplit[0]);
        $sizeunitGM = 0;

        if (isset($SizeSplit[1])) {
            $sizeunitGM = str_replace("gm", "", $SizeSplit[1]);
        }

        if (!empty($SizeSplit[0])) {

            $vori =  isset($sizeunit[0]) ? $sizeunit[0] : 0;
            $ana =  isset($sizeunit[1]) ? $sizeunit[1] : 0;
            $ratti =  isset($sizeunit[2]) ? $sizeunit[2] : 0;
            $point =  isset($sizeunit[3]) ? $sizeunit[3] : 0;
            $gram =  $sizeunitGM;

            $result_point = self::fnGetGoldTotalPoint($vori, $ana, $ratti, $point, $gram);

            return $result_point;
        } else {
            return 0;
        }
    }

    public static function fnGetGoldTotalPoint($vori, $ana, $ratti, $point, $gram)
    {
        $result_point = 0;

        $vori_to_point = $vori * 960;
        $ana_to_point = $ana * 60;
        $ratti_to_point = $ratti * 10;
        $point_to_point = $point;
        // dd($vori_to_point,$ana_to_point,$ratti_to_point,$point_to_point);

        // if(gram>0){
        //     gram_to_point =
        // }

        $result_point = $vori_to_point + $ana_to_point + $ratti_to_point + $point_to_point;

        return $result_point;
    }

    public static function fnGetTotalSizeNameWithQnt($size_name, $quantity = 1)
    {
        $total_point = self::fnGetTotalPointBySizeName($size_name);

        $total_point_with_quantity = $total_point * $quantity;
        $result_size = self::fnMakeSizeByPoint($total_point_with_quantity);

        $data = array(
            'size_name' => $result_size,
            'total_point_with_quantity' => $total_point_with_quantity
        );

        return $data;
    }

    public static function fnMakeSizeByPoint($total_sum_point)
    {
        $reminder = $total_sum_point;

        $total_vori = $reminder / 960;
        $reminder = $reminder % 960;

        $total_ana  = $reminder / 60;
        $reminder = $reminder % 60;

        $total_roti  = $reminder / 10;
        $reminder = $reminder % 10;

        $total_point = $reminder;

        $total_measure_size = floor($total_vori) . "." . floor($total_ana)  . "." . floor($total_roti) . "." . floor($total_point);

        return $total_measure_size;
    }

    public static function branchMaxDate($branchID)
    {
        if ($branchID == null) {
            $branchID = Common::getBranchId();
        }

        $maxDate = DB::table('pos_day_end')
            ->where('branch_id', $branchID)
            ->max('branch_date');

        return $maxDate;
    }
    /*
    max transactional date lagbe na may be ata r
    public static function branchMaxTransactionDate($branchID)
    {
        if ($branchID == null) {
            $branchID = Common::getBranchId();
        }

        $DateArray = array();

        $maxDateIssue = DB::table('pos_issues_m')
        ->where('branch_from', $branchID)
        ->where([['is_active', 1],['is_delete', 0]])
        ->max('issue_date');

        if(!empty($maxDateIssue)){
            array_push($DateArray,$maxDateIssue);
        }

        $maxDateIssueR = DB::table('pos_issues_r_m')
        ->where('branch_from', $branchID)
        ->where([['is_active', 1],['is_delete', 0]])
        ->max('return_date');
        if(!empty($maxDateIssueR)){
            array_push($DateArray,$maxDateIssueR);
        }


        $maxDateSales = DB::table('pos_sales_m')
        ->where('branch_id', $branchID)
        ->where([['is_active', 1],['is_delete', 0]])
        ->max('sales_date');
        if(!empty($maxDateSales)){
            array_push($DateArray,$maxDateSales);
        }

        $maxDateShopSales = DB::table('pos_shop_sales_m')
        ->where('branch_id', $branchID)
        ->where([['is_active', 1],['is_delete', 0]])
        ->max('sales_date');

        if(!empty($maxDateShopSales)){
            array_push($DateArray,$maxDateShopSales);
        }


        $maxDateTransfer = DB::table('pos_transfers_m')
        ->where('branch_from', $branchID)
        ->where([['is_active', 1],['is_delete', 0]])
        ->max('transfer_date');
        if(!empty($maxDateTransfer)){
            array_push($DateArray,$maxDateTransfer);
        }

        $maxDateWaiver = DB::table('pos_waiver_product_m')
        ->where('branch_id', $branchID)
        ->where([['is_active', 1],['is_delete', 0]])
        ->max('date');
        if(!empty($maxDateWaiver)){
            array_push($DateArray,$maxDateWaiver);
        }

        $maxDatePurchaseR = DB::table('pos_purchases_r_m')
        ->where('branch_id', $branchID)
        ->where([['is_active', 1],['is_delete', 0]])
        ->max('return_date');

        if(!empty($maxDatePurchaseR)){
            array_push($DateArray,$maxDatePurchaseR);
        }


        $maxDate = (max($DateArray));

        return $maxDate;
    }
    */



    /**
     *  @  peramiter  ===   $req , $branch, $previousData = null, $branchTo = false
     *
     *  $req  = new product id array and qntitiy array in $req object
     *  $branch  = branch id (from branch or to branch)
     *  $previousData  = previous product and qnt object (for updating data)
     *  $branchTo  = branch to stock check (condition jodi ager qnt theke kom qnt dite chay stock check minus a jacce ki na )
     *
     */
    public static function CheckProductStockinTransaction($req, $branch, $previousData = null, $branchTo = false)
    {
        $errorMsg   = null;
        $BranchId   = $branch;
        $BranchData = DB::table('gnl_branchs')
            ->where([['is_active', 1], ['is_delete', 0]])
            ->where('id', $BranchId)
            ->first();
        ## formId 16 = product negative sales
        $productNegativeSales = (!empty(DB::table('gnl_company_config')->where([['company_id', Common::getCompanyId()], ['form_id', 16]])->first())) ? 1 : 0;

        // $req_products = $req->product_id_arr;
        $product_id_arr       = (isset($req->product_id_arr) ? $req->product_id_arr : array());
        $product_quantity_arr = (isset($req->product_quantity_arr) ? $req->product_quantity_arr : array());
        // $maxDateFromAll       = self::branchMaxTransactionDate($BranchId);
        $StockData            = self::stockQuantity_Multiple($BranchId, $product_id_arr, null, null, $checkToDate = false);

        foreach ($product_id_arr as $key => $product_id_sin) {
            if (!empty($product_id_sin)) {
                $productID  = $product_id_sin;
                $productQnt = $product_quantity_arr[$key];

                if ($productQnt <= 0) {
                    $errorMsg = 'Product Quantity must be gather than 0 .';
                    break;
                    return $errorMsg;
                }
                $message     = '';
                $ProductData = DB::table('pos_products')->where([['is_delete', 0], ['is_active', 1]])
                    ->where('id', $productID)
                    ->select('id', 'product_name', 'prod_barcode')
                    ->first();
                $PreviousQnt = 0;
                if (!empty($previousData)) {
                    $PreviousQnt = !empty($previousData->where('product_id', $productID)->first()) ? $previousData->where('product_id', $productID)->first()->product_quantity : 0;
                }



                $Stock = $StockData[$productID]['Stock'];

                if ($branchTo) { ## if branch to check
                    $checkstock = $Stock - ($PreviousQnt - $productQnt);
                } else {
                    $checkstock = $Stock - $productQnt + $PreviousQnt;
                }
                // dd($Stock,$PreviousQnt,$productQnt);
                // $checkstock =  $Stock - $productQnt;

                if ($checkstock < 0 && $productNegativeSales == 0) {
                    $message .= $BranchData->branch_name . '(' . $BranchData->branch_code . ')';
                    ##dont delete this code
                    // $date     = new DateTime($maxDateFromAll);
                    // $date     = $date->format('d-m-Y');
                    // " . $date . "
                    $errorMsg = "Stock goes negative (" . $checkstock . ") for  \nProduct Name: " . $ProductData->product_name . "(" . $ProductData->prod_barcode . ") \n ";
                    if (!empty($message)) {
                        $errorMsg .= ' For ' . $message;
                    }
                    break;
                }
            }
        }

        return $errorMsg;
    }

    /** End Stock function */

    /** start Bill generator function  */
    public static function generateBillPurchase($branchID = null)
    {
        $BranchT         = 'App\\Model\\GNL\\Branch';
        $PurchaseMasterT = 'App\\Model\\POS\\PurchaseMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "PUR" . $BranchCode;

        $record = $PurchaseMasterT::select(['id', 'bill_no'])
            ->where('branch_id', $branchID)
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }
        return $BillNo;
    }

    /** start Supplier  Payment Bill no  generator function  */
    public static function generatePaymentBillNo($branchID = null)
    {
        $BranchT  = 'App\\Model\\GNL\\Branch';
        $PaymentT = 'App\\Model\\POS\\Payment';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "PAY" . $BranchCode;

        $record = $PaymentT::select(['id', 'payment_no'])
            ->where('branch_id', $branchID)
            ->where('payment_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('payment_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->payment_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }
        return $BillNo;
    }

    public static function generateBillPurchaseReturn($branchID = null)
    {
        $BranchT               = 'App\\Model\\GNL\\Branch';
        $PurchaseReturnMasterT = 'App\\Model\\POS\\PurchaseReturnMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "PR" . $BranchCode;

        $record = $PurchaseReturnMasterT::where('branch_id', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillAudit($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $AuditM  = 'App\\Model\\POS\\AuditMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "AU" . $BranchCode;

        $record = $AuditM::where('branch_id', $branchID)
            ->select(['id', 'audit_code'])
            ->where('audit_code', 'LIKE', "{$PreBillNo}%")
            ->orderBy('audit_code', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->audit_code);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillIssue($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $IssuemT = 'App\\Model\\POS\\Issuem';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "IS" . $BranchCode;

        $record = $IssuemT::where('branch_from', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillIssueReturn($branchID = null)
    {
        $BranchT       = 'App\\Model\\GNL\\Branch';
        $IssueReturnmT = 'App\\Model\\POS\\IssueReturnm';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "IR" . $BranchCode;

        $record = $IssueReturnmT::where('branch_from', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillTransfer($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\TransferMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "TRA" . $BranchCode;

        $record = $ModelT::where('branch_from', $branchID)
            ->select(['id', 'bill_no'])
            ->where('bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillShopSales($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\ShopSalesMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');
        $Counter = Common::getCounterNo();

        $PreBillNo = "SL" . $BranchCode . $Counter;
        $record    = $ModelT::select(['id', 'sales_bill_no'])
            ->where('branch_id', $branchID)
            ->where('sales_bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('sales_bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->sales_bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillSales($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\SalesMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');
        $Counter = Common::getCounterNo();

        $PreBillNo = "SL" . $BranchCode . $Counter;
        $record    = $ModelT::select(['id', 'sales_bill_no'])
            ->where('branch_id', $branchID)
            ->where('sales_bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('sales_bill_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->sales_bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillSalesReturn($BranchId = null)
    {
        $BranchT     = 'App\\Model\\GNL\\Branch';
        $SalesReturn = 'App\\Model\\POS\\SaleReturnm';

        $BranchCode = $BranchT::where(['is_delete' => 0, 'is_approve' => 1, 'id' => $BranchId])
            ->select('branch_code')
            ->first();

        // $ldate = date('Ym');
        $Counter = Common::getCounterNo();

        $PreBillNo = "SR" . $BranchCode->branch_code . $Counter;

        $record = $SalesReturn::where('branch_id', $BranchId)
            ->select(['id', 'return_bill_no'])
            ->where('return_bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('return_bill_no', 'DESC')
            ->first();

        if ($record) {

            $OldBillNoA = explode($PreBillNo, $record->return_bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillShopSalesReturn($BranchId = null)
    {
        $BranchT     = 'App\\Model\\GNL\\Branch';
        $SalesReturn = 'App\\Model\\POS\\ShopSaleReturnm';

        $BranchCode = $BranchT::where(['is_delete' => 0, 'is_approve' => 1, 'id' => $BranchId])
            ->select('branch_code')
            ->first();

        // $ldate = date('Ym');
        $Counter = Common::getCounterNo();

        $PreBillNo = "SR" . $BranchCode->branch_code . $Counter;

        $record = $SalesReturn::where('branch_id', $BranchId)
            ->select(['id', 'return_bill_no'])
            ->where('return_bill_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('return_bill_no', 'DESC')
            ->first();

        if ($record) {

            $OldBillNoA = explode($PreBillNo, $record->return_bill_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillPOBCustomer($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\POBDueSaleMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "POBC" . $BranchCode;
        $record    = $ModelT::select(['id', 'ob_no'])
            ->where('branch_id', $branchID)
            ->where('ob_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('ob_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->ob_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillPOBS($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\POBStockMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "POBS" . $BranchCode;
        $record    = $ModelT::select(['id', 'ob_no'])
            ->where('branch_id', $branchID)
            ->where('ob_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('ob_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->ob_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillRequisiton($branchID = null)
    {
        $BranchT      = 'App\\Model\\GNL\\Branch';
        $RequisitionM = 'App\\Model\\POS\\RequisitionMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreReqNo = "REQ" . $BranchCode;

        $record = $RequisitionM::select('id', 'requisition_no')
            ->where('branch_from', $branchID)
            ->where('requisition_no', 'LIKE', "{$PreReqNo}%")
            ->orderBy('requisition_no', 'DESC')
            ->first();

        if ($record) {
            $OldReqNoA = explode($PreReqNo, $record->requisition_no);
            $ReqNo     = $PreReqNo . sprintf("%05d", ($OldReqNoA[1] + 1));
        } else {
            $ReqNo = $PreReqNo . sprintf("%05d", 1);
        }

        return $ReqNo;
    }

    public static function generateBillOrder($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $OrderM  = 'App\\Model\\POS\\OrderMaster';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreOrderNo = "OR" . $BranchCode;

        $record = $OrderM::select('id', 'order_no')
            ->where('order_from', $branchID)
            ->where('order_no', 'LIKE', "{$PreOrderNo}%")
            ->orderBy('order_no', 'DESC')
            ->first();

        if ($record) {
            $OldOrderNo = explode($PreOrderNo, $record->order_no);
            $OrderNo    = $PreOrderNo . sprintf("%05d", ($OldOrderNo[1] + 1));
        } else {
            $OrderNo = $PreOrderNo . sprintf("%05d", 1);
        }

        return $OrderNo;
    }

    public static function generateBillDelivery($branchID = null)
    {
        $BranchT  = 'App\\Model\\GNL\\Branch';
        $Delivery = 'App\\Model\\POS\\Delivery';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreDeliveeryNo = "DL" . $BranchCode;

        $record = $Delivery::select('id', 'delivery_no')
            ->where('branch_id', $branchID)
            ->where('delivery_no', 'LIKE', "{$PreDeliveeryNo}%")
            ->orderBy('delivery_no', 'DESC')
            ->first();

        if ($record) {
            $OldDeliveryNo = explode($PreDeliveeryNo, $record->delivery_no);
            $deliveryNo    = $PreDeliveeryNo . sprintf("%05d", ($OldDeliveryNo[1] + 1));
        } else {
            $deliveryNo = $PreDeliveeryNo . sprintf("%05d", 1);
        }

        return $deliveryNo;
    }

    public static function generateCollectionNo($branchID = null)
    {
        // $Counter = 00;
        $BranchT     = 'App\\Model\\GNL\\Branch';
        $CollectionT = 'App\\Model\\POS\\Collection';

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $Counter = Common::getCounterNo();
        // dd($Counter);

        $PreColNo = "COL" . $BranchCode . $Counter;

        $record = $CollectionT::select(['id', 'collection_no'])
            ->where('branch_id', $branchID)
            ->where('collection_no', 'LIKE', "{$PreColNo}%")
            ->orderBy('collection_no', 'DESC')
            // ->orderBy('id', 'DESC')
            ->first();

        // dd($record->toArray());

        if ($record) {
            $OldCOlNoA = explode($PreColNo, $record->collection_no);
            $CollNo    = $PreColNo . sprintf("%05d", ($OldCOlNoA[1] + 1));
        } else {
            $CollNo = $PreColNo . sprintf("%05d", 1);
        }

        return $CollNo;
    }

    public static function generateBillWaiverProduct($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\WaiverProductMaster";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $Counter = Common::getCounterNo();

        $PreBillNo = "WP" . $BranchCode . $Counter;
        $record    = $ModelT::select(['id', 'waiver_product_no'])
            ->where('branch_id', $branchID)
            ->where('waiver_product_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('waiver_product_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->waiver_product_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateDayendNo($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\DayEnd";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "DE" . $BranchCode;
        $record    = $ModelT::select(['id', 'dayend_no'])
            ->where('branch_id', $branchID)
            ->where('dayend_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('dayend_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->dayend_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateMonthendNo($branchID = null)
    {
        $BranchT = 'App\\Model\\GNL\\Branch';
        $ModelT  = "App\\Model\\POS\\MonthEnd";

        $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "ME" . $BranchCode;
        $record    = $ModelT::select(['id', 'monthend_no'])
            ->where('branch_id', $branchID)
            ->where('monthend_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('monthend_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->monthend_no);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillDiscount($branchID = null)
    {
        // $BranchT = 'App\\Model\\GNL\\Branch';
        // $PurchaseMasterT = 'App\\Model\\POS\\PurchaseMaster';

        // $BranchCodeQuery = $BranchT::where([['is_delete', 0], ['is_approve', 1], ['id', $branchID]])
        //     ->select('branch_code')
        //     ->first();

        // if ($BranchCodeQuery) {
        //     $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        // } else {
        //     $BranchCode = sprintf("%04d", 0);
        // }

        // $PreBillNo = "PUR" . $BranchCode;
        $PreBillNo = "DIS";

        $record = DB::table('pos_discount_m')
            ->select(['id', 'dis_code'])
            // ->where('branch_id', $branchID)
            ->where('dis_code', 'LIKE', "{$PreBillNo}%")
            ->orderBy('dis_code', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->dis_code);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }
        return $BillNo;
    }

    /** Start Generate Price Updating Code*/
    public static function generatePriceUpdatingCode()
    {
        $prefix = "PUP";

        $record = DB::table('pos_price_updating_m')
            ->select(['id', 'price_updating_code'])
            ->where('price_updating_code', 'LIKE', "{$prefix}%")
            ->orderBy('price_updating_code', 'DESC')
            ->first();

        if ($record) {
            $lastCode = explode($prefix, $record->price_updating_code);
            $newCode  = $prefix . sprintf("%05d", ($lastCode[1] + 1));
        } else {
            $newCode = $prefix . sprintf("%05d", 1);
        }
        return $newCode;
    }
    /** End Generate Price Updating Code*/
    /** end Bill generator function  */

    /** Start Function for Retrieving Updated Sales Price*/
    public static function fnForUpdatedSalesPrice($productId, $salesDate)
    {
        if (!empty($salesDate)) {
            $salesDate = (new Datetime($salesDate))->format('Y-m-d');
        }

        $salePrice   = '';
        $priceUpdate = DB::table('pos_price_updating_m')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->get();

        $priceM = $priceUpdate->where('effective_date', '<=', $salesDate);

        if (count($priceM) > 0) {

            $productCode = $priceM->pluck('price_updating_code');

            if (count($productCode) > 0) {
                $queryData = DB::table('pos_price_updating_d')
                    ->where('product_id', $productId)
                    ->whereIn('price_updating_code', $productCode)
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }

            if ($queryData) {

                $salePrice = $queryData->updated_price;
            }
        }

        return $salePrice;
    }
    /** End Function for Retrieving Updated Sales Price*/

    /** Start Function for Retrieving Updated Sales Price*/
    public static function fnUpdatedSalesPrice_Multiple($salesDate, $productId = [])
    {
        if (!empty($salesDate)) {
            $salesDate = (new Datetime($salesDate))->format('Y-m-d');
        }

        $priceUpdate = DB::table('pos_price_updating_m')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->get();

        $productCode = $priceUpdate->where('effective_date', '<=', $salesDate)->pluck('price_updating_code');

        $queryData = array();
        if (count($productCode) > 0) {

            $queryData = DB::table('pos_price_updating_d as d')
                ->leftjoin('pos_price_updating_m as m', function ($query) {
                    $query->on('d.price_updating_code', '=', 'm.price_updating_code')
                        ->where([['m.is_delete', 0], ['m.is_active', 1]]);
                })
                ->where(function ($queryData) use ($productId) {
                    if (count($productId) > 0) {
                        $queryData->whereIn('d.product_id', $productId);
                    }
                })
                ->whereIn('m.price_updating_code', $productCode)
                ->when(true, function ($queryData) {
                    if (Common::getDBConnection() == "sqlite") {
                        $queryData->selectRaw('d.product_id, SUBSTR(MAX(SUBSTR(m.effective_date, 30, "$") || d.updated_price),31) AS updated_price_t');
                    } else {
                        $queryData->selectRaw('d.product_id, SUBSTR(MAX(CONCAT(LPAD(m.effective_date,30, "$"),d.updated_price)),31) AS updated_price_t');
                    }
                })
                ->groupBy('d.product_id')
                ->orderBy('m.effective_date', 'DESC')
                ->pluck('updated_price_t', 'product_id')
                ->toArray();
        }

        return $queryData;
    }

    /** End Function for Retrieving Updated Sales Price*/

    ## This function is used to check if tx exists under an employee
    ## before transfer/termination
    public static function checkTransactionForEmployee($employeeId, $action = "terminating")
    {
        $moduleFlag = false;
        $errMessage = '';

        if (Common::checkActivatedModule('pos')) {
            $moduleFlag = true;
        }

        ## write code for checking transaction
        if ($moduleFlag == true) {

            $employeeNo = DB::table('hr_employees')
                ->where('id', $employeeId)
                ->first()
                ->employee_no;

            $getSales = DB::table('pos_sales_m')
                ->where([['is_delete', 0], ['is_active', 1], ['is_complete', 0]])
                ->whereRaw('CASE WHEN transfer_to IS NOT NULL THEN transfer_to = "' . $employeeNo . '" ELSE employee_id = "' . $employeeNo . '" END')
                ->count();

            if ($getSales > 0) {
                $errMessage = "Transaction exists in Sales.";
            }

            // if($errMessage == false){
            //     $getSales = DB::table('pos_sales_return_m')
            //         ->where([['is_delete',0],['is_active',1]])
            //         ->whereRaw('CASE WHEN transfer_to IS NOT NULL THEN transfer_to = "' . $employeeNo . '" ELSE employee_id = "' . $employeeNo . '" END')
            //         ->get();

            //     if (count($getSales) > 0) {
            //         $errMessage = true;
            //     }
            // }
        }

        if ($errMessage != '') {
            $errMessageTxt = "This Employee has transaction in POS Module. Please Transfer/Remove Transaction before " . $action . " this employee." . $errMessage;
        } else {
            $errMessageTxt = $errMessage;
        }
        return $errMessageTxt;
    }

    ## This function is used to Generate barcode for shop and ngo
    public static function fnGenerateBarcode($GroupID, $CatID, $BrandID, $Type = null)
    {

        if (empty($Type) || $Type == null) {
            $Type = Common::getCompanyType();
        }

        if ($Type == 2) {

            $BAR_INITIAL = '';

            $Group_short = DB::table('gnl_groups')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->first();

            if (!empty($Group_short->short_form)) {
                $BAR_INITIAL = $Group_short->short_form;
            } else {
                $BAR_INITIAL = 'GT';
            }

            $GroupID = sprintf("%02d", $GroupID);
            $CatID   = sprintf("%03d", $CatID);
            $BrandID = sprintf("%03d", $BrandID);
            $newID   = sprintf("%05d", "1");

            $barcode_pre = $BAR_INITIAL . $GroupID . $CatID . $BrandID;

            $data = DB::table('pos_products')
                ->select(['id', 'sys_barcode'])
                ->where('sys_barcode', 'LIKE', "{$barcode_pre}%")
                ->orderBy('sys_barcode', 'DESC')->first();

            if ($data) {

                $oldBarNum = explode($barcode_pre, $data->sys_barcode);
                $barcode   = $barcode_pre . sprintf("%05d", ($oldBarNum[1] + 1));
                $barcode   = sprintf("%014s", $barcode);
            } else {
                $barcode = $barcode_pre . $newID;
                $barcode = sprintf("%014s", $barcode);
            }
        } else {

            $GroupID = sprintf("%02d", $GroupID);
            $CatID   = sprintf("%03d", $CatID);
            $BrandID = sprintf("%03d", $BrandID);
            $newID   = sprintf("%05d", "1");

            $barcode_pre = $GroupID . $CatID . $BrandID;

            $data = DB::table('pos_products')
                ->select(['id', 'sys_barcode'])
                ->where('sys_barcode', 'LIKE', "{$barcode_pre}%")
                ->orderBy('sys_barcode', 'DESC')->first();

            if ($data) {
                $barcode = $data->sys_barcode + 0;
                $barcode += 1;
                $barcode = (string) $barcode;

                // dd($barcode, sprintf("%013s", $barcode));

                $barcode = sprintf("%013s", $barcode);
            } else {
                $barcode = $barcode_pre . $newID;
                $barcode = sprintf("%013s", $barcode);
            }
        }

        $barcode_generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        $image             = '<img width="80%" src="data:image/png;base64,' . base64_encode($barcode_generator->getBarcode($barcode, $barcode_generator::TYPE_CODE_128)) . '">';

        $Data = [
            'barcode'   => $barcode,
            'bar_image' => $image,
        ];

        return $Data;
    }

    /** Start Get barcode image with config paramiters */
    public static function genarateBarcodeImagebyCofigPeramiters($prod_barcode = null, $barcode_colors = null, $arrayText = null, $BarcodeImageShow = null)
    {

        $barcode_generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        // $barcode_image = base64_encode('<img width="100%" src="data:image/png;base64,' . base64_encode($barcode_generator->getBarcode($prod_barcode, $barcode_generator::TYPE_CODE_128, 2, 30, $barcode_colors, $arrayText, $BarcodeImageShow)) . '">');
        $barcode_image = base64_encode($barcode_generator->getBarcode($prod_barcode, $barcode_generator::TYPE_CODE_128_A, 2, 40, $barcode_colors, $arrayText, $BarcodeImageShow));
        return $barcode_image;
    }
    /** End Function for Retrieving Updated Sales Price*/
    public static function fnOrderRemainingOnt($ProductId, $order)
    {

        $queryData = DB::table('pos_orders_m as pom')
            ->where([['pom.order_no', $order], ['pod.product_id', $ProductId]])
            ->select('pod.product_id')
            ->leftjoin('pos_orders_d as pod', function ($queryData) {
                $queryData->on('pod.order_no', '=', 'pom.order_no');
            })
            ->leftjoin('pos_products as prod', function ($queryData) {
                $queryData->on('prod.id', '=', 'pod.product_id')
                    ->where([['prod.is_delete', 0], ['prod.is_active', 1]]);
            })
            ->addSelect([
                'remaining_qtn' => DB::table('pos_purchases_m as ppm')
                    ->select(DB::raw('(pod.product_quantity - IFNULL(SUM(ppd.product_quantity), 0))'))
                    ->leftjoin('pos_purchases_d as ppd', function ($queryData) {
                        $queryData->on('ppd.purchase_bill_no', '=', 'ppm.bill_no');
                    })
                    ->whereColumn([['pom.order_no', 'ppm.order_no'], ['ppd.product_id', 'pod.product_id']])
                    ->where([['ppm.is_delete', 0], ['ppm.is_active', 1]])
                    ->limit(1),
            ])
            ->first();

        $remaining_qtn =  (!empty($queryData->remaining_qtn)) ? $queryData->remaining_qtn : 0;

        return $remaining_qtn;
    }


    public static function fnGetSizeIDorCreate($productID, $sizeName)
    {
        $SizeData = DB::table('pos_p_sizes')->where([['is_delete', 0], ['is_active', 1]])->where('size_name', 'LIKE', $sizeName)->first();

        if (!empty($SizeData)) {
            $sizeID = $SizeData->id;
        } else {

            $productData = DB::table('pos_products')->where([['is_delete', 0], ['is_active', 1]])->where('id', $productID)->first();

            // dd($productData,$productID);


            $id = DB::table('pos_p_sizes')->insertGetId(
                [
                    'size_name' => $sizeName,
                    'prod_group_id' => $productData->prod_group_id,
                    'prod_cat_id' => $productData->prod_cat_id,
                    'prod_sub_cat_id' => $productData->prod_sub_cat_id

                ]
            );

            $sizeID = $id;
        }

        return $sizeID;
    }


    public static function fnInsertGoldProduct($productID, $sizeName, $purchase_date, $purchase_bill_no, $unit_cost_price, $unit_other_cost, $is_other_cost = 0)
    {

        $ProductData = DB::table('pos_products')->where([['is_delete', 0], ['is_active', 1]])->where('id', $productID)->first();

        $serial_barcode  = self::generateSerialBarcode(Common::getBranchId(), $productID);

        $prod_size_id = self::fnGetSizeIDorCreate($productID, $sizeName);
        // dd($is_other_cost);

        $id = DB::table('pos_product_details')->insertGetId(
            [
                'product_id' => $productID,
                'prod_size_id' => $prod_size_id,
                'purchase_bill_no' => $purchase_bill_no,
                'purchase_date' => $purchase_date,
                'prod_barcode' => $ProductData->prod_barcode,
                'serial_barcode' => $serial_barcode,
                'unit_cost_price' => $unit_cost_price,
                'unit_other_cost' => $unit_other_cost,
                'is_other_cost' => $is_other_cost,
            ]
        );

        $ProddetailsID = $id;

        return $ProddetailsID;
    }


    /** start Serial barcode no  generator function  */
    public static function generateSerialBarcode($branchID = null, $productID = null)
    {
        $ProductT  = 'App\\Model\\POS\\Product';

        $CodeQuery = $ProductT::where([['is_delete', 0], ['id', $productID]])
            ->select('prod_barcode')
            ->first();

        $prodCode = $CodeQuery->prod_barcode;

        $PreBillNo = $prodCode . ".";

        $record = DB::table('pos_product_details')->select(['id', 'serial_barcode'])
            ->where('serial_barcode', 'LIKE', "{$PreBillNo}%")
            ->orderBy('serial_barcode', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->serial_barcode);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }
        return $BillNo;
    }
}
