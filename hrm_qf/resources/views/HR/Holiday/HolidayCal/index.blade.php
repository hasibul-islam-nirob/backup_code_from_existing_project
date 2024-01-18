@extends('Layouts.erp_master')
@section('content')

<link rel="stylesheet" type="text/css" href="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.css"> 
<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">  -->
<link rel="stylesheet" type="text/css" href="https://unpkg.com/bootstrap-datepicker@1.8.0/dist/css/bootstrap-datepicker.standalone.min.css"> 


<style>
    body {
      font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    }
    .calendar-header{
      display: none;
    }
    .calendar{
      overflow: hidden;
      padding-bottom: 5%;
    }
    .day-content{
      color: #000000;
    }
    element.style {
      /* color: #ffffff; */
      box-shadow: green 30px -4px 0px 0px inset !important;
    }
</style>
<script src="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script src="https://unpkg.com/popper.js@1.14.7/dist/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>


<div class="row mb-4">
  <div class="col-lg-4 ml-auto mr-auto">
    <label class="input-title">Select Year</label>
    <div class="input-group">
        @php
            $headOfficeOpeningDate = DB::table('gnl_branchs')->where('id', 1)->value('branch_opening_date');
            $startYear = (int)date('Y', strtotime($headOfficeOpeningDate));
            $endYear = (int)date('Y') + 1;
            $elements = [];
            while ($endYear >= $startYear) {
                array_push($elements, $endYear);
                $endYear--;
            }
        @endphp
        <select class="form-control clsSelect2" name="year" id="year">
            <option value="">Select Year</option>
            @foreach ($elements as $element)
            <option value="{{ $element }}">{{ $element }}</option>
            @endforeach

        </select>
    </div>
  </div>
</div>

<div id="calendar"></div>

{{-- <div class="modal fade" id="event-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Event</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="event-index">
        <form class="form-horizontal">
          <div class="form-group row">
            <label for="event-name" class="col-sm-4 control-label">Name</label>
            <div class="col-sm-8">
              <input id="event-name" name="event-name" type="text" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <label for="event-location" class="col-sm-4 control-label">Location</label>
            <div class="col-sm-8">
              <input id="event-location" name="event-location" type="text" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <label for="min-date" class="col-sm-4 control-label">Dates</label>
            <div class="col-sm-8">
              <div class="input-group input-daterange" data-provide="datepicker">
                <input id="min-date" name="event-start-date" type="text" class="form-control">
                <div class="input-group-prepend input-group-append">
                    <div class="input-group-text">to</div>
                </div>
                <input name="event-end-date" type="text" class="form-control">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="save-event">
          Save
        </button>
      </div>
    </div>
  </div>
</div> --}}
<div id="context-menu">
</div>

