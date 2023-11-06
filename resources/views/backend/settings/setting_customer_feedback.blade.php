@extends('layouts.admin_app')

@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">  Customers' Feedback
            </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active"> Customers' Feedback </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>


          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Customers' Feedback List</h3>

           <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap table-bordered">
                  <thead>
                    <tbody>
                    <tr align="center">

                      <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Feedback</th>
                    </tr>
                  </thead>
    <tbody>

                 @foreach($student as $row => $user) 
                <tr>
                    <td>
                       {{ $row+1 }}
                        <input type="hidden" class="row_id" value="{{ $user->id }}">
                    </td>
                    <td>{{$user->user_name}}</td>
                    <td>{{$user->user_mail}}</td>
                    <td>{{$user->user_feedback}}</td>
                </tr>
               
               
              @endforeach
                
            </tbody>
        </table>

                     </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>



@endsection
