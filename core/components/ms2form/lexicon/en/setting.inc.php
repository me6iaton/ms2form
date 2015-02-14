<?php
/**
 * Settings Russian Lexicon Entries
 */

$_lang['area_ms2form_main'] = 'Основные';
$_lang['area_ms2form_mail'] = 'Почтовые уведомления';

$_lang['setting_ms2form_frontend_css'] = 'Стили фронтенда';
$_lang['setting_ms2form_frontend_css_desc'] = 'Путь к файлу со стилями магазина. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_ms2form_frontend_js'] = 'Скрипты фронтенда';
$_lang['setting_ms2form_frontend_js_desc'] = 'Путь к файлу со скриптами магазина. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';

$_lang['setting_ms2form_default_template'] = 'Шаблон для новых тикетов';
$_lang['setting_ms2form_default_template_desc'] = 'Шаблон "по умолчанию" для новых тикетов. Используется и в административной части, и при создании тикета на фронтенде.';

$_lang['setting_ms2form_hidemenu_force'] = 'Не показывать тикеты в меню';
$_lang['setting_ms2form_hidemenu_force_desc'] = 'Обязательное указание параметра "hidemenu" у тикетов';
$_lang['setting_ms2form_ticket_show_in_tree_default'] = 'Показывать в дереве по умолчанию';
$_lang['setting_ms2form_ticket_show_in_tree_default_desc'] = 'Включите эту опцию, чтобы все создаваемые тикеты были видны в дереве ресурсов.';

$_lang['setting_ms2form_private_ticket_page'] = 'Редирект с приватных тикетов';
$_lang['setting_ms2form_private_ticket_page_desc'] = 'Id существующего ресурса MODX, на который отправлять пользователя, если у него недостаточно прав для просмотра приватного тикета.';

$_lang['setting_ms2form_unpublished_ticket_page'] = 'Страница неопубликованных тикетов';
$_lang['setting_ms2form_unpublished_ticket_page_desc'] = 'Id существующего ресурса MODX, которая будет показана при запросе неопубликованного тикета.';


$_lang['setting_ms2form_mail_from'] = 'Ящик исходящей почты';
$_lang['setting_ms2form_mail_from_desc'] = 'Адрес для отправки почтовых уведомлений. Если не заполнен - будет использована настройка "emailsender".';
$_lang['setting_ms2form_mail_from_name'] = 'Имя отправителя';
$_lang['setting_ms2form_mail_from_name_desc'] = 'Имя, от которого будут отправлены все уведомления. Если не заполнен - будет использована настройка "site_name".';
$_lang['setting_ms2form_mail_createdby'] = 'Оправлять создателю';
$_lang['setting_ms2form_mail_createdby_desc'] = 'Отправлять уведомление создателю ресурса';


$_lang['setting_ms2form_mail_bcc'] = 'Уведомлять администраторов';
$_lang['setting_ms2form_mail_bcc_desc'] = 'Укажите через запятую список <b>id</b> администраторов, которым нужно отправлять сообщения о новых тикетах и комментариях.';