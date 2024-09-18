<?php


use GlpiPlugin\Action1\Action1;

include ('../../../inc/includes.php');

if ($_SESSION["glpiactiveprofile"]["interface"] == "central") {
   Html::header("TITRE", $_SERVER['PHP_SELF'], "plugins", Action1::class, "");
} else {
   Html::helpHeader("TITRE", $_SERVER['PHP_SELF']);
}


//checkTypeRight(Example::class,"r");

//Search::show(Example::class);

echo '<iframe src="https://app.action1.com/login/" width="600" height="400" frameborder="0" allowfullscreen></iframe>';
echo '</div>';

Html::footer();