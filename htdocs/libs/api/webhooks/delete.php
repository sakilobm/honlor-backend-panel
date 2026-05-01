<?php
// API: webhooks/delete
$delete = function() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) { $this->response($this->json(['error' => 'Missing id']), 400); return; }
    $this->response($this->json(['success' => \App\Webhook::delete($id)]), 200);
};
