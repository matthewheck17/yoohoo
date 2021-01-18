CREATE TABLE users (
	user_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20),
    password VARCHAR(20),
    imageFileName VARCHAR(100)
);

CREATE TABLE messages (
	message_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sendTime DATETIME NOT NULL,
    content VARCHAR(500),
    sender_id int,
    recipient_id int,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (recipient_id) REFERENCES users(user_id)
);

ALTER TABLE users
ADD UNIQUE (user_id);

INSERT INTO users (username, password, imageFileName)
VALUES ('global','global','global.png');
