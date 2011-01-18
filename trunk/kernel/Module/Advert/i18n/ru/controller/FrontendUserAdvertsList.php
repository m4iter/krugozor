<?php
return array
(
    'title' => array('ћой личный кабинет', 'ћои объ€влени€'),

    'advert_types_form_3' => array
    (
        'sale' => 'о предложени€х',
        'buy' => 'о спросе',
    ),

    'notification' => array
    (
        'bad_id_advert' => '<p>”казан некорректный идентификатор объ€влени€</p>',
        'advert_does_not_exist' => '<p>«апрошенное объ€вление не существует</p>',
        'advert_save_ok' => '<p>ќбъ€вление &laquo;<strong>{advert_header}</strong>&raquo; успешно сохранено и доступно дл€ поиска. ¬ы можете посмотреть, как <a target="_blank" href="/categories{category_url}{id}.xhtml">объ€вление выгл€дит на сайте</a>.</p>
        <p>ќбратите внимание, что наибольша€ эффективность от поданного объ€влени€ достигаетс€ только в том случае, если ¬аше объ€вление увидит как можно больше людей. ¬ы можете повысить количество просмотров объ€влени€ путЄм размещени€ ссылки на объ€вление в форумах или в блогах. ƒл€ этого воспользуйтесь следующим кодом <a onclick="void(document.getElementById(\'codes_for_paste_blogs\').style.display=\'block\')" href="#">показать</a>:</p>
        <div id="codes_for_paste_blogs">
        <p><strong> од дл€ вставки в форумы:</strong></p>
        <div class="codes_for_blogs">[b][url=http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml]{advert_header}[/url][/b]</div>
        <p><strong> од дл€ вставки в блоги:</strong></p>
        <div class="codes_for_blogs">&lt;p&gt;&lt;a href="http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml"&gt;&lt;strong&gt;{advert_header}&lt;/strong&gt;&lt;/a&gt;&lt;/p&gt;</div>
        </div>',
        'advert_delete' => '<p>ќбъ€вление &laquo;<strong>{advert_header}</strong>&raquo; удалено</p>',
        'advert_date_create_update' => '<p>ќбъ€вление &laquo;<strong>{advert_header}</strong>&raquo; подн€то в результатах поиска на сайте. Ёто значит, его увид€т больше посетителей сайта '.$_SERVER['HTTP_HOST'].'. —ледующее подн€тие данного объ€влени€ в поиске возможно через один час.</p>',
        'advert_date_create_not_update' => '<p>ќбъ€вление &laquo;<strong>{advert_header}</strong>&raquo; не может быть подн€то, т.к. недавно создано или уже было подн€то в результатах поиска менее одного часа назад. ѕовторите попытку после {date} минут.</p>',
        'advert_active_0' => '<p>ѕоказ объ€влени€ &laquo;<strong>{advert_header}</strong>&raquo; приостановлен. ќбъ€вление скрыто и недоступно дл€ просмотра пользовател€ми сайта.</p>',
        'advert_active_1' => '<p>ѕоказ объ€влени€  &laquo;<strong>{advert_header}</strong>&raquo; возобновлЄн. ќбъ€вление доступно дл€ просмотра всеми пользовател€ми сайта.</p>',
    ),
)
?>