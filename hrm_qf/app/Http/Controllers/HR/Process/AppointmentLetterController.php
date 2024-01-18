<?php

namespace App\Http\Controllers\HR\Process;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentLetterController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('HR.Process.AppointmentLetter.index');
        }
        $columns = array(
            0 => 'name',
            1 => 'printed_at',
        );
        $orderColumnIndex = (int)$request->input('order.0.column') <= 1 ? 0 : (int)$request->input('order.0.column') - 1;
        

        // Searching variable
        $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

        $letter_data = DB::table('hr_appointment_letters')->orderBy('emp_id');

        $totalData = (clone $letter_data)->count();
        $letter_data = $letter_data->limit($request->input('length'))
                            ->offset($request->input('start'))
                            ->get();

        $sl = (int)$request->start + 1;
        foreach ($letter_data as $key => $row) {
            $letter_data[$key]->sl = $sl++;
            $letter_data[$key]->id = encrypt($row->id);
            $letter_data[$key]->name = DB::table('hr_employees')->where('id',$row->emp_id)->where('is_active',1)->pluck('name_eng')->first();
            if (is_null($row->updated_at)) {
                $letter_data[$key]->printed_at = $row->created_at;
            }else {
                $letter_data[$key]->printed_at = $row->updated_at;
            }
        }
        if ($search != null) {
            $letter_data->where('name', 'LIKE', "%{$search}%");
        }
        $data = array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => $totalData,
            "recordsFiltered"   => $totalData,
            'data'              => $letter_data,
        );


        return response()->json($data);
    }

    public function view($id){
        $letter_data = DB::table('hr_appointment_letters')
                    ->where('id',decrypt($id))
                    ->pluck('appointment_letter')
                    ->first();
        // dd(gettype($letter_data));
        $letter = strval($letter_data);
        $letter_data = nl2br($letter);
        // dd($letter);
        // dd(substr_count($letter,"<br />"));
        for ($i=0; $i < substr_count($letter_data,"<br />"); $i++) {
            $appointment_letter[$i] = explode("<br />",$letter_data);
            $emp_letter_data = $appointment_letter[$i];
        }
        // $letters = $letter_data[0];
        // dd($emp_letter_data);
        return view('HR.Process.AppointmentLetter.view',compact('emp_letter_data'));
    }

    public function format(Request $request)
    {
        if ($request->isMethod('post')) {
            $letter_data = str_replace(['**','_','- ','###'],'',$request->content);
            DB::beginTransaction();
            try {
                DB::table('hr_config')->where('title','appointment_letter')
                                    ->update([
                                        'content' => $letter_data,
                                    ]);
                DB::commit();
                $notification = array(
                    'message'       => 'Your Letter has been Inserted Successfully!',
                    'alert-type'    => 'success',
                );

                return response()->json($notification);
            } catch (\Throwable $th) {
                DB::rollback();
                $notification = array(
                    'alert-type'    => 'error',
                    'message'       => 'Something went wrong',
                    'consoleMsg'    => $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage()
                );

                return response()->json($notification);
            }
        }
        $appontment_letter_data = DB::table('hr_config')
                                ->where('title','appointment_letter')
                                ->select('content')
                                ->first();
        return view('HR.Process.AppointmentLetter.format',compact('appontment_letter_data'));
    }

    public function employeeAppointmentLetter($id)
    {
        $employeeData = DB::table('hr_employees')
                        // ->where('id',decrypt($id))
                        ->where('id',$id)
                        ->where('is_active',1)
                        ->select('name_eng','join_date','position_id')
                        ->first();
        $orgPosition = DB::table('hr_designations')->where('is_delete',0)->where('id',$employeeData->position_id)->select('name')->first();
        $letter = DB::table('hr_config')->where('title','appointment_letter')->pluck('content')->first();
        $letter = strval($letter);
        $name = $employeeData->name_eng;
        $appointment_date = $employeeData->join_date;

        $letter = str_replace('{employeename}',$name, $letter);
        $letter = str_replace('{designation}',$orgPosition->name, $letter);
        $letter = str_replace('{joining_date}',$appointment_date, $letter);
        $letter = nl2br($letter);
        // dd($letter);
        // dd(substr_count($letter,"<br />"));
        for ($i=0; $i < substr_count($letter,"<br />"); $i++) {
            $appointment_letter[$i] = explode("<br />",$letter);
            $letter_data = $appointment_letter[$i];
        }
        // $letters = $letter_data[0];
        // dd($letter_data);

        return view('HR.Process.AppointmentLetter.letter',compact('letter_data','id'));
    }
    public function saveLetter(Request $request)
    {
        // dd($request->all());
        $letter_data = DB::table('hr_appointment_letters')
                    ->where('emp_id',$request->emp_id)
                    ->first();
        DB::beginTransaction();
        if (empty($letter_data)) {
            try {
                DB::table('hr_appointment_letters')
                            ->insert([
                                'emp_id' => $request->emp_id,
                                'appointment_letter' => $request->letter,
                                'created_at'    => Carbon::now(),
                            ]);
                DB::commit();
                $notification = array(
                    'message'       => 'Appointment Letter has been saved Successfully!',
                    'alert-type'    => 'success',
                );

                return response()->json($notification);
            } catch (\Throwable $th) {
                DB::rollback();
                $notification = array(
                    'alert-type'    => 'error',
                    'message'       => 'Something went wrong',
                    'consoleMsg'    => $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage()
                );

                return response()->json($notification);
            }
        }else {
            try {
                DB::table('hr_appointment_letters')
                            ->where('emp_id',$request->emp_id)
                            ->update([
                                'appointment_letter' => $request->letter,
                                'updated_at'    => Carbon::now(),
                            ]);
                DB::commit();
                $notification = array(
                    'message'       => 'Appointment Letter has been updated Successfully!',
                    'alert-type'    => 'success',
                );

                return response()->json($notification);
            } catch (\Throwable $th) {
                DB::rollback();
                $notification = array(
                    'alert-type'    => 'error',
                    'message'       => 'Something went wrong',
                    'consoleMsg'    => $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage()
                );

                return response()->json($notification);
            }
        }
    }
}
