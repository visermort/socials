<?php

echo '<style>  .inline-block { display: inline-block;  margin: 10px; }</style>';

include 'config.php';
/*
 *
 * TestVisermort
http://visermort.ru/

API Yandex

Права:
Получение статистики, чтение параметров своих и доверенных счётчиков
Создание счётчиков, изменение параметров своих и доверенных счётчиков
ID: 3732be95e273483aa0d0cad50c41a22f
Пароль: bdb69d1a888348228deda19d442ba068
Callback URL: http://visermort.ru

token:AQl0F0gAAxrMvU5VbmkoQwabyMHJsREsVA


 * */
//подставляем id счётчика и токен
//$url = 'https://api-metrika.yandex.ru/stat/geo.json?id=36842480&pretty=1&oauth_token=AQl0F0gAAxrMvU5VbmkoQwabyMHJsREsVA';
//$json = file_get_contents($url);
//if (!empty($json)) {
//    $ar=json_decode($json,true);
//    echo '<pre>';
//    print_r($ar);
//    echo '</pre>';
//}


//api vk
//ID приложения:	5428835
//ключ MG3SM48yzsWHYPDDmxio

echo '<h2>Мои друзья</h2>';
//user_id=1519895
$url="https://api.vk.com/method/friends.get?user_id=1519895&fields=nickname,domain,sex,bdate,city,country,timezone,photo_100,online,status,universities";
$json = file_get_contents($url);
if (!empty($json)) {
    $ar=json_decode($json,true)['response'];
}

$params = array(
    'client_id' => $id,
    'secret' => $secret,
    'redirect_uri' => $redirect,
    'display' => 'page',
    'scope' => $scope
);

foreach ( $ar as $value) {
    $userId = $value['uid'];
    $linkProfile ='http://vk.com/id'.$userId;
    $linkFavour = $favourUrl.'?user_id='.$userId.'&'.urldecode(http_build_query($params));
    $fr = '<div class="inline-block"><a href="http://vk.com/id'.$value['uid'].'"><p>'.$value['first_name'].'<br>'.$value['last_name'].'</p>';
    $fr .= '<img src = "'.$value["photo_100"].'"></a>';
    $fr .= '<div><a href="'.$linkFavour.'" >Подробнее</a>';
    $fr .= '</div></div>';
    echo $fr;
}
$link = $favourUrl.'?'.urldecode(http_build_query($params));
$link = '<a href="'.$link.'">'.$link.' Это ссылка для получения Code</a>';
echo $link;


//9b7178be188fdcbcb3









