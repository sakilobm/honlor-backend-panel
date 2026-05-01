<?php
// API: webhooks/list — GET all registered webhooks
$list = function() {
    \App\Webhook::ensureTable();
    $hooks = \App\Webhook::getAll();
    $this->response($this->json(['webhooks' => $hooks]), 200);
};
