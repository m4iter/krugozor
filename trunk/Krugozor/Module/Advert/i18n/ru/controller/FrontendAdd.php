<?php
return array
(
    'title' => array('����� ������� ������ ����������'),

    'notification' => array
    (
        'advert_save_ok' => '<p>���������� &laquo;<strong>{advert_header}</strong>&raquo; ������� ��������� � �������� ��� ������. �� ������ ����������, ��� <a target="_blank" href="/categories{category_url}{id}.xhtml">���������� �������� �� �����</a>.</p>
        <p>�������� ��������, ��� ���������� ������������� �� ��������� ���������� ����������� ������ � ��� ������, ���� ���� ���������� ������ ��� ����� ������ �����. �� ������ �������� ���������� ���������� ���������� ���� ���������� ������ �� ���������� � ������� ��� � ������. ��� ����� �������������� ��������� ����� <a onclick="void(document.getElementById(\'codes_for_paste_blogs\').style.display=\'block\')" href="#">��������</a>:</p>
        <div id="codes_for_paste_blogs">
        <p><strong>��� ��� ������� � ������:</strong></p>
        <div class="codes_for_blogs">[b][url=http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml]{advert_header}[/url][/b]</div>
        <p><strong>��� ��� ������� � �����:</strong></p>
        <div class="codes_for_blogs">&lt;p&gt;&lt;a href="http://'.$_SERVER['HTTP_HOST'].'/categories{category_url}{id}.xhtml"&gt;&lt;strong&gt;{advert_header}&lt;/strong&gt;&lt;/a&gt;&lt;/p&gt;</div>
        </div>',
    )
)
?>