
CREATE TABLE `chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `from` varchar(255) NOT NULL DEFAULT '',
  `to` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recd` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `chat_lastactivity` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `members` (
  `userid` int(11) NOT NULL,
  `username` varchar(15) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `gender` int(11) NOT NULL,
  `relationship` int(11) NOT NULL,
  `timezone` varchar(15) NOT NULL DEFAULT '',
  `sus` varchar(1) NOT NULL DEFAULT '0',
  `ban` varchar(1) NOT NULL DEFAULT '0',
  `verified` varchar(1) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL DEFAULT '',
  `joindate` varchar(11) NOT NULL DEFAULT '',
  `lastonline` varchar(11) NOT NULL DEFAULT '',
  `avatar` varchar(65) NOT NULL DEFAULT '',
  `online` varchar(1) NOT NULL DEFAULT '0',
  `status` varchar(10) NOT NULL DEFAULT 'Larva',
  `admin` varchar(1) NOT NULL DEFAULT '0',
  `moderator` varchar(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `chat_lastactivity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `members`
  ADD PRIMARY KEY (`userid`);

ALTER TABLE `chat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `chat_lastactivity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `members`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT;
