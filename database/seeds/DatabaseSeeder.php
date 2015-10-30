<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\ApiKey;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        ApiKey::create(['api_key' => '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1']);

        Model::reguard();
    }
}
