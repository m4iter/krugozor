<?php
class Module_Category_Helper_TreeBackendTable extends Helper_Abstract
{
    private $tree;

    public function __construct(Cover_Array $tree, Base_View &$view)
    {
        $this->tree = $tree;
        $this->view = $view;
    }

    public function getHtml()
    {
        return $this->createRows($this->tree);
    }

    private function createRows($tree)
    {
        $str = '';

        foreach ($tree as $item)
        {
            ob_start();
        ?>
            <tr class="color_hover">
                <td class="center"><?=$item->getId()?></td>
                <td style="padding-left:<?=($item->getIndent()*15)?>px; <? if(!$item->getPid()) :?>font-weight:bold;<? endif; ?>"><?=$this->view->getHelper('Helper_Format')->hsc($item->getName())?></td>
                <td class="td_actions"><a href="/admin/category/edit/0/<?=$item->getId()?>/"><img src="/http/image/system/icon/add.png" alt="" /></a></td>
                <td class="td_actions"><a href="/admin/category/edit/<?=$item->getId()?>/?referer=<?=$this->view->urlencode_full_request_uri?>"><img alt="" src="/http/image/system/icon/edit.png" /></a></td>
                <td class="td_actions"><a onclick="return confirm('¬ы действительно хотите удалить категорию &laquo;<?=$this->view->getHelper('Helper_Format')->run($item->getName(), 'entDec', 'confirm')?>&raquo; (id: <?=$item->getId()?>)?')" href="/admin/category/delete/?id=<?=$item->getId()?>&amp;referer=<?=$this->view->urlencode_full_request_uri?>"><img src="/http/image/system/icon/delete.png" alt="" /></a></td>
            <td class="td_actions"><a href="/admin/category/motion/up/<?=$item->getId()?>/<?=$item->getPid()?>/?referer=<?=$this->view->urlencode_full_request_uri?>"><img src="/http/image/system/icon/up.gif" title="ѕодн€ть запись на одну позицию выше" alt="" /></a></td>

        <td class="td_actions"><a href="/admin/category/motion/down/<?=$item->getId()?>/<?=$item->getPid()?>/?referer=<?=$this->view->urlencode_full_request_uri?>"><img src="/http/image/system/icon/down.gif" alt="" /></a></td>
            </tr>
        <?php
            $str .= ob_get_contents();
            ob_end_clean();

            if ($item->getTree() && $item->getTree()->count())
            {
                $str .= $this->createRows($item->getTree());
            }
        }

        return $str;
    }
}
?>