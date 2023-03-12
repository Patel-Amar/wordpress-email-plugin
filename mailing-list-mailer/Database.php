<?php

if (!defined('ABSPATH')){
    exit;
 }

 define("servername", "localhost:3307"); 
 define("username", "root");
 define("password", "");
 define("dbname", "wordpress");

 #Gets all of the events in the events database
function getAllEvents() {
  $allEventTypes = getUsers("wp_em_events", "event_slug");
  $out = array();
  foreach ($allEventTypes as $group) {
   $out[$group] = encodeData(getUserEmail("wp_em_events", "wp_em_bookings", "wp_users", "event_id", "event_id", "person_id", "event_slug", "user_email", $group));
  }
  return $out;
}

#Gets all of the member types in the membership database
function getAllMembershipTypes() {
  $allMembershipTypes = getUsers("wp_restrict_content_pro", "name");
  $out = array();
  foreach ($allMembershipTypes as $group) {
   $out[$group] = encodeData(getUserEmail("wp_restrict_content_pro", "wp_rcp_memberships", "wp_users", "object_id", "id", "user_id", "name", "user_email", $group));
  }
  return $out;
}

#Gets all of the users in the system
function getAllUsers() {
  $allUsers = getUsers("wp_users", "display_name");
  $out = array();
  foreach ($allUsers as $group) {
   $out[$group] = encodeData(getEmailFromDirectUser($group));
  }
  return $out;
}

#Gets the emails needed for the Everyone selection
function getEveryoneEmail() {
  $allUsers = getEmailForEveryone();
  $out["Everyone"] = encodeData($allUsers);
  return $out;
}

#Gets the users on the user database
function getUsers($tableName, $columnName) {
  $conn = new mysqli(servername, username, password, dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT " . $columnName . " FROM " . $tableName;

  $result = $conn->query($sql);

  $listOfGroups = array();
  if ($result ->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      array_push($listOfGroups, encodeData($row[$columnName]));
    }
  }

  $conn->close();
  return $listOfGroups;
}

#Gathers all of the different email groups/emails to be called by the MailingForm
function getAllEmailGroups() {
  $everyone = getEveryoneEmail();
  $membershipTypes = getAllMembershipTypes();
  $eventTypes = getAllEvents();
  $users = getAllUsers();
  $masterList = array_merge($everyone, $membershipTypes, $eventTypes, $users);
  return $masterList;
}

#Puts all of the emails in the system into one array
function getEmailForEveryone() {
  $conn = new mysqli(servername, username, password, dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $listOfEmails = array();

  $sql = "SELECT user_email,display_name FROM wp_users";

  $result = $conn->query($sql);
  if ($result ->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      array_push($listOfEmails, array("Name" => $row["display_name"], "Email" => $row["user_email"]));
    }
  }

  $conn->close();
  $output = array("Info" => $listOfEmails);
  return $output;
}


#Gets an email for a specific user
function getEmailFromDirectUser($userName) {
  $conn = new mysqli(servername, username, password, dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $listOfEmails = array();

  $sql = "SELECT user_email,display_name FROM wp_users WHERE display_name = " . $userName;

  $result = $conn->query($sql);
  if ($result ->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      array_push($listOfEmails, array("Name" => $row["display_name"], "Email" => $row["user_email"]));
    }
  }

  $conn->close();
  $output = array("Info" => $listOfEmails);
  return $output;
}

#The first method to retrieving an email. Takes a user id and gets the user email
function getUserEmail($eventIDTableName, $personIDTableName, $userTableName, $eventIDColumnNameInPersonTable, $eventIDColumnNameInTypeTable,
   $personIDColumnName, $eventNameColumnName, $emailColumn, $emailGroup) {
  $conn = new mysqli(servername, username, password, dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $personId = getPersonId($eventIDTableName, $personIDTableName, $eventIDColumnNameInPersonTable, $eventIDColumnNameInTypeTable, $personIDColumnName, $eventNameColumnName, $emailGroup);
  $listOfEmails = array();

  foreach ($personId as $person) {
    $sql = "SELECT user_email,display_name FROM " . $userTableName . " WHERE ID = " . $person;

    $result = $conn->query($sql);
    if ($result ->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        array_push($listOfEmails, array("Name" => $row["display_name"], "Email" => $row["user_email"]));
      }
    }
  }
  $conn->close();
  $output = array("Info" => $listOfEmails);
  return $output;
}

#The second method to retrieving an email. Takes a event/membership ip and gets the user id associated with it
function getPersonId($eventIDTableName, $personIDTableName, $eventIDColumnNameInPersonTable, $eventIDColumnNameInTypeTable, $personIDColumnName, $eventNameColumnName, $emailGroup) {
  $conn = new mysqli(servername, username, password, dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $eventId = getTypeIds($eventIDTableName, $eventIDColumnNameInTypeTable, $eventNameColumnName, $emailGroup);
  $sql = "SELECT " . $personIDColumnName . " FROM " . $personIDTableName . " WHERE " . $eventIDColumnNameInPersonTable . " = " . $eventId;

  $result = $conn->query($sql);

  $personId = array();
  if ($result ->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      array_push($personId, $row[$personIDColumnName]);
    }
  }

  $conn->close();
  return $personId;
}

#The third method to retrieving an email. Takes a event/membership name and gets the id associated with it
function getTypeIds($tableName, $idColumnName, $typeColumnName, $emailGroup) {
  $conn = new mysqli(servername, username, password, dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT " . $idColumnName . " FROM " . $tableName . " WHERE " . $typeColumnName . " = " . $emailGroup;

  $result = $conn->query($sql);

  $eventId = "";
  if ($result ->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $eventId = $row[$idColumnName];
    }
  }

  $conn->close();
  return $eventId;
}

function encodeData($data) {
  return json_encode($data);
}

?>