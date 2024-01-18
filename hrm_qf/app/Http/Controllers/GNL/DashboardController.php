<?php

namespace App\Http\Controllers\GNL;

/* Base Controller */

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\HtmlService as HTML;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/* Model Load Start */

// use App\Model\POS\Product;
/* Model Load End */

class DashboardController extends Controller
{
    public function __construct()
    {
        // // $this->middleware(['auth', 'permission']);
        // $this->middleware(['auth']);
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //        return view('GNL.Dashboard.dashboard');
        if (Auth::check()) {

            // $allRoutes = Route::getRoutes();
            // $prefix_array = array();

            // $ignoreInnerLayout = [
            //     '^gnl',
            //     '^pos',
            //     '^acc',
            //     '^mfn',
            //     '^fam',
            //     '^inv',
            //     '^proc',
            //     '^bill',
            //     '^hr',
            //     '^hms'
            // ];

            // // converts the array into a regex friendly or list
            // $patterns_flattened = implode('|', $ignoreInnerLayout);

            // foreach($allRoutes as $route){
            //     if ( preg_match('/'. $patterns_flattened .'/', $route->getPrefix()) == true)
            //     {
            //         $prefix_array[] = $route->getPrefix();
            //     }
            // }

            // dd(array_values(array_filter(array_unique($prefix_array))));



            // dd(HTML::makeMenus());

            // $Menus = HTML::makeMenus();

            // dd($Menus);

            return view('GNL.Dashboard.dashboard');
        }
        return Redirect::to("login")->withSuccess('Opps! You do not have access');
    }

    public function script($moduleShortName = null)
    {
        $allRoutes = Route::getRoutes();
        $prefix_array = array();

        $machingPrefixList = [
            '^gnl',
            '^pos',
            '^acc',
            '^mfn',
            '^fam',
            '^inv',
            '^proc',
            '^bill',
            '^hr',
            '^hms'
        ];

        ## converts the array into a regex friendly or list
        $patterns_flattened = implode('|', $machingPrefixList);

        if(!empty($moduleShortName)){

            $patterns_flattened = "^".$moduleShortName;
        }

        foreach ($allRoutes as $route) {
            if (preg_match('/' . $patterns_flattened . '/', $route->getPrefix()) == true) {
                $prefix_array[] = $route->getPrefix();
            }
        }

        $routeList = array_values(array_filter(array_unique($prefix_array)));

        $menuEntry = DB::table("gnl_sys_menus")->where('is_delete', 0)->whereIn('route_link', $routeList)->orderBy('module_id')->pluck('route_link')->toArray();

        $menuNotEntry = array_diff($routeList, $menuEntry);

        $dataArray["Menu_Not_Entry_List"] = $menuNotEntry;
        $dataArray["Menu_Entry_List"] = $menuEntry;
        $dataArray["Route_List"] = $routeList;

        // "Menu Not Entry List:", $menuNotEntry, "Menu Entry List:", $menuEntry, "RouteList:", $menuRoutes
        dd($dataArray);
    }
}
