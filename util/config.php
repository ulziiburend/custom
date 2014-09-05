<?php

session_start();
define ("ROOT" , $_SERVER["DOCUMENT_ROOT"]."/custom");

require_once ROOT.'/util/db.php';
//require_once ROOT.'/server_json/functions/other_err.php';
require_once ROOT.'/util/error_func.php';
