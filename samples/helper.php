<?php

require_once('config.php');

/**
 * @throws Exception
 */
function getAdminToken()
{
    $urlRest = BASE_URL . "/rest/all/V1/integration/admin/token?username=" . ADMIN_USERNAME . "&password=" . ADMIN_PASSWORD;

    $headers = array();
    $headers[] = 'Content-Type: application/json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlRest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    $magentoAuthorizationToken = null;
    if ($result !== false) {
        $magentoAuthorizationToken = json_decode($result, true);
    }

    if (isset($magentoAuthorizationToken['message'])) {
        throw new Exception($magentoAuthorizationToken['message']);
    }
    if (empty($magentoAuthorizationToken)) {
        throw new Exception('Empty token');
    }

    return $magentoAuthorizationToken;
}