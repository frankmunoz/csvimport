<?php
$statusId = isset($_GET['status_id'])?$_GET['status_id']:1;
?>
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Contacts List'</div>

                    <div class="box box-info">
                        <div class="box-body">
                            <label for="status">Status:</label>
                            <select onchange="filterStatus(this.value)" id="status">
                                <option value="1" {{ $statusId == 1? 'selected':'' }}>Success</option>
                                <option value="2" {{ $statusId == 2    ? 'selected':'' }}>Error</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class= "col-md-12">
                            <div class="box box-info">
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="display table table-striped table-bordered table-hover table-responsive-lg" cellspacing="0" width="100%" id = "dataTable" name ="dataTable">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Birthdate</th>
                                                <th>Phone</th>
                                                <th>Address</th>
                                                <th>Credit-card</th>
                                                <th>Franchise</th>
                                                <th>Email</th>
                                                @if ($statusId == 2)
                                                <th>Error</th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($contacts as $contact)
                                                @if ($contact->status == $statusId)
                                                <tr>
                                                    <td>{{ $contact->name }}</td>
                                                    <td>{{ date_format(date_create($contact->birthdate),"Y M d")}}</td>
                                                    <td>{{ $contact->phone }}</td>
                                                    <td>{{ $contact->address }}</td>
                                                    <td>{{ $contact->credit_card?'XXX-XXX-XXX-'.substr($contact->credit_card,-4):'' }}</td>
                                                    <td>{{ $contact->franchise }}</td>
                                                    <td>{{ $contact->email }}</td>
                                                    @if ($statusId == 2)
                                                    <td>{{ $contact->error }}</td>
                                                    @endif
                                                </tr>
                                                @endif
                                            @endforeach

                                            </tbody>
                                        </table>
                                        {{ $contacts->appends(['status_id'=>$statusId])->links() }}

                                    </div>
                                    <!-- /.table-responsive -->
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterStatus(value){
            window.location.href='./contacts?status_id=' + value
        }
    </script>
@stop