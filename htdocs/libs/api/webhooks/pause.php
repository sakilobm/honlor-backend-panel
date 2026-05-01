<?php
// API: webhooks/pause — toggle pause state
$pause = function() {
    $id     = (int)($_POST['id'] ?? 0);
    $paused = filter_var($_POST['paused'] ?? false, FILTER_VALIDATE_BOOLEAN);
    if (!$id) { $this->response($this->json(['error' => 'Missing id']), 400); return; }
    \App\Webhook::update($id, ['paused' => $paused ? 1 : 0, 'status' => $paused ? 'paused' : 'healthy']);
    $this->response($this->json(['success' => true]), 200);
};
