@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\RoleService as Role;
?>
<!-- Page -->

<!-- <link rel="stylesheet" href="calendar.css"> -->
   <link rel="stylesheet" type="text/css" href="https://www.jqueryscript.net/demo/Year-Calendar-Bootstrap-4/jquery.bootstrap.year.calendar.css"> 
 
    <script src="https://www.jqueryscript.net/demo/Year-Calendar-Bootstrap-4/jquery.bootstrap.year.calendar.js"></script> 
   
<div class="container main-section p-3" >

    <div class="calendar-section border border-danger"></div>
</div>
<!-- End Page -->
<style>
*{
    font-family: 'Roboto Condensed', sans-serif;
}
/* body{
    background-color: #000 !important;
} */
/* .header h1{
    font-size:22px;
} */

.calendar-section{
    padding:0px 20px;
    background-color: #fff;
}
.jqyc-year{
    font-size: 25px;
}
.jqyc .border-top{
    border:none !important;
}
.jqyc .border-top{
    border-bottom: 1px solid #d2d2d2 !important;
}

</style>

<script>
  $('.calendar-section').calendar();

//   function refreshLog() {
//     $("#logs").scrollTop($("#logs")[0].scrollHeight);
// }
// console.log(refreshLog);
// $(function() {
//     var currentYear = new Date().getFullYear();

//     $('.calendar-section').calendar({
//         enableRangeSelection: true,
//         renderEnd: function(e) {
//      if($('#render-end').prop('checked'))
//    {
//     $('#logs').append('<div class="render-end" style="color:#1C7C26">Render end (' + e.currentYear + ')Click (' + e.date.toLocaleDateString() + ')Context menu (' + e.date.toLocaleDateString() + ')Select range (' + e.startDate.toLocaleDateString() + ' -> ' + e.endDate.toLocaleDateString() +')Mouse on (' + e.date.toLocaleDateString() + ')Mouse out (' + e.date.toLocaleDateString() + ')</div>
 
</script>


 
@endsection
