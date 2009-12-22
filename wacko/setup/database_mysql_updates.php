<?php

/*
	Wacko Wiki MySQL Table Updates Script

	These are all the updates that need to applied to earlier Wacko version to bring them up to 4.3 specs
*/

$pref = $config["table_prefix"];

// ACL
$alter_acls_r2_1 = "ALTER TABLE {$pref}acls ADD supertag VARCHAR(250) NOT NULL DEFAULT '', CHANGE page_tag page_tag VARCHAR(250) NOT NULL, ADD INDEX(supertag)";
$alter_acls_r3_1 = "ALTER TABLE {$pref}acls CHANGE page_tag page_tag VARCHAR(250) BINARY NOT NULL";
$alter_acls_r4_2 = "ALTER TABLE {$pref}acls ADD page_id INT(10) UNSIGNED NOT NULL AFTER page_tag";
$alter_acls_r4_2_1 = "ALTER TABLE {$pref}acls CHANGE privilege privilege VARCHAR(10) NOT NULL";
$alter_acls_r4_2_2 = " ALTER TABLE {$pref}acls DROP PRIMARY KEY";
$alter_acls_r4_2_3 = " ALTER TABLE {$pref}acls ADD UNIQUE idx_page_id (page_id,privilege)";
$alter_acls_r4_2_4 = "ALTER TABLE {$pref}pages DROP page_tag";
$alter_acls_r4_2_5 = "ALTER TABLE {$pref}pages DROP supertag";

$update_acls_r4_2 = "UPDATE {$pref}acls AS acls, (SELECT id, tag FROM {$pref}pages) AS pages SET acls.page_id = pages.id WHERE acls.page_tag = pages.tag";

// CONFIG
$table_config_r4_2 = "CREATE TABLE {$pref}config (".
					"id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,".
					"name VARCHAR(100) NOT NULL DEFAULT '',".
					"value TEXT,".
					// "updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,".
					"PRIMARY KEY (id),".
					"UNIQUE KEY name (name)".
				") TYPE=MyISAM";

// CACHE
$alter_cache_r4_2 = "ALTER TABLE {$pref}cache ADD time TIMESTAMP NOT NULL, ADD INDEX timestamp (time)";

// LINKS
$alter_links_r2_1 = "ALTER TABLE {$pref}links CHANGE from_tag from_tag VARCHAR(250) NOT NULL, CHANGE to_tag to_tag VARCHAR(250) NOT NULL";
$alter_links_r3_1 = "ALTER TABLE {$pref}links CHANGE from_tag from_tag CHAR(250) BINARY NOT NULL";
$alter_links_r3_2 = "ALTER TABLE {$pref}links CHANGE to_tag to_tag CHAR(250) BINARY NOT NULL";
$alter_links_r3_3 = "ALTER TABLE {$pref}links ADD to_supertag VARCHAR(250) NOT NULL";
$alter_links_r4_2 = "ALTER TABLE {$pref}links ADD id INT(10) UNSIGNED NOT NULL auto_increment FIRST, ADD PRIMARY KEY (id)";
$alter_links_r4_2_1 = "ALTER TABLE {$pref}links ADD from_page_id INT(10) UNSIGNED NOT NULL AFTER from_tag";
$alter_links_r4_2_2 = "ALTER TABLE {$pref}links ADD to_page_id INT(10) UNSIGNED NOT NULL AFTER to_tag";

$update_links_r4_2 = "UPDATE {$pref}links AS links, (SELECT id, tag FROM {$pref}pages) AS pages SET links.from_page_id = pages.id WHERE links.from_tag = pages.tag";
$update_links_r4_2_1 = "UPDATE {$pref}links AS links, (SELECT id, tag FROM {$pref}pages) AS pages SET links.to_page_id = pages.id WHERE links.to_tag = pages.tag";

