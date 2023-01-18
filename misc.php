<?php
define("MSG_EXTENSION_LOADED_SUCCESS",  "✅MongoDB Extension loaded | Method #1");
define("MSG_EXTENSION_LOADED_FAIL",     "❌MongoDB Extension not loaded | Method #1");
define("MSG_EXTENSION_LOADED2_SUCCESS", "✅MongoDB PHP Extension loaded  | Method #2");
define("MSG_EXTENSION_LOADED2_FAIL",    "❌MongoDB PHP Extension not loaded | Method #2");

define("MSG_LIBRARY_PRESENT",           "✅MongoDB PHP Library is available");
define("MSG_LIBRARY_MISSING",           "❌MongoDB PHP Library not included");

define("MSG_CLIENT_SUCCESS",            "✅MongoDB\Client created");
define("MSG_CLIENT_FAIL",               "❌Could not create MongoDB\Client");

define("MSG_CLIENT_AUTH_SUCCESS",       "✅user/password Authenticated");

define("MSG_EXCEPTION",                 "❌Exception:");

define("MSG_DATABASE_FOUND",            "✅Found desired database");
define("MSG_DATABASE_NOT_FOUND",        "❌Could not find desired database");
define("MSG_COLLECTION_FOUND",          "✅Found desired collection");
define("MSG_COLLECTION_NOT_FOUND",      "❌Could not find desired collection");

define("MSG_INSERTONE_SUCCESS",         "✅insertOne() success");
define("MSG_INSERTONE_FAIL",            "❌insertOne() exception");

define("MSG_FINDONE_SUCCESS",           "✅findOne() success");

define("MSG_DELETEONE_SUCCESS",         "✅deleteOne() success");

function c_echo( $text ) {
    echo('Checker | '.$text."<br>");
}
?>