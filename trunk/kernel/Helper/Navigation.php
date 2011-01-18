<?php
class Helper_Navigation
{
    private $navigation;

    public function __construct(Base_Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Формирует строку навигации.
     * Принимает массив параметров $params, в котором могут быть установлены значения, отвечающие
     * за визуальное оформление и GET-параметры строки навигации.
     * Выглядит массив примерно так:
     * $pages_html_params = array(
     * 			'css.normalLinkClass' => 'navigation_normal_link', // CSS-класс гиперссылки.
     * 			'css.activeLinkClass' => 'navigation_active_link', // CSS-класс открытой в данный момент гиперссылки.
     * 			'GET' => array(                                    // Дополнительные GET-параметры
     *				'search' => 'вася',
     * 				'sort'   => 'DESC',
     *			),
     *		);
     */
    public function getHtml($params = array())
    {
        ob_start();

        // CSS-класс гиперссылки.
        $normalLinkClass = !empty($params['css.normalLinkClass'])
                           ? ' class="'.$params['css.normalLinkClass'].'"'
                           : '';

        // CSS-класс открытой в данный момент гиперссылки.
        $activeLinkClass = !empty($params['css.activeLinkClass'])
                           ? ' class="'.$params['css.activeLinkClass'].'"'
                           : '';

    	// CSS-класс гиперссылки "на первую страницу"
        $css_current__Separator = isset($params['css.current__Separator'])
                                  ? 'class="'.$params['css.current__Separator'].'"'
                                  : $normalLinkClass;

    	// CSS-класс гиперссылки "напоследнюю страницу"
        $css_last__Separator = isset($params['css.last__Separator'])
                                  ? 'class="'.$params['css.last__Separator'].'"'
                                  : $normalLinkClass;

    	// CSS-класс гиперссылки "предыдущий блок страниц"
        $css_last_block__Separator = isset($params['css.last_block__Separator'])
                                     ? 'class="'.$params['css.last_block__Separator'].'"'
                                     : $normalLinkClass;

    	// CSS-класс гиперссылки "следующий блок страниц"
        $css_next_block__Separator = isset($params['css.next_block__Separator'])
                                     ? 'class="'.$params['css.next_block__Separator'].'"'
                                     : $normalLinkClass;

    	// CSS-класс гиперссылки "Предыдущая страница"
    	$css_last_page__Page = isset($params['css.last_page__Page'])
                               ? 'class="'.$params['css.last_page__Page'].'"'
                               : $normalLinkClass;

    	// CSS-класс гиперссылки "Следующая страница"
    	$css_next_page__Page = isset($params['css.next_page__Page'])
                               ? 'class="'.$params['css.next_page__Page'].'"'
                               : $normalLinkClass;

        $anchor = isset($params['anchor'])
                  ? '#'.$params['anchor']
                  : '';

    	if (strpos($_SERVER["REQUEST_URI"], '?') !== FALSE) {
    	    $self_uri = substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], '?'));
    	} else {
    	    $self_uri = $_SERVER["REQUEST_URI"];
    	}

        // Формируем строку QUERY_STRING GET-переменных.
        $get = '';

        if (isset($params['GET']) && $params['GET'])
        {
            foreach ($params['GET'] as $key => $value)
            {
                // Спорно, нужно ли делаь эту проверку..
                if ($value)
                {
                    $get .= $key.'='.htmlentities(urlencode($value)).'&amp;';
                }
            }
        }

        // Левая метка отстраничивателя. По умолчанию - «
        $leftLinkLabel = !empty($params['html.leftLinkLabel'])
                         ? $params['html.leftLinkLabel']
                         : '«';

        // Правая метка отстраничивателя. По умолчанию - »
        $rightLinkLabel = !empty($params['html.rightLinkLabel'])
                          ? $params['html.rightLinkLabel']
                          : '»';

        // Левая метка предыдущего блока страниц. По умолчанию - ««
        $lastBlockLeftLinkLabel = !empty($params['html.lastBlockLeftlinkLabel'])
                             ? $params['html.lastBlockLeftlinkLabel']
                             : str_repeat($leftLinkLabel, 2);

        // Правая метка следующего блока страниц. По умолчанию - »»
        $lastBlockRightLinkLabel = !empty($params['html.lastBlockRightlinkLabel'])
                             ? $params['html.lastBlockRightlinkLabel']
                             : str_repeat($rightLinkLabel, 2);

        $lastLeftLinkLabel = !empty($params['html.lastLeftlinkLabel'])
                             ? $params['html.lastLeftlinkLabel']
                             : str_repeat($leftLinkLabel, 4);

        $lastRightLinkLabel = !empty($params['html.lastRightlinkLabel'])
                             ? $params['html.lastRightlinkLabel']
                             : str_repeat($rightLinkLabel, 4);

