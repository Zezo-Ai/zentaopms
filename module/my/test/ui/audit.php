#!/usr/bin/env php
<?php

/**
title=检查地盘审批数据/在审批列表中进行评审
timeout=0
cid=0
*/
chdir(__DIR__);
include '../lib/audit.ui.class.php';

$product = zenData('product');
$product->id->range('1');
$product->program->range('0');
$product->name->range('产品1');
$product->PO->range('admin');
$product->status->range('normal');
$product->type->range('normal');
$product->gen(1);
$tester->closeBrowser();
