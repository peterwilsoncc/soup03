<?php

get_header();
echo "<pre>";
echo "\n==== head above ===";
echo "\nbody class: ";
body_class();
the_post();
echo "\npost class 1: ";
post_class();
the_post();
echo "\npost class 2: ";
post_class();
echo "\n==== foot below ===\n";
echo "</pre>";
wp_footer();
echo "<pre>";
echo "\n==== foot above ===\n";
echo "\nqueries: ";
echo get_num_queries();
echo "\ntime: ";
timer_stop(1);
echo "</pre>";
?>