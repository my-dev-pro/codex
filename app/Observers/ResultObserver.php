<?php

namespace App\Observers;

use App\Models\TestResult;
use App\Services\Whatsapp\Irsl;
use Illuminate\Support\Facades\Storage;

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
//        $doctor = $testResult->test->doctor;
        $notification = $testResult->test->notifications;
        $file_path = $testResult->result_path;

        if ($file_path != null) {

            $file_url = config('app.url') . Storage::url($file_path);

            if (! empty($notification->receiver)) {
                $whatsapp = new Irsl();
                foreach ($notification->receiver as $receiver) {

                    // receiver mobile number
                    $number = $testResult->test->{$receiver}->mobile;
                    $message = "Hello, {$patient->first_name} {$patient->last_name} test result has been released. Thank you for using Codex services";

                    // check if whatsapp account is valid
                    if ( $whatsapp->getConnectionStatus() && $whatsapp->isValidNumber($number) ) {

                        // send WhatsApp message
                        $whatsapp->sendMessage($number, $message, $file_url);

                    }

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
