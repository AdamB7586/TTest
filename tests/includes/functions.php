<?php

/**
 * Create a header redirect for the page
 * @param string $url this is the URL of the location you wish to redirect the page to
 * @param boolean $permanent if true set the header to say the page has moved permanently (default false)
 * @return void
 */
function redirect($url, $permanent = false)
{
    if ($permanent == true) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header('Location: '.$url);
    exit;
}
