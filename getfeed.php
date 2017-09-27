<?php
    header("Content-type:text/xml");
    $feed = file_get_contents("https://www.amazon.in/rss/bestsellers/books?tag=my-assoc-tag");
    echo $feed;
?>