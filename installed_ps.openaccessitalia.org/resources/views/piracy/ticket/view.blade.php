@extends('layouts.home')

@section('page')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-6 py-lg-8 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Ticket {{$ticket->ticket_id}}</h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Page Heading-->
            </div>
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="ticket_id">Ticket ID</label>
                            <div class="input-group">
                                <input class="form-control" id="ticket_id" name="ticket_id" readonly value="{{$ticket->ticket_id}}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="status">Downloaded when in status</label>
                            <div class="input-group">
                                <input class="form-control" id="status" name="status" readonly value="{{$ticket->status}}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-traffic-light text-dark mr-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="timestamp">Downloaded at</label>
                            <div class="input-group">
                                <input class="form-control" id="timestamp" name="timestamp" readonly value="{{$ticket->timestamp}}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-clock text-dark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="metadata.created_at">Created at</label>
                            <div class="input-group">
                                <input class="form-control" id="metadata.created_at" name="metadata.created_at" readonly value="{{json_decode($ticket->metadata)->created_at}}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-clock text-dark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-custom mt-2">
                <div class="card-body">
                    <h4>FQDNs</h4>
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Given feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (json_decode($ticket->fqdns) as $fqdn)
                                <tr>
                                    <td>{{$fqdn}}</td>
                                    <td>
                                        @php
                                            $feedback = \App\Piracy\TicketItemsLog::where('ticket_id',$ticket->ticket_id)->where('item_type','fqdn')->where('item',$fqdn)->get()->first();
                                        @endphp
                                        @if($feedback)
                                            {{$feedback->status}} at {{$feedback->timestamp}}
                                        @else
                                            Feedback not sent
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card card-custom mt-2">
                <div class="card-body">
                    <h4>IPv4s</h4>
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Given feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (json_decode($ticket->ipv4s) as $ipv4)
                                <tr>
                                    <td>{{$ipv4}}</td>
                                    <td>
                                        @php
                                            $feedback = \App\Piracy\TicketItemsLog::where('ticket_id',$ticket->ticket_id)->where('item_type','ipv4')->where('item',$ipv4)->get()->first();
                                        @endphp
                                        @if($feedback)
                                            {{$feedback->status}} at {{$feedback->timestamp}}
                                        @else
                                            Feedback not sent
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card card-custom mt-2">
                <div class="card-body">
                    <h4>IPv6s</h4>
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Given feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (json_decode($ticket->ipv6s) as $ipv6)
                                <tr>
                                    <td>{{$ipv6}}</td>
                                    <td>
                                        @php
                                            $feedback = \App\Piracy\TicketItemsLog::where('ticket_id',$ticket->ticket_id)->where('item_type','ipv6')->where('item',$ipv6)->get()->first();
                                        @endphp
                                        @if($feedback)
                                            {{$feedback->status}} at {{$feedback->timestamp}}
                                        @else
                                            Feedback not sent
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<script>
    $(document).ready(function(){
        
    });
</script>

@endsection