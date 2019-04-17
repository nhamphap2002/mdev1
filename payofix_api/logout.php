<?php

/*
 * Created on : Apr 8, 2019, 9:33:41 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */
include_once 'config.php';
unset($_SESSION["login"]);
header('Location: login.php');
