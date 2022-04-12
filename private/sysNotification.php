<?
/* variables */
$broadcast = false;
$message = "Not indicative of the final product";

/* CSS */
$class = "sys-notification";

function notification(){ 
    global $broadcast, $message, $class;
    
    if ($broadcast) echo "<div class=\"$class\">$message</div>";
}
?>
