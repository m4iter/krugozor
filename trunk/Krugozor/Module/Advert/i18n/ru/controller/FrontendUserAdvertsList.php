<?php
return array
(
    'title' => array('��� ������ �������', '��� ����������'),

    'advert_types_form_3' => array
    (
        'sale' => '� ������������',
        'buy' => '� ������',
    ),

    'notification' => array
    (
        'bad_id_advert' => '<p>������ ������������ ������������� ����������</p>',
        'advert_does_not_exist' => '<p>����������� ���������� �� ����������</p>',
        'advert_save_ok' => '<p>���������� &laquo;<strong>{advert_header}</strong>&raquo; ������� ��������� � �������� ��� ������. �� ������ ����������, ��� <a target="_blank" href="/categories{category_url}{id}.xhtml">���������� �������� �� �����</a>.</p>
        <p>�������� ��������, ��� ���������� ������������� �� ��������� ���������� ����������� ������ � ��� ������, ���� ���� ���������� ������ ��� ����� ������ �����. �� ������ �������� ���������� ���������� ���������� ���� ���������� ������ �� ���������� � ������� ��� � ������. ��� ����� �������������� ��������� ����� <a onclick="void(document.getElementById(\'codes_for_paste_blogs\').style.display=\'block\')" href="#">��������</a>:</p>
        <div id="codes_for_paste_blogs">
        <p><strong>��� ��� ������� � ������:</strong></p>
        <div class="codes_for_blogs">[b][url=http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml]{advert_header}[/url][/b]</div>
        <p><strong>��� ��� ������� � �����:</strong></p>
        <div class="codes_for_blogs">&lt;p&gt;&lt;a href="http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml"&gt;&lt;strong&gt;{advert_header}&lt;/strong&gt;&lt;/a&gt;&lt;/p&gt;</div>
        </div>',
        'advert_delete' => '<p>���������� &laquo;<strong>{advert_header}</strong>&raquo; �������</p>',
        'advert_date_create_update' => '<p>���������� &laquo;<strong>{advert_header}</strong>&raquo; ������� � ����������� ������ �� �����. ��� ������, ��� ������ ������ ����������� ����� '.$_SERVER['HTTP_HOST'].'. ��������� �������� ������� ���������� � ������ �������� ����� ���� ���.</p>',
        'advert_date_create_not_update' => '<p>���������� &laquo;<strong>{advert_header}</strong>&raquo; �� ����� ���� �������, �.�. ������� ������� ��� ��� ���� ������� � ����������� ������ ����� ������ ���� �����. ��������� ������� ����� {date} �����.</p>',
        'advert_active_0' => '<p>����� ���������� &laquo;<strong>{advert_header}</strong>&raquo; �������������. ���������� ������ � ���������� ��� ��������� �������������� �����.</p>',
        'advert_active_1' => '<p>����� ����������  &laquo;<strong>{advert_header}</strong>&raquo; ����������. ���������� �������� ��� ��������� ����� �������������� �����.</p>',
    ),
)
?>