<script>
  let calendar = null;
  let holidayData = [];
  let selectedyear = new Date().getFullYear();
  let flag = false;

  function editEvent(event) {
      $('#event-modal input[name="event-index"]').val(event ? event.id : '');
      $('#event-modal input[name="event-name"]').val(event ? event.name : '');
      $('#event-modal input[name="event-location"]').val(event ? event.location : '');
      $('#event-modal input[name="event-start-date"]').datepicker('update', event ? event.startDate : '');
      $('#event-modal input[name="event-end-date"]').datepicker('update', event ? event.endDate : '');
      $('#event-modal').modal();
  }

  function deleteEvent(event) {
      var dataSource = calendar.getDataSource();
      
      calendar.setDataSource(dataSource.filter(item => item.id == event.id));
  }

  $('#year').change(function(e){
      selectedyear= $('#year').val();
      getHolidayList(selectedyear);
  });

  // function saveEvent() {
  //     var event = {
  //         id: $('#event-modal input[name="event-index"]').val(),
  //         name: $('#event-modal input[name="event-name"]').val(),
  //         location: $('#event-modal input[name="event-location"]').val(),
  //         startDate: $('#event-modal input[name="event-start-date"]').datepicker('getDate'),
  //         endDate: $('#event-modal input[name="event-end-date"]').datepicker('getDate')
  //     }
      
  //     var dataSource = calendar.getDataSource();
    
  //     if (event.id) {
  //         for (var i in dataSource) {
  //             if (dataSource[i].id == event.id) {
  //                 dataSource[i].name = event.name;
  //                 dataSource[i].location = event.location;
  //                 dataSource[i].startDate = event.startDate;
  //                 dataSource[i].endDate = event.endDate;
  //             }
  //         }
  //     }
  //     else
  //     {
  //         var newId = 0;
  //         for(var i in dataSource) {
  //             if(dataSource[i].id > newId) {
  //                 newId = dataSource[i].id;
  //             }
  //         }
          
  //         newId++;
  //         event.id = newId;
      
  //         dataSource.push(event);
  //     }
      
  //     calendar.setDataSource(dataSource);
  //     $('#event-modal').modal('hide');
  // }
  
  function initCalender(holidayData){
    // var currentYear = new Date().getFullYear();
      
    calendar = new Calendar('#calendar', { 
          enableContextMenu: true,
          enableRangeSelection: true,
          // contextMenuItems:[
          //     {
          //         text: 'Update',
          //         click: editEvent
          //     },
          //     {
          //         text: 'Delete',
          //         click: deleteEvent
          //     }
          // ],
          // selectRange: function(e) {
          //     editEvent({ startDate: e.startDate, endDate: e.endDate });
          // },
        
          mouseOnDay: function(e) {
              if(e.events.length > 0) {
                  var content = '';
                
                  for(var i in e.events) {
                      content += '<div class="event-tooltip-content " >'
                                      + '<div class="event-name" style="color:' + e.events[i].color + '">' + e.events[i].name + '</div>'
                                      + '<div class="event-location">' + e.events[i].location + '</div>'
                                  + '</div>';
                  }
              
                  $(e.element).popover({ 
                      trigger: 'manual',
                      container: 'body',
                      html:true,
                      content: content
                  });
                  
                  $(e.element).popover('show');
              }
          },
          mouseOutDay: function(e) {
              if(e.events.length > 0) {
                  $(e.element).popover('hide');
              }
          },
          dayContextMenu: function(e) {

              $(e.element).popover('hide');
          },
          yearChanged: function(e) {
            console.log("New year selected: " + e.currentYear);
          },
          dataSource : holidayData
          // dataSource: <?php $holidayDataSource ?>
        
      });

      calendar.setYear(selectedyear);
  }

  function getHolidayList(selectedyear){
    $.ajax({
        method: "GET",
        url: "{{url('/hr/holidaycalendar')}}?year="+selectedyear,
        dataType: "json",                                                                  
        success: function(data) {
          if(data){
            holidayData = data.holidayDataSource.map((holiday)=>{
                return{
                  id: holiday.id,
                  location: holiday.location,
                  name: holiday.name,
                  startDate : new Date(holiday.startDate),
                  endDate : new Date(holiday.endDate),
                  color: holiday.color,
                  bgColor:holiday.bg_color,
                }
            });
            initCalender(holidayData);
          }
            
        }
    });
  }

  $(function() {

    // getHolidayList(selectedyear);
    $('#year').val(selectedyear);
    $('#year').change();

    $('#save-event').click(function() {
        saveEvent();
    });
  });


  /*
  
  const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  let current_dateString = new Date();
  let current_dateObj = new Date(current_dateString);
  let current_day = current_dateObj.getDate();
  let current_month = months[current_dateObj.getMonth()];
  let current_year = current_dateObj.getFullYear();
  let currentDate = `${current_dateObj.toLocaleString('en', { weekday: 'short' })} ${current_month} ${current_day} ${current_year}`;

  let event_dateString = e.date;
  let event_dateObj = new Date(event_dateString);
  let event_day = event_dateObj.getDate();
  let event_month = months[event_dateObj.getMonth()];
  let event_year = event_dateObj.getFullYear();
  let eventDate = `${event_dateObj.toLocaleString('en',{ weekday: 'short' })} ${event_month} ${event_day} ${event_year}`;

  if (eventDate == currentDate) {
    $(this).current_dateString.css('background-color', 'red');
  }
  
  */
</script>

 
@endsection
