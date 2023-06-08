@extends('backend.layout.main') 
@section('content')

@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if(session()->has('message'))
  <div id="msg" class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section>
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>{{trans('file.Add Service')}}</h4>
                </div>
                <div class="card-body">
                    <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    {!! Form::open(['route' => ['services.update',$lims_service_data->id], 'method' => 'put', 'files' => true]) !!}
                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Service Title')}} *</strong> </label>
                                    <input type="text" name="title" class="form-control" id="name" aria-describedby="name" value="{{$lims_service_data->title}}" required>
                                    @if($errors->has('title'))
                                   <span>
                                       <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Service Code')}} *</strong> </label>
                                    <div class="input-group">
                                        <input type="text" name="code" class="form-control" id="code" aria-describedby="code" value="{{$lims_service_data->code}}" required>
                                        <div class="input-group-append">
                                            <button id="genbutton" type="button" class="btn btn-sm btn-default" title="{{trans('file.Generate')}}"><i class="fa fa-refresh"></i></button>
                                        </div>
                                    </div>
                                    @if($errors->has('code'))
                                    <span>
                                        <strong>{{ $errors->first('code') }}</strong>
                                     </span>
                                     @endif                               
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Service Price')}} *</strong> </label>
                                    <input type="number" name="price" class="form-control" id="price" aria-describedby="price" value="{{$lims_service_data->price}}" required>
                                    @if($errors->has('price'))
                                    <span>
                                        <strong>{{ $errors->first('price') }}</strong>
                                     </span>
                                     @endif                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="{{trans('file.submit')}}" id="submit-btn" class="btn btn-primary">
                        </div>
                </div>   
                {!! Form::close() !!}
            </div>
        </div>
    </div>  
</section>
@endsection
@push('scripts')
<script type="text/javascript">

    $("ul#service").siblings('a').attr('aria-expanded','true');
    $("ul#service").addClass("show");
    $("ul#service #service-add").addClass("active");


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#genbutton').on("click", function(){
      $.get('gencode', function(data){
        $("input[name='code']").val(data);
      });
    });

    // $('#submit-btn').on("click", function (e) {
        
    //     $.ajax({
    //         type:'POST',
    //         url:'{{route('services.store')}}',
    //         data: $("#service-form").serialize(),

    //         success:function(data){
    //             $(location).attr('href', '/services');
    //             alert(data.message);
    //             //$('#msg').("Service created successfully");
    //         }
    //     });
    
    
    // });


</script>    
@endpush