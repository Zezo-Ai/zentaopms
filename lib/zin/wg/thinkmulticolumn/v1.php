<?php
declare(strict_types=1);
namespace zin;

requireWg('thinkQuestion');

class thinkMulticolumn extends thinkQuestion
{
    protected static array $defineProps = array
    (
        'fields?: array',       // 列标题
        'requiredCols?: array', // 必填列
        'supportAdd?: int=1',   // 是否支持用户添加行
        'canAddRows: int',      // 可添加行数
        'linkColumn?: array',   // 关联区块的列
    );

    public static function getPageJS(): string
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    public static function getPageCSS(): ?string
    {
        $baseCss = file_get_contents(dirname(__FILE__, 2) . DS . 'thinkstepbase' . DS . 'css' . DS . 'v1.css');
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css') . $baseCss;
    }

    protected function buildFormBatchItem(string $label, int $index, $isRun, $quotedQuestions, $hasResult): wg
    {
        $step         = $this->prop('step');
        $key          = $index + 1;
        $requiredCols = isset($step->options->requiredCols) ? $step->options->requiredCols : array();

        return formBatchItem
        (
            set::label($label),
            set::name("result[col$key]"),
            set::width('110px'),
            set::disabled($isRun && !empty($quotedQuestions) && $hasResult),
            set::required(in_array($key, $requiredCols))
        );
    }

    protected function processResult(array $data): array
    {
        if(empty($data)) return array();

        $result = array();
        for($i = 1; $i <= count(get_object_vars($data['col1'])); $i++)
        {
            $item = new stdClass();
            foreach($data as $key => $values)
            {
                $values = (array)$values;
                $name   = "result[$key]";
                $item->$name = isset($values[$i]) ? $values[$i] : '';
            }
            $result[$i] = $item;
        }
        $filterData = array_filter($result, function($resultItem)
        {
            return array_filter((array)$resultItem);
        });
        return array_values($filterData);
    }

    protected function buildDetail(): array
    {
        global $lang;
        $detailWg = parent::buildDetail();
        list($step, $fields, $canAddRows, $mode, $isRun, $quotedQuestions, $isResult) = $this->prop(array('step', 'fields', 'canAddRows', 'mode', 'isRun', 'quotedQuestions', 'isResult'));
        if($mode != 'detail') return array();

        $result = array();
        if($step)
        {
            $fields     = $step->options->fields;
            $canAddRows = $step->options->supportAdd == 1 ? $step->options->canAddRows : 0;
            $answer     = $step->answer;
            $result     = isset($answer->result) && !empty($answer->result) ? (array) $answer->result : array();
        }
        jsVar('canAddRowsOfMulticol', $canAddRows + 5);
        jsVar('addRowsTips', $lang->thinkrun->tips->addRow);
        jsVar('addLang', $lang->thinkrun->add);
        jsVar('disabled', $isRun && !empty($quotedQuestions) && !empty($result));

        $fields     = array_values((array)$fields);
        $batchItems = array();
        foreach($fields as $key => $field) $batchItems[] = $this->buildFormBatchItem($field, (int)$key, $isRun, $quotedQuestions, !empty($result));

        if($isResult || $isRun) $result = $this->processResult($result);
        $detailWg[] = formBatch
        (
            setClass('think-form-batch'),
            set::minRows(5),
            set::mode($isResult ? 'edit' : 'add'),
            set::actions(array()),
            set::onRenderRow(jsRaw('renderRowData')),
            $isRun ? formHidden('status', '') : null,
            set::data($result),
            $batchItems
        );
        return $detailWg;
    }

