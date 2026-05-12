<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class GenerateTaskReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:tasks {--project_id= : Filter tasks by specific project ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates and displays a summary table of tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filterProject = $this->option('project_id');
        $taskQuery = Task::query();

        if ($filterProject) {
            $taskQuery->where('project_id', $filterProject);
            $this->info("Fetching data for Project ID: {$filterProject}");
        } else {
            $this->info("Fetching data for all available projects");
        }

        $fetchedTasks = $taskQuery->get();

        if ($fetchedTasks->isEmpty()) {
            $this->warn('No tasks found matching your criteria.');
            return;
        }

        $formattedRows = $fetchedTasks->map(function ($item) {
            return [
                'Task ID' => $item->id,
                'Name'    => $item->title,
                'State'   => $item->status,
                'Due'     => $item->due_date ?? 'N/A',
            ];
        });

        $this->table(['Task ID', 'Name', 'State', 'Due'], $formattedRows);
    }
}