?>
    <? if($this->navigation->getCurrentSeparator() && $this->navigation->getCurrentSeparator() != 1): ?>
        &nbsp;<a<?=$css_current__Separator?> title="На первую страницу" href="<?=$self_uri?>?<?=$get?><?=$this->navigation->getPageName()?>=1<?=$anchor?>"><?=$lastLeftLinkLabel?></a>&nbsp;
    <? endif; ?>


    <? if($this->navigation->getLastBlockSeparator()): ?>
        <a<?=$css_last_block__Separator?> title="Предыдущий блок страниц" href="<?=$self_uri?>?<?=$get?><?=$this->navigation->getSeparatorName()?>=<?=$this->navigation->getLastBlockSeparator()?><?=$anchor?>"><?=$lastBlockLeftLinkLabel?></a>&nbsp;
    <? endif; ?>


    <? if($this->navigation->getLastPageSeparator() && $this->navigation->getLastPagePage()): ?>
        <a<?=$css_last_page__Page?> title="Предыдущая страница" href="<?=$self_uri?>?<?=$get?><?=$this->navigation->getPageName()?>=<?=$this->navigation->getLastPagePage()?>&amp;<?=$this->navigation->getSeparatorName()?>=<?=$this->navigation->getLastPageSeparator()?><?=$anchor?>"><?=$leftLinkLabel?></a>&nbsp;
    <? endif; ?>


    <? foreach($this->navigation->getPagesArray() as $row): ?>
        <? if($this->navigation->getCurrentPage() == $row["page"]): ?>
            <span<?=$activeLinkClass?>><?=$row["page"]?></span>
        <? else: ?>
            <a<?=$normalLinkClass?> href="<?=$self_uri?>?<?=$get?><?=$this->navigation->getSeparatorName()?>=<?=$row["separator"]?>&amp;<?=$this->navigation->getPageName()?>=<?=$row["page"]?><?=$anchor?>"><?=$row["page"]?></a>
        <? endif; ?>
    <? endforeach; ?>


    <? if($this->navigation->getNextPageSeparator() && $this->navigation->getNextPagePage()): ?>
        &nbsp;<a<?=$css_next_page__Page?> title="Следующая страница" href="<?=$self_uri?>?<?=$get?><?=$this->navigation->getPageName()?>=<?=$this->navigation->getNextPagePage()?>&amp;<?=$this->navigation->getSeparatorName()?>=<?=$this->navigation->getNextPageSeparator()?><?=$anchor?>"><?=$rightLinkLabel?></a>
    <? endif; ?>


    <? if($this->navigation->getNextBlockSeparator()): ?>
        &nbsp;<a<?=$css_next_block__Separator?> title="Следующий блок страниц" href="<?=$self_uri?>?<?=$get?><?=$this->navigation->getSeparatorName()?>=<?=$this->navigation->getNextBlockSeparator()?><?=$anchor?>"><?=$lastBlockRightLinkLabel?></a>
    <? endif; ?>


    <? if($this->navigation->getLastSeparator() && $this->navigation->getCurrentSeparator() != $this->navigation->getLastSeparator()): ?>
        &nbsp;<a<?=$css_last__Separator?> title="На последнюю страницу" href="<?=$self_uri?>?<?=$get?><?=$this->navigation->getPageName()?>=<?=$this->navigation->getLastPage()?>&amp;<?=$this->navigation->getSeparatorName()?>=<?=$this->navigation->getLastSeparator()?><?=$anchor?>"><?=$lastRightLinkLabel?></a>
    <? endif; ?>
<?
        $str = ob_get_contents();
        ob_end_clean();

        return $str;
    }
}
?>