<?php
function is_HTTPS()
{
    if (!isset($_SERVER['HTTPS'])) return FALSE;
    if ($_SERVER['HTTPS'] === 1) {  //Apache
        return TRUE;
    } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
        return TRUE;
    } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
        return TRUE;
    }
    return FALSE;
}

function getDomain()
{
    $server_name = $_SERVER['SERVER_NAME'];

    if (strpos($server_name, 'www.') !== false) {
        return substr($server_name, 4);
    }

    return $server_name;
}


$appid = '';
$scope = 'snsapi_login';
$state = '';
$code = '';
$redirect_uri = '';
$device = '';
$protocol = '';

if (is_HTTPS()) {
    $protocol = 'https';
} else {
    $protocol = 'http';
}

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
if (isset($_GET['scope'])) {
    $scope = $_GET['scope'];
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
        '&redirect_uri=' . urlencode($protocol . '://' . $_SERVER['HTTP_HOST'] . '/'),
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
        getDomain(),
        "; expires=" . gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT", time() + 60),
        "; Max-Age=" + 60,
        "; httponly"
    ]));

    header('Location: ' . implode('', $options));
} else {
    if (isset($_COOKIE['redirect_uri'])) {
        $back_url = urldecode($_COOKIE['redirect_uri']);
        header('Location: ' . implode('', [
                $back_url,
                strpos($back_url, '?') ? '&' : '?',
                'code=' . $code,
                '&state=' . $state
            ]));
    }
}
?>