// LOG
$table_log_r4_2 = "CREATE TABLE {$pref}log (".
				"id INT(10) UNSIGNED NOT NULL auto_increment,".
				"time TIMESTAMP NOT NULL,".
				"level TINYINT(1) NOT NULL,".
				"user VARCHAR(100) NOT NULL,".
				"ip VARCHAR(15) NOT NULL,".
				"message TEXT NOT NULL,".
				"PRIMARY KEY (id),".
				"KEY idx_level (level),".
				"KEY idx_user (user),".
				"KEY idx_ip (ip),".
				"KEY idx_time (time)".
			") TYPE=MyISAM";

// PAGES
$alter_pages_r0_1 = "ALTER TABLE {$pref}pages ADD body_r TEXT NOT NULL DEFAULT '' AFTER body";
$alter_pages_r2_1 = "ALTER TABLE {$pref}pages ADD supertag VARCHAR(250) NOT NULL DEFAULT '' after tag, CHANGE tag tag VARCHAR(250) NOT NULL, ADD INDEX supertag (supertag)";
$alter_pages_r2_2 = "ALTER TABLE {$pref}pages DROP INDEX idx_tag, ADD UNIQUE idx_tag (tag)";
$alter_pages_r3_1 = "ALTER TABLE {$pref}pages DROP INDEX fts";
$alter_pages_r3_2 = "ALTER TABLE {$pref}pages DROP INDEX body";
$alter_pages_r3_3 = "ALTER TABLE {$pref}pages DROP INDEX tag";
$alter_pages_r3_4 = "ALTER TABLE {$pref}pages ADD FULLTEXT (body)";
$alter_pages_r3_5 = "ALTER TABLE {$pref}pages CHANGE tag tag VARCHAR(250) BINARY NOT NULL";
$alter_pages_r3_6 = "ALTER TABLE {$pref}pages CHANGE comment_on comment_on VARCHAR(250) BINARY NOT NULL";
$alter_pages_r3_7 = "ALTER TABLE {$pref}pages ADD hits INT DEFAULT '0' NOT NULL";
$alter_pages_r3_8 = "ALTER TABLE {$pref}pages ADD super_comment_on VARCHAR(250) NOT NULL AFTER comment_on";
$alter_pages_r3_9 = "ALTER TABLE {$pref}pages ADD lang VARCHAR(10) NOT NULL";
$alter_pages_r3_10 = "ALTER TABLE {$pref}pages ADD description VARCHAR(250) NOT NULL DEFAULT ''";
$alter_pages_r3_11 = "ALTER TABLE {$pref}pages ADD keywords VARCHAR(250) BINARY NOT NULL DEFAULT ''";
$alter_pages_r3_12 = "ALTER TABLE {$pref}pages ADD body_toc TEXT NOT NULL DEFAULT '' AFTER body_r";
$alter_pages_r4_2_1 = "ALTER TABLE {$pref}pages MODIFY COLUMN body MEDIUMTEXT NOT NULL";
$alter_pages_r4_2_2 = "ALTER TABLE {$pref}pages MODIFY COLUMN body_r MEDIUMTEXT NOT NULL";
$alter_pages_r4_2_3 = "ALTER TABLE {$pref}pages ADD created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER supertag, ADD INDEX idx_created (created), DROP INDEX idx_latest";
$alter_pages_r4_2_4 = "ALTER TABLE {$pref}pages ADD title VARCHAR(100) NOT NULL DEFAULT '' AFTER lang, ADD INDEX idx_title (title), ADD edit_note VARCHAR(100) NOT NULL DEFAULT '' AFTER user";
$alter_pages_r4_2_5 = "ALTER TABLE {$pref}pages CHANGE hits hits INT(11) UNSIGNED NOT NULL DEFAULT '0'";
$alter_pages_r4_2_6 = "ALTER TABLE {$pref}pages ADD owner_id INT(10) UNSIGNED NOT NULL AFTER id";
$alter_pages_r4_2_7 = "ALTER TABLE {$pref}pages ADD user_id INT(10) UNSIGNED NOT NULL AFTER owner_id";
$alter_pages_r4_2_8 = "ALTER TABLE {$pref}pages CHANGE latest latest TINYINT(1) NOT NULL DEFAULT '1'";
$alter_pages_r4_2_9 = "ALTER TABLE {$pref}pages CHANGE lang lang VARCHAR(2) NOT NULL DEFAULT ''";
$alter_pages_r4_2_10 = "ALTER TABLE {$pref}pages ADD minor_edit TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER edit_note, ADD INDEX idx_minor_edit (minor_edit)";
$alter_pages_r4_2_11 = "ALTER TABLE {$pref}pages ADD comment_on_id INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER super_comment_on, ADD INDEX idx_comment_on_id (comment_on_id)";
$alter_pages_r4_2_12 = "ALTER TABLE {$pref}pages DROP comment_on";
$alter_pages_r4_2_13 = "ALTER TABLE {$pref}pages DROP super_comment_on";
$alter_pages_r4_2_14 = "ALTER TABLE {$pref}pages ADD comments INT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER comment_on_id";
$alter_pages_r4_2_15 = "ALTER TABLE {$pref}pages DROP owner";
$alter_pages_r4_2_16 = "ALTER TABLE {$pref}pages DROP user";

