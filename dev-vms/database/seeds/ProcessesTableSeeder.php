<?php
 
use Illuminate\Database\Seeder;
 
class ProcessesTableSeeder extends Seeder {
 
    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        DB::table('processes')->delete();
 
        $processes = array(
            ['id' => 1, 'fk_project_id' => 1, 'name' => 'Process 1', 'slug' => 'process-1', 'description' => 'First child process of project 1', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id' => 2, 'fk_project_id' => 1, 'name' => 'Process 2', 'slug' => 'process-2', 'description' => 'Second child process of project 1', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id' => 3, 'fk_project_id' => 1, 'name' => 'Process 3', 'slug' => 'process-3', 'description' => 'Third child process of project 1', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['id' => 4, 'fk_project_id' => 2, 'name' => 'Process 4', 'slug' => 'process-4', 'description' => 'First child process of project 2', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        );
 
        // Uncomment the below to run the seeder
        DB::table('processes')->insert($processes);
    }
 
}

