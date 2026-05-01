<?php
// API: webhooks/create — POST new webhook
$create = function() {
    $name   = trim($_POST['name']   ?? '');
    $url    = trim($_POST['url']    ?? '');
    $events = json_decode($_POST['events'] ?? '[]', true) ?: [];
    $secret = trim($_POST['secret'] ?? '');

    if (!$name || !$url) {
        $this->response($this->json(['error' => 'Name and URL required']), 400); return;
    }
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $this->response($this->json(['error' => 'Invalid URL']), 400); return;
    }
    $id = \App\Webhook::create($name, $url, (array)$events, $secret);
    $this->response($this->json(['success' => true, 'id' => $id]), 200);
};