    protected function buildFormItem(): array
    {
        global $lang, $app;
        $app->loadLang('thinkstep');
        $formItems = parent::buildFormItem();

        list($step, $questionType, $required, $fields, $supportAdd, $canAddRows, $requiredCols, $quotedQuestions, $linkColumn) = $this->prop(array('step', 'questionType', 'required', 'fields', 'supportAdd', 'canAddRows', 'requiredCols', 'quotedQuestions', 'linkColumn'));
        $requiredItems = $lang->thinkstep->requiredList;
        $linkColumn    = !empty($linkColumn) ? $linkColumn : array();

        $requiredOptions = array();
        if($step)
        {
            $required = isset($step->options->required) ? $step->options->required : 1;
            if(!empty($step->options->fields)) $step->options->fields = is_string($step->options->fields) ? explode(', ', $step->options->fields) : array_values((array)$step->options->fields);
            $fields       = $step->options->fields;
            $requiredCols = $required && isset($step->options->requiredCols) ? $step->options->requiredCols : '';
            $supportAdd   = $step->options->supportAdd;
            $canAddRows   = $supportAdd && isset($step->options->canAddRows) ? $step->options->canAddRows : '';
            $linkColumn   = !empty($step->link) ? json_decode($step->link)->column : array();
            foreach($fields as $key => $field) $requiredOptions[] = array('value' => $key + 1, 'text' => $field);
        }
        jsVar('canAddRowsOfMulticol', (int)$canAddRows + 5);
        jsVar('addRowsTips', $lang->thinkrun->tips->addRow);
        jsVar('addLang', $lang->thinkrun->add);
        jsVar('tipQuestion', $lang->thinkstep->tips->question);
        jsVar('requiredColTip', $lang->thinkstep->tips->requiredCol);

        $formItems[] = array(
            formHidden('options[questionType]', $questionType),
            formGroup
            (
                set::label($lang->thinkstep->label->columnTitle),
                setStyle(array('padding-bottom' => 'calc(4 * var(--space))')),
                thinkMatrixOptions(set::colName('options[fields]'), set::cols($fields), set::quotedQuestions($quotedQuestions), set::linkColumn($linkColumn))
            ),
            formRow
            (
                setClass('mb-3'),
                formGroup
                (
                    setClass('step-required'),
                    set::width('1/2'),
                    set::label($lang->thinkstep->label->required,),
                    set::labelHint(!empty($quotedQuestions) ? $lang->thinkstep->tips->required : null),
                    radioList
                    (
                        set::name('options[required]'),
                        set::inline(true),
                        set::value($required),
                        set::items($requiredItems),
                        set::disabled(!empty($quotedQuestions)),
                        on::change()->toggleClass('.required-options', 'hidden', 'target.value == 0')
                    )
                ),
                formGroup
                (
                    set::width('1/2'),
                    set::label($lang->thinkstep->label->requiredCol),
                    set::required(true),
                    setClass('required-options', $required ? '' : 'hidden'),
                    setData('quotedQuestions', $quotedQuestions),
                    setData('requiredCols', $requiredCols),
                    picker
                    (
                        set::name('options[requiredCols][]'),
                        set::items($requiredOptions),
                        set::value($requiredCols),
                        set::multiple(true),
                        bind::change('changeRequiredCols()')
                    )
                )
            ),
            formRow
            (
                setClass('mb-3'),
                formGroup
                (
                    setClass('w-1/2'),
                    set::label($lang->thinkstep->label->isSupportAdd),
                    set::labelHint($lang->thinkstep->tips->supportAdd),
                    radioList
                    (
                        set::name('options[supportAdd]'),
                        set::inline(true),
                        set::items($lang->thinkstep->requiredList),
                        set::value(is_null($supportAdd) ? 1 : $supportAdd),
                        on::change()->toggleClass('.can-add-rows', 'hidden', 'target.value == 0')
                    )
                ),
                formGroup
                (
                    setClass('w-1/2 can-add-rows', (is_null($supportAdd) || $supportAdd) ? '' : 'hidden'),
                    set::label($lang->thinkstep->label->canAddRows),
                    set::required(true),
                    input
                    (
                        set::type('number'),
                        set::name('options[canAddRows]'),
                        set::value($canAddRows),
                        set::placeholder($lang->thinkstep->placeholder->inputContent),
                        set::min(1),
                        on::input('changeRows')
                    )
                )
            )
        );
        return $formItems;
    }
}
