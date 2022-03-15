<?php

declare(strict_types=1);

require_once __DIR__ . '/../common.inc';

use WebPageTest\Exception\ClientException;
use WebPageTest\RequestContext;
use WebPageTest\Template;
use WebPageTest\Util;
use WebPageTest\ValidatorPatterns;

(function (RequestContext $request_context) {
    if (!Util::getSetting('cp_auth')) {
        $protocol = $request_context->getUrlProtocol();
        $host = Util::getSetting('host');
        $route = '/';
        $redirect_uri = "{$protocol}://{$host}{$route}";

        header("Location: {$redirect_uri}");
        exit();
    }

    $request_method = strtoupper($_SERVER['REQUEST_METHOD']);

    if ($request_method == 'POST') {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token');

        if ($csrf_token != $_SERVER['csrf_token']) {
            error_log('CSRF token mismatch');
            throw new ClientException("There was an error signing up, please try again", "/signup");
        }

        $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, array(
          'options' => '/' . ValidatorPatterns::getContactInfo() . '/'
        ));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_VALIDATE_REGEXP, array(
          'options' => '/' . ValidatorPatterns::getPassword() . '/'
        ));
        $confirm_password = filter_input(INPUT_POST, 'comfirm-password');

        $company_name = $_POST['company-name'];

        if (!empty($company_name)) {
            $company_name = filter_var($company_name, FILTER_VALIDATE_REGEXP, array(
            'options' => '/' . ValidatorPatterns::getContactInfo() . '/'
            ));
        }

        if (
            empty($name) ||
            empty($email) ||
            empty($password) ||
            empty($confirm_password)
        ) {
            throw new ClientException("Please fill in all required fields", "/signup");
        }

        if ($password != $confirm_password) {
            throw new ClientException("Passwords must match", "/signup");
        }

        exit();
    } elseif ($request_method == 'GET') {
        $csrf_token = bin2hex(random_bytes(32));
        $_SERVER['csrf_token'] = $csrf_token;

        $vars = array(
          'csrf_token' => $csrf_token,
          'contact_info_pattern' => ValidatorPatterns::getContactInfo(),
          'password_pattern' => ValidatorPatterns::getPassword()
        );
        $tpl = new Template('account');
        echo $tpl->render('signup', $vars);
        exit();
    } else {
        throw new ClientException("Method not supported on this endpoint");
    }
})($request_context);