$update_pages_r3_1 = "UPDATE {$pref}pages SET body_r=''";
$update_pages_r3_2 = "UPDATE {$pref}pages SET body_toc=''";
$update_pages_r4_2 = "UPDATE {$pref}pages SET body_r=''";
$update_pages_r4_2_1 = "UPDATE {$pref}pages AS pages, (SELECT id, name FROM {$pref}users) AS users SET pages.owner_id = users.id WHERE pages.owner = users.name";
$update_pages_r4_2_2 = "UPDATE {$pref}pages AS pages, (SELECT id, name FROM {$pref}users) AS users SET pages.user_id = users.id WHERE pages.user = users.name";
$update_pages_r4_2_3 = "UPDATE {$pref}pages AS pages, (SELECT id, tag FROM {$pref}pages) AS pages2 SET pages.comment_on_id = pages2.id WHERE pages.comment_on = pages2.tag";
$update_pages_r4_2_4 = "UPDATE {$pref}pages AS pages, (SELECT comment_on_id, COUNT(comment_on_id) as n FROM {$pref}pages WHERE comment_on_id != '0' GROUP BY comment_on_id) AS comments_on SET pages.comments = comments_on.n WHERE pages.id = comments_on.comment_on_id";
$update_pages_r4_2_5 = "UPDATE {$pref}pages as pages, (SELECT tag, MIN(time) AS oldest FROM wacko_revisions GROUP BY tag) AS revisions SET pages.created = revisions.oldest WHERE pages.tag = revisions.tag AND pages.created IS NULL";
$update_pages_r4_2_6 = "UPDATE {$pref}pages as pages, SET pages.created = pages.time WHERE pages.id = pages.id AND pages.created IS NULL";

// REFERRERS
$alter_referrers_r2_1 = "ALTER TABLE {$pref}referrers CHANGE page_tag page_tag VARCHAR(250) NOT NULL";
$alter_referrers_r3_1 = "ALTER TABLE {$pref}referrers CHANGE page_tag page_tag CHAR(250) BINARY NOT NULL";
$alter_referrers_r4_2 = "ALTER TABLE {$pref}referrers DROP INDEX idx_page_tag, CHANGE page_tag page_id INT(10) UNSIGNED NOT NULL DEFAULT '0', ADD INDEX idx_page_id (page_id)";

