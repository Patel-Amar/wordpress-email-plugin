<?php

if (!defined('ABSPATH')){
    exit;
 }

function createMetaBox() {
    add_meta_box( "mailing-list-info", "Email Info", "displayEmail" , "mailing_list");
    add_meta_box( "mailing-list-rec", "Recipients", "readUnread" , "mailing_list");
}
 function displayEmail() {
    $Content = get_post_meta( get_the_ID(), "Contents", true);
    $Subject = get_post_meta( get_the_ID(), "Subject", true);

    echo "<ul>";
    echo "<li><strong>" . "Email-Content" . " </strong>:<br /> " . $Content . "</li>";
    echo "<li><strong>" . "Email-Subject" . " </strong>:<br /> " . $Subject . "</li>";
    echo "</ul>";
}


#The code that displays whether an email has been read or not. Getting the actuall read/unread status is gathered from tracker.php
function readUnread() {
    echo "<table style='width: 100%; border: 2px solid black; border-collapse: collapse;'>";
    echo "<th style='text-align:center; border: 2px solid black; border-collapse: collapse;'> Email</th> 
            <th style='text-align:center; border: 2px solid black; border-collapse: collapse;'>Name</th> 
            <th style='text-align:center; border: 2px solid black; border-collapse: collapse;'>Status</th>";

    foreach (get_post_meta( get_the_ID()) as $key => $value) {
        $email = get_post_meta( get_the_ID(), $key, false);
        if (gettype($email[0]) == "array" and array_key_exists("Email", $email[0])) {
            echo "<tr style='text-align:center; border: 2px solid black'>";
            echo "<td style='text-align:center; border: 2px solid black; border-collapse: collapse;'>" . $email[0]["Email"] . 
            "</td> <td style='text-align:center; border: 2px solid black; border-collapse: collapse;'>" . $email[0]["Name"] . "</td>"; 
            if ($email[0]["Status"] == "Read") {
                echo "<td style='text-align:center; border: 2px solid black; border-collapse: collapse; background-color:#BAFF66;'>" . $email[0]["Status"] . "</td>";
            }
            else {
                echo "<td style='text-align:center; border: 2px solid black; border-collapse: collapse; background-color:#FF7276;'>" . $email[0]["Status"] . "</td>";
            }
            echo "</tr>";
        }
    }
    echo "</table>";
}


?>