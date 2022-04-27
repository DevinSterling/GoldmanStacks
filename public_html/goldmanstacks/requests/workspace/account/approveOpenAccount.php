<?php
$object = (object)array();
$object->response = true;
$object->message = "Fetch API Success";

$json = json_encode($object);

echo $json;
