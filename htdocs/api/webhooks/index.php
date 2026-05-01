<?php
use App\Webhook;
use Aether\REST;

if (!defined('HTDOCS_ROOT')) {
    require_once dirname(__DIR__, 3) . '/libs/load.php';
}

$rest = new REST();
$rest->requireAuth();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'list':
        $rest->response(['webhooks' => Webhook::getAll()]);
        break;

    case 'create':
        $p      = $rest->getJsonPayload();
        $name   = trim($p['name']   ?? '');
        $url    = trim($p['url']    ?? '');
        $events = $p['events']  ?? [];
        $secret = $p['secret']  ?? '';

        if (!$name || !$url) {
            $rest->response(['error' => 'Name and URL are required'], 400);
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $rest->response(['error' => 'Invalid URL format'], 400);
        }

        $id = Webhook::create($name, $url, (array)$events, $secret);
        $rest->response(['success' => true, 'id' => $id]);
        break;

    case 'update':
        $p  = $rest->getJsonPayload();
        $id = (int)($p['id'] ?? 0);
        if (!$id) { $rest->response(['error' => 'Missing id'], 400); }
        unset($p['id']);
        $rest->response(['success' => Webhook::update($id, $p)]);
        break;

    case 'delete':
        $p  = $rest->getJsonPayload();
        $id = (int)($p['id'] ?? 0);
        if (!$id) { $rest->response(['error' => 'Missing id'], 400); }
        $rest->response(['success' => Webhook::delete($id)]);
        break;

    case 'ping':
        $p  = $rest->getJsonPayload();
        $id = (int)($p['id'] ?? 0);
        if (!$id) { $rest->response(['error' => 'Missing id'], 400); }
        $rest->response(Webhook::ping($id));
        break;

    case 'toggle_pause':
        $p      = $rest->getJsonPayload();
        $id     = (int)($p['id'] ?? 0);
        $paused = (bool)($p['paused'] ?? false);
        if (!$id) { $rest->response(['error' => 'Missing id'], 400); }
        $status = $paused ? 'paused' : 'healthy';
        Webhook::update($id, ['paused' => $paused ? 1 : 0, 'status' => $status]);
        $rest->response(['success' => true]);
        break;

    case 'errors':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { $rest->response(['error' => 'Missing id'], 400); }
        $rest->response(['errors' => Webhook::getErrors($id, 20)]);
        break;

    default:
        $rest->response(['error' => 'Unknown action'], 400);
}
