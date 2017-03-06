CREATE TABLE implants (`hostname` TEXT, `current_user` TEXT, `process_id` TEXT, `os` TEXT, `last_seen` TEXT);
CREATE TABLE users (`username` TEXT, `hashed_password` TEXT, `salt` TEXT);
CREATE TABLE listeners (`external_address` TEXT, `protocol` TEXT, `port` TEXT, `beacon_uri` TEXT, `update_uri` TEXT, `executed_out_of` TEXT, `crt_file` TEXT, `key_file` TEXT);
CREATE TABLE tasks (`task_id` INT AUTO_INCREMENT, `hostname` TEXT, `process_id` TEXT, `task_action` TEXT, `task_secondary` TEXT, PRIMARY KEY (task_id));

INSERT INTO implants (`hostname`, `current_user`, `process_id`, `os`, `last_seen`) VALUES ("Hunter's iMac", "user", "1781", "Mac OS X 10.8", "2017-02-15 01:32:17");
INSERT INTO implants (`hostname`, `current_user`, `process_id`, `os`, `last_seen`) VALUES ("Hunter's iMac", "user", "2053", "Mac OS X 10.8", "2017-02-15 01:33:10");
INSERT INTO implants (`hostname`, `current_user`, `process_id`, `os`, `last_seen`) VALUES ("t3ntman's MacBook Pro", "t3ntman", "6429", "Mac OS X 10.9", "2017-02-15 10:26:49");
INSERT INTO listeners (`external_address`, `protocol`, `port`, `beacon_uri`, `update_uri`) VALUES ("56.123.4.53", "http", "8080", "/index.aspx", "/home.aspx");
