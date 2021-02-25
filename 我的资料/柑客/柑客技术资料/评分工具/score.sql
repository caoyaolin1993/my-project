CREATE TABLE `score_member` (
  `openid` varchar(60) NOT NULL DEFAULT '' COMMENT '用户openid',
  `nick_name` varchar(200) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar_url` varchar(1024) NOT NULL DEFAULT '' COMMENT '用户头像',
  `province` varchar(50) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(50) NOT NULL DEFAULT '' COMMENT '城市',
  `country` varchar(50) NOT NULL DEFAULT '' COMMENT '国家',
  `sex` int(4) NOT NULL DEFAULT '0' COMMENT '用户性别，1男性，2女性，0未知',
  `extra_field_one` varchar(150) NOT NULL DEFAULT '' COMMENT '预留字段一',
  `extra_field_two` varchar(150) NOT NULL DEFAULT '' COMMENT '预留字段二',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
  PRIMARY KEY (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评分小程序用户表';

/*项目表*/
CREATE TABLE `score_project` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `creat_openid` varchar(60) NOT NULL DEFAULT '' COMMENT '创建人openid',
  `item` varchar(200) NOT NULL DEFAULT '' COMMENT '昵称',
  `high_point` int(10) NOT NULL DEFAULT 100 COMMENT '最高分',
  `calculate_type` int(4) NOT NULL DEFAULT 1 COMMENT '计算规则:1求平均分，2求总分',
  `name_show` int(4) NOT NULL DEFAULT 1 COMMENT '是否匿名:1匿名，2不匿名',
  `subtract_point` int(4) NOT NULL DEFAULT 1 COMMENT '是否去掉最高最低分:1去掉，2不去',
  `project_status` int(4) NOT NULL DEFAULT 0 COMMENT '0正常，1已结算，2已删除',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
  PRIMARY KEY (`id`),
  KEY `creat_openid` (`creat_openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评分项目表';


/*关联选手表*/
CREATE TABLE `score_project_player` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) NOT NULL DEFAULT 0 COMMENT '对应赛事项目id',
  `project_item` varchar(100) NOT NULL DEFAULT '' COMMENT '对应赛事名',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '参赛选手名称',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='赛事参赛选手列表';


/*关联评委表*/
CREATE TABLE `score_project_judges` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) NOT NULL DEFAULT 0 COMMENT '对应赛事项目id',
  `project_item` varchar(100) NOT NULL DEFAULT '' COMMENT '对应赛事名',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '评委名称',
  `avatar_url` varchar(1024) NOT NULL DEFAULT '' COMMENT '评委头像',
  `openid` varchar(60) NOT NULL DEFAULT '' COMMENT '评委openid',
  `judges_status` INT(11) NOT NULL DEFAULT 0 COMMENT '状态：1-待审核，2-已通过，3-已删除，4-已驳回',
  `read_status` INT(11) NOT NULL DEFAULT 0 COMMENT '状态：0-未读，1-已读',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='赛事评委列表';


/*评委评分表*/
CREATE TABLE `score_project_judges_score` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应赛事项目id',
  `people_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应参赛人员id',
  `people_name` varchar(100) NOT NULL DEFAULT '' COMMENT '对应参赛人员名称',
  `judge_openid` varchar(100) NOT NULL DEFAULT '' COMMENT '评分人openid',
  `judge_head` varchar(1024) NOT NULL DEFAULT '' COMMENT '评分人头像',
  `judge_nick` varchar(100) NOT NULL DEFAULT '' COMMENT '评分人名称',
  `judge_point` int(11) NOT NULL DEFAULT 100 COMMENT '评审分值,9999-为弃票处理',
  `high_point` int(11) NOT NULL DEFAULT 100 COMMENT '最高分制',
  `name_show` int(11) NOT NULL DEFAULT '0' COMMENT '是否匿名状态，1-不匿名，2-匿名',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`) USING BTREE,
  KEY `judge_openid` (`judge_openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评委评分表';


-- 评分项目结算排行
CREATE TABLE `score_player_ranking` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应赛事项目id',
  `people_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应参赛人员id',
  `people_name` varchar(100) NOT NULL DEFAULT '' COMMENT '对应参赛人员名称',
  `score` varchar(50) NOT NULL DEFAULT '' COMMENT '得分',
  `rank` int(11) NOT NULL DEFAULT 0 COMMENT '名次',
  `type` int(3) NOT NULL DEFAULT 0 COMMENT '1-平均得分，2-总得分',
  `subtract_point` int(3) NOT NULL DEFAULT 0 COMMENT '是否去掉最高分 ，是否去掉最高最低分:1去掉，2不去',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评分项目结算排行表';



-- 评分项目 发送模版消息 form_id 收集表
CREATE TABLE `score_form_id` (
  `openid` char(80) NOT NULL DEFAULT '' COMMENT '用户openid',
  `form_id` char(80) NOT NULL DEFAULT '' COMMENT 'form_id',
  `expires_time` INT(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间戳',
  `type` tinyint(3) NOT NULL DEFAULT 0 COMMENT '1-正常，2-已使用',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
  PRIMARY KEY `openid_form_id` (`openid`,`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模版消息 form_id';


--花店
-- CREATE TABLE `score_player_ranking` (
--   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
--   `project_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应赛事项目id',
--   `people_id` int(11) NOT NULL DEFAULT '0' COMMENT '对应参赛人员id',
--   `people_name` varchar(100) NOT NULL DEFAULT '' COMMENT '对应参赛人员名称',
--   `score` varchar(50) NOT NULL DEFAULT '' COMMENT '得分',
--   `rank` int(11) NOT NULL DEFAULT 0 COMMENT '名次',
--   `type` int(3) NOT NULL DEFAULT 0 COMMENT '1-平均得分，2-总得分',
--   `subtract_point` int(3) NOT NULL DEFAULT 0 COMMENT '是否去掉最高分 ，是否去掉最高最低分:1去掉，2不去',
--   `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间，有修改自动更新',
--   PRIMARY KEY  (`id`),
--   KEY `project_id` (`project_id`) USING BTREE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评分项目结算排行表';



SELECT
  id,
  creat_openid,
  item AS '评分标题',
  created_at AS '创建时间',
  ( CASE project_status WHEN '1' THEN '已结算' WHEN '2' THEN '已删除' ELSE '进行中' END ) 项目状态 
FROM
  score_project 
WHERE
  creat_openid NOT IN ('ohmep5ddkXRJzqyZklHRz_Gq2HPA','o4Wuu4g9KkCUWORIsWPZbGspLTzs','ohmep5fhrzUS5vln8ztBGZhtQQnU','ohmep5aVchtcGqwT16e4WRiThxI0','ohmep5aVchtcGqwT16e4WRiThxI0','ohmep5d5aHOQYu2hp9gagckfi6n8','ohmep5ZjDibjbnuJAWJaXgdbjRe8')
