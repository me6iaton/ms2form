<?php
/*
 * Properties English Lexicon Entries
 *
 * */

$_lang['ms2form_prop_limit'] = 'The number of results to limit.';
$_lang['ms2form_prop_offset'] = 'An offset of resources returned by the criteria to skip';
$_lang['ms2form_prop_depth'] = 'Integer value indicating depth to search for resources from each parent.';
$_lang['ms2form_prop_sortby'] = 'The field to sort by.';
$_lang['ms2form_prop_sortdir'] = 'The direction to sort by';
$_lang['ms2form_prop_parents'] = 'Container list, separated by commas, to search results. By default, the query is limited to the current parent. If set to 0, query not limited.';
$_lang['ms2form_prop_parents_include_tvs'] = 'ms2form_prop_parents_include_tvs';
$_lang['ms2form_prop_parents_sortby'] = 'ms2form_prop_parents_sortby';
$_lang['ms2form_prop_parents_sortdir'] = 'ms2form_prop_parents_sortdir';
$_lang['ms2form_prop_resources'] = 'Comma-delimited list of ids to include in the results. Prefix an id with a dash to exclude the resource from the result.';
$_lang['ms2form_prop_resources'] = 'Comma-delimited list of threads to include in the results. Prefix an id with a dash to exclude this thread from the result.';
$_lang['ms2form_prop_where'] = 'A JSON-style expression of criteria to build any additional where clauses from';
$_lang['ms2form_prop_tvPrefix'] = 'The prefix for TemplateVar properties, "tv." for ex`ample. By default it is empty.';
$_lang['ms2form_prop_includeContent'] = 'Retrieve field "content" from resources.';
$_lang['ms2form_prop_includeTVs'] = 'An optional comma-delimited list of TemplateVar names to include in selection. For example "action,time" give you placeholders [[+action]] and [[+time]].';
$_lang['ms2form_prop_toPlaceholder'] = 'If not empty, the snippet will save output to placeholder with that name, instead of return it to screen.';
$_lang['ms2form_prop_outputSeparator'] = 'An optional string to separate each tpl instance.';

$_lang['ms2form_prop_showLog'] = 'Display additional information about snippet work. Only for authenticated in context "mgr".';
$_lang['ms2form_prop_showUnpublished'] = 'Show unpublished resources.';
$_lang['ms2form_prop_showDeleted'] = 'Show deleted goods.';
$_lang['ms2form_prop_showHidden'] = 'Show goods, that hidden in menu.';
$_lang['ms2form_prop_fastMode'] = 'If enabled, then in chunk will be only received values ​​from the database. All raw tags of MODX, such as filters, snippets calls will be cut.';

$_lang['ms2form_prop_action'] = 'Mode of snippet';
$_lang['ms2form_prop_cacheKey'] = 'Name of snippet cache. If empty - caching will be disabled.';
$_lang['ms2form_prop_cacheTime'] = 'Time of cache.';
$_lang['ms2form_prop_thread'] = 'Name of comments thread. By default, "resource-[[*id]]".';
$_lang['ms2form_prop_user'] = 'Select only elements created by this user.';

$_lang['ms2form_prop_tpl'] = 'The chunk tpl to use for each row.';
$_lang['ms2form_prop_tplFormCreate'] = 'Chunk for creation of new ticket';
$_lang['ms2form_prop_tplFormUpdate'] = 'Chunk for update existing ticket';
$_lang['ms2form_prop_tplSectionRow'] = 'Chunk for template one section of form select';
$_lang['ms2form_prop_tplPreview'] = 'Chunk for preview ticket before publish';
$_lang['ms2form_prop_tplCommentForm'] = 'The add comment form. Can either be a chunk name or value. If set to a value, will override the chunk.';
$_lang['ms2form_prop_tplCommentAuth'] = 'Chunk for displaying comment for authorized user.';
$_lang['ms2form_prop_tplCommentGuest'] = 'Chunk for displaying comment for guests.';
$_lang['ms2form_prop_tplCommentDeleted'] = 'Chunk for displaying deleted comment.';
$_lang['ms2form_prop_tplComments'] = 'Chunk for the outer wrapper for comments.';
$_lang['ms2form_prop_tplLoginToComment'] = 'Chunk  for guests with requirement of authorization.';
$_lang['ms2form_prop_tplCommentEmailOwner'] = 'Chunk for notification of new comments of ticket owner.';
$_lang['ms2form_prop_tplCommentEmailReply'] = 'Chunk for notification of user who replied to a comment.';
$_lang['ms2form_prop_tplCommentEmailSubscription'] = 'Chunk for notification of a subscriber about new comment.';
$_lang['ms2form_prop_tplCommentEmailBcc'] = 'Chunk for bcc notification about new ticket.';
$_lang['ms2form_prop_tplTicketEmailBcc'] = 'Chunk for bcc notification about new comment.';
$_lang['ms2form_prop_tplWrapper'] = 'Name of a chunk serving as a wrapper template for the output. This does not work with toSeparatePlaceholders.';

