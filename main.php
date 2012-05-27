<?php
/*
 * plugin name: Google Calender with Wordpress Posts debug
 * author: Mahibul Hasan Sohag
 * author uri: http://sohag07hasan.elance.com
 * plugin uri: http://sohag07hasan.elance.com
 * Description: Creates a nice interface to control the google calender with the posts, pages and custom posts
 * version: 1.1.0
 */

define('GCALENDERDIR', dirname(__FILE__));
define("GCALENDERURL", plugins_url('', __FILE__));

include GCALENDERDIR . '/classes/Gcalender.class.php';
include GCALENDERDIR . '/classes/gc.class.php';
Gc_Integration :: init();