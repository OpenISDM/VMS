@extends('app')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
        <div class="panel-heading">Register</div>
        <div class="panel-body">
          @if (count($errors) > 0)
            <div class="alert alert-danger">
              <strong>Whoops!</strong> There were some problems with your input.<br><br>
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/register') }}">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">

            <div class="form-group">
              <label class="col-md-4 control-label">Full Name</label>
              <div class="col-md-6">
                <input class="form-control" name="full_name" type="text" value="{{ old('full_name') }}">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-4 control-label">E-Mail Address</label>
              <div class="col-md-6">
                <input class="form-control" name="email" type="email" value="{{ old('email') }}">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-4 control-label">Password</label>
              <div class="col-md-6">
                <input class="form-control" name="password" type="password">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-4 control-label">Confirm Password</label>
              <div class="col-md-6">
                <input class="form-control" name="password_confirmation" type="password">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-4 control-label">Gender</label>
              <div class="col-md-6">
                <input class="form-control" name="gender" type="text" value="{{ old('gender') }}">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-4 control-label">Birthdate</label>
              <div class="col-md-6">
                <input class="form-control" id="startdatepicker" name="birth_date" type="text" value="{{ old('birth_date') }}">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-4 control-label">Phone Number</label>
              <div class="col-md-6">
                <input class="form-control" name="phone" type="text" value="{{ old('phone') }}">
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-6 col-md-offset-4">
                <button class="btn btn-primary" type="submit">
                  Register
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