// REVISIONS
$table_revisions_r2 = "CREATE TABLE {$pref}revisions (".
						"id INT(10) UNSIGNED NOT NULL auto_increment,".
						"tag VARCHAR(250) NOT NULL DEFAULT '',".
						"supertag VARCHAR(250) NOT NULL DEFAULT '',".
						"time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',".
						"body TEXT NOT NULL,".
						"body_r TEXT NOT NULL,".
						"owner VARCHAR(50) NOT NULL DEFAULT '',".
						"user VARCHAR(50) NOT NULL DEFAULT '',".
						"latest ENUM('Y','N') NOT NULL DEFAULT 'N',".
						"handler VARCHAR(30) NOT NULL DEFAULT 'page',".
						"comment_on VARCHAR(50) NOT NULL DEFAULT '',".
						"PRIMARY KEY (id),".
						"KEY idx_tag (tag),".
						"KEY idx_supertag (supertag),".
						"KEY idx_time (time),".
						"KEY idx_latest (latest),".
						"KEY idx_comment_on (comment_on),".
						"KEY supertag (supertag)".
						") TYPE=MyISAM;";

$alter_revisions_r3_1 = "ALTER TABLE {$pref}revisions CHANGE tag tag VARCHAR(250) BINARY NOT NULL";
$alter_revisions_r3_2 = "ALTER TABLE {$pref}revisions CHANGE comment_on comment_on VARCHAR(250) BINARY NOT NULL";
$alter_revisions_r3_3 = "ALTER TABLE {$pref}revisions ADD super_comment_on VARCHAR(250) NOT NULL AFTER comment_on";
$alter_revisions_r3_4 = "ALTER TABLE {$pref}revisions ADD lang VARCHAR(10) NOT NULL";
$alter_revisions_r3_5 = "ALTER TABLE {$pref}revisions ADD description VARCHAR(250) NOT NULL DEFAULT ''";
$alter_revisions_r3_6 = "ALTER TABLE {$pref}revisions ADD keywords VARCHAR(250) BINARY NOT NULL DEFAULT ''";
$alter_revisions_r4_2_1 = "ALTER TABLE {$pref}revisions MODIFY COLUMN body MEDIUMTEXT NOT NULL";
$alter_revisions_r4_2_2 = "ALTER TABLE {$pref}revisions MODIFY COLUMN body_r MEDIUMTEXT NOT NULL";
$alter_revisions_r4_2_3 = "ALTER TABLE {$pref}revisions ADD created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER supertag, DROP INDEX idx_latest";
$alter_revisions_r4_2_4 = "ALTER TABLE {$pref}revisions ADD title VARCHAR(100) NOT NULL DEFAULT '' AFTER lang, ADD edit_note VARCHAR(100) NOT NULL DEFAULT '' AFTER user";
$alter_revisions_r4_2_5 = "ALTER TABLE {$pref}revisions ADD owner_id INT(10) UNSIGNED NOT NULL AFTER id";
$alter_revisions_r4_2_6 = "ALTER TABLE {$pref}revisions ADD user_id INT(10) UNSIGNED NOT NULL AFTER owner_id";
$alter_revisions_r4_2_7 = "ALTER TABLE {$pref}revisions CHANGE lang lang VARCHAR(2) NOT NULL DEFAULT ''";
$alter_revisions_r4_2_8 = "ALTER TABLE {$pref}revisions ADD minor_edit TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER edit_note, ADD INDEX idx_minor_edit (minor_edit)";
$alter_revisions_r4_2_9 = "ALTER TABLE {$pref}revisions CHANGE latest latest TINYINT(1) NOT NULL DEFAULT '0'";
$alter_revisions_r4_2_10 = "ALTER TABLE {$pref}revisions ADD comment_on_id INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER super_comment_on";
$alter_revisions_r4_2_11 = "ALTER TABLE {$pref}revisions DROP comment_on";
$alter_revisions_r4_2_12 = "ALTER TABLE {$pref}revisions DROP super_comment_on";
$alter_revisions_r4_2_13 = "ALTER TABLE {$pref}revisions ADD page_id INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER id";
$alter_revisions_r4_2_14 = "ALTER TABLE {$pref}revisions DROP owner";
$alter_revisions_r4_2_15 = "ALTER TABLE {$pref}revisions DROP user";

