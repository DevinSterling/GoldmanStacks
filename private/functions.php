<?php
function checkNotEmpty(...$params) {
	foreach ($params as $param) {
		if (empty($param)) {
			return false;
		}
	}
	
	return true;
}

function convertToCurrency($amount) {
    $currency = '$';

    if ($amount < 0) { // Negative amount
        $currency = '-' . $currency . $amount * -1;
    } else { // Positive amount
        $currency = $currency . $amount;
    }

    return $currency;
}