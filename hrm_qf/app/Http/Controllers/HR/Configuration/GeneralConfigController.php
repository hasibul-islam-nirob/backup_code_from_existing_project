<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralConfigController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            // dd($request->all());
            $emp_code = array();
            $emp_code['emp_code_generator'] = $request->gen_type;
            $emp_code['prefix_position'] = $request->prefix_val != null? 1:null;
            $emp_code['org_emp_serial_position'] = $request->org_emp_serial_position;
            $emp_code['project_emp_serial_position'] = $request->pro_emp_serial_position;
            $emp_code['project_code_position'] = $request->project_code_position;
            $emp_code['year_month_position'] = $request->year_month_position;
            $emp_code['prefix_val'] = $request->prefix_val;
            $emp_code['org_emp_serial_length'] = $request->org_emp_serial_length;
            $emp_code['project_emp_serial_length'] = $request->pro_emp_serial_length;
            $emp_code['separator_val'] = $request->separator_val;
            $emp_code_format = json_encode($emp_code);
            DB::beginTransaction();
            try {
                DB::table('hr_config')->where('title', 'emp_code_format')
                        ->update([
                            'content'     => $emp_code_format,
                        ]);
                        DB::commit();
                        $notification = array(
                            'message'    => 'Employee Code Format has been updated Successfully',
                            'alert-type' => 'success',
                        );

                        return response()->json($notification);
            } catch (\Throwable $e) {
                DB::rollback();
                        $notification = array(
                            'alert-type' => 'error',
                            'message'    => 'Something went wrong',
                            'consoleMsg' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
                        );
                    return response()->json($notification);
            }
            // dd($emp_code_format);
        }
        return view('HR.Configuration.GeneralConfig.home');
    }

    public function emp_code(Request $request)
    {
        if (is_null($request->prefix_val)) {
            $prefix_postion = 0;
        }else {
            $prefix_postion = 1;
        }
        //dd($request->all());
        try {
            if ($prefix_postion == 1) {
                $employee_code = $this->positionPrefixOne($request);
            }
            elseif ($request->org_emp_serial_position == 1) {
                $employee_code = $this->positionOrgSerialOne($request);
            }elseif ($request->pro_emp_serial_position == 1) {
                $employee_code = $this->positionProSerialOne($request);
            }elseif ($request->project_code_position == 1) {
                $employee_code = $this->positionProCodeOne($request);
            }else {
                $employee_code = $this->positionYMOne($request);
            }
            return response()->json($employee_code);
        } catch (\Throwable $th) {
            $notification = array(
                'alert-type' => 'error',
                'message'    => 'Something went wrong',
                'consoleMsg' => $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage(),
            );
            return response()->json($notification);
        }

    }

    public function positionPrefixOne(Request $request)
    {
        if (!is_null($request->pre_position)) {
            $emp_code = $request->prefix_val . $request->separator_val;
            //when prefix 1 and for position 2

            if ($request->org_emp_serial_position == 2) {
                 $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
            }elseif ($request->pro_emp_serial_position == 2) {
                 $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
            }elseif ($request->project_code_position == 2) {
                 $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
            }elseif ($request->year_month_position == 2) {
                $emp_code = $emp_code . "202103" . $request->separator_val;
            }else {
                $emp_code = $emp_code;
            }

            //when prefix 1 and for position 3
            if ($request->org_emp_serial_position == 3) {
                 $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
            }elseif ($request->pro_emp_serial_position == 3) {
                 $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
            }elseif ($request->project_code_position == 3) {
                 $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
            }elseif ($request->year_month_position == 3) {
                $emp_code = $emp_code . "202103" . $request->separator_val;
            }else {
                $emp_code = $emp_code;
            }

            ////when prefix 1 and for position 4
            if ($request->org_emp_serial_position == 4) {
                 $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
            }elseif ($request->pro_emp_serial_position == 4) {
                 $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
            }elseif ($request->project_code_position == 4) {
                 $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
            }elseif ($request->year_month_position == 4) {
                $emp_code = $emp_code . "202103" . $request->separator_val;
            }else {
                $emp_code = $emp_code;
            }

            ///when prefix 1 and for position 5
            if ($request->org_emp_serial_position == 5) {
                 $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT);
            }elseif ($request->pro_emp_serial_position == 5) {
                 $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT);
            }elseif ($request->project_code_position == 5) {
                 $emp_code = $emp_code . 130; //130 as project code
            }elseif ($request->year_month_position == 5) {
                $emp_code = $emp_code . "202103";
            }else {
                $emp_code = $emp_code;
            }

            return $emp_code;
        }
    }

    public function positionOrgSerialOne(Request $request)
    {
        $emp_code = str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;


        if ($request->pro_emp_serial_position == 2) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->project_code_position == 2) {
                $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
        }elseif ($request->year_month_position == 2) {
            $emp_code = $emp_code . "202103" . $request->separator_val;
        }else {
            $emp_code = $emp_code;
        }


        if ($request->pro_emp_serial_position == 3) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->project_code_position == 3) {
                $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
        }elseif ($request->year_month_position == 3) {
            $emp_code = $emp_code . "202103" . $request->separator_val;
        }else {
            $emp_code = $emp_code;
        }

        if ($request->pro_emp_serial_position == 4) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT);
        }elseif ($request->year_month_position == 4) {
            $emp_code = $emp_code . "202103";
        }else {
            $emp_code = $emp_code;
        }
        return $emp_code;
    }

    public function positionProSerialOne(Request $request)
    {
        $emp_code = str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;


        if ($request->org_emp_serial_position == 2) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->project_code_position == 2) {
                $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
        }elseif ($request->year_month_position == 2) {
            $emp_code = $emp_code . "202103" . $request->separator_val;
        }else {
            $emp_code = $emp_code;
        }


        if ($request->org_emp_serial_position == 3) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->project_code_position == 3) {
                $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
        }elseif ($request->year_month_position == 3) {
            $emp_code = $emp_code . "202103" . $request->separator_val;
        }else {
            $emp_code = $emp_code;
        }

        if ($request->org_emp_serial_position == 4) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT);
        }elseif ($request->project_code_position == 4) {
                $emp_code = $emp_code . 130; //130 as project code
        }elseif ($request->year_month_position == 4) {
            $emp_code = $emp_code . "202103";
        }else {
            $emp_code = $emp_code;
        }
        return $emp_code;
    }

    public function positionProCodeOne(Request $request)
    {
        $emp_code = 130 . $request->separator_val;


        if ($request->org_emp_serial_position == 2) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->pro_emp_serial_position == 2) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->year_month_position == 2) {
            $emp_code = $emp_code . "202103" . $request->separator_val;
        }else {
            $emp_code = $emp_code;
        }


        if ($request->org_emp_serial_position == 3) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->pro_emp_serial_position == 3) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->year_month_position == 3) {
            $emp_code = $emp_code . "202103" . $request->separator_val;
        }else {
            $emp_code = $emp_code;
        }

        if ($request->org_emp_serial_position == 4) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT);
        }elseif ($request->pro_emp_serial_position == 4) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT);
        }elseif ($request->year_month_position == 4) {
            $emp_code = $emp_code . "202103";
        }else {
            $emp_code = $emp_code;
        }
        return $emp_code;
    }

    public function positionYMOne(Request $request)
    {
        $emp_code = "202103" . $request->separator_val;


        if ($request->org_emp_serial_position == 2) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->pro_emp_serial_position == 2) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->project_code_position == 2) {
                $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
        }else {
            $emp_code = $emp_code;
        }


        if ($request->org_emp_serial_position == 3) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->pro_emp_serial_position == 3) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT) . $request->separator_val;
        }elseif ($request->project_code_position == 3) {
                $emp_code = $emp_code . 130 . $request->separator_val; //130 as project code
        }else {
            $emp_code = $emp_code;
        }

        if ($request->org_emp_serial_position == 4) {
                $emp_code = $emp_code . str_pad(23, $request->org_emp_serial_length, "0", STR_PAD_LEFT);
        }elseif ($request->pro_emp_serial_position == 4) {
                $emp_code = $emp_code . str_pad(18, $request->pro_emp_serial_length, "0", STR_PAD_LEFT);
        }elseif ($request->project_code_position == 4) {
                $emp_code = $emp_code . 130; //130 as project code
        }else {
            $emp_code = $emp_code;
        }
        return $emp_code;
    }
}
