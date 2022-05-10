<?php
require_once('../config.php');

$db = getUpdateConnection();

if ($db !== null) {
    /* Update balance of all savings accounts using interest rate */
    $updateBalance = $db->prepare("UPDATE accountDirectory A INNER JOIN savings S ON A.accountNum=S.accountNum SET A.balance=A.balance*(S.interestRate/100+1)");
    
    if ($db->affected_rows === 0) {
        /* Write log file */
    }
    
    $db->close();
}
