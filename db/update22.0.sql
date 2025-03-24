CREATE TABLE `zt_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('global','group') NOT NULL DEFAULT 'global',
  `workflowGroup` varchar(255) DEFAULT NULL,
  `objectType` varchar(30) NOT NULL,
  `action` varchar(30) NOT NULL,
  `nodes` mediumtext DEFAULT NULL,
  `method` enum('sync','async') NOT NULL DEFAULT 'sync',
  `status` char(30) NOT NULL DEFAULT 'disable',
  `notifyUsers` text NULL,
  `notifyMethod` varchar(30) NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `createdDate` date DEFAULT NULL,
  `lastEditedBy` varchar(30) DEFAULT NULL,
  `lastEditedDate` date DEFAULT NULL,
  `lastRunTime` datetime DEFAULT NULL,
  `lastRunResult` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE INDEX `objectType`  ON `zt_rule` (`objectType`);
CREATE INDEX `action`  ON `zt_rule` (`action`);

CREATE TABLE `zt_rulequeue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule` int(8) NOT NULL,
  `fromObject` text DEFAULT NULL,
  `actions` longtext DEFAULT NULL,
  `status` char(30) NOT NULL DEFAULT 'wait',
  `log` text DEFAULT NULL,
  `triggeredBy` varchar(30) DEFAULT NULL,
  `triggeredDate` date DEFAULT NULL,
  `executedTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `zt_cron` (`m`, `h`, `dom`, `mon`, `dow`, `command`, `remark`, `type`, `buildin`, `status`) VALUES
('*/1', '*', '*', '*', '*', ' moduleName=rulequeue&methodName=run', '异步执行规则引擎', 'zentao', 1, 'normal');

UPDATE `zt_workflowfield` SET `control` = 'multi-select' WHERE `module` = 'testcase' AND `field` = 'stage';
UPDATE `zt_workflowfield` SET `control` = 'multi-select' WHERE `module` = 'bug' AND `field` = 'os';
UPDATE `zt_workflowfield` SET `control` = 'multi-select' WHERE `module` = 'bug' AND `field` = 'browser';
UPDATE `zt_workflowfield` SET `control` = 'select'       WHERE `module` = 'project' AND `field` = 'model';
UPDATE `zt_workflowfield` SET `control` = 'select'       WHERE `module` = 'project' AND `field` = 'type';
UPDATE `zt_workflowfield` SET `control` = 'select', `options` = "{\"extend\":\"\\u7ee7\\u627f\",\"reset\":\"\\u91cd\\u65b0\\u5b9a\\u4e49\"}" WHERE `module` = 'project' AND `field` = 'auth';
