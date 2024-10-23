<?php
declare(strict_types=1);
/**
 * The batchedit view file of task module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */
namespace zin;
/* ====== Preparing and processing page data ====== */

/* zin: Set variables to define picker options for form. */
jsVar('executionTeams', $executionTeams);
jsVar('users', $users);
jsVar('teams', $teams);
jsVar('currentUser', $app->user->account);
jsVar('moduleGroup', $moduleGroup);

$beforeSubmit       = null;
$childTasks         = json_encode($childTasks);
$nonStoryChildTasks = json_encode($nonStoryChildTasks);
$tasksJsVar         = json_encode($tasks);
if(!empty($nonStoryChildTasks))
{
    $beforeSubmit = jsRaw("() =>
    {
        const \$taskBatchForm    = $('#taskBatchEditForm{$executionID}');
        const childTasks         = $childTasks;
        const nonStoryChildTasks = $nonStoryChildTasks;
        const \$taskBatchFormTrs = \$taskBatchForm.find('tbody tr');
        const tasks              = $tasksJsVar;

        var confirmID = '';
        var tipAll    = true;
        for(let i = 0; i < \$taskBatchFormTrs.length; i++)
        {
            const \$currentTr = $(\$taskBatchFormTrs[i]);
            const taskID      = \$currentTr.find('.form-batch-control[data-name=id]').find('input[name^=id]').val();
            const storyID     = \$currentTr.find('.form-batch-control[data-name=story]').find('input[name^=story]').val();
            if(tasks[taskID].story == storyID) continue;
            if(!storyID && tasks[taskID].parent <= 0) continue;
        }
        return false;
    }");
}

/* ====== Define the page structure with zin widgets ====== */
formBatchPanel
(
    set::title($lang->task->batchEdit),
    set::mode('edit'),
    set::data(array_values($tasks)),
    set::onRenderRow(jsRaw('renderRowData')),
    set::customFields(array('list' => $customFields, 'show' => explode(',', $showFields), 'key' => 'batchEditFields')),
    !empty($nonStoryChildTasks) ? set::ajax(array('beforeSubmit' => $beforeSubmit)) : null,
    set::formID('taskBatchEditForm' . $executionID),
    formBatchItem
    (
        set::name('id'),
        set::label($lang->idAB),
        set::control('hidden'),
        set::hidden(true)
    ),
    formBatchItem
    (
        set::name('id'),
        set::label($lang->idAB),
        set::control('index'),
        set::width('64px')
    ),
    formBatchItem
    (
        set::name('name'),
        set::control('colorInput'),
        set::label($lang->task->name),
        set::width('240px')
    ),
    formBatchItem
    (
        set::name('module'),
        set::label($lang->task->module),
        set::control('picker'),
        set::items($modules),
        set::width('200px'),
        set::ditto(true),
        set::defaultDitto('off')
    ),
    formBatchItem
    (
        set::name('story'),
        set::label($lang->task->story),
        set::control('picker'),
        set::items($stories),
        set::width('200px'),
        set::ditto(true),
        set::defaultDitto('off')
    ),
    formBatchItem
    (
        set::name('assignedTo'),
        set::label($lang->task->assignedTo),
        set::control('picker'),
        set::items(array()),
        set::width('128px'),
        set::ditto(true),
        set::defaultDitto('off')
    ),
    formBatchItem
    (
        set::name('type'),
        set::label($lang->task->type),
        set::control('picker'),
        set::items($lang->task->typeList),
        set::width('128px'),
        set::ditto(true),
        set::defaultDitto('off')
    ),
    formBatchItem
    (
        set::name('status'),
        set::label($lang->task->status),
        set::control('picker'),
        set::items($lang->task->statusList),
        set::width('128px'),
        set::ditto(true),
        set::defaultDitto('off')
    ),
    formBatchItem
    (
        set::name('estStarted'),
        set::label($lang->task->estStarted),
        set::control('date'),
        set::width('128px')
    ),
    formBatchItem
    (
        set::name('deadline'),
        set::label($lang->task->deadline),
        set::control('date'),
        set::width('128px')
    ),
    formBatchItem
    (
        set::name('pri'),
        set::label($lang->task->pri),
        set::control('priPicker'),
        set::items($lang->task->priList),
        set::width('80px')
    ),
    formBatchItem
    (
        set::name('estimate'),
        set::label($lang->task->estimateAB),
        set::width('64px'),
        set::control
        (
            array(
                'type' => 'inputControl',
                'suffix' => $lang->task->suffixHour,
                'suffixWidth' => 20
            )
        )
    ),
    formBatchItem
    (
        set::name('consumed'),
        set::label($lang->task->consumedThisTime),
        set::width('64px'),
        set::control
        (
            array(
                'type' => 'inputControl',
                'suffix' => $lang->task->suffixHour,
                'suffixWidth' => 20
            )
        )
    ),
    formBatchItem
    (
        set::name('left'),
        set::label($lang->task->leftAB),
        set::width('64px'),
        set::control
        (
            array(
                'type' => 'inputControl',
                'suffix' => $lang->task->suffixHour,
                'suffixWidth' => 20
            )
        )
    )
);
/* ====== Render page ====== */
render();