$insert_revisions_r2_1 = "INSERT INTO {$pref}revisions ( id, tag, supertag, time, body, body_r, owner, user, latest, handler, comment_on ) SELECT id, tag, supertag, time, body, body_r, owner, user, latest, handler, comment_on FROM {$pref}pages WHERE latest='N';";

$update_revisions_r4_2 = "UPDATE {$pref}revisions AS revisions, (SELECT id, name FROM {$pref}users) AS users SET revisions.owner_id = users.id WHERE revisions.owner = users.name";
$update_revisions_r4_2_1 = "UPDATE {$pref}revisions AS revisions, (SELECT id, name FROM {$pref}users) AS users SET revisions.user_id = users.id WHERE revisions.user = users.name";
$update_revisions_r4_2_2 = "UPDATE {$pref}revisions SET latest = '0'";
$update_revisions_r4_2_3 = "UPDATE {$pref}revisions AS revisions, (SELECT id, tag FROM {$pref}pages) AS pages SET revisions.page_id = pages.id WHERE revisions.tag = pages.tag";
# $update_revisions_r4_2_4 = "UPDATE {$pref}revisions AS revisions, (SELECT id, tag FROM {$pref}pages) AS pages2 SET revisions.comment_on_id = pages2.id WHERE revisions.comment_on = pages2.tag";

// UPLOAD
$alter_upload_r4_2 = "ALTER TABLE {$pref}upload CHANGE id id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
																	CHANGE page_id page_id INT(10) UNSIGNED NOT NULL DEFAULT '0',
																	CHANGE filesize filesize INT(10) UNSIGNED NOT NULL DEFAULT '0',
																	CHANGE picture_w picture_w INT(10) UNSIGNED NOT NULL DEFAULT '0',
																	CHANGE picture_h picture_h INT(10) UNSIGNED NOT NULL DEFAULT '0',
																	ADD user_id INT(10) UNSIGNED NOT NULL AFTER page_id,
																	ADD hits INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER file_ext,
																	DROP INDEX user_id,
																	ADD INDEX idx_user_id (user_id)";

$alter_upload_r4_2_1 = "ALTER TABLE {$pref}upload DROP user";

$update_upload_r4_2 = "UPDATE {$pref}upload AS upload, (SELECT id, name FROM {$pref}users) AS users SET upload.user_id = users.id WHERE upload.user = users.name";

// USERS
$alter_users_r0_1 = "ALTER TABLE {$pref}users ADD bookmarks TEXT NOT NULL DEFAULT '', ADD lang VARCHAR(20) NOT NULL DEFAULT '', ADD show_spaces ENUM('Y','N') NOT NULL DEFAULT 'Y'";
$alter_users_r2_1 = "ALTER TABLE {$pref}users ADD showdatetime ENUM('Y','N') NOT NULL DEFAULT 'Y', ADD typografica ENUM('Y','N') NOT NULL DEFAULT 'Y'";
$alter_users_r3_1 = "ALTER TABLE {$pref}users ADD more TEXT NOT NULL";
$alter_users_r3_2 = "ALTER TABLE {$pref}users ADD changepassword VARCHAR(100) NOT NULL";
$alter_users_r3_3 = "ALTER TABLE {$pref}users ADD email_confirm VARCHAR(100) NOT NULL";
$alter_users_r4_2 = "ALTER TABLE {$pref}users ADD id INT(10) UNSIGNED NOT NULL auto_increment FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (id)";
$alter_users_r4_2_1 = "ALTER TABLE {$pref}users CHANGE lang lang VARCHAR(2) NOT NULL DEFAULT ''";
$alter_users_r4_2_2 = "ALTER TABLE {$pref}users CHANGE doubleclickedit doubleclickedit TINYINT(1) NOT NULL DEFAULT '1'";
$alter_users_r4_2_3 = "ALTER TABLE {$pref}users CHANGE show_comments show_comments TINYINT(1) NOT NULL DEFAULT '0'";
$alter_users_r4_2_4 = "ALTER TABLE {$pref}users CHANGE show_spaces show_spaces TINYINT(1) NOT NULL DEFAULT '1'";
$alter_users_r4_2_5 = "ALTER TABLE {$pref}users CHANGE showdatetime show_datetime TINYINT(1) NOT NULL DEFAULT '1'";
$alter_users_r4_2_6 = "ALTER TABLE {$pref}users CHANGE typografica typografica TINYINT(1) NOT NULL DEFAULT '1'";
$alter_users_r4_2_7 = "ALTER TABLE {$pref}users ADD total_pages INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER email_confirm";
$alter_users_r4_2_8 = "ALTER TABLE {$pref}users ADD total_revisions INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER total_pages";
$alter_users_r4_2_9 = "ALTER TABLE {$pref}users ADD total_comments INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER total_revisions";
$alter_users_r4_2_10 = "ALTER TABLE {$pref}users DROP INDEX idx_name, ADD UNIQUE idx_name (name)";
$alter_users_r4_2_11 = "ALTER TABLE {$pref}users ADD real_name VARCHAR(80) NOT NULL DEFAULT '' AFTER name";

