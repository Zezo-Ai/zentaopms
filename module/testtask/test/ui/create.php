#!/usr/bin/env php
<?php

/**
title=创建测试单
timeout=0
cid=1
 */

chdir(__DIR__);
include '../lib/create.ui.class.php';

$product = zenData('product');
$product->id->range('1-100');
$product->name->range('产品1, 产品2');
$product->type->range('normal');
$product->gen(2);

$project = zenData('project');
$project->id->range('1-100');
$project->project->range('0, 1{3}');
$project->model->range('scrum, []{3}');
$project->type->range('project, sprint{3}');
$project->auth->range('extend, []{3}');
$project->storytype->range('`story,epic,requirement`');
$project->path->range('`,1,`, `,1,2,`, `,1,3,`, `,1,4,`');
$project->grade->range('1');
$project->name->range('项目1, 项目1执行1, 项目1执行2, 项目1执行3');
$project->hasProduct->range('1');
$project->status->range('wait');
$project->acl->range('open');
$project->gen(3);

$projectProduct = zenData('projectproduct');
$projectProduct->project->range('1, 2, 3');
$projectProduct->product->range('1{3}, 2{3}');
$projectProduct->gen(6);

$build = zenData('build');
$build->id->range('1-100');
$build->project->range('1');
$build->product->range('1, 2');
$build->branch->range('0');
$build->execution->range('2{4}, 3{2}');
$build->name->range('1-100');
$build->scmPath->range('[]');
$build->filePath->range('[]');
$build->deleted->range('1, 0{100}');
$build->gen(6);
