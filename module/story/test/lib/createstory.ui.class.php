<?php
declare(strict_types=1);
/**
 * The control file of example module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      lijie
 * @package     story
 * @link        http://www.zentao.net
 */
include dirname(__FILE__, 5).'/test/lib/ui.php';
class createStoryTester extends tester
{
    /**
     * Create a default story.
     *
     * @param  string $type string $storyName
     * @access public
     * @return object
     */
    public function createDefault($storyType, $storyName)
    {
        /* 提交表单 */
        $createStoryParam = array(
            'product'   => '1',
            'branch'    => 'all',
            'moduleID'  => '0',
            'storyID'   => '0',
            'projectID' => '0',
            'bugID'     => '0',
            'planID'    => '0',
            'todoID'    => '0',
            'extra'     => '',
            'storyType' => $storyType
        );
        $browseStoryParam = array(
            'productID'  => '1',
            'branch'     => '',
            'browseType' => 'unclosed',
            'param'      => '0',
            'storyType'  => $storyType
        );
        $form = $this->initForm('product', 'browse', $browseStoryParam, 'appIframe-product');
        $form = $this->initForm($storyType, 'create', $createStoryParam, 'appIframe-product');
        $form->dom->title->setValue($storyName);
        $form->dom->assignedTo->picker('admin');
        #$form->dom->reviewer->multiPicker(array('admin'));
        $form->dom->btn($this->lang->save)->click();
        $form->wait(1);

        if($this->response('method') != 'browse')
        {
            if($this->checkFormTips('story')) return $this->success('创建需求表单页面提示正确');
            return $this->failed('创建需求表单页面提示信息不正确');
        }

        /* 跳转到需求列表页面搜索创建需求并进入该需求详情页。 */
        if($storyName != '研发需求')
        {
            $ext = array(
                'productID'  => '1',
                'branch'     => '',
                'brwoseType' => 'unclosed',
                'param'      => '0',
                'storyType'  => $storyType
            );
            $browsePage = $this->loadPage('product', 'browse', $ext);
        }
        else
        {
            $browsePage = $this->loadPage('product', 'browse');
        }
        $browsePage->dom->search($searchList = array("需求名称,包含,$storyName"));
        $form->wait(1);
        $browsePage->dom->browseStoryName->click();
        $form->wait(1);

        $viewPage = $this->loadPage('story', 'view');
        if($viewPage->dom->storyName->getText() != $storyName) return $this->failed('需求名称不正确');
        if($viewPage->dom->status->getText() != '激活') return $this->failed('需求状态不正确');
        $viewPage->dom->btn($this->lang->story->legendLifeTime)->click();
        if(strpos($viewPage->dom->openedBy->getText() , 'admin') === false) return $this->failed('创建人不正确');

        return $this->success('创建'.$storyName.'成功');
    }
}
