var showFriend = function() {
    var sessionId = 0,
        appId = 0,
        blockRectangle =  {
            top : 0,
            left: 0,
            height : 0,
            width : 0
        },
        mousePos = {
            top: 0,
            left: 0
        };

    var initModule = function (session,idApp) {
        sessionId = session;
        appId = idApp;
        console.log (sessionId,appId);
        document.addEventListener('mouseover',mouseOver,true);
        document.addEventListener('mouseout',mouseOut,true);
    };

    //выясняем, находится мышь внутри элемента, или снаружи
    var mouseIn = function (mouseX,mouseY) {
        return( (mouseX >= blockRectangle.left) & ( mouseX <= blockRectangle.left + blockRectangle.width)
        & (mouseY >= blockRectangle.top) & ( mouseY <= blockRectangle.top + blockRectangle.height));
    };

    //запись положения элемента  - запомнить, пока мышь находится в пределах элемента
    var writeElemRect = function (elem){
        blockRectangle.top = elem.offsetTop;
        blockRectangle.left = elem.offsetLeft;
        blockRectangle.height = elem.offsetHeight;
        blockRectangle.width = elem.offsetWidth;
    };
    //очищаем данные эапомненного элемента
    var clearElemRect = function () {
        blockRectangle.top = 0;
        blockRectangle.left = 0;
        blockRectangle.width = 0;
        blockRectangle.height = 0;
    };

    //задаём положение всплывающего окна на экране
    var setPos = function (div) {
        console.log( div.offsetWidth,div.offsetHeight);
        if ( mousePos.top + div.offsetHeight > document.body.clientHeight)  {
            div.style.top = document.body.clientHeight - div.offsetHeight+'px';
        } else {div.style.top = mousePos.top+'px'; }
        if ( mousePos.left + div.offsetWidth > document.body.clientWidth)  {
            div.style.left = document.body.clientWidth - div.offsetWidth+'px';
        } else {div.style.left = mousePos.left+'px'; }
    };

    //вывод информации о пользователе
    var showUser = function (user) {
        console.log(user);
        var div = document.createElement('div');
        html='';
        div.className = "popup-win";
        html = html+' <p>'+user.first_name+' '+user.last_name+'</p>';
        if (user.interests) {
            html = html +'<p>'+user.interests+'</p>';
        }
        if (user.university_name) {
            html = html +'<p>'+user.university_name+'</p>';
        }
        if (user.photo_200_orig) {
            html = html +'<img src="'+user.photo_200_orig+'">';
        }
        div.innerHTML = html;
        document.body.appendChild(div);
        setPos(div);//задаём положение блока на экране
    };

    //уничтожение всплывающего окна
    var hideUser = function () {
        var divs = document.getElementsByClassName('popup-win');
        //      console.log(divs);
        for (var i=0; i<divs.length; i++) {
            divs[i].remove();
        }
    };

    //информация про пользователя - получаем из vk
    var getUser = function (user) {
        //          //делаем запрос с
        console.log('Запрос',user);
        promise = new Promise (function(resolve,reject){
            var apiMethod = 'users.get',
                params = {
                    user_ids: user,
                    fields:  'bdate, city, country, home_town,  photo_200_orig, online, contacts, site, education, universities, status, last_seen, followers_count, common_count, occupation, nickname, relation, personal, connections, exports, wall_comments, activities, interests, music, movies, tv, books, about, quotes, is_favorite, screen_name, maiden_name, crop_photo, is_friend, friend_status, career, military'
                };
            VK.Api.call(apiMethod,params,(response) => {
                if (response.error) {
                console.log(response.error.message);
                reject(new Error(response.error.error_msg));
            }else {
                //console.log(response.response);
                showUser(response.response[0]);
            }
        })
        });
        //такой способ работает, но попробуем через промисы - смотри реализацию выше
//            VK.Api.call('users.get', {
//                    user_ids: user,
//                    fields:  'bdate, city, country, home_town,  photo_200_orig, online, contacts, site, education, universities, status, last_seen, followers_count, common_count, occupation, nickname, relation, personal, connections, exports, wall_comments, activities, interests, music, movies, tv, books, about, quotes, is_favorite, screen_name, maiden_name, crop_photo, is_friend, friend_status, career, military'
//                       },
//                    function(r) {
//                        if(r.response) {
//                        console.log(r.response);
//                        }
//            });
        //           console.log('завершение запроса');
    };

    /*photo_id, verified, sex, bdate, city, country, home_town, has_photo, photo_50, photo_100, photo_200_orig, photo_200, photo_400_orig, photo_max, photo_max_orig, online, lists, domain, has_mobile, contacts, site, education, universities, schools, status, last_seen, followers_count, common_count, occupation, nickname, relatives, relation, personal, connections, exports, wall_comments, activities, interests, music, movies, tv, books, games, about, quotes, can_post, can_see_all_posts, can_see_audio, can_write_private_message, can_send_friend_request, is_favorite, is_hidden_from_feed, timezone, screen_name, maiden_name, crop_photo, is_friend, friend_status, career, military, blacklisted, blacklisted_by_me*/

    var mouseOver = function (e) {
        var elem = e.target,
            className =elem.className;
        if (className.indexOf('friend-block')>-1) {
            if (!mouseIn(e.clientX, e.clientY)) {
                writeElemRect(elem);
                var userId = elem.getAttribute('data-id');
                //      console.log('Запись',userId);
                mousePos.top = e.clientY;
                mousePos.left = e.clientX;
                getUser(userId);//запрос из vk на юсера
            }//если мышь не внутри записанного элемента или отсутствующего, то записываем
        }
    };

    var mouseOut = function (e) {
        var elem = e.target;
        if (!mouseIn(e.clientX, e.clientY)) {
            clearElemRect();
            hideUser();
            //   console.log('выход');
            //              console.log(blockRectangle,e.clientX, e.clientY,'очистили')
        }//если мышь не внутри записанного элемента, то очищаем
    };

    return {init : initModule
    }
}();

VK.init({
    apiId: 5428835
});
function authInfo(response) {
    if (response.session) {
        // alert('user: '+response.session.mid);
        showFriend.init(response.session.mid);
    } else {
        //  alert('not auth');
    }
}
VK.Auth.getLoginStatus(authInfo);
//     VK.UI.button('login_button');