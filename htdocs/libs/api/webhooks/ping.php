<?php
// API: webhooks/ping — ping a hook URL and record health
$ping = function() {
    $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
    if (!$id) { $this->response($this->json(['error' => 'Missing id']), 400); return; }
    $result = \App\Webhook::ping($id);
    $this->response($this->json($result), 200);
};
