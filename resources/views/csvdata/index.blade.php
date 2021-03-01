<?php
$statusId = isset($_GET['status_id'])?$_GET['status_id']:1;
?>

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">CSV Files List'</div>

                    <div class="box box-info">
                        <div class="box-body">
                            <label for="status">Status:</label>
                            <select onchange="filterStatus(this.value)" id="status">
                                <option value="1" {{ $statusId == 1 ? 'selected':'' }}>Waiting list</option>
                                <option value="2" {{ $statusId == 2 ? 'selected':'' }}>Processing</option>
                                <option value="3" {{ $statusId == 3 ? 'selected':'' }}>Failed</option>
                                <option value="4" {{ $statusId == 4 ? 'selected':'' }}>Finished</option>
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
                                                    <th>File Name</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($files as $file)
                                                    @if ($file->csv_status == $statusId)
                                                    <tr>
                                                        <td>{{ $file->csv_filename }}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach

                                                </tbody>
                                            </table>
                                            {{ $files->appends(['status_id'=>$statusId])->links() }}

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
    </div>

    <script>
        function filterStatus(value){
            window.location.href='./files?status_id=' + value
        }
    </script>
@stop