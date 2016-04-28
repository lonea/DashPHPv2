<?php
//Referrer dection
$pattern = array('');

foreach ($pattern as $item){
    if (preg_match($item, $_SERVER["HTTP_REFERER"])){
        $smarty->assign("referred", true);
        $smarty->assign("referItem", $item);
    }
}

?>