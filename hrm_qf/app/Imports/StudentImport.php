<?php

namespace App\Imports;

use App\Model\HMS\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DateTime;

class StudentImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //$dateTime = ;
        // ss($row['datetime']);

        // print_r("<pre>");
        // print_r($row['datetime']);

        // return new EmployeeAttendance([
        //     'emp_code' => $row['no'],
        //     'name' => $row['name'],
        //     //'schedule' => $row['schedule'],
        //     //'date' => (new DateTime($row['date']))->format('Y-m-d'),
        //     'time_and_date' => (new DateTime($row['datetime']))->format('Y-m-d H:i:s'),
        //     /* 'timetable' => $row['timetable'],
        //     'on_duty' => $row['on_duty'],
        //     'off_duty' => $row['off_duty'], */
        //     //'clock_in' => $row['clock_in'],
        //     //'clock_out' => $row['clock_out'],
        //     /* 'late' => $row['late'],
        //     'early' => $row['early'],
        //     'absent' => $row['absent'],
        //     'ot_time' => $row['ot_time'],
        //     'work_time' => $row['work_time'],
        //     'department' => $row['department'], */
        // ]);
    }
}
