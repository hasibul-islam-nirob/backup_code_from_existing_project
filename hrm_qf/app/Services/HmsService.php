<?php

namespace App\Services;

use App\Model\HMS\Building;
use App\Model\HMS\Floor;
use DateTime;
use Exception;
use DateInterval;
use function PHPSTORM_META\type;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Services\CommonService as Common;
use Illuminate\Http\Request;

class HmsService
{
    private $config;

   
    public static function getConfig()
    {
        $config = DB::table('gnl_company_config')
            ->where([
                ['module_id', 14],
                ['form_id', 'HMS.2'],
                ['form_value', 1]
            ])
            ->get();
        
        return $config;
    }

    public static function getOptionsForBuilding($parameter = [])
    {
        $building = DB::table('hms_building')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select Building';
        $buildArr[0]['html'] = 'Select Building';
        $buildArr[0]['title'] = 'Select Building';
        foreach ($building as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text'] = $item->code ? $item->name.' ['.$item->code.']' : $item->name;

            $buildArr[$key + 1]['html']  =   $item->code ? $item->name.' ['.$item->code.']' : $item->name;
        }
        
        return $buildArr;
    }
    public static function getOptionsForPackages($packageIds = [])
    {

        $packages = DB::table('hms_hall_fee_package')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($masterQuery) use ($packageIds) {
                        if (isset($packageIds) && count($packageIds) > 0) {
                            $masterQuery->whereIn('id', $packageIds);
                        }
                    })
                    ->get();

        $packagearr            = [];
        $packagearr[0]['id']   = '';
        $packagearr[0]['text'] = 'Select Packages';
        $packagearr[0]['html'] = 'Select Packages';
        $packagearr[0]['title'] = 'Select Packages';

        foreach ($packages as $key => $item) {

            $packagearr[$key + 1]['id']    = $item->id;
            $packagearr[$key + 1]['text'] = $item->name_bn;

            $packagearr[$key + 1]['html']  =   $item->name_bn;
        }

