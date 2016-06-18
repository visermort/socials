<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ссылки на сервисы ВК</title>
    <style>
        .services-list__item {
            list-style-type: none;
        }
        .services-list__link {
            color: black;
            text-decoration: none;
            font-size: 15px;
        }
        .services-list__link:hover {
            color: blue;
            text-decoration: underline;
            /*font-size: 14px;*/
        }
    </style>
</head>
<body>
<?php
include 'common/config.php';
include 'common/functions.php';

$friendsParams = array(
    'client_id' => $vkApp['id'],
    'secret' => $vkApp['secret'],
    'redirect_uri' => 'http://visermort.ru/socials/vkfriends.php',
    'display' => 'page',
    'response_type' => 'code',
    'scope' => 'friends,photos,wall'
);
$wallParamsStandalone = array(
    'client_id' => $vkApp2['id'],
  //  'secret' => $vkApp2['secret'],
    'redirect_uri' => 'https://oauth.vk.com/blank.html',
    'display' => 'page',
    'response_type' => 'token',
    'scope' => 'wall,offline,friends,photos,market',
    'v' => '5.21'
);
//запрос на сод для веб-приложения
$link = 'https://oauth.vk.com/authorize?'.urldecode(http_build_query($friendsParams));
$linkDatabase = 'vkdatabase.php';

//запрос на токен standalone приложения -  используем 1 раз при разработке
$linkToken = 'https://oauth.vk.com/authorize?'.urldecode(http_build_query($wallParamsStandalone));
$linkWall = 'wall.php'

?>
<h2>Ссылки на сервисы</h2>
<ul class="services-list__list">
    <li class="services-list__item"><a class="services-list__link" href="<?php echo($link)?>">Мои друзья ВКонтакте</a></li>
    <li class="services-list__item"><a class="services-list__link" href="<?php echo($linkWall)?>">Моя стена ВКонтакте</a></li>
    <li class="services-list__item"><a class="services-list__link" href="<?php echo($linkDatabase)?>">Базы данных VK</a></li>
<!--    <li class="services-list__item"><a class="services-list__link" href="--><?php //echo($linkToken)?><!--">Получить токен VK Standalone</a></li>-->
</ul>


</body>
</html>