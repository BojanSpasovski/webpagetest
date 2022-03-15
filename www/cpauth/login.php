<?php

declare(strict_types=1);

require_once __DIR__ . '/../common.inc';

use WebPageTest\Util;
use WebPageTest\Template;
use WebPageTest\Exception\ClientException;

if (!Util::getSetting('cp_auth')) {
    $protocol = $request_context->getUrlProtocol();
    $host = Util::getSetting('host');
    $route = '/';
    $redirect_uri = "{$protocol}://{$host}{$route}";

    header("Location: {$redirect_uri}");
    exit();
}

$request_method = strtoupper($_SERVER['REQUEST_METHOD']);

if ($request_method === 'POST') {
    $csrf_token = filter_input(INPUT_POST, 'csrf_token');
    if ($csrf_token !== $_SESSION['csrf_token']) {
        error_log("Incorrect CSRF token");
        throw new ClientException("There was an error logging you in. Please try again.", "/login", 403);
        exit();
    }

    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');
    } catch (Exception $e) {
        error_log("Password or email missing");
        throw new ClientException($e->getMessage(), "/login", 400);
    }

    try {
        $auth_token = $request_context->getClient()->login($email, $password);
        $request_context->getClient()->authenticate($auth_token->access_token);
    } catch (Exception $e) {
        throw new ClientException($e->getMessage(), "/login", 403);
    }

    $protocol = $request_context->getUrlProtocol();
    $host = Util::getSetting('host');
    setcookie('cp_access_token', $auth_token->access_token, time() + $auth_token->expires_in, "/", $host);
    setcookie('cp_refresh_token', $auth_token->refresh_token, time() + 60 * 60 * 24 * 30, "/", $host);

    $redirect_uri = isset($_GET["redirect_uri"]) ? htmlspecialchars($_GET["redirect_uri"]) : "{$protocol}://{$host}";

    header("Location: {$redirect_uri}");
    exit();
} elseif ($request_method === 'GET') {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(35));
    $error = $_SESSION['error_message'] ?? "";
    unset($_SESSION['error_message']);

    if ($request_context->getUser() && $request_context->getUser()->getUserId() && isset($_GET["redirect_uri"])) {
        $redirect_uri = htmlspecialchars($_GET["redirect_uri"]);
        header("Location: {$redirect_uri}");
        exit();
    }

    $tpl = new Template('account');
    $tpl->setLayout('headless');
    $args = array(
        'csrf_token' => $_SESSION['csrf_token'],
        'has_error' => !empty($error)
    );
    echo $tpl->render(
        'login',
        $args
    );
    exit();
}
