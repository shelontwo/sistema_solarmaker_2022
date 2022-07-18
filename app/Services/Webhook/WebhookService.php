<?php

namespace App\Services\Webhook;

use Exception;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookService
{
    protected $data;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function novoWebhook($data)
    {
        try {
            $webhook = Webhook::create($data);
            $this->request->webhook_id = $webhook->web_id;
        } catch (\Exception $error) {
            return ['status' => false, 'msg' => $error->getMessage()];
        }
    }
}