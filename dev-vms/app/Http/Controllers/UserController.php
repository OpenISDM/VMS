<?php
namespace App\Http\Controllers;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 顯示給定使用者的個人資料。
     *
     * @param  int  $id
     * @return Response
     */
    public function showProfile($id)
    {

        //database information is in .env file
        $user = DB::table('personprofile')
                ->where('id', $id)
                ->first();
                
        $username = $user->username;
        $sex = $user->sex;
        $birthdate = $user->birthdate;
        $email = $user->email;
        $cellphone = $user->cellphone;
        $city = $user->city;
        
        
        return view('greeting', ['id'=> $id,
                                'username'=> $username,
                                'sex'=> $sex,
                                'birthdate'=> $birthdate,
                                'email'=> $email,
                                'cellphone'=> $cellphone,
                                'city'=> $city]);
    }
    public function editProfile(Request $request)
    {
        $id = $request->input('id');
        $username = $request->input('username');
        $sex = $request->input('sex');
        $birthdate = $request->input('birthdate');
        $email = $request->input('email');
        $cellphone = $request->input('cellphone');
        $city = $request->input('city');
        
        
        DB::table('personprofile')
            ->where('id', $id)
            ->update(['username'=> $username,
                      'sex'=> $sex,
                      'birthdate'=> $birthdate,
                      'email'=> $email,
                      'cellphone'=> $cellphone,
                      'city'=> $city]);
        
        return redirect('user/'.$id);
    }
}
