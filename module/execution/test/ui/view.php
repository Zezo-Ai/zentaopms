#!/usr/bin/env php
<?php

/**
title=概况页面
timeout=0
cid=1
 */

chdir(__DIR__);
include '../lib/view.ui.class.php';

$product = zenData('product');
$product->id->range('1-100');
$product->name->range('产品1,产品2');
$product->type->range('normal');
$product->gen(2);

$project = zenData('project');
$project->id->range('1-100');
$project->project->range('0, 0, 1');
$project->model->range('[], scrum, []');
$project->type->range('program, project, sprint');
$project->auth->range('[], extend, []');
$project->storyType->range('[], story, []');
$project->parent->range('0, 1, 2');
$project->path->range('`,1,`, `,1,2,`, `,1,2,3`');
$project->grade->range('1, 2, 1');
$project->name->range('项目集, 项目, 执行');
$project->begin->range('2024-11-01')->type('timestamp')->format('YY/MM/DD');
$project->end->range('2024-11-31')->type('timestamp')->format('YY/MM/DD');
$project->openedBy->range('user1');
$project->acl->range('open');
$project->status->range('doing');
$project->gen(3);

$projectproduct = zenData('projectproduct');
$projectproduct->project->range('2{2}, 3');
$projectproduct->product->range('1, 2, 1');
$projectproduct->gen(3);

$story->id->range('1-100');
$story->parent->range('0');
$story->isParent->range('0');
$story->root->range('1-100');
$story->path->range('`,1,`, `,2,`, `,3,`');
$story->grade->range('1');
$story->product->range('1');
$story->module->range('0');
$story->plan->range('0');
$story->title->range('1-100');
$story->type->range('story');
$story->estimate->range('0');
$story->status->range('active');
$story->stage->range('projected');
$story->assignedTo->range('[]');
$story->version->range('1');
$story->gen(3);
