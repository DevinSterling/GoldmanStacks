<?
/* variables */
$broadcast = false;
$message = "Not indicative of the final product";

/* CSS */
$class = "sys-notification";

function notification(){ 
    global $broadcast, $message, $class;
    
    if ($broadcast === false) return;
    else echo "<div class=\"$class\">$message</div>";
}
?>
