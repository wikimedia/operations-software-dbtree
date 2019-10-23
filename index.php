<?php

include 'inc/config.php';
include 'inc/sanity.php';

set_error_handler(

    function($errno, $message, $file, $line, $context)
    {
        if ($errno === E_WARNING) {
            backtrace();
        }
        return false;
    }
);

# return http 503 if database connection fails to prevent
# error pages from being cached in varnish (T163143)
function db_fail($db_name, $db_host) {
    header($_SERVER['SERVER_PROTOCOL'] . ' Service Unavailable', true, 503);
    die('database connection to ' . $db_name . ' on ' . $db_host . 'failed');
}

function db()
{
    static $db = null;
    global $db_host, $db_user, $db_pass, $db_name;
    if (is_null($db) || !is_resource($db))
    {
        e("db connect: host");
        $db = @mysqli_connect($db_host, $db_user, $db_pass)
            or db_fail($db_name, $db_host);
        mysqli_select_db($db_name);
    }
    return $db;
}

include 'inc/tree.php';

$tree = new Tree();
list ($clusters) = $tree->generate();

include 'inc/template.php';
