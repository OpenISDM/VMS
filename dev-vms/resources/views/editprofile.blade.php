<html>
    <head>
        <title>VMS edit profile</title>
        <meta charset = "utf-8" http-equiv="Page-Enter" content="blendTrans(Duration=10.0)">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <script src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.10.2.min.js"></script>
        {!! csrf_field() !!}
        <script type="text/javascript">
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
        </script>
        
        {!! HTML::style('css/greeting.css') !!}
        
    </head>
    <body>
        
        
        
        
        
            <p id = 'edit'>
                edit profile
            </p>
            
            <form name ='form1'
                  id = 'form1'
                  method = 'post'
                  action = '/user/edit'>
            
                姓名:<input type = 'text' name = 'username' value ='{!! $username !!}'>
                <br>
                性別:<input type = 'text' name = 'sex' value ='{!! $sex !!}'>
                <br>
                生日:<input type = 'text' name = 'birthdate' value ='{!! $birthdate !!}'>
                <br>
                電子郵件:<input type = 'text' name = 'email' value ='{!! $email !!}'>
                <br>
                手機號碼:<input type = 'text' name = 'cellphone' value ='{!! $cellphone !!}'>
                <br>
                
                
                <input type='hidden' name='id' value='{!! $id !!}'>
                
                <input type = 'Submit' class = 'button' name = 'submit1' value ='save'>
                <input type = 'button' class = 'button' value ='reset' onclick="location.href='/user'">
                <input type = 'button' class = 'button' value ='menu' onclick="location.href='/home'">
                <input type = 'button' class = 'button' value ='next' onclick="location.href='002.php'">
            
                       
        
        
            <!!laravel will check token if we have post some message to other pages.>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
        
    </body>
</html>