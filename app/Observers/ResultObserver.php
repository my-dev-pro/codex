<?php

namespace App\Observers;

use App\Models\TestRequest;
use App\Models\TestResult;
use Illuminate\Support\Facades\Log;

class ResultObserver
{
    /**
     * Handle the TestResult "created" event.
     */
    public function created(TestResult $testResult): void
    {
        //
    }

    /**
     * Handle the TestResult "updated" event.
     */
    public function updated(TestResult $testResult): void
    {
        $patient = $testResult->test->patient;
        $doctor = $testResult->test->doctor;
        $notification = $testResult->test->notifications;

        if ($testResult->result_path != null) {
            if (! empty($notification->receiver)) {
                foreach ($notification->receiver as $receiver) {
                    // check if whatsapp account
                    // sent whatsapp message
                    Log::info('Whatsapp message sent to ' . $testResult->test->{$receiver}->mobile);
                }
            }
        }
    }

    /**
     * Handle the TestResult "deleted" event.
     */
    public function deleted(TestResult $testResult): void
    {
        //
    }

    /**
     * Handle the TestResult "restored" event.
     */
    public function restored(TestResult $testResult): void
    {
        //
    }

    /**
     * Handle the TestResult "force deleted" event.
     */
    public function forceDeleted(TestResult $testResult): void
    {
        //
    }
}
