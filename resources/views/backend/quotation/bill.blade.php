@extends('backend.layout.main') 
@section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section id="quotation-Email">
    <div id="quotation-details" class="w-85 p-4">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="container mt-3 pb-2 border-bottom">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                        </div>
                        <div class="col-md-12 text-center">
                            <i style="font-size: 15px;">{{trans('file.Quotation Details')}}</i>
                        </div>
                    </div>
                </div>
                    <div id="quotation-content" class="modal-body">
                        @foreach($lims_quotation_data as $quotation)
                            <strong>{{trans("file.Date")}}: </strong>{{$quotation->created_at}}<br>
                            <strong>{{trans("file.reference")}}: </strong>{{$quotation->reference_no}}<br>
                            @if ($quotation->quotation_status == 1)
                                <strong>{{trans("file.Status")}}: </strong>Pending<br>
                            @else
                                <strong>{{trans("file.Status")}}: </strong>Sent<br>
                            @endif
                        @endforeach

                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>{{trans("file.From")}} :</strong>
                                <br><strong>{{trans("file.name")}} :</strong> {{$biller[0]->name}}
                                <br><strong>{{trans("file.Company Name")}} :</strong> {{$biller[0]->company_name}}
                                <br><strong>{{trans("file.Email")}} :</strong> {{$biller[0]->email}}
                                <br><strong>{{trans("file.Phone Number")}} :</strong> {{$biller[0]->phone_number}}
                                <br><strong>{{trans("file.Address")}} :</strong> {{$biller[0]->address}}
                                <br><strong>{{trans("file.City")}} :</strong> {{$biller[0]->city}}
                            </div>
                            <div class="col-md-6">
                                <div class="float-right">
                                    <strong>{{trans("file.To")}}:</strong>
                                    <br><strong>{{trans("file.name")}} :</strong> {{$customer[0]->name}}
                                    <br><strong>{{trans("file.Phone Number")}} :</strong> {{$customer[0]->phone_number}}
                                    <br><strong>{{trans("file.Address")}} :</strong> {{$customer[0]->address}}
                                    <br><strong>{{trans("file.City")}} :</strong> {{$customer[0]->city}}
                                </div>
                            </div>
                        </div>
                        <br>
                    {!! Form::open(['route' => 'quotation.sendBySpecifiedMail', 'method' => 'post', 'files' => false]) !!}
                        @foreach($lims_quotation_data as $quotation)
                        <input type="hidden" name="quotation_id" value="{{$quotation->id}}">
                            <strong>{{trans("file.Note")}} :</strong> {{$quotation->note}}
                        @endforeach
                        <br><br>
                        <strong>{{trans("file.Created By")}} :</strong> {{$user[0]->name}}<br>
                        <strong>{{trans("file.Email")}} :</strong> {{$customer[0]->email}}<br>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <p class="italic"><small>{{trans('file.Please enter the new email you want to send this bill')}}.</small></p>
                            <strong>{{trans('file.Email')}} :</strong><input type="email" name="email" placeholder="example@example.com" required class="form-control">
                            @if($errors->has('email'))
                                <span>
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
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






</script>
@endpush


