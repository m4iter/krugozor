<?php
return array
(
    'title' => array('Выбор способа подачи объявления'),

    'notification' => array
    (
        'advert_save_ok' => '<p>Объявление &laquo;<strong>{advert_header}</strong>&raquo; успешно сохранено и доступно для поиска. Вы можете посмотреть, как <a target="_blank" href="/categories{category_url}{id}.xhtml">объявление выглядит на сайте</a>.</p>
        <p>Обратите внимание, что наибольшая эффективность от поданного объявления достигается только в том случае, если Ваше объявление увидит как можно больше людей. Вы можете повысить количество просмотров объявления путём размещения ссылки на объявление в форумах или в блогах. Для этого воспользуйтесь следующим кодом <a onclick="void(document.getElementById(\'codes_for_paste_blogs\').style.display=\'block\')" href="#">показать</a>:</p>
        <div id="codes_for_paste_blogs">
        <p><strong>Код для вставки в форумы:</strong></p>
        <div class="codes_for_blogs">[b][url=http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml]{advert_header}[/url][/b]</div>
        <p><strong>Код для вставки в блоги:</strong></p>
        <div class="codes_for_blogs">&lt;p&gt;&lt;a href="http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml"&gt;&lt;strong&gt;{advert_header}&lt;/strong&gt;&lt;/a&gt;&lt;/p&gt;</div>
        </div>',
    )
)
?>