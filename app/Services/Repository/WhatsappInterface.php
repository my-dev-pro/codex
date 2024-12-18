<?php

namespace App\Services\Repository;

interface WhatsappInterface
{
    public function getConnectionStatus() : bool;
    public function isValidNumber($number);
    public function generateMessage($number, $message, $fileUrl);
    public function sendMessage($number, $message, $fileUrl);
}
