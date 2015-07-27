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
        <style type="text/css">
            #edit
            {
                width:350px;
                height:300px;   
                text-align: center;
                font-family:Meiryo,Microsoft JhengHei,SimHei;
                font-size:50px;
                
                        
                position:absolute;
                left:50%;
                top:10%;
                        
                
                margin-left:-150px; 
            }       
            #form1
            {
                width:450px;
                height:300px;
                text-align:right; 
                
                font-family:Meiryo,Microsoft JhengHei,SimHei;
                font-size:24px;
                
                        
                position:absolute;
                left:50%;
                top:10%;
                        
                margin-top:130px;
                margin-left:-250px; 
            }
            
            input
            {
                font-family:Courier New,Meiryo,Microsoft YaHei;
                font-size:20px;
                width:300px;
                border-radius: 10px;
                border: 1px dashed blue;
            }   
                    

        </style>


    </head>
    <body>
        
        
        <?php echo "
        
        
            <p id = 'edit'>
                edit profile
            </p>
            
            <form name ='form1'
                  id = 'form1'
                  method = 'post'
                  action = '" . $id . "/" . $username . "/" . $sex . "/" . $birthdate . "/" . $email . "/" . $cellphone . "/" . $city . "'>
            
                姓名:<input type = 'text' name = 'username' value ='" . $username . "'>
                <br>
                性別:<input type = 'text' name = 'sex' value ='" . $sex . "'>
                <br>
                生日:<input type = 'text' name = 'birthdate' value ='" . $birthdate . "'>
                <br>
                電子郵件:<input type = 'text' name = 'email' value ='" . $email . "'>
                <br>
                手機號碼:<input type = 'text' name = 'cellphone' value ='" . $cellphone . "'>
                <br>
                居住城市:<input type = 'text' name = 'city' value ='" . $city . "'>
                <br>
                
                <input type='hidden' name='id' value='" . $id . "'>
                
                <input type = 'Submit' name = 'submit1' value ='save' style = 'width:100px'>
                <input type = 'button' value ='reset' style = 'width:100px' onclick=\"location.href='" . $id . "'\">
                <input type = 'button' value ='menu' style = 'width:100px' onclick=\"location.href='002.php'\">
                <input type = 'button' value ='next' style = 'width:100px' onclick=\"location.href='002.php'\">
            
            ";
            
        ?>
        
            <!!laravel will check token if we have post some message to other pages.>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
        
    </body>
</html>