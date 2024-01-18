@extends('Layouts.erp_master')

@section('content')

<div class="table-responsive">
    <table class="table w-full table-hover table-bordered table-striped">
        <thead>
            <tr>
                <th style="width: 5%;"class="text-center">SL</th>
                <th>Title</th>
                <th>For</th>
                <th>Module</th>

                <th>Designation</th>
                <th>Employee</th>
                <th>Order</th>
                <th style="width: 10%;" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=0; ?>
            
                @foreach ($dataSignature as $item)
                <tr>
                    <td class="text-center">{{++$i}}</td>
                    <td>{{$item->title}} </td>
                    <td>
                        @if ($item->applicableFor === 1)
                            Head Office
                        @elseif ($item->applicableFor === 2)
                            Branch Office
                        @elseif ($item->applicableFor === -1)
                            All
                        @else
                            
                        @endif
                    </td>
                    
                    <td>{{$item->module_name}}</td>
                    <td>
                        @if($item->signatorDesignationId != -1 && $item->signatorDesignationId != -2)
                            {{$item->designation['name']}}
                        @else
                            @if($item->signatorDesignationId == -1)
                                Logged in user
                            @else
                                N/A
                            @endif <!-- Missing </td> tag -->
                        @endif
                    </td>
                    <td>{{(!empty($item->employee['emp_name']))? $item->employee['emp_name'] : ''}}</td>
                    
                    <td class="text-center">{{$item->positionOrder}} </td>
                    
                    <td class="text-center">
                        <a href="{{ url('gnl/signature_set/edit/'.$item->id) }}" title="Edit">
                            <i class="icon wb-edit mr-2 blue-grey-600"></i>
                        </a>
                        @if($item->status == 1)
                        <a href="{{ url('gnl/signature_set/publish/'.$item->id) }}" title="Unpublish"
                            class="btnUnpublish">
                            <i class="icon fa-check-square-o mr-2 blue-grey-600"></i>
                        </a>
                        @else
                        <a href="{{ url('gnl/signature_set/publish/'.$item->id) }}" title="Publish"
                            class="btnPublish">
                            <i class="icon fa-square-o mr-2 blue-grey-600"></i>
                        </a>
                        @endif
                        <a href="{{ url('gnl/signature_set/delete/'.$item->id) }}"title="Delete"
                            class="btnDelete">
                            <i class="icon wb-trash mr-2 blue-grey-600"></i>
                        </a>
                    </td>
                </tr>
                    
                @endforeach
               
        </tbody>
    </table>
</div>

@endsection
