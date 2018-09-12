# 表索引

CREATE TABLE file_index (
	id INT (10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	path VARCHAR (225) NOT NULL DEFAULT '',
	created_at datetime NOT NULL,
	updated_at datetime NOT NULL,
	access_at datetime NOT NULL
);