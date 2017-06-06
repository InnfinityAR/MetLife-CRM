<?php

// 判断用户是否登录
function is_login() {
    return session("user_id") ? true : false;
}
