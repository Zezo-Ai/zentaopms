<?php
declare(strict_types=1);
/**
 * The task view file of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yanyi Cao<caoyanyi@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */

namespace zin;

$executions = array(0 => $lang->execution->allExecutions) + $executions;
featureBar
(
    set::current('all'),
    set::linkParams("toExecution={$execution->id}"),
    inputGroup
    (
        setClass('ml-6'),
        $lang->execution->selectExecution,
        picker
        (
            set::width(200),
            set::name('execution'),
            set::items($executions),
            set::value($fromExecution),
            set::required(true),
            on::change('changeExecution')
        )
    )
);

$config->task->dtable->importTask->fieldList['execution']['map'] = $executions;
if($execution->lifetime == 'ops' || in_array($execution->attribute, array('request', 'review'))) unset($config->task->dtable->importTask->fieldList['story']);

$footToolbar['items'][] = array(
    'text'      => $lang->execution->importTask,
    'className' => 'btn secondary toolbar-item batch-btn size-sm',
    'data-url'  => createLink('execution', 'importtask', "executionID={$execution->id}&fromExecution={$fromExecution}")
);
if(!isInModal())
{
    $footToolbar['items'][] = array(
        'text'      => $lang->goback,
        'btnType'   => 'info',
        'className' => 'btn-info toolbar-item size-sm text-gray',
        'url'       => createLink('execution', 'task', "executionID={$execution->id}")
    );
}

jsVar('executionID', $execution->id);
jsVar('childrenAB', $lang->task->childrenAB);
jsVar('parentAB', $lang->task->parentAB);
dtable
(
    set::userMap($memberPairs),
    set::cols(array_values($config->task->dtable->importTask->fieldList)),
    set::data($tasks2Imported),
    set::showToolbarOnChecked(false),
    set::orderBy($orderBy),
    set::sortLink(createLink('execution', 'importTask', "executionID={$execution->id}&fromExecution={$fromExecution}&orderBy={name}_{sortType}&recPerPage={$pager->recPerPage}")),
    set::footToolbar($footToolbar),
    set::onRenderCell(jsRaw('window.renderCell')),
    set::footPager(
        usePager
        (
            array('linkCreator' => helper::createLink('execution', 'importTask', "executionID={$execution->id}&fromExecution={$fromExecution}&orderBy=$orderBy&recPerPage={recPerPage}&page={page}"))
        )
    )
);

render();
