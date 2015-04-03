<?php
/*
 * Properties English Lexicon Entries
 *
 * */

$_lang['ms2form_prop_sortby'] = 'The field to sort by.';
$_lang['ms2form_prop_sortdir'] = 'The direction to sort by';

$_lang['ms2form_prop_parent'] = 'Id основной категории для публикации ресурса. Обязательно для заполнения';
$_lang['ms2form_prop_parents'] = 'Список id, через запятую,
 родителей категорий в которых будет опубликован ресурс одновременно с основной категорией. По умолчанию выводятся все доступные категории';
$_lang['ms2form_prop_parentsIncludeTVs'] = 'Список названий TV, через запятую, которые будут выводится вместе с дополнительными категориями';
$_lang['ms2form_prop_parentsSortby'] = 'Поле для сортировки дополнительных категорий, можно использовать TV';
$_lang['ms2form_prop_parentsSortdir'] = 'Направление сортировки дополнительных категорий';
$_lang['ms2form_prop_resources'] = 'Список id, через запятую, категорий в которых будет опубликован ресурс одновременно с основной категориией. Альтернатива parents';
$_lang['ms2form_prop_templates'] = 'Список id шаблонов для публикации ресурсов формата "1==Базовый,2==Дополнительный", можно указать только один id шаблона, по умолчанию используется шаблон с id равным 1';

$_lang['ms2form_prop_tplCreate'] = 'Chunk for creation of new   resource';
$_lang['ms2form_prop_tplUpdate'] = 'Chunk for update existing';
$_lang['ms2form_prop_tplSectionRow'] = 'Chunk for section option';
$_lang['ms2form_prop_tplTagRow'] = 'Chunk for tag';
$_lang['ms2form_prop_tplTicketEmailBcc'] = 'Chunk for bcc notification about new comment.';
$_lang['ms2form_prop_allowedFields'] = 'Fields of the resource, which allowed to fill the user. You can specify the names of the TVs.';
$_lang['ms2form_prop_requiredFields'] = 'The required fields of the resource, which the user must fill in to send the form.';
$_lang['ms2form_prop_sections_permissions'] = 'Checking publishing rights section. By
default is checked permission "section_add_children".';
$_lang['ms2form_prop_allowFiles'] = 'Allow user to upload files to the server.';
$_lang['ms2form_prop_source'] = 'Id of media source for upload files. By default will be used media source specified in system setting "ms2form.source_default".';
$_lang['ms2form_prop_tplFiles'] = 'Container to display the uploader and the list of already uploaded files.';
$_lang['ms2form_prop_tplFile'] = 'The chunk for uploaded file, that not an image.';
$_lang['ms2form_prop_tplImage'] = 'The chunk for uploaded image.';
$_lang['ms2form_prop_meta_tplFile'] = 'The chunk for file in the list.';
$_lang['ms2form_prop_tags'] = 'Разрешить или запретить вывод тегов';