        return $packagearr;
    }
    public static function getOptionsForReason($parameter = [])
    {
        $reason = DB::table('hms_seat_cancel_reason')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select Reason';
        $buildArr[0]['html'] = 'Select Reason';
        $buildArr[0]['title'] = 'Select Reason';

        foreach ($reason as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text'] = $item->title;

            $buildArr[$key + 1]['html']  =   $item->title;
        }

        return $buildArr;
    }
    public static function getOptionsForStatus($parameter = [])
    {
        $building = DB::table('hms_academic_status')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->orderBy('hms_academic_status.order_by')
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select Status';
        $buildArr[0]['html'] = 'Select Status';
        $buildArr[0]['title'] = 'Select Status';

        foreach ($building as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text']  = $item->name;

            $buildArr[$key + 1]['html']  =  $item->name ;
        }

        return $buildArr;
    }
   
    public static function getOptionsForSession($parameter = [])
    {
        $building = DB::table('hms_academic_session')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select Session';
        $buildArr[0]['html'] = 'Select Session';
        $buildArr[0]['title'] = 'Select Session';

        foreach ($building as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text']  = $item->name;

            $buildArr[$key + 1]['html']  =  $item->name ;
        }

        return $buildArr;
    }
    public static function getOptionsForAcademicDepartments($parameter = [])
    {
        $building = DB::table('hms_academic_departments')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select Department';
        $buildArr[0]['html'] = 'Select Department';
        $buildArr[0]['title'] = 'Select Department';

        foreach ($building as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text']  = $item->dept_name;

            $buildArr[$key + 1]['html']  =  $item->dept_name ;
        }

        return $buildArr;
    }
    public static function getOptionsForOrganization($parameter = [])
    {
        $organization = DB::table('hms_organizations')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select Organization';
        $buildArr[0]['html'] = 'Select Organization';
        $buildArr[0]['title'] = 'Select Organization';

        foreach ($organization as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text']  = $item->name;

            $buildArr[$key + 1]['html']  =  $item->name ;
        }

        return $buildArr;
    }
    public static function getOptionsForScholarship($parameter = [])
    {
        $scholarships = DB::table('hms_scholarships')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select Scholarship';
        $buildArr[0]['html'] = 'Select Scholarship';
        $buildArr[0]['title'] = 'Select Scholarship';

        foreach ($scholarships as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text']  = $item->name;

            $buildArr[$key + 1]['html']  =  $item->name ;
        }

        return $buildArr;
    }

    public static function getOptionsForFloor($buildingId = null)
    {
        $floors = DB::table('hms_floor as hf')
                    ->where([['hf.is_delete', 0], ['hf.is_active', 1]])
                    ->where([['hb.is_delete', 0], ['hb.is_active', 1]])
                    ->join('hms_building as hb', 'hb.id', '=', 'hf.building_id')
                    ->where([['hb.is_delete',0],['hb.is_active',1]])
                    ->select('hf.*', 'hb.name as b_name', 'hb.code as b_code')
                    ->where(function ($query) use ($buildingId){
                        if(!empty($buildingId)){
                            $query->where('building_id', $buildingId);
                        }
                    })
                    ->get();
       
        $floorArr            = [];
        $floorArr[0]['id']   = '';
        $floorArr[0]['text'] = 'Select Floor';
        $floorArr[0]['html'] = 'Select Floor';
        $floorArr[0]['title'] = 'Select Floor';

        $configflag = self::getConfig();
        if($configflag->count() > 0){

            foreach ($floors as $key => $item) {
            
                $floorArr[$key + 1]['id']    = $item->id;

                $buildingName = $item->b_code ? $item->b_name . ' [' . $item->b_code . ']' :  $item->b_name;

                $floorNo = $item->code ? $item->name . ' [' . $item->code . ']' :  $item->name;

                $floorArr[$key + 1]['text']  = $buildingName. ' - ' . $floorNo;

                $floorArr[$key + 1]['html']  =  $buildingName. ' - ' . $floorNo;
            }
        }
        else{
            foreach ($floors as $key => $item) {
            
                $floorArr[$key + 1]['id']    = $item->id;

                $buildingName =  $item->b_name;

                $floorNo = $item->name;

                $floorArr[$key + 1]['text']  = $buildingName. ' - ' . $floorNo;

                $floorArr[$key + 1]['html']  =  $buildingName. ' - ' . $floorNo;
            }
        }

        return $floorArr;
    }

    public static function getOptionsForRoom($floorId = null)
    {
        $rooms = DB::table('hms_room')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($query) use ($floorId){
                        if(!empty($floorId)){
                            $query->where('floor_id', $floorId);
                        }
                    })
                    ->get();
        
        $roomArr            = [];
        $roomArr[0]['id']   = '';
        $roomArr[0]['text'] = 'Select Room';
        $roomArr[0]['html'] = 'Select Room';
        $roomArr[0]['title'] = 'Select Room';

        $configflag = self::getConfig();
        if($configflag->count() > 0){

            foreach ($rooms as $key => $item) {
            
                $building = Building::find($item->building_id);
                $floor    = Floor::find($item->floor_id);

                $buildingName = $building->name;
                $floorNo = $floor->name;

                $value = $item->room_no . ($item->code ? ' [' . $item->code . ']' : '');
        
                $roomArr[$key + 1]['id']    = $item->id;
                $roomArr[$key + 1]['text']  = $value;

                $roomArr[$key + 1]['html']  =  $value;
            }
        }
        else{
            foreach ($rooms as $key => $item) {
            
                $building = Building::find($item->building_id);
                $floor    = Floor::find($item->floor_id);

                $buildingName = $building->name;
                $floorNo = $floor->name;

                $value = $item->room_no;
        
                $roomArr[$key + 1]['id']    = $item->id;
                $roomArr[$key + 1]['text']  = $value;

                $roomArr[$key + 1]['html']  =  $value;
            }
        }
        return $roomArr;
    }
    public static function getOptionsForRoomOnly($floorId = null)
    {
        $rooms = DB::table('hms_room')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($query) use ($floorId){
                        if(!empty($floorId)){
                            $query->where('floor_id', $floorId);
                        } 
                    })
                    ->get();
        
        $roomArr            = [];
        $roomArr[0]['id']   = '';
        $roomArr[0]['text'] = 'Select Room';
        $roomArr[0]['html'] = 'Select Room';
        $roomArr[0]['title'] = 'Select Room';
        
        $configflag = self::getConfig();
        if($configflag->count() > 0){

            foreach ($rooms as $key => $item) {
            
                $building = Building::find($item->building_id);
                $floor    = Floor::find($item->floor_id);

                $buildingName = $building->name;
                $floorNo = $floor->name;
                $value = $item->code ? $item->room_no . ' [' . $item->code . ']' : $item->room_no;
                
                $roomArr[$key + 1]['id']    = $item->id;
                $roomArr[$key + 1]['text']  = $value;

                $roomArr[$key + 1]['html']  =  $value;
            }
        }
        else{
            foreach ($rooms as $key => $item) {
            
                $building = Building::find($item->building_id);
                $floor    = Floor::find($item->floor_id);

                $buildingName = $building->name;
                $floorNo = $floor->name;
                $value = $item->room_no;
                
                $roomArr[$key + 1]['id']    = $item->id;
                $roomArr[$key + 1]['text']  = $value;

                $roomArr[$key + 1]['html']  =  $value;
            }
        }
        return $roomArr;
    }

    public static function getOptionsForSeat($roomId = null)
    {
        $seats = DB::table('hms_seat')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($query) use ($roomId){
                        if(!empty($roomId)){
                            $query->where('room_id', $roomId);
                        }
                    })
                    ->get();

        $seatArr             = [];
        $seatArr[0]['id']    = '';
        $seatArr[0]['text']  = 'Select Seat';
        $seatArr[0]['html']  = 'Select Seat';
        $seatArr[0]['title'] = 'Select Seat';
        $configflag = self::getConfig();
        foreach ($seats as $key => $item) {
            $value = $item->seat_no;
        
            if ($configflag->count() > 0) {
                // If $configflag has values, concatenate $item->code to $value
                $value .= $item->code ? ' [' . $item->code . ']' : '';
            }
        
            $seatArr[$key + 1]['id'] = $item->id;
            $seatArr[$key + 1]['text'] = $value;
            $seatArr[$key + 1]['html'] = $value;
        }

        return $seatArr;
    }

    public static function getOptionsForVacantSeatStudent($roomId = null, $student_id = null)
    {
        // dd($roomId);
        $student_seats = DB::table('hms_students')->where('id', $student_id)->value('last_seat_id');

        $student_seats_arr = explode(',', $student_seats);

        $seats = DB::table('hms_seat')
                ->where([['is_delete', 0], ['is_active', 1],
                // ['is_empty', 1],
                // , ['is_assigned', 0]
                ])
                ->where(function ($query) use ($roomId){
                    if(!empty($roomId)){
                        $query->where('room_id', $roomId);
                    }
                })
                ->where(function ($query) use ($student_seats_arr){
                    if(count($student_seats_arr) > 0){
                        $query->whereIn('id', $student_seats_arr)
                            ->orWhere('is_empty', 1);
                    }else {
                        $query->where('is_empty', 1);
                    }
                })
                ->get();
        
        $seatArr             = [];
        $seatArr[0]['id']    = '';
        $seatArr[0]['text']  = 'Select Seat';
        $seatArr[0]['html']  = 'Select Seat';
        $seatArr[0]['title'] = 'Select Seat';
        $configflag = self::getConfig();
        foreach ($seats as $key => $item) {
            $value = $item->seat_no;
        
            if ($configflag->count() > 0) {
                $value .= $item->code ? ' [' . $item->code . ']' : '';
            }
        
            $seatArr[$key + 1]['id'] = $item->id;
            $seatArr[$key + 1]['text'] = $value;
            $seatArr[$key + 1]['html'] = $value;
        }
        // dd($seatArr, $roomId);
        return $seatArr;
    }
    public static function getOptionsForVacantSeat($roomId = null)
    {
        // dd($roomId);
        $seats = DB::table('hms_seat')
                    ->where([['is_delete', 0], ['is_active', 1],
                    ['is_empty', 1],
                    // , ['is_assigned', 0]
                    ])
                    ->where(function ($query) use ($roomId){
                        if(!empty($roomId)){
                            $query->where('room_id', $roomId);
                        }
                    })
                    ->get();
        // dd($seats);
        $seatArr             = [];
        $seatArr[0]['id']    = '';
        $seatArr[0]['text']  = 'Select Seat';
        $seatArr[0]['html']  = 'Select Seat';
        $seatArr[0]['title'] = 'Select Seat';
        $configflag = self::getConfig();
        foreach ($seats as $key => $item) {
            $value = $item->seat_no;
        
            if ($configflag->count() > 0) {
                $value .= $item->code ? ' [' . $item->code . ']' : '';
            }
        
            $seatArr[$key + 1]['id'] = $item->id;
            $seatArr[$key + 1]['text'] = $value;
            $seatArr[$key + 1]['html'] = $value;
        }
        // dd($seatArr, $roomId);
        return $seatArr;
    }
    public static function getOptionsForAssignedSeat($roomId = null)
    {
        $seats = DB::table('hms_seat')
                    ->where([['is_delete', 0], ['is_active', 1], ['is_assigned', 1]])
                    ->where(function ($query) use ($roomId){
                        if(!empty($roomId)){
                            $query->where('room_id', $roomId);
                        }
                    })
                    ->get();

        $seatArr             = [];
        $seatArr[0]['id']    = '';
        $seatArr[0]['text']  = 'Select Seat';
        $seatArr[0]['html']  = 'Select Seat';
        $seatArr[0]['title'] = 'Select Seat';
        $configflag = self::getConfig();
        foreach ($seats as $key => $item) {
            $value = $item->seat_no;
        
            if ($configflag->count() > 0) {
                $value .= $item->code ? ' [' . $item->code . ']' : '';
            }
        
            $seatArr[$key + 1]['id'] = $item->id;
            $seatArr[$key + 1]['text'] = $value;
            $seatArr[$key + 1]['html'] = $value;
        }

        return $seatArr;
    }
    public static function getOptionsForAssignedNotEmptySeat($roomId = null)
    {
        $seats = DB::table('hms_seat')
                    ->where([['is_delete', 0], ['is_active', 1], ['is_assigned', 1],['is_empty', 0 ]])
                    ->where(function ($query) use ($roomId){
                        if(!empty($roomId)){
                            $query->where('room_id', $roomId);
                        }
                    })
                    ->get();

        $seatArr             = [];
        $seatArr[0]['id']    = '';
        $seatArr[0]['text']  = 'Select Seat';
        $seatArr[0]['html']  = 'Select Seat';
        $seatArr[0]['title'] = 'Select Seat';
        $configflag = self::getConfig();
        foreach ($seats as $key => $item) {
            $value = $item->seat_no;
        
            if ($configflag->count() > 0) {
                $value .= $item->code ? ' [' . $item->code . ']' : '';
            }
        
            $seatArr[$key + 1]['id'] = $item->id;
            $seatArr[$key + 1]['text'] = $value;
            $seatArr[$key + 1]['html'] = $value;
        }

        return $seatArr;
    }

    public static function getOptionsForStudentPromotion($admission = null)
    {
        $students = DB::table('gnl_dynamic_form_value')
                    ->where([['is_delete', 0], ['is_active', 1], ['form_id', 'HMS.2']])
                    ->where(function ($query) use ($admission){
                        if(!empty($admission)){
                            $query->whereIn('uid', ['1,7']);
                        }
                    })
                    ->orderBy('order_by')
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Year';
        $studentArr[0]['html']  = 'Select Year';
        $studentArr[0]['title'] = 'Select Year';
        $configflag = self::getConfig(); 
        foreach ($students as $key => $item) {
            $value = $item->name;
        
            if ($configflag->count() > 0) {
                $value .= $item->code ? ' [' . $item->code . ']' : '';
            }
        
            $studentArr[$key + 1]['id'] = $item->value_field;
            $studentArr[$key + 1]['text'] = $value;
            $studentArr[$key + 1]['html'] = $value;
        }
        return $studentArr;
    }

    public static function getOptionsForStudent($departmentId = null)
    {
        $students = DB::table('hms_students')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($query) use ($departmentId){
                        if(!empty($departmentId)){
                            $query->where('department', $departmentId);
                        }
                    })
                    // ->where(function ($query) use ($status){
                    //     if(!empty($status)){
                    //         $query->where('status', $status);
                    //     }
                    // })
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = encrypt($item->id);
            $studentArr[$key + 1]['text']  =  $item->full_name_en . ' [' . $item->hall_id . ']';

            $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }
    public static function getOptionsForStudentBysession($session = null)
    {
        $students = DB::table('hms_students')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($query) use ($session){
                        if(!empty($session)){
                            $query->where('admission_session', $session);
                        }
                    })
                    
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = encrypt($item->id);
            $studentArr[$key + 1]['text']  =  $item->full_name_en . ' [' . $item->hall_id . ']';

            $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }
    public static function getOptionsForRemarkby()
    {
        $students = DB::table('gnl_sys_users')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select User';
        $studentArr[0]['html']  = 'Select User';
        $studentArr[0]['title'] = 'Select User';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    =  $item->id;
            $studentArr[$key + 1]['text']  =  $item->full_name;

            $studentArr[$key + 1]['html']  =  $item->full_name;
        }

        return $studentArr;
    }
    public static function getOptionsForResidentBySessionButNotReleased($sessioId = null)
    {
        $students = DB::table('hms_students')
                    ->where([['is_delete', 0], ['is_active', 1], ['status', 1], ['resident_seat_empty',0]])
                    ->where(function ($query) use ($sessioId){
                        if(!empty($sessioId)){
                            $query->where('admission_session', $sessioId);
                        }
                    })
                   
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = encrypt($item->id);
            $studentArr[$key + 1]['text']  =  $item->full_name_en . ' [' . $item->hall_id . ']';

            $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }
    public static function getOptionsForResidentBySession($sessioId = null)
    {
        $students = DB::table('hms_students')
                    ->where([['is_delete', 0], ['is_active', 1], ['status', 1]])
                    ->where(function ($query) use ($sessioId){
                        if(!empty($sessioId)){
                            $query->where('admission_session', $sessioId);
                        }
                    })
                   
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = encrypt($item->id);
            $studentArr[$key + 1]['text']  =  $item->full_name_en . ' [' . $item->hall_id . ']';

            $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }
    public static function getOptionsForNonResidentBySession($sessioId = null)
    {
        $students = DB::table('hms_students')
                    ->where([['is_delete', 0], ['is_active', 1], ['status', 2]])
                    ->where(function ($query) use ($sessioId){
                        if(!empty($sessioId)){
                            $query->where('admission_session', $sessioId);
                        }
                    })
                   
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = encrypt($item->id);
            $studentArr[$key + 1]['text']  =  $item->full_name_en . ' [' . $item->hall_id . ']';

            $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }

    public static function getOptionsForResidence($sessioId = null)
    {
        $students = DB::table('hms_students')
                    ->where([['is_delete', 0], ['is_active', 1], ['status', 1]])
                    ->where(function ($query) use ($sessioId){
                        if(!empty($sessioId)){
                            $query->where('admission_session', $sessioId);
                        }
                    })
                    // ->where(function ($query) use ($status){
                    //     if(!empty($status)){
                    //         $query->where('status', $status);
                    //     }
                    // })
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = $item->id;
            $studentArr[$key + 1]['text']  = $item->full_name_en . ' [ '. $item->hall_id . ' ]';

            $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [ '. $item->hall_id . ' ]';
        }

        return $studentArr;
    }

    public static function getOptionsForCurrentStudentbyadmissionsession($admission_session = null){
        $students = DB::table('hms_students')
        ->where([['is_delete', 0], ['is_active', 1],['status', '!=', 3]])
        ->where(function ($query) use ($admission_session){
            if(!empty($admission_session)){
                $query->where('admission_session', $admission_session);
            }
        })
        ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

        $studentArr[$key + 1]['id']    = encrypt($item->id);
        $studentArr[$key + 1]['text']  =  $item->full_name_en . ' [' . $item->hall_id . ']';

        $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }
    public static function getOptionsForCurrentStudent($sessioId = null)
    {
        
        $students = DB::table('hms_students')
        ->where([['is_delete', 0], ['is_active', 1],['status', '!=', 3]])
        ->where(function ($query) use ($sessioId){
            if(!empty($sessioId)){
                $query->where('admission_session', $sessioId);
            }
        })
        ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

        $studentArr[$key + 1]['id']    = encrypt($item->id);
        $studentArr[$key + 1]['text']  =  $item->full_name_en . ' [' . $item->hall_id . ']';

        $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }

    public static function getOptionsForNonResidence($sessioId = null)
    {
        $students = DB::table('hms_students')
                    ->where([['is_delete', 0], ['is_active', 1], ['status', 2]])
                    ->where(function ($query) use ($sessioId){
                        if(!empty($sessioId)){
                            $query->where('admission_session', $sessioId);
                        }
                    })
                    // ->where(function ($query) use ($status){
                    //     if(!empty($status)){
                    //         $query->where('status', $status);
                    //     }
                    // })
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = $item->id;
            $studentArr[$key + 1]['text']  = $item->full_name_en;

            $studentArr[$key + 1]['html']  =  $item->full_name_en ;
        }

        return $studentArr;
    }

    public static function getOptionsForDegreeCompleted($sessioId = null)
    {
        $students = DB::table('hms_students')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where(function ($query) use ($sessioId){
                if(!empty($sessioId)){
                    $query->where('admission_session', $sessioId);
                }
            })
            ->select('id', 'hall_id', 'full_name_en')
            ->get();


        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Student / Hall ID';
        $studentArr[0]['html']  = 'Select Student / Hall ID';
        $studentArr[0]['title'] = 'Select Student / Hall ID';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    =  encrypt($item->id);;
            $studentArr[$key + 1]['text']  = $item->full_name_en . ' [' . $item->hall_id . ']';

            $studentArr[$key + 1]['html']  =  $item->full_name_en . ' [' . $item->hall_id . ']';
        }

        return $studentArr;
    }

    public static function getOptionsForInactiveTutor($sessioId = null)
    {
        $students = DB::table('hms_house_tutor')
                    ->where([['is_delete', 0], ['is_active', 0]])
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Tutor';
        $studentArr[0]['html']  = 'Select Tutor';
        $studentArr[0]['title'] = 'Select Tutor';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = $item->id;
            $studentArr[$key + 1]['text']  = $item->name;

            $studentArr[$key + 1]['html']  =  $item->name;
        }

        return $studentArr;
    }
    public static function getOptionsForDesignation($sessioId = null)
    {
        $students = DB::table('hr_designations')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Designation';
        $studentArr[0]['html']  = 'Select Designation';
        $studentArr[0]['title'] = 'Select Designation';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = $item->id;
            $studentArr[$key + 1]['text']  = $item->name;
            $studentArr[$key + 1]['html']  =  $item->name;
        }

        return $studentArr;
    }
    public static function getOptionsForAllTutor($sessioId = null)
    {
        $students = DB::table('hms_house_tutor')
                    ->where([['is_delete', 0]])
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Tutor';
        $studentArr[0]['html']  = 'Select Tutor';
        $studentArr[0]['title'] = 'Select Tutor';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = $item->id;
            $studentArr[$key + 1]['text']  = $item->name;

            $studentArr[$key + 1]['html']  =  $item->name;
        }

        return $studentArr;
    }


   
    public static function getOptionsForActiveTutor($departmentId = null)
    {
        $students = DB::table('hms_house_tutor')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    // ->where(function ($query) use ($departmentId){
                    //     if(!empty($departmentId)){
                    //         $query->where('department_id', $departmentId);
                    //     }
                    // })
                    // ->where(function ($query) use ($status){
                    //     if(!empty($status)){
                    //         $query->where('status', $status);
                    //     }
                    // })
                    ->get();

        $studentArr             = [];
        $studentArr[0]['id']    = '';
        $studentArr[0]['text']  = 'Select Tutor';
        $studentArr[0]['html']  = 'Select Tutor';
        $studentArr[0]['title'] = 'Select Tutor';

        foreach ($students as $key => $item) {

            $studentArr[$key + 1]['id']    = $item->id;
            $studentArr[$key + 1]['text']  = $item->name;

            $studentArr[$key + 1]['html']  =  $item->name;
        }

        return $studentArr;
    }

    public static function getOptionsForFeeCategory($noEncrypt = null)
    {
        $categories = DB::table('hms_hall_fee_category')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->orderBy('order_by')
                    ->get();

        $categoryArr             = [];
        $categoryArr[0]['id']    = '';
        $categoryArr[0]['text']  = 'Select Category';
        $categoryArr[0]['html']  = 'Select Category';
        $categoryArr[0]['title'] = 'Select Category';

        foreach ($categories as $key => $item) {

            $categoryArr[$key + 1]['id']    = $noEncrypt ? ($item->id) : encrypt($item->id);
            $categoryArr[$key + 1]['text']  = $item->name_bn;
            $categoryArr[$key + 1]['amount']  = $item->amount;

            $categoryArr[$key + 1]['html']  =  $item->name_bn;
        }

        return $categoryArr;
    }

    public static function getOptionsForDegrees(){

        $degrees = DB::table('gnl_dynamic_form_value')
                    ->where([['type_id', 10], ['form_id', 'HMS.4']])
                    ->orderBy('order_by')
                    ->get();
        $degreeArr             = [];
        $degreeArr[0]['id']    = '';
        $degreeArr[0]['text']  = 'Select Title';
        $degreeArr[0]['html']  = 'Select Title';
        $degreeArr[0]['title'] = 'Select Title';

        foreach ($degrees as $key => $item) {

            $degreeArr[$key + 1]['id']    = $item->value_field;
            $degreeArr[$key + 1]['text']  = $item->name;

            $degreeArr[$key + 1]['html']  =  $item->name;
        }

        return $degreeArr;
    }

    public static function numberToWordBangla($Number = null)
    {
        return self::numberToWordBanglaT($Number) . " টাকা মাত্র";
    }

    public static function numberToWordBanglaT($number) {
        $banglaNumbers = array(
            1 => 'এক', 2 => 'দুই', 3 => 'তিন', 4 => 'চার', 5 => 'পাঁচ',
            6 => 'ছয়', 7 => 'সাত', 8 => 'আট', 9 => 'নয়', 10 => 'দশ',
            11 => 'এগারো', 12 => 'বারো', 13 => 'তেরো', 14 => 'চৌদ্দ',
            15 => 'পনের', 16 => 'ষোল', 17 => 'সতের', 18 => 'আঠারো',
            19 => 'উনিশ', 20 => 'বিশ', 21 => 'একুশ', 22 => 'বাইশ', 23 => 'তেইশ',
            24 => 'চব্বিশ', 25 => 'পঁচিশ', 26 => 'ছাব্বিশ', 27 => 'সাতাশ', 28 => 'আটাশ',
            29 => 'ঊনত্রিশ', 30 => 'ত্রিশ', 31 => 'একত্রিশ', 32 => 'বত্রিশ', 33 => 'তেত্রিশ',
            34 => 'চৌত্রিশ', 35 => 'পঁয়ত্রিশ', 36 => 'ছত্রিশ', 37 => 'সাঁইত্রিশ', 38 => 'আটত্রিশ',
            39 => 'ঊনচল্লিশ', 40 => 'চল্লিশ', 41 => 'একচল্লিশ', 42 => 'বিয়াল্লিশ', 43 => 'তেতাল্লিশ',
            44 => 'চুয়াল্লিশ', 45 => 'পঁয়তাল্লিশ', 46 => 'ছেচল্লিশ', 47 => 'সাতচল্লিশ', 48 => 'আটচল্লিশ',
            49 => 'ঊনপঞ্চাশ', 50 => 'পঞ্চাশ', 51 => 'একান্ন', 52 => 'বায়ান্ন', 53 => 'তিপ্পান্ন',
            54 => 'চুয়ান্ন', 55 => 'পঞ্চান্ন', 56 => 'ছাপ্পান্ন', 57 => 'সাতান্ন', 58 => 'আটান্ন',
            59 => 'ঊনষাট', 60 => 'ষাট', 61 => 'একষট্টি', 62 => 'বাষট্টি', 63 => 'তেষট্টি',
            64 => 'চৌষট্টি', 65 => 'পঁয়ষট্টি', 66 => 'ছেষট্টি', 67 => 'সাতষট্টি', 68 => 'আটষট্টি',
            69 => 'ঊনসত্তর', 70 => 'সত্তর', 71 => 'একাত্তর', 72 => 'বাহাত্তর', 73 => 'তেহাত্তর',
            74 => 'চুয়াত্তর', 75 => 'পঁচাত্তর', 76 => 'ছিয়াত্তর', 77 => 'সাতাত্তর', 78 => 'আটাত্তর',
            79 => 'ঊনআশি', 80 => 'আশি', 81 => 'একাশি', 82 => 'বিরাশি', 83 => 'তিরাশি',
            84 => 'চুরাশি', 85 => 'পঁচাশি', 86 => 'ছিয়াশি', 87 => 'সাতাশি', 88 => 'আটাশি',
            89 => 'ঊননব্বই', 90 => 'নব্বই', 91 => 'একানব্বই', 92 => 'বিরানব্বই', 93 => 'তিরানব্বই',
            94 => 'চুরানব্বই', 95 => 'পঁচানব্বই', 96 => 'ছিয়ানব্বই', 97 => 'সাতানব্বই', 98 => 'আটানব্বই',
            99 => 'নিরানব্বই'
        );

        $amountInBanglaWords = '';

        if ($number < 10) {
            $amountInBanglaWords = $banglaNumbers[$number];
        } elseif ($number < 100) {
            if (isset($banglaNumbers[$number])) {
                $amountInBanglaWords = $banglaNumbers[$number];
            } else {
                $tensDigit = substr($number, 0, 1) * 10;
                $unitDigit = substr($number, 1, 1);
                
                $amountInBanglaWords = $banglaNumbers[$tensDigit] . ' ' . $banglaNumbers[$unitDigit];
            }
        } elseif ($number < 1000) {
            $amountInBanglaWords = $banglaNumbers[substr($number, 0, 1)] . ' শত ';
            if (substr($number, 1) != 00) {
                $amountInBanglaWords .= ' এবং ' . self::numberToWordBanglaT(substr($number, 1));
            }
        } elseif ($number < 10000) {
            $amountInBanglaWords = self::numberToWordBanglaT(substr($number, 0, 1)) . ' হাজার';
            if (substr($number, 1) != 000) {
                $amountInBanglaWords .= ' ' . self::numberToWordBanglaT(substr($number, 1));
            }
        } elseif ($number < 100000) {
            $amountInBanglaWords = $banglaNumbers[substr($number, 0, 2)] . ' হাজার';
            if (substr($number, 2) != 000) {
                $amountInBanglaWords .= ' এবং ' . self::numberToWordBanglaT(substr($number, 2));
            }
        }

        return $amountInBanglaWords;
    }

    public static function getStudentStatus()
    {
        $studentStatus = DB::table('gnl_dynamic_form_value')
            ->where([['is_delete', 0], ['is_active', 1], ['type_id', 10] , ['form_id', 'HMS.1']])
            ->orderBy('order_by', 'ASC')
            ->pluck('name','value_field')
            ->toArray();

        return $studentStatus;
    }

    /**
     * @param $hmllType = Html Type [slider/checkbox, textbox, icon etc] 
     *        $clsName = Value for class & Name Attribute
     *        $rowId = Value for Id attribute
     */
    public static function createHtml($type, $clsName, $data, $id, $title=''){
        $html = '';

        if($type == 'slider'){
            $html = '<label class="action-switch">';
            $html .= '<input type="checkbox" class="'. $clsName .'" name="'. $clsName .'" 
                data="'. $data.'" id="'. $id.'" title"'. (isset($title) ? $title : '') .'">';
            $html .= '<span class="slider action-slider round"></span>';
            $html .= '</label>';
        }
        if($type == 'check'){
            $html = '<a href="javascript:void(0)" type="button" class="text-success" 
                    data-toggle="tooltip" data-placement="top" title="'. (isset($title) ? $title : '') .'">';
            $html .= '<i class="icon fa-check-circle"></i>';
            $html .= '</a>';
        }
        if($type == 'cross'){
            $html = '<a href="javascript:void(0)" type="button" class="text-danger" 
                    data-toggle="tooltip" data-placement="top" title="'. (isset($title) ? $title : '') .'">';
            $html .= '<i class="icon fa-times-circle-o"></i>';
            $html .= '</a>';
        }
        
        return $html;
    }

    public static function getOptionsForDistrict($parameter = [])
    {
        $district = DB::table('gnl_districts')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->get();

        $buildArr            = [];
        $buildArr[0]['id']   = '';
        $buildArr[0]['text'] = 'Select District';
        $buildArr[0]['html'] = 'Select District';
        $buildArr[0]['title'] = 'Select District';

        foreach ($district as $key => $item) {

            $buildArr[$key + 1]['id']    = $item->id;
            $buildArr[$key + 1]['text']  = $item->district_name;

            $buildArr[$key + 1]['html']  =  $item->district_name ;
        }

        return $buildArr;
    }
}
