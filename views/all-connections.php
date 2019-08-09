<?php
$banks = $moneyhubClient->getAllConnections();

$result = '<ul>';
foreach ($banks as $bank) {
    $result .= '<li><a href="data?bankid=' . $bank['id'] . '">' . $bank['name'] . '</a></li>';
}
$result .= '</ul>';

echo $result;
