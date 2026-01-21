<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class RegenerateNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:regenerate {--type=all : Type of notifications (all, vaccines, inventory)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually regenerate notifications for vaccines and inventory';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        $this->info('Starting notification regeneration...');

        if ($type === 'all' || $type === 'vaccines') {
            $this->info('Generating vaccine notifications...');
            $this->notificationService->generateAllChildVaccineNotifications();
            $this->info('✓ Vaccine notifications generated successfully.');
        }

        if ($type === 'all' || $type === 'inventory') {
            $this->info('Generating inventory notifications...');
            $this->notificationService->generateAllInventoryNotifications();
            $this->info('✓ Inventory notifications generated successfully.');
        }

        $this->info('All notifications regenerated successfully!');

        return Command::SUCCESS;
    }
}