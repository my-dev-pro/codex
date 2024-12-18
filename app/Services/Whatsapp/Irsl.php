<?php

namespace App\Services\Whatsapp;

use App\Services\Repository\WhatsappInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Irsl implements WhatsappInterface
{

    public function getConnectionStatus(): bool
    {
        $response = Http::get($this->getUrl('connection'))->json();
        if ($response['ErrCode']) {
            Log::info('Connection timeout!' . now());
            throw new \Exception("WhatsApp not connected! " . $response['Result']);
        }

        return match ($response['Result']['status']) {
            'open' => true,
            'default' => false,
        };

    }

    public function isValidNumber($number)
    {

        $response = Http::get($this->getUrl("ExistsOnWhatsApp/{$number}"))->json();
        if ($response['error']) {
            Log::info("Number {$number} is Not Valid!");
        }

        return $response['Result']['ExistsOnWhatsApp'];
    }

    public function generateMessage($number, $message, $fileUrl)
    {
        // Not required
    }

    public function sendMessage($number, $message, $fileUrl)
    {

        if ( ! $this->getConnectionStatus() ) {
            return false;
        }

        if ( ! $this->isValidNumber($number) ) {
            return false;
        }

        $data['recipient'] = $number;
        $data['content']['text'] = $message;
        $data['content']['document']['url'] = $fileUrl;

        try {
            $response = Http::post($this->getUrl("sendMessage"), $data)->json();
            if ($response['success']) {
                Log::info('Whatsapp message with result sent to ' . $number);
            }
        } catch (\Exception $e) {
            throw new \Exception('Error sending message: ' . $e->getMessage());
        }

        return true;
    }

    protected function getUrl($endpoint): string
    {
        return "https://srv{$this->getServerId()}.irsl.io/instance{$this->getInstanceId()}/{$this->getToken()}/{$endpoint}";
    }

    protected function getServerId(): int
    {
        return config('whatsapp.irsl.server_id');
    }

    protected function getInstanceId(): int
    {
        return config('whatsapp.irsl.instance_id');
    }

    protected function getToken(): string
    {
        return config('whatsapp.irsl.token');
    }

}
