CREATE TABLE /*_*/streamer (
  `streamer_id` int(14) NOT NULL AUTO_INCREMENT,
  `service` int(2) NOT NULL DEFAULT '0',
  `remote_name` varbinary(255) DEFAULT NULL,
  `display_name` varbinary(255) DEFAULT NULL,
  `page_title` varbinary(255) DEFAULT NULL,
  PRIMARY KEY (`streamer_id`),
  UNIQUE KEY `service_remote_name` (`service`,`remote_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;