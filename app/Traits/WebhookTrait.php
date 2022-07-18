<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait WebhookTrait
{
    public function send($sendTo, $data)
    {
        try {
            $response = Http::post($sendTo, $data);

            return $response->status();
        } catch (\Throwable $th) {
            return false;
        }
    }
}