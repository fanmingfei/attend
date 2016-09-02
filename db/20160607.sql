--
-- 表的结构 `message`
--

DROP TABLE `message`;

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT '消息类型1为学生2为老师',
  `tid` text COMMENT '老师id ,分割',
  `sid` text COMMENT '学生id ,分割',
  `title` text NOT NULL COMMENT '消息标题',
  `content` text NOT NULL COMMENT '发送内容',
  `url` text COMMENT '通知网页url',
  `remark` text COMMENT '备注',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态:0 未发送, 1 已发送',
  `time` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 表的结构 `failmessage`
--

CREATE TABLE IF NOT EXISTS `failmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msgid` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '用户类型,1学生,2老师',
  `uid` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `msgid` (`msgid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;