<?php
/**
 * 按产品统计的年度关闭研发需求数。
 * Count of annual closed story in product.
 *
 * 范围：product
 * 对象：story
 * 目的：scale
 * 度量名称：按产品统计的年度关闭研发需求数
 * 单位：个
 * 描述：按产品统计的年度关闭研发需求规模数表示产品在某年度关闭的研发需求数。该度量项反映了产品团队每年因完成、不做或取消等原因关闭的研发需求数，可以用于评估产品团队的研发需求规模管理和调整情况。
 * 定义：产品中关闭时间在某年的研发需求的个数求和;过滤已删除的研发需求;过滤已删除的产品;
 *
 * @copyright Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @author    zhouxin <zhouxin@easycorp.ltd>
 * @package
 * @uses      func
 * @license   ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @Link      https://www.zentao.net
 */
class count_of_annual_closed_story_in_product extends baseCalc
{
    public $dataset = 'getDevStories';

    public $fieldList = array('t1.product', 't1.closedDate', 't1.status');

    public $result = array();

    public function calculate($row)
    {
        if($row->status != 'closed') return false;
        if(empty($row->closedDate))  return false;

        $product    = $row->product;
        $closedDate = $row->closedDate;

        $year = $this->getYear($closedDate);
        if(!$year) return false;

        if(!isset($this->result[$product]))        $this->result[$product] = array();
        if(!isset($this->result[$product][$year])) $this->result[$product][$year] = 0;
        $this->result[$product][$year] += 1;
    }

    public function getResult($options = array())
    {
        $records = array();
        foreach($this->result as $product => $years)
        {
            foreach($years as $year => $value)
            {
                $records[] = array(
                    'product' => $product,
                    'year'    => $year,
                    'value'   => $value,
                );
            }
        }

        return $this->filterByOptions($records, $options);
    }
}
