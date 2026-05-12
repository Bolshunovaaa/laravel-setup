<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class CreateTaskInteractive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:task-interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new task via interactive CLI prompts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $taskName = $this->ask('Provide the task title');
        $taskDesc = $this->ask('Provide a short description (optional)');
        $deadline = $this->ask('Enter deadline in format YYYY-MM-DD');

        $currentStatus = $this->choice('What is the initial status?', ['new', 'in_progress', 'done'], 0);

        $projId = $this->ask('Type the project ID');
        $userId = $this->ask('Type the assignee ID (leave blank if none)');

        if ($this->confirm('Save this task to the database?', true)) {

            $insertData = [
                'title' => $taskName,
                'description' => $taskDesc ? $taskDesc : null,
                'due_date' => $deadline ? $deadline : null,
                'status' => $currentStatus,
                'project_id' => $projId,
                'author_id' => 1,
            ];

            if ($userId) {
                $insertData['assigned_to'] = $userId;
            }

            $newTask = Task::create($insertData);

            $this->info("Success! Task ID {$newTask->id} ({$newTask->title}) has been saved.");

        } else {
            $this->warn('Operation cancelled by user.');
        }
    }
}
