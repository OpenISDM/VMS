<?php
 
use Illuminate\Database\Seeder;
 
class UsersTableSeeder extends Seeder {
 
    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        DB::table('users')->delete();

        $users = array(
            [
                'id' => 1, 
                'full_name' => 'Harrison Lin', 
                'email' => 'harrisonllin@gmail.com', 
                'password' => Hash::make('harrison'), 
                'gender' => 'M',
                'birth_date' => date("Y-m-d",strtotime('1995-05-17')),
                'phone' => '408-480-6078',
                'is_project_manager'=> true,
                'created_at' => new DateTime, 
                'updated_at' => new DateTime
            ],

            [
                'id' => 2, 
                'full_name' => 'Albert Chu', 
                'email' => 'albertchu1616@gmail.com', 
                'password' => Hash::make('albert'), 
                'gender' => 'M',
                'birth_date' => date("Y-m-d",strtotime('1995-05-18')),
                'phone' => '408-480-8888',
                'is_project_manager'=> false,
                'created_at' => new DateTime, 
                'updated_at' => new DateTime
            ],
             
        );
 
        // Uncomment the below to run the seeder
        DB::table('users')->insert($users);
    }
 
}
