<?php
session_start();
$_SESSION['reply'] = false;
$_SESSION['forward'] = false;
?>

<!DOCTYPE html>
<html>
<head>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}


/* Style the button that is used to open and close the collapsible content */
.collapsible {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 8px;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
}

/* Style the collapsible content. Note: hidden by default */
.content {
  padding: 0 18px;
  overflow: hidden;
  background-color: #f1f1f1;
}

/* Style the collapsible content actions. Note: hidden by default */
.actions {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
}

.msgs {
  display: none;
}

.button {
  color: #444;
  cursor: pointer;
}

/* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
.active, .activecontent:hover, .content:hover, .collapsible:hover {
  background-color: #ccc;
}
.activecontent {
  background-color: #DFDFDF;
}
</style>
</head>
<body>

  <form id='search' action='groupMessages.php' method='post' accept-charset='UTF-8'>
      <fieldset>
          <legend>Search Group Messages</legend>
          <input type='hidden' name='submitted' id='submitted' value='1'/>
          <label for='groupname'>Group Name:</label>
          <input type='text' name='groupname' id='groupnameID' maxlength="50"/>
          <label for='categoryname'>Category:</label>
          <input type='text' name='categoryname' id='categoryID' maxlength="50"/>
          <label for='messagecontent'>Message Content:</label>
          <input type='text' name='messagecontent' id='messagecontentID' maxlength="100"/>
          <input type='submit' name='Search' value='Search'/>
          <input type='submit' name='Clear' value='Clear'/>       

      </fieldset>
  </form>

<?php
// Echo session variables that were set on previous page
//print_r($_SESSION);
$user_id = $_SESSION['userID'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
//echo 'You are signed in as ' . $firstname .  ' ' . $lastname . '.';

include 'test_con.php';

try {
	// Connect to DB
  $pdo = connect();
  
  if(isset($_POST['Reply'])) {
    $_SESSION['reply'] = true;
    $_SESSION['reply_msg_id'] = trim($_POST['msg_id'], '/');
    $_SESSION['reply_user_id'] = null;
    $_SESSION['reply_group_id'] = trim($_POST['group_id'], '/');
    $_SESSION['reply_group_name'] = trim($_POST['group_name'], '/');
    $_SESSION['reply_user_fname'] = trim($_POST['creator_fname'], '/');
    $_SESSION['reply_user_lname'] = trim($_POST['creator_lname'], '/');
    header('Location: message.php');
  }
  if(isset($_POST['Forward'])) {
    $_SESSION['forward'] = true;
    $_SESSION['forward_msg_id'] = trim($_POST['msg_id'], '/');
    $_SESSION['forward_user_id'] = trim($_POST['creator_id'], '/');
    $_SESSION['forward_subject'] = trim($_POST['msg_subject'], '/');
    $_SESSION['forward_content'] = trim($_POST['msg_content'], '/');
    $_SESSION['forward_attach_id'] = trim($_POST['msg_attach_id'], '/');
    header('Location: message.php');
  }
  if(isset($_POST['Delete'])) {
    $msg_id =  trim($_POST['msg_id'], '/');
    $messages_delete = $pdo->prepare(
      "UPDATE message
      SET subject = '[DELETED]', message = '[DELETED]', attach_id = '[DELETED]'
      WHERE msg_id = ?;");
    $messages_delete->bindParam(1, $msg_id);
    $messages_delete->execute();
  }
  if(isset($_POST['View_Attachment'])) {
    $_SESSION['view_attach_id'] = trim($_POST['msg_attach_id'], '/');
    header('Location: viewAttach.php');
  }
  if(isset($_POST['Home'])) {
    header('Location: home.php');
  }
  
  if(isset($_POST['Search'])) {
    $searchGName = trim($_POST['groupname']);
    $searchCName = trim($_POST['categoryname']);
    //$searchMessageContent = trim($_POST['messagecontent']);
    $messages_query = $pdo->prepare("SELECT group_name, c_name, user_group.group_id
                                    FROM groups
                                        INNER JOIN category ON category.cat_id=groups.cat_id
                                        INNER JOIN user_group ON user_group.group_id=groups.group_id
                                    WHERE user_id = ?
                                    AND (group_name LIKE ? OR c_name LIKE ?);");
    $messages_query->bindParam(1, $user_id);
    $messages_query->bindParam(2, $searchGName);
    $messages_query->bindParam(3, $searchCName);

  } elseif (!isset($_POST['Search']) || isset($_POST['Clear'])) {
    $messages_query = $pdo->prepare("SELECT group_name, c_name, user_group.group_id
                                    FROM groups
                                        INNER JOIN category ON category.cat_id=groups.cat_id
                                        INNER JOIN user_group ON user_group.group_id=groups.group_id
                                    WHERE user_id = ?");
    $messages_query->bindParam(1, $user_id);

  }

  //Execute query
  $messages_query->execute();
  $result = $messages_query->fetchAll();
  
    
    //echo print_r($result);
	/* fetch values */
	echo "
	<h2>Recieved Messages</h2>
	<table>
		<tr>
			<th width=20%>Sending Info</th>
			<th>Group</th>
			<th>Category</th>
        </tr>";
	foreach ( $result as $row ) {
    $groupID = $row['group_id'];
    if(isset($_POST['Search'])) {
      $searchMessageContent = trim($_POST['messagecontent']);
      $messages_query2 = $pdo->prepare("SELECT DISTINCT message.create_date, sender.first_name, sender.last_name, subject, message, message.msg_id, sender.user_id, attach_id
                                      FROM msg_recipient
                                        INNER JOIN message ON message.msg_id = msg_recipient.msg_id
                                        INNER JOIN user sender ON sender.user_id = message.creator_id
                                        INNER JOIN groups ON group_id=recipient_group_id
                                      WHERE (recipient_id = ? OR creator_id = ? ) AND group_id = ?
                                      AND message LIKE ?
                                      ORDER BY message.create_date DESC;");                  
      $messages_query2->bindParam(1, $user_id);
      $messages_query2->bindParam(2, $user_id);
      $messages_query2->bindParam(3, $groupID);
      $messages_query2->bindParam(4, $searchMessageContent);
  
    } elseif (!isset($_POST['Search']) || isset($_POST['Clear'])) {
      $messages_query2 = $pdo->prepare("SELECT DISTINCT first_name, last_name, message.create_date, subject, message, message.msg_id, user.user_id, attach_id
                                      FROM msg_recipient
                                        INNER JOIN message ON message.msg_id = msg_recipient.msg_id
                                        INNER JOIN user ON user.user_id = message.creator_id
                                        INNER JOIN groups ON groups.group_id=msg_recipient.recipient_group_id
                                      WHERE (recipient_id = ? OR creator_id = ? ) AND group_id = ?
                                      ORDER BY message.create_date DESC;");
      $messages_query2->bindParam(1, $user_id);
      $messages_query2->bindParam(2, $user_id);
      $messages_query2->bindParam(3, $groupID);
    }
    //Execute Query
    $messages_query2->execute();
    $result2 = $messages_query2->fetchAll();
    //echo print_r($result2);
    echo "
    <tbody class='msg'>
			<tr class='collapsible'>
				<td></td>
				<td>", $row['group_name'], "</td>
				<td>", $row['c_name'], "</td>
      </tr>
      <tbody class='msgs'>";
		foreach ($result2 as $msg){
      //echo $row['group_name'];
      echo "
      <tr class='content'>
                <td width=20% colspan='1'>", $msg['first_name'], " ", $msg['last_name'], ": ", $msg['create_date'], "</td>
                <td colspan='2'>", $msg['subject'], ": ", $msg['message'], "</td>
            </tr>
      <tr class='actions'>
        <td colspan='3'>
          <form id='message_actions' action='groupMessages.php' method='post' accept-charset='UTF-8'>
            <fieldset>
              <legend>Message Actions</legend>
                <input type='hidden' name='msg_id' value=", $msg['msg_id'] ,"/>
                <input type='hidden' name='creator_id' value=", $msg['user_id'] ,"/>
                <input type='hidden' name='creator_fname' value=", $msg['first_name'] ,"/>
                <input type='hidden' name='creator_lname' value=", $msg['last_name'] ,"/>
                <input type='hidden' name='msg_subject' value=", $msg['subject'] ,"/>
                <input type='hidden' name='msg_content' value=", $msg['message'] ,"/>
                <input type='hidden' name='msg_attach_id' value=", $msg['attach_id'] ,"/>
                <input type='hidden' name='group_id' value=", $row['group_id'] ,"/>
                <input type='hidden' name='group_name' value=", $row['group_name'] ,"/>
                <input class='button' type='submit' name='Reply' value='Reply'/>
                <input class='button' type='submit' name='Forward' value='Forward'/>
                <input class='button' type='submit' name='Delete' value='Delete'/>";
                if ($msg['attach_id'] != null) echo "<input type='submit' name='View_Attachment' value='View Attachment'/>";
            echo "</fieldset>
          </form>
        </td>
      </tr>";
    }
    echo "</tbody></tbody>";
	}
	echo "
	</table>";
    //$messages_query->bind_result($date, $fname, $lname, $subject, $message);
	
     /* fetch values */
    //while ($messages_query->fetch()) {
    //    printf ("%s %s\n", $fname, $lname);
    //}
	
	
} catch(PDOException $e) {
    echo $e;
}
?>
<script>
var groups = document.getElementsByClassName("msg");
var coll = document.getElementsByClassName("collapsible");
var coll2 = document.getElementsByClassName("content");
var i;

for (i = 0; i < groups.length; i++) {
  groups[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var msgs = this.nextElementSibling;
    if (msgs.style.display === "table-row-group") {
      msgs.style.display = "none";
    } else {
      msgs.style.display = "table-row-group";
    }
  });
}
for (i = 0; i < coll2.length; i++){
  coll2[i].addEventListener("click", function() {
    this.classList.toggle("activecontent");
    var actions = this.nextElementSibling;
    if (actions.style.display === "table-row") {
      actions.style.display = "none";
    } else {
      actions.style.display = "table-row";
    }
  });
}
</script>
  <br><br>
  <form id='buttons' action='home.php' method='post'>
    <input type='submit' name='home' value='Home'/>
  </form>

</body>
</html>
