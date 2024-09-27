#!/usr/bin/env php
<?php

/**

title=指派设计测试
timeout=0
cid=2

- 指派设计最终测试状态 @SUCCESS

*/
chdir(__DIR__);
include '../lib/assigndesign.ui.class.php';

zendata('project')->loadYaml('project', false, 2)->gen(10);
zendata('design')->loadYaml('design', false, 2)->gen(2);
$tester = new assignDesignTester();
$tester->login();

$design = array(
    array('assignedTo' => 'admin'),
);

r($tester->assignDesign($design['0'])) && p('status') && e('SUCCESS'); //指派设计

$tester->closeBrowser();
