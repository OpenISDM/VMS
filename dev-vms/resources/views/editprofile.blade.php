@extends('app')

@section('content')

<p id="edit">edit profile</p>
<form id="form1" name="form1" method="post" action="/user/edit">
  姓名:<input name="username" type="text" value="{!! $username !!}">
  <br>
  性別:<input name="sex" type="text" list="gender" value="{!! $sex !!}">
  <datalist id="gender">
    <option value="M">
    <option value="F">
  </datalist>
  <br>
  生日:<input id="startdatepicker" name="birthdate" type="text" value="{!! $birthdate !!}">
  <br>
  電子郵件:<input name="email" type="text" value="{!! $email !!}">
  <br>
  手機號碼:<input name="cellphone" type="text" value="{!! $cellphone !!}">
  <br>
  <input name="id" type="hidden" value="{!! $id !!}">
  <input class="button" name="submit1" type="Submit" value="save">
  <input class="button" type="button" value="reset" onclick="location.href='/user'">
  <input class="button" type="button" value="menu" onclick="location.href='/home'">
  <input class="button" type="button" value="next" onclick="location.href='/home'">
  <!!laravel will check token if we have post some message to other pages.>
  <?php echo csrf_field(); ?>
</form>

@endsection
