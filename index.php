<?php
$appid = '';
$scope = 'snsapi_login';
$state = '';
$code = '';
$redirect_uri = '';
$device = '';

if (isset($_GET['device'])) {
    $device = $_GET['device'];
}

if (isset($_GET['appid'])) {
    $appid = $_GET['appid'];
}
if (isset($_GET['state'])) {
    $state = $_GET['state'];
}
if (isset($_GET['redirect_uri'])) {
    $redirect_uri = $_GET['redirect_uri'];
}
if (isset($_GET['code'])) {
    $code = $_GET['code'];
}

if ($code == 'test') {
    exit;
}

if (empty($code)) {
    $authUrl = '';
    if ($device == 'pc') {
        $authUrl = 'https://open.weixin.qq.com/connect/qrconnect';
    } else {
        $authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    }

    $options = [
        $authUrl,
        '?appid=' . $appid,
        '&redirect_uri=' . urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/'),
        '&response_type=code',
        '&scope=' . $scope,
        '&state=' . $state,
        '#wechat_redirect'
    ];

    //把redirect_uri先写到cookie
    header(implode('', [
        "Set-Cookie: redirect_uri=",
        urlencode($redirect_uri),
        "; path=/; domain=",
        $_SERVER['HTTP_HOST'],
        "; expires=" . gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT", time() + 60),
        "; Max-Age=" + 60,
        "; httponly"
    ]));

    header('Location: ' . implode('', $options));
} else {
    if (isset($_COOKIE['redirect_uri'])) {
        header('Location: ' . implode('', [
                urldecode($_COOKIE['redirect_uri']),
                '?code=' . $code,
                '&state=' . $state
            ]));
    }
}
?>