$update_users_r4_2 = "UPDATE {$pref}users SET doubleclickedit = '0' WHERE doubleclickedit = '2'";
$update_users_r4_2_1 = "UPDATE {$pref}users SET show_comments = '0' WHERE show_comments = '2'";
$update_users_r4_2_2 = "UPDATE {$pref}users SET show_spaces = '0' WHERE show_spaces = '2'";
$update_users_r4_2_3 = "UPDATE {$pref}users SET show_datetime = '0' WHERE show_datetime = '2'";
$update_users_r4_2_4 = "UPDATE {$pref}users SET typografica = '0' WHERE typografica = '2'";

// WATCHES
$table_watches_r0 = "CREATE TABLE {$pref}pagewatches (".
						"id INT(10) NOT NULL auto_increment, ".
						"user VARCHAR(80) NOT NULL DEFAULT '', ".
						"tag VARCHAR(50) binary NOT NULL DEFAULT '', ".
						"time TIMESTAMP NOT NULL, ".
						"PRIMARY KEY (id)) TYPE=MyISAM";

$alter_watches_r2_1 = "ALTER TABLE {$pref}pagewatches CHANGE tag tag VARCHAR(250) NOT NULL";
$alter_watches_r3_1 = "ALTER TABLE {$pref}pagewatches CHANGE tag tag VARCHAR(250) BINARY NOT NULL";
$alter_watches_r4_2 = "ALTER TABLE {$pref}pagewatches CHANGE id id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT";
$alter_watches_r4_2_1 = "ALTER TABLE {$pref}pagewatches ADD user_id INT(10) UNSIGNED NOT NULL AFTER user";
$alter_watches_r4_2_2 = "ALTER TABLE {$pref}pagewatches ADD page_id INT(10) UNSIGNED NOT NULL AFTER tag";
$alter_watches_r4_2_3 = "ALTER TABLE {$pref}pagewatches DROP user";
$alter_watches_r4_2_4 = "ALTER TABLE {$pref}pagewatches DROP tag";

$update_watches_r4_2 = "UPDATE {$pref}pagewatches AS pagewatches, (SELECT id, name FROM {$pref}users) AS users SET pagewatches.user_id = users.id WHERE pagewatches.user = users.name";
$update_watches_r4_2_1 = "UPDATE {$pref}pagewatches AS pagewatches, (SELECT id, tag FROM {$pref}pages) AS pages SET pagewatches.page_id = pages.id WHERE pagewatches.tag = pages.tag";

$rename_watches_r4_2 = "RENAME TABLE {$pref}pagewatches TO {$pref}watches";

?>