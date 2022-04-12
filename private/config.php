<?php
// Database
const HOST = "";
const DATABASE = "";

// Database Accounts
const QUERY_USERNAME = ""; // Used when querying rows
const QUERY_PASSWORD = "";
const INSERT_USERNAME = ""; // Used when inserting rows
const INSERT_PASSWORD = "";
const UPDATE_USERNAME = ""; // Used when updating rows
const UPDATE_PASSWORD = "";
const DELETE_USERNAME = ""; // Used when deleting rows
const DELETE_PASSWORD = "";

// Database Connections
function getQueryConnection() {
    $conn = new mysqli(HOST, QUERY_USERNAME, QUERY_PASSWORD, DATABASE);
    
    if (mysqli_connect_errno()) { // If true, db connection failed
        return null;
    }
    
    return $conn;
}

function getInsertConnection() {
    $conn = new mysqli(HOST, INSERT_USERNAME, INSERT_PASSWORD, DATABASE);
    
    if (mysqli_connect_errno()) { // If true, db connection failed
        return null;
    }
    
    return $conn;
}

function getUpdateConnection() {
    $conn = new mysqli(HOST, UPDATE_USERNAME, UPDATE_PASSWORD, DATABASE);
    
    if (mysqli_connect_errno()) { // If true, db connection failed
        return null;
    }
    
    return $conn;
}

function getDeleteConnection() {
    $conn = new mysqli(HOST, DELETE_USERNAME, DELETE_PASSWORD, DATABASE);
    
    if (mysqli_connect_errno()) { // If true, db connection failed
        return null;
    }
    
    return $conn;
}
?>
