<?php

/**
 * @file
 * Database installation script.
 */

$sql = [];
$sql[] = "CREATE TABLE IF NOT EXISTS `config` (
  `name` varchar(128) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$sql[] = "DELETE FROM `config` WHERE `name`='version';";
$sql[] = "INSERT INTO `config` (`name`, `value`) VALUES
('version', '1.0');";

$sql[] = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `password_md5` varchar(128) NOT NULL,
  `status` int(11) NOT NULL,
  `obj` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

// Execute sql dump.
try {
  require_once 'api/includes/environment.php';
    $link = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
  if ($link->connect_errno) {
    printf("Connect failed: %s\n", $link->connect_error);
    exit;
  }
  foreach ($sql as $query) {
    if (!$link->query($query)) {
      printf("Error message: %s\n", $link->error);
      exit;
    }
  }
  $link->close();
  print 'Successfully installed';
}
catch (Exception $ex) {
  print $ex->getMessage();
}