$_lang['ms2form_prop_commentsDepth'] = 'Integer value indicating maximum depth of comments thread.';
$_lang['ms2form_prop_autoPublish'] = 'If true, all comments of authenticated users in thread will be published without moderation.';
$_lang['ms2form_prop_autoPublishGuest'] = 'If true, all comments of anonymous users in thread will be published without moderation.';
$_lang['ms2form_prop_formBefore'] = 'If true, commenting form will be placed before comments.';
//$_lang['ms2form_prop_dateFormat'] = 'The format of the dates displayed for a comment, with function date().';
$_lang['ms2form_prop_gravatarIcon'] = 'The default Gravatar icon to load if none is found for a user.';
$_lang['ms2form_prop_gravatarSize'] = 'The size, in pixels, of the Gravatar.';
$_lang['ms2form_prop_gravatarUrl'] = 'The URL of Gravatar';

$_lang['ms2form_prop_allowedFields'] = 'Fields of the ticket, which allowed to fill the user. You can specify the names of the TVs.';
$_lang['ms2form_prop_requiredFields'] = 'The required fields of the ticket, which the user must fill in to send the form.';
$_lang['ms2form_prop_redirectUnpublished'] = 'You can specify what document to send the user when creating unpublished ticket.';
$_lang['ms2form_prop_sections_parents'] = 'There is all sections are displayed by default, but you can limit them, specifying comma-separated list of particular parents of sections.';
$_lang['ms2form_prop_sections_resources'] = 'There is all sections are displayed by default, but you can limit them, specifying comma-separated list of particular sections.';
$_lang['ms2form_prop_sections_permissions'] = 'Checking publishing rights section. By default is checked permission "section_add_children".';
$_lang['ms2form_prop_sections_sortby'] = 'Field to sort the list of sections.';
$_lang['ms2form_prop_sections_sortdir'] = 'Sort direction for the list of sections.';
$_lang['ms2form_prop_sections_context'] = 'Comma separated list of contexts to search sections.';

$_lang['ms2form_prop_meta_tpl'] = 'Chunk for templating information about a ticket.';
$_lang['ms2form_prop_getSection'] = 'Make additional query in the DB to obtain the parent section?';
$_lang['ms2form_prop_getUser'] = 'Make additional query in the DB to obtain a profile of the author?';

$_lang['ms2form_prop_allowGuest'] = 'Enable commenting for unauthorized users?';
$_lang['ms2form_prop_allowGuestEdit'] = 'Allow unauthorized users to edit their comments?';
$_lang['ms2form_prop_allowGuestEmails'] = 'Send to the guests email notifications about the replies?';
$_lang['ms2form_prop_enableCaptcha'] = 'Enable protection against spam for unauthorized users?';
$_lang['ms2form_prop_minCaptcha'] = 'The minimum number for generate spam-protecting code.';
$_lang['ms2form_prop_maxCaptcha'] = 'The maximum number for generate spam-protecting code.';

$_lang['ms2form_prop_allowFiles'] = 'Allow user to upload files to the server.';
$_lang['ms2form_prop_source'] = 'Id of media source for upload files. By default will be used media source specified in system setting "ms2form.source_default".';
$_lang['ms2form_prop_tplFiles'] = 'Container to display the uploader and the list of already uploaded files.';
$_lang['ms2form_prop_tplFile'] = 'The chunk for uploaded file, that not an image.';
$_lang['ms2form_prop_tplImage'] = 'The chunk for uploaded image.';

$_lang['ms2form_prop_getFiles'] = 'Display a list of uploaded files?';
$_lang['ms2form_prop_unusedFiles'] = 'Display only those files, links on which are absent in the content of the ticket.';
$_lang['ms2form_prop_meta_tplFile'] = 'The chunk for file in the list.';

$_lang['ms2form_prop_class'] = 'Specify class for selecting results.';