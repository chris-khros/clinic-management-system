<?php

namespace App\Console\Commands;

use App\Services\OtpService;
use Illuminate\Console\Command;

class CleanupExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTP codes from the database';

    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        parent::__construct();
        $this->otpService = $otpService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting OTP cleanup...');

        $cleanedCount = $this->otpService->cleanupExpiredOtps();

        $this->info("Cleaned up {$cleanedCount} expired OTP codes.");

        return Command::SUCCESS;
    }
}
