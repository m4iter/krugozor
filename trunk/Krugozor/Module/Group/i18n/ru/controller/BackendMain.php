<?php
return array
(
    'title' => array('Группы', 'Список групп'),

    'adding_group' => 'Добавление группы',
    'list_of_groups' => 'Список групп',
    'group_name' => 'Имя группы',
    'group_active' => 'Группа активна',
    'question_delete_group' => 'Вы действительно хотите удалить группу «%s»?\n\nУдаление группы приведёт к обнулению всех административных прав у пользователей, закреплённых за этой группой',
    'empty_list_of_groups' => 'Ещё не заведено ни одной группы',

    'notification' => array
    (
        'bad_id_group' => '<p>Указан некорректный идентификатор группы.</p>',
        'group_does_not_exist' => '<p>Группы с идентификатором <strong>{id}</strong> не существует.</p>',
        'group_edit_ok' => '<p>Данные группы <strong><a href="/admin/group/edit/?id={id}">{group_name}</a></strong> сохранены.</p>',
        'id_group_not_exists' => '<p>Не указан идентификатор группы.</p>',
        'group_delete' => '<p>Группа <strong>{group_name}</strong> удалена.</p>',
    )
)?>