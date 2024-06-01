<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::where('email', 'demo@demo.com')->first();

        if ($user) {
            // Create several root tasks
            $rootTasks = Task::factory(3)->create([
                'user_id'   => $user->id,
                'parent_id' => null,
            ]);

            foreach ($rootTasks as $rootTask) {
                $subtasks = Task::factory(3)->create([
                    'user_id'   => $user->id,
                    'parent_id' => $rootTask->id,
                ]);

                foreach ($subtasks as $subtask) {
                    Task::factory(2)->create([
                        'user_id'   => $user->id,
                        'parent_id' => $subtask->id,
                    ]);
                }
            }
        }
    }
}
