<?php

wp_head();
echo "<pre>";
echo "\n==== head above ===";
echo "\nbody class: ";
$soup->bodyClass();
echo "\npost class 1: ";
$soup->postClass();
echo "\npost class 2: ";
$soup->postClass();
echo "\n==== foot above ===\n";
echo "</pre>";
wp_footer();
?>