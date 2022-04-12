<?php
function checkNotEmpty(...$params) {
	foreach ($params as $param) {
		if (empty($param)) {
			return false;
		}
	}
	
	return true;
}