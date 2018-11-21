INSERT INTO user (user_id, first_name, last_name)
VALUES((SELECT (MAX(user_id) + 1) FROM user), "Bob", "Saget");

UPDATE user
SET first_name = "Bobby", last_name = "Newport"
WHERE user_id = 10;

UPDATE user
SET first_name = "[Deleted]", last_name = "[Deleted]"
WHERE user_id = 10;

INSERT INTO category (cat_id, c_name)
VALUES ((SELECT (MAX(cat_id) + 1) FROM category), "Test category");

UPDATE category
SET c_name = "Social2"
WHERE cat_id = 11;

INSERT INTO groups (group_id, group_name, cat_id)
VALUES ((SELECT (MAX(group_id) + 1) FROM groups), "CSE 120",  1);

INSERT INTO message (msg_id, subject, message, creator_id, create_date, parent_msg_id, attach_id)
VALUES((SELECT (MAX(msg_id) +1) FROM message), "TEST MESSAGE SUBJECT", "MESSAGE TEXT FOR TEST MESSAGE", 4, 2018-11-07, 0, 0);
	
INSERT INTO msg_recipient (msg_rec_id, recipient_id, recipient_group_id, msg_id)
VALUES((SELECT MAX(msg_rec_id) + 1 FROM msg_recipient), 5, 0, testMsgId);

INSERT INTO msg_recipient (msg_rec_id, recipient_id, recipient_group_id, msg_id)
VALUES((SELECT MAX(msg_rec_id) + 1 FROM msg_recipient), 0, 1, testMsgId);

INSERT INTO message (msg_id, subject, message, creator_id, create_date, parent_msg_id, attach_id)
VALUES((SELECT (MAX(msg_id) +1) FROM message), "Respond to test message", "Message text for response to test message", 5, 2018-11-07, testMsgId, 0);

SELECT message.create_date, sender.first_name, sender.last_name, subject, message
FROM msg_recipient
INNER JOIN message ON message.msg_id = msg_recipient.msg_id
INNER JOIN user sender ON sender.user_id = message.creator_id
WHERE recipient_id = 4
ORDER BY message.create_date DESC;

SELECT sender.first_name, sender.last_name, subject, message
FROM msg_recipient
INNER JOIN message ON message.msg_id = msg_recipient.msg_id
INNER JOIN user sender ON sender.user_id = message.creator_id
WHERE recipient_id = 4 AND sender.first_name Like "Peyton";

SELECT DISTINCT sender.first_name, sender.last_name, subject, message
FROM msg_recipient
INNER JOIN message ON message.msg_id = msg_recipient.msg_id
INNER JOIN user sender ON sender.user_id = message.creator_id
WHERE recipient_id=4 AND message LIKE "%19zjt9z%";

SELECT DISTINCT group_name
FROM groups, category, user, user_group
WHERE c_name = "Sports"
 AND groups.cat_id = category.cat_id
 AND user_group.group_id = groups.group_id
 AND user_group.user_id = user.user_id
 AND user.user_id = 4;

SELECT message.create_date, sender.first_name, sender.last_name, subject, message
FROM msg_recipient
INNER JOIN message ON message.msg_id = msg_recipient.msg_id
INNER JOIN user sender ON sender.user_id = message.creator_id
WHERE recipient_id = 4 AND create_date BETWEEN "2018-11-06" AND "2018-11-08"
ORDER BY message.create_date DESC;

SELECT message
FROM message, user
WHERE creator_id = user_id
     AND user_id = 4
ORDER BY create_date DESC;

SELECT first_name
FROM user, user_group, groups
WHERE user.user_id = user_group.user_id
     AND user_group.group_id = groups.group_id
     AND groups.group_id = 1;

INSERT INTO user_group (user_id, user_group_id, group_id)
VALUES (4, (SELECT (MAX(user_group_id) + 1) FROM user_group), 1);

SELECT group_name
FROM groups, user_group, user
WHERE user.user_id = user_group.user_id
     AND user_group.group_id = groups.group_id
     AND user.user_id = 4;

SELECT message, attach_msg
FROM message, attachment, user
WHERE message.attach_id = attachment.attach_id
     AND creator_id = user_id
     AND user_id = 4;
