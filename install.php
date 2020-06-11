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

$sql[] = "DELETE FROM `users` WHERE `id`=1;";
$sql[] = "INSERT INTO `users` (`id`, `email`, `first_name`, `last_name`, `password_md5`, `status`, `obj`) VALUES
(1, 'admin', 'Admin', 'Admin', '21232f297a57a5a743894a0e4a801fc3', 1, '');";

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
