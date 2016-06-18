<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Стена на моей странице ВКонтакте</title>
    <link rel="stylesheet" href="css/main.css">

</head>
<body>
<div class="wrapper">
<?php

ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
include 'common/config.php';
include 'common/functions.php';
require 'common/getvkfriends.class.php';


$token=$vaAppSpandalone['access_token'];
?>


<?php
if ($token) {

    //получим альбомы поьлзователя, чтобы он мог выбрать, в какой грузить фото
    $hrefPost = 'https://api.vk.com/method/photos.getAlbums';
    $params = array (
        'access_token' => $token
    );
    $albums =  json_decode(getCurl($hrefPost,$params),true)['response'];

    ?>

    <div class="form-post">
        <div class="form-post__button-toggle"><></div>
        <div class="form-post__title">Добавить запись</div>
        <form  method="post" class="form-post__form"  enctype="multipart/form-data"  >
            <input type="hidden" name="hiddenElement" value="">
            <div class="form-post__form-grop">
                <textarea  name="text" class="form-post__input form-post__input-text"></textarea>
            </div>
            <div class="form-post__form-grop">
                <label for="form-post__input-select">Выберите альбом для загрузки фото</label>
                <select id="form-post__input-select" name="album" class="form-post__input" >
                    <?php
                    foreach ($albums as $key => $album){
                        echo '<option value="'.$album['aid'].'" '.($key == 0 ? 'selected': '').'  >'.$album['title'].'</option>';
                    }
                    ?>

                </select>

            </div>

            <div class="form-post__form-grop">
                <input name="file1" class="form-post__input" type="file">
            </div>
            <div class="form-post__form-grop">
                <input name="file2" class="form-post__input" type="file">
            </div>
            <div class="form-post__form-grop">
                <input name="file3" class="form-post__input" type="file">
            </div>
            <div class="form-post__form-grop">
                <input name="file4" class="form-post__input" type="file">
            </div>
            <div class="form-post__form-grop">
                <input name="file5" class="form-post__input" type="file">
            </div>
            <div class="form-post__form-grop">
                <input type="submit" value="Добавить">
            </div>
        </form>
    </div>


<?php    //проверяем, есть ли данные в POST
    if (isset($_POST['text']) && $_POST['text'] &&(!isset($_POST['hiddenElement']) || !$_POST['hiddenElement'])) {
        //вначале смотрим, есть ли загруженные файлы, чтобы вставить в пост
        if (isset($_FILES) && ($_FILES['file1']['tmp_name'] || $_FILES['file2']['tmp_name'] || $_FILES['file3']
                ['tmp_name'] || $_FILES['file4']['tmp_name'] || $_FILES['file5']['tmp_name'])){
            //чтобы к посту прикрепить фотографии, нужно выполнить ряд операций
            //1. Получить url сервера для загрузки фото
            $hrefPost = 'https://api.vk.com/method/photos.getUploadServer';//?message='.$_POST['text'].'&acceoken='.$token;
            $params = array (
                'album_id' => $_POST['album'], //;$vkApp2['album_id'],
                'access_token' => $token
            );
            $uploadUrl =  json_decode(getCurl($hrefPost,$params),true);
            $uploadUrl=$uploadUrl['response'];
            $uploadUrl=$uploadUrl['upload_url'];

            //2.сохраняем загруженные фото в папке на сервере
            $fileList=[];
            for ($i=0; $i< 5; $i++) {
                if ($_FILES['file'.($i+1)]['tmp_name']) {
                    $tmp='tmp/'.($i+1).'.jpg';
                    move_uploaded_file($_FILES['file'.($i+1)]['tmp_name'],$tmp);
                    $fileList['file'.($i+1)]= '@'. $tmp;
                }
            }
            //3.готовим запрос на загрузку фотографий на вк
            $curl = curl_init($uploadUrl );
            curl_setopt ($curl, CURLOPT_HEADER, false );
            curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt ($curl, CURLOPT_POST, true );
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $fileList);//array( 'file1' => '@' . 'tmp/111.jpg' ) );
            $data = curl_exec($curl);
            curl_close($curl);
            $files = json_decode( $data,true );
            $server=$files['server'];
            $fileList = stripslashes($files['photos_list']);
            $aid = $files['aid'];
            $hash = $files['hash'];
            //echo 'server '.$server.' foto '.print_r($fileList,true).' aid '.$aid.' hash '.$hash;
            //4. запрос на сохранение фото
            $hrefPost = 'https://api.vk.com/method/photos.save';
            $params = array (
                'album_id' => $aid,
                'access_token' => $token,
                'server' => $server,
                'photos_list' =>$fileList,
                'hash' => $hash,
            );
            $saveResult =  json_decode(getCurl($hrefPost,$params),true);
            $photos = $saveResult['response'];
            // 5. готовим фото к вложению в пост

            $attachPhotos='';
            foreach ($photos as $key => $photo){
                if ($key = 0) {
                    $attachPhotos .= $photo['id'];
                } else {
                    $attachPhotos .= ',' . $photo['id'];
                }
             }


        }





        //добавляем запись на стену
        $hrefPost = 'https://api.vk.com/method/wall.post';

        $params = array (
            'message'=> $_POST['text']
            ,'access_token' => $token
            ,'attachments' => (isset($attachPhotos))? $attachPhotos : '' //здесь список вложений, если они есть
        );
        $response = json_decode(getCurl($hrefPost,$params),true);
        if (isset($response['response']['post_id'])){
            echo 'Запись на стене размещена.';
        }

    }






   // плучим записи со стены
    $wallPosts = new VkFriends('https://api.vk.com/method/wall.get',$token);
    if (isset($_GET['page']) && $_GET['page']){
        $pageNum = $_GET['page'];
    } else {
        $pageNum = 0;
    }


    $posts = $wallPosts->getFriends('', $pageNum, 6, '', '');
    echo '<h3>Записи на моей стене</h3>';
    foreach ( $posts['persons'] as $key => $value) {
        if ($key == 0 ){
            continue;
        }
        echo '<div class="inline-block wallposts" >';
            echo '<div>Пользователь '.$value['from_id'].' '.$value['date'].' </div>';
            echo '<div>'.$value['text'].' </div>';
            if (isset($value['attachment']) && $value['attachment']['type'] == 'photo') {
                echo '<div class="wallpast__imagewrap"><img class="wallpast__image" src="'.$value['attachment']['photo']['src_big'].'"/> </div>';
            }
            if (isset($value['attachments'])) {
                foreach ($value['attachments'] as $attachment) {
                    if ($attachment['type']=='link') {
                        echo '<a class="wallpast__link-attachment" href="'.$attachment['link']['url'].'">'.$attachment['link']['title'].'</a>';
                    } elseif ($attachment['type']=='photo') {
                        echo '<img class="wallpast__image-attachment" src="'.$attachment['photo']['src_small'].'"/>';
                    }
                }
            }


        echo '</div>';
    }
    //формируем пагинацию

    function createPageButton($page,$current) {
        if ($page == ($current)) {
            echo  'Страница' . ($page+1);
        } else {
            $hrefnext = 'page=' . $page;
            echo '<a href="wall.php?' . $hrefnext . '" ><button>Страница' . ($page+1) . '</button></a>';
        }
    }
    echo '<div class="pagination">';
    for ($i=0; $i<=$posts['pages']['page']; $i++) {
        createPageButton($i,$posts['pages']['page']);
    }
    if ($posts['pages']['nextPage']) {
        createPageButton($posts['pages']['page']+1,$posts['pages']['page']);
    }
    echo '</div>';

    //echo '<pre>'.print_r($posts,true) .'</pre>';





}




?>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>