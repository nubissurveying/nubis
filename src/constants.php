<?php

/* 
------------------------------------------------------------------------
Copyright (C) 2014 Bart Orriens, Albert Weerman

This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
------------------------------------------------------------------------
*/

define('SOFTWARE_TITLE', 'NubiS');
define('SOFTWARE_LOGO', 'NubiS.png');

/* javascript */

define('JAVASCRIPT_INDICATOR','js_enabled');

define('JAVASCRIPT_CHOSEN','js_chosen');



/* survey execution modes */

define('SURVEY_EXECUTION_MODE', 'SURVEY_EXECUTION_MODE');

define('SURVEY_EXECUTION_MODE_NORMAL', 0);

define('SURVEY_EXECUTION_MODE_TEST', 1);



/* survey components */
define('SURVEY_COMPONENT_SECTION', 1);
define('SURVEY_COMPONENT_VARIABLE', 2);
define('SURVEY_COMPONENT_TYPE', 3);
define('SURVEY_COMPONENT_SETTING', 4);
define('SURVEY_COMPONENT_FILL', 5);
define('SURVEY_COMPONENT_INLINEFIELDS', 6);
define('SURVEY_COMPONENT_ROUTING', 7);
define('SURVEY_COMPONENT_GROUP', 8);

/* basic engine*/
define('ACTION_ENTRY', 1);
define('ACTION_EXIT_BACK', 2);
define('ACTION_EXIT_NEXT', 3);
define('ACTION_EXIT_DK', 4);
define('ACTION_EXIT_RF', 5);
define('ACTION_EXIT_NA', 6);
define('ACTION_EXIT_UPDATE', 7);
define('ACTION_EXIT_LANGUAGE_CHANGE', 8);
define('ACTION_EXIT_MODE_CHANGE', 9);
define('ACTION_EXIT_VERSION_CHANGE', 10);
define('ACTION_EXIT_PROGRAMMATIC_UPDATE', 11);
define('ACTION_DYNAMIC', 12);
define('ACTION_SURVEY_ENTRY', 13);
define('ACTION_SURVEY_REENTRY', 14);
define('ACTION_SURVEY_END', 15);
define('ACTION_SURVEY_RETURN', 16);
define('ACTION_WINDOW_OUT', 17);
define('ACTION_WINDOW_IN', 18);

/* post parameters admin */
define('SMS_POST_LANGUAGE', 'surveylanguage');
define('SMS_POST_MODE', 'surveymode');
define('SMS_POST_SURVEY', 'survey');

/* post parameters (for survey start) */
define('POST_PARAM_SUID', 'suid');
define('POST_PARAM_SEID', 'seid');
define('POST_PARAM_PRIMKEY', 'primkey');
define('POST_PARAM_STATEID', 'stateid');
define('POST_PARAM_PARADATA', 'para');
define('POST_PARAM_DISPLAYED', 'displayed');
define('POST_PARAM_LANGUAGE', 'language');
define('POST_PARAM_GROUP', 'groupname');
define('POST_PARAM_TEMPLATE', 'template');
define('POST_PARAM_DEFAULT_LANGUAGE', 'defaultlanguage');
define('POST_PARAM_DEFAULT_MODE', 'defaultmode');
define('POST_PARAM_PHPID', 'phpid');
define('POST_PARAM_MODE', 'mode');
define('POST_PARAM_VERSION', 'version');
define('POST_PARAM_RGID', 'rgid');
define('POST_PARAM_SE', 'se');
define('POST_PARAM_R', 'r');
define('POST_PARAM_FULLRESET', 'rs'); // drop post
define('POST_PARAM_RESET', 'ss'); // clear, but keep session id and keep post (SMS start)
define('POST_PARAM_RESET_TEST', 'ts'); // clear, but keep session id and keep post (SURVEY TESTER)
define('POST_PARAM_RESET_EXTERNAL', 'ms'); // end but keep post (EXTERNAL SURVEY START, NOT SMS)
define('POST_PARAM_PRELOAD', 'pd'); //preload
define('POST_PARAM_SURVEY_EXECUTION_MODE', 'executionmode');
define('POST_PARAM_URID', 'pu'); //preload
define('POST_PARAM_NEW_PRIMKEY', 'newpk');
define('POST_PARAM_SCREENSHOT', 'sshot');
define('POST_PARAM_SCREENSHOT_CAPTURE', 'sshotcapture');
define('POST_PARAM_REMARK_STORE', 'remarkstore');
define('POST_PARAM_REMARK_REMOVE', 'remarkremove');
define('POST_PARAM_REMARK', 'remark');
define('POST_PARAM_REMARK_INDICATOR', 'rm');
define('POST_PARAM_ALIVE_INDICATOR', 'ai');
define('POST_PARAM_WINDOW_ACTIVE', 'windowactive');
define('POST_PARAM_WINDOW_SWITCH', 'ws');
define('POST_PARAM_ERROR_SHOWN', 'errorshown');
define('POST_PARAM_ERROR_TYPE_SHOWN', 'es');
define('POST_PARAM_EXECUTION_MODE', 'executionmode');
define('POST_PARAM_SYSTEM_KEY', 'sk');

define('SESSION_PARAM_VARIABLES', "variables");
define('SESSION_PARAM_GROUP', 'group');
define('SESSION_PARAM_RGID', "rgid");
define('SESSION_PARAMS_ANSWER', "answer");
define('SESSION_PARAM_PRIMKEY', "primkey");
define('SESSION_PARAM_LANGUAGE', "language");
define('SESSION_PARAM_TIMESTAMP', "timestamp");
define('SESSION_PARAM_NEWLANGUAGE', "newlanguage");
define('SESSION_PARAM_SURVEY', "survey");
define('SESSION_PARAM_NEWSURVEY', "newsurvey");
define('SESSION_PARAM_VERSION', "version");
define('SESSION_PARAM_NEWVERSION', "newversion");
define('SESSION_PARAM_MODE', "mode");
define('SESSION_PARAM_NEWMODE', "newmode");
define('SESSION_PARAM_LASTACTION', "lastaction");
define('SESSION_PARAM_SUID', 'suid');
define('SESSION_PARAM_SEID', "seid");
define('SESSION_PARAM_TEMPLATE', 'template');
define('SESSION_PARAM_NEWTEMPLATE', 'newtemplate');

define('SESSION_PARAM_MAINSEID', 'mainseid');

define('PARAMETER_SURVEY_RETRIEVAL', '1');
define('PARAMETER_ADMIN_RETRIEVAL', '2');

define('NAVIGATION_LANGUAGE_CHANGE' , 'hiddenlanguagechange');
define('NAVIGATION_MODE_CHANGE', 'hiddenmodechange');
define('NAVIGATION_VERSION_CHANGE', 'hiddenversionchange');
define('PROGRAMMATIC_UPDATE', 'programmaticupdate');

define('VARIABLE_PRIMKEY', "prim_key");
define('VARIABLE_INTRODUCTION', 'introduction');
define('VARIABLE_THANKS', 'thanks');
define('VARIABLE_LOCKED', 'locked');
define('VARIABLE_IN_PROGRESS', 'inprogress');
define('VARIABLE_COMPLETED', 'completed');
define('VARIABLE_LANGUAGE', "language");
define('VARIABLE_EXECUTION_MODE', 'execution_mode');
define('VARIABLE_BEGIN', "begintime");
define('VARIABLE_DIRECT', 'direct');
define('VARIABLE_END', "endtime");
define('VARIABLE_LOGIN', 'login');
define('VARIABLE_VERSION', "version");
define('VARIABLE_MODE', "mode");
define('VARIABLE_PLATFORM', 'platform');
define('VARIABLE_DEVICE', 'device');
define('VARIABLE_ACCESS', 'access');
define('VARIABLE_CLOSED', 'closed');
define('VARIABLE_TEMPLATE', 'template');


define('TYPE_LANGUAGE', 'TLanguage');

define('SEPARATOR_SETOFENUMERATED', "-");
define('SEPARATOR_SETOFENUMERATED_OLD', '~');
define('SEPARATOR_COMPARISON', "#");
define('INDICATOR_INLINEFIELD_ANSWER', "~");
define('INDICATOR_INLINEFIELD_TEXT', "`");
define('INDICATOR_FILL', "^");
define('INDICATOR_FILL_NOVALUE', "*");
define('DUMMY_INDICATOR_FILL', "DUMMY_INDICATOR_FILL");
define('DUMMY_INDICATOR_FILL_NOVALUE', "DUMMY_INDICATOR_FILL_NOVALUE");
define('DUMMY_INDICATOR_INLINEFIELD_ANSWER', "DUMMY_INDICATOR_INLINEFIELD_ANSWER");
define('DUMMY_INDICATOR_INLINEFIELD_TEXT', "DUMMY_INDICATOR_INLINEFIELD_TEXT");

define('INDICATOR_CUSTOMTEMPLATE', "#");
define('INDICATOR_CUSTOMTEMPLATEQUESTION', "TEXT");
define('INDICATOR_CUSTOMTEMPLATEANSWER', "INPUT");


define('IMPORT_TYPE_BLAISE', 1);
define('IMPORT_TYPE_MMIC', 2);
define('IMPORT_TYPE_NUBIS', 3);
define('IMPORT_TARGET_ADD', 1);
define('IMPORT_TARGET_REPLACE', 2);

define('MODE_CAPI', 1);
define('MODE_CATI', 2);
define('MODE_CASI', 3);
define('MODE_CADI', 4);

define('MODE_LABEL_CAPI', "Face-to-face");
define('MODE_LABEL_CATI', "Telephone");
define('MODE_LABEL_CASI', "Self-administered");
define('MODE_LABEL_CADI', "Data entry");




define('SCREENDUMPS_YES', 1);
define('SCREENDUMPS_NO', 2);
define('PARADATA_YES', 1);
define('PARADATA_NO', 2);
define('SLIDER_LABEL_PLACEMENT_BOTTOM', 1);
define('SLIDER_LABEL_PLACEMENT_TOP', 2);
define('DATA_DIRTY', 1);
define('DATA_CLEAN', 2);
define('DATA_NOTHIDDEN', 1);
define('DATA_HIDDEN', 0);
define('DATA_TEST', 1);
define('DATA_REAL', 2);
define('PARADATA_RAW', 1);
define('PARADATA_PROCESSED', 2);
define('PARADATA_ERRORS', 3);
define('SUBDATA_YES', 1);
define('SUBDATA_NO', 2);
define('VALUELABEL_YES', 1);
define('VALUELABEL_NO', 2);
define('VALUELABELNUMBERS_YES', 1);
define('VALUELABELNUMBERS_NO', 2);
define('VALUELABEL_WIDTH_SHORT', 1);
define('VALUELABEL_WIDTH_FULL', 2);
define('PRIMARYKEY_YES', 1);
define('PRIMARYKEY_NO', 2);
define('FIELDNAME_LOWERCASE', 1);
define('FIELDNAME_UPPERCASE', 2);
define('FILETYPE_STATA', 1);
define('FILETYPE_CSV', 2);
define('VARIABLES_WITHOUT_DATA_YES', 1);
define('VARIABLES_WITHOUT_DATA_NO', 2);
define('MARKEMPTY_IN_VARIABLE', 1);
define('MARKEMPTY_IN_SKIP_VARIABLE', 2);
define('MARKEMPTY_NO', 3);
define('ALIGN_LEFT', 1);
define('ALIGN_RIGHT', 2);
define('ALIGN_JUSTIFIED', 3);
define('ALIGN_CENTER', 4);
define('FORMATTING_BOLD', 1);
define('FORMATTING_ITALIC', 2);
define('FORMATTING_UNDERLINED', 3);
define('SETOFENUMERATED_DEFAULT', 1);
define('SETOFENUMERATED_BINARY', 2);

define('DATA_OUTPUT_MAINTABLE', 'maintable');
define('DATA_OUTPUT_MAINDATATABLE', 'maindatatable');
define('DATA_OUTPUT_FILETYPE', "filetype");
define('DATA_OUTPUT_FILENAME', "filename");
define('DATA_OUTPUT_FILENAME_STATA', "statafilename");
define('DATA_OUTPUT_FILENAME_CSV', "csvfilename");
define('DATA_OUTPUT_PRIMARY_KEY_IN_DATA', "primkeyindata");
define('DATA_OUTPUT_SETOFENUMERATED', "setofenumeratedoutput");
define('DATA_OUTPUT_VARIABLES_WITHOUT_DATA', "variableswithoutdata");
define('DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION', 'primkeyencryption');
define('DATA_OUTPUT_FIELDNAME_CASE','fieldnamecase');
define('DATA_OUTPUT_INCLUDE_VALUE_LABELS', "includevaluelabels");
define('DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS', "includevaluelabelnumbers");
define('DATA_OUTPUT_MARK_EMPTY', 'markempty');
define('DATA_OUTPUT_KEEP_ONLY', 'datakeeponly');
define('DATA_OUTPUT_CLEAN', 'cleandata');
define('DATA_OUTPUT_COMPLETED', 'completedinterviews');
define('DATA_OUTPUT_TYPEDATA', 'typedata');
define('DATA_OUTPUT_SUBDATA', 'subdata');
define('DATA_OUTPUT_TYPEPARADATA', 'typeparadata');
define('DATA_OUTPUT_VARLIST', 'varlist');
define('DATA_OUTPUT_LANGUAGES', 'languages');
define('DATA_OUTPUT_VERSIONS', 'versions');
define('DATA_OUTPUT_TYPE', 'retrievetype');
define('DATA_OUTPUT_TYPE_DATA_TABLE', 'datatable');
define('DATA_OUTPUT_TYPE_DATARECORD_TABLE', 'datarecordtable');
define('DATA_OUTPUT_MODES', 'modes');
define('DATA_OUTPUT_SURVEY', 'survey');
define('DATA_OUTPUT_HIDDEN', 'hidden');
define('DATA_OUTPUT_VALUELABEL_PREFIX', 'valueLabelPrefix');
define('DATA_OUTPUT_VALUELABEL_WIDTH', 'valueLabelWidth');
define('DATA_OUTPUT_FORMATLANGUAGE', 'formatlanguage');
define('DATA_OUTPUT_FORMATMODE', 'formatmode');
define('DATA_OUTPUT_FILEEXTENSION_STATA', 'dta');
define('DATA_OUTPUT_FILEEXTENSION_CSV', 'csv');
define('DATA_OUTPUT_ENCODING', 'encoding');
define('DATA_OUTPUT_FROM', 'datafrom');
define('DATA_OUTPUT_TO', 'datato');
define('OUTPUT_SCREENDUMPS_TYPE_HTML', "1");
define('OUTPUT_SCREENDUMPS_TYPE_CAROUSEL', "2");
define('OBJECT_SURVEY', 1);

define('OBJECT_VARIABLEDESCRIPTIVE', 2);

define('OBJECT_SECTION', 3);

define('OBJECT_TYPE', 4);

define('OBJECT_GROUP', 5);






/* generator */

define('CLASS_BASICENGINE', "BasicEngine");
define('CLASS_ENGINE', "Engine");
define('CLASS_BASICFILL', "BasicFill");
define('CLASS_GETFILL', "GetFill");
define('CLASS_SETFILL', "SetFill");
define('CLASS_BASICINLINEFIELD', 'BasicInlineField');
define('CLASS_INLINEFIELD', 'InlineField');
define('CLASS_BASICCHECK', 'BasicCheck');
define('CLASS_CHECK', "Check");

define('LOOP_MAXIMUM_IF_UNDEFINED', 20); // is 20 a reasonable number to assume (not likely to have loops constituting more than 20 times)
define('LOOP_MINIMUM_IF_UNDEFINED', 1); // is 1 a reasonable number to assume (not likely to have reverse loops going below 1)

define('LOOP_MARKER', '!L');

define('FUNCTION_IS_NULL', 'is_null');
define('FUNCTION_DO_ACTION', 'doAction');
define('FUNCTION_GET_FIRST_ACTION', 'getFirstAction');
define('FUNCTION_DO_LOOP', 'doForLoop');
define('FUNCTION_DO_LOOP_LEFTOFF', 'addForLoopLastAction');
define('FUNCTION_DO_WHILE', 'doWhileLoop');
define('FUNCTION_DO_WHILE_GROUP', 'doWhileLoopGroup');
define('FUNCTION_DO_WHILE_LEFTOFF', 'addWhileLastAction');
define('FUNCTION_DO_LOOP_GROUP', 'doForLoopGroup');
define('FUNCTION_DO_REVERSELOOP', 'doReverseForLoop');
define('FUNCTION_DO_REVERSELOOP_GROUP', 'doReverseForLoopGroup');
define('FUNCTION_DO_GROUP', 'doGroup');
define('FUNCTION_DO_SUBGROUP', 'doSubGroup');
define('FUNCTION_DO_END', 'doEnd');
define('FUNCTION_DO_ASSIGNMENT', 'doAssignment');
define('FUNCTION_DO_SECTION', 'doSection');
define('FUNCTION_DO_FILL', 'doFill');
define('FUNCTION_DO_INSPECT', 'doInspect');
define('FUNCTION_DO_INSPECT_SECTION', 'doInspectSection');
define('FUNCTION_DO_IF', 'doIf');
define('FUNCTION_DO_ELSEIF', 'doElseIf');
define('FUNCTION_DO_ELSE', 'doElse');
define('FUNCTION_DO_MOVE_BACKWARD', 'doMoveBackward');
define('FUNCTION_DO_MOVE_FORWARD', 'doMoveForward');
define('FUNCTION_SHOW_QUESTION', 'showQuestion');
define('FUNCTION_GET_ANSWER', 'getAnswer');
define('FUNCTION_SET_ANSWER', 'setAnswer');
define('FUNCTION_CHECK_ANSWER', 'checkAnswer');
define('FUNCTION_DO_EXIT', 'doExit');
define('FUNCTION_IN_ARRAY', 'inArray');
define('FUNCTION_ADD_ASSIGNMENT', 'addAssignment');
define('FUNCTION_ADD_INLINE_FIELD', 'addInlineField');
//define('FUNCTION_IS_INLINE_FIELD', 'isInlineField');
define('FUNCTION_GET_INLINE_FIELD', 'getInlineField');
define('FUNCTION_GET_INLINE_FIELD_VALUE', 'getInlineFieldValue');
define('FUNCTION_GET_FILL_VALUE', 'getFillValue');
define('FUNCTION_GET_FILL_TEXT_BY_LINE', 'getFillTextByLine');
define('FUNCTION_SET_FILL_VALUE', 'setFillValue');
define('FUNCTION_ADD_FILL_VALUE', 'addFillValue');
define('FUNCTION_ADD_SUB_DISPLAY', 'addSubDisplay');
define('FUNCTION_ADD_SUB_GROUP', 'addSubGroup');
define('FUNCTION_SHOW', 'show');
define('FUNCTION_GET_ERROR_TEXT_BY_LINE', 'getCheckTextByLine');
define('FUNCTION_DO_CHECK_RETURN', 'returnCheck');
define('FUNCTION_SET_CHECK_LEVEL', 'setCheckLevel');
define('FUNCTION_CHECK_ERROR_RETURN', 'RETURN');
define('VARIABLE_THIS', "this");

define('VARIABLE_VALUE_NULL', "NULL");

define('VARIABLE_VALUE_DK', "DK");
define('VARIABLE_VALUE_NA', "NA");
define('VARIABLE_VALUE_RF', "RF");

define('VARIABLE_VALUE_INARRAY', "INARRAY");

define('VARIABLE_VALUE_EMPTY', 'EMPTY');

define('VARIABLE_VALUE_FILL', 'FILL');
define('VARIABLE_VALUE_SOFT_ERROR', 'SOFTERROR');
define('VARIABLE_VALUE_HARD_ERROR', 'HARDERROR');
define('VARIABLE_VALUE_RESPONSE', 'RESPONSE');

define('VARIABLE_HIDDEN', 1);

define('VARIABLE_VISIBLE', 0);
define('VARIABLE_ENABLED', 1);
define('VARIABLE_DISABLED', 2);
define('TEXT_RANDOM', 'dnudsqdqwa');

define('TEXT_RANDOM_FILL', 'dnudsqdqwa');

define('TEXT_MODULE_DOT', '_buysdtqw_');
define('TEXT_BRACKET_RIGHT_MODULE', ') & ');

define('TEXT_BRACKET_LEFT', '(');

define('TEXT_BRACKET_RIGHT', ')');



define('ROUTING_IF', 'IF');

define('ROUTING_ELSEIF', 'ELSEIF');

define('ROUTING_ELSE', 'ELSE');

define('ROUTING_THEN', 'THEN');

define('ROUTING_ENDIF', 'ENDIF');

define('ROUTING_FOR', 'FOR');

define('ROUTING_ENDDO', 'ENDDO');

define('ROUTING_GROUP', 'GROUP');
define('ROUTING_SUBGROUP', 'SUBGROUP');
define('ROUTING_ENDGROUP', 'ENDGROUP');
define('ROUTING_ENDSUBGROUP', 'ENDSUBGROUP');

define('ROUTING_KEEP', '.KEEP');
define('ROUTING_IDENTIFY_EXIT', 'EXIT');
define('ROUTING_IDENTIFY_EXITFOR', 'EXITFOR');
define('ROUTING_IDENTIFY_EXITWHILE', 'EXITWHILE');


define('ROUTING_IDENTIFY_IF', 'IF ');

define('ROUTING_IDENTIFY_ELSEIF', 'ELSEIF ');

define('ROUTING_IDENTIFY_ELSE', 'ELSE');

define('ROUTING_IDENTIFY_ENDIF', 'ENDIF');

define('ROUTING_IDENTIFY_FOR', 'FOR ');
define('ROUTING_IDENTIFY_FORREVERSE', 'FORREVERSE ');
define('ROUTING_IDENTIFY_DO', 'DO');
define('ROUTING_IDENTIFY_ENDDO', 'ENDDO');
define('ROUTING_IDENTIFY_WHILE', 'WHILE ');
define('ROUTING_IDENTIFY_ENDWHILE', 'ENDWHILE');

define('ROUTING_IDENTIFY_GROUP', 'GROUP.');

define('ROUTING_IDENTIFY_ENDGROUP', 'ENDGROUP');

define('ROUTING_IDENTIFY_SUBGROUP', 'SUBGROUP.');

define('ROUTING_IDENTIFY_ENDSUBGROUP', 'ENDSUBGROUP');

define('ROUTING_IDENTIFY_KEEP', '.KEEP');
define('ROUTING_IDENTIFY_INLINE', '.INLINE');
define('ROUTING_IDENTIFY_INSPECT', '.INSPECT');
define('ROUTING_IDENTIFY_INSPECT_SECTION', '.INSPECTSECTION');
define('ROUTING_IDENTIFY_FILL', '.FILL');

define('ROUTING_MOVE_BACKWARD', 'MOVEBACKWARD.');

define('ROUTING_MOVE_FORWARD', 'MOVEFORWARD.');



/* settings */

/* keyboard binding */
define('KEYBOARD_BINDING_NO', 1);
define('KEYBOARD_BINDING_YES', 2);

define('INDIVIDUAL_DKRFNA_YES', 1);
define('INDIVIDUAL_DKRFNA_NO', 2);

define('TIMEOUT_YES', 1);
define('TIMEOUT_NO', 2);


/* input masking */
define('INPUTMASK_INTEGER', 'integer');
define('INPUTMASK_DOUBLE', 'decimal');
define('INPUTMASK_USPHONE', 'usphone');
define('INPUTMASK_USCURRENCY', 'uscurrency');
define('INPUTMASK_EUROCURRENCY', 'eurocurrency');
define('INPUTMASK_EMAILSHORT', 'emailshort');
define('INPUTMASK_SOCIAL', 'social');
define('INPUTMASK_MEDICARE', 'medicare');
define('INPUTMASK_CUSTOM', 'custom');

define('INPUT_MASK_NO', 1);
define('INPUT_MASK_YES', 2);

define('DEFAULT_SURVEY_NO', 1);
define('DEFAULT_SURVEY_YES', 2);

/* knob rotation */
define('KNOB_ROTATION_CLOCKWISE', '1');
define('KNOB_ROTATION_ANTICLOCKWISE', '2');

/* survey */
define('SETTING_TITLE', 'surveytitle');
define('SETTING_DEFAULT_LANGUAGE', "defaultlanguage");
define('SETTING_ALLOWED_LANGUAGES', "availablelanguages");
define('SETTING_CHANGE_LANGUAGE', "changelanguage");
define('SETTING_BACK_LANGUAGE', 'backlanguage');
define('SETTING_REENTRY_LANGUAGE', "reentrylanguage");
define('SETTING_DEFAULT_SURVEY', "defaultsurvey");
define('SETTING_DEFAULT_VERSION', "defaultversion");
define('SETTING_DEFAULT_MODE', "defaultmode");
define('SETTING_ALLOWED_MODES', "availablemodes");
define('SETTING_CHANGE_MODE', "changemode");
define('SETTING_BACK_MODE', 'backmode');
define('SETTING_REENTRY_MODE', "reentrymode");
define('SETTING_SCRIPTS', 'scripts');

define('SETTING_ON_NEXT', 'onnext');
define('SETTING_ON_BACK', 'onback');
define('SETTING_ON_DK', 'ondk');
define('SETTING_ON_RF', 'onrf');
define('SETTING_ON_NA', 'onna');
define('SETTING_ON_UPDATE', 'onupdate');
define('SETTING_ON_LANGUAGE_CHANGE', 'onlanguagechange');
define('SETTING_ON_MODE_CHANGE', 'onmodechange');
define('SETTING_ON_VERSION_CHANGE', 'onversionchange');

define('SETTING_CLICK_NEXT', 'clicknext');
define('SETTING_CLICK_BACK', 'clickback');
define('SETTING_CLICK_DK', 'clickdk');
define('SETTING_CLICK_RF', 'clickrf');
define('SETTING_CLICK_NA', 'clickna');
define('SETTING_CLICK_UPDATE', 'clickupdate');
define('SETTING_CLICK_LANGUAGE_CHANGE', 'clicklanguagechange');
define('SETTING_CLICK_MODE_CHANGE', 'clickmodechange');
define('SETTING_CLICK_VERSION_CHANGE', 'clickversionchange');

define('SETTING_PAGE_HEADER', 'pageheader');
define('SETTING_PAGE_FOOTER', 'pagefooter');

define('SETTING_ACCESS_TYPE', "accesstype");
define('SETTING_ACCESS_DEVICE', "accessdevice");

define('SETTING_ACCESS_DATES_FROM', "accessdatesfrom");

define('SETTING_ACCESS_DATES_TO', "accessdatesto");

define('SETTING_ACCESS_TIMES_FROM', "accesstimesfrom");

define('SETTING_ACCESS_TIMES_TO', "accesstimesto");

define('SETTING_ACCESS_RETURN', "accessreturn");
define('SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION', 'accessreturnaftercompletionaction');
define('SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO', 'accessreturnaftercompletionpreloadredo');
define('SETTING_ACCESS_REENTRY_ACTION', "accessreentryaction");
define('SETTING_ACCESS_REENTRY_PRELOAD_REDO', "accessreentrypreloadredo");
define('SETTING_ACCESS_EXIT', "accessexit");

define('SETTING_DATA_KEEP_ONLY', 'datakeeponly');
define('SETTING_DATA_KEEP', 'datakeep');
define('SETTING_DATA_SKIP', 'dataskip');
define('SETTING_DATA_SKIP_POSTFIX', 'dateskipostfix');
define('SETTING_DATA_INPUTMASK', 'datainputmask');
define('SETTING_DATA_STORE_LOCATION', 'storelocation');
define('SETTING_DATA_STORE_LOCATION_EXTERNAL', 'storelocationexternal');

/* templates */
define('TABLE_TEMPLATE_DEFAULT', 'default');
define('TABLE_TEMPLATE_SIMPLE', 'simpletable');
define('TABLE_TEMPLATE_SINGLEROW', 'singlerowtable');
define('TABLE_TEMPLATE_SINGLECOLUMN', 'singlecolumntable');
define('TABLE_TEMPLATE_TWOCOLUMN', 'twocolumntable');
define('TABLE_TEMPLATE_THREECOLUMN', 'threecolumntable');
define('TABLE_TEMPLATE_FOURCOLUMN', 'fourcolumntable');
define('TABLE_TEMPLATE_FIVECOLUMN', 'fivecolumntable');
define('TABLE_TEMPLATE_SIXCOLUMN', 'sixcolumntable');
define('TABLE_TEMPLATE_SEVENCOLUMN', 'sevencolumntable');
define('TABLE_TEMPLATE_EIGHTCOLUMN', 'eightcolumntable');
define('TABLE_TEMPLATE_NINECOLUMN', 'ninecolumntable');
define('TABLE_TEMPLATE_ENUMERATED', 'enumeratedtable');
define('TABLE_TEMPLATE_ENUMERATED_REVERSE', 'reverseenumeratedtable');
define('TABLE_TEMPLATE_CUSTOM', 'custom');
define('TABLE_QUESTION_COLUMN_WIDTH', 25);
define('TABLE_WIDTH', 100);
define('TABLE_SCROLL', 600);
define('TABLETEMPLATE_STRIPED', 'striped');
define('TABLETEMPLATE_CONDENSED', 'condensed');
define('TABLETEMPLATE_HOVERED', 'hovered');
define('TABLETEMPLATE_BORDERED', 'bordered');


/* search */
define('SEARCH_OPEN_YES', 1);
define('SEARCH_OPEN_NO', 2);

/* settings */
define('SETTING_TABLE_HEADERS', 'tableheaders');
define('SETTING_TABLE_WIDTH', 'tablewidth');
define('SETTING_QUESTION_COLUMN_WIDTH', 'questioncolumnwidth');
define('SETTING_NAME', 'name');
define('SETTING_DESCRIPTION', 'description');
define('SETTING_ROUTING', 'routing');
define('SETTING_QUESTION', 'question');
define('SETTING_REQUIREANSWER', 'requireanswer');
define('SETTING_SECTION_HEADER', 'sectionheader');
define('SETTING_SECTION_FOOTER', 'sectionfooter');
define('SETTING_SHOW_SECTION_HEADER', 'showsectionheader');
define('SETTING_SHOW_SECTION_FOOTER', 'showsectionfooter');

define('SETTING_OPTIONS', 'options');
define('SETTING_ENUMERATED_ORIENTATION', 'enumeratedorientation');
define('SETTING_ENUMERATED_BORDERED', 'enumeratedbordered');
define('SETTING_ENUMERATED_SPLIT', 'enumeratedsplit');
define('SETTING_ENUMERATED_ORDER', 'enumeratedorder');
define('SETTING_ENUMERATED_CUSTOM', 'enumeratedcustom');
define('SETTING_ENUMERATED_TEXTBOX', 'enumeratedtextbox');
define('SETTING_ENUMERATED_TEXTBOX_LABEL', 'enumeratedtextboxlabel');
define('SETTING_ENUMERATED_TEXTBOX_POSTTEXT', 'eneumeratedtextboxposttext');
define('SETTING_ENUMERATED_LABEL', 'enumeratedlabel');
define('SETTING_ENUMERATED_CLICK_LABEL', 'enumeratedclicklabel');
define('SETTING_ENUMERATED_RANDOMIZER', 'enumeratedrandomizer');
define('SETTING_ENUMERATED_COLUMNS', 'enumeratedcolumns');
define('SETTING_SETOFENUMERATED_RANKING', 'setofenumeratedranking');
define('SETOFENUMERATED_RANKING_YES', 1);
define('SETOFENUMERATED_RANKING_NO', 2);
define('ENUMERATED_LABEL_LABEL_ONLY', 1);
define('ENUMERATED_LABEL_LABEL_CODE', 2);
define('ENUMERATED_LABEL_LABEL_CODE_VALUELABEL', 3);
define('ENUMERATED_LABEL_INPUT_ONLY', 4);
define('SETTING_DROPDOWN_OPTGROUP', 'dropdownoptgroup');
define('SETTING_RANK_COLUMN', 'rankcolumn');
define('CLICK_LABEL_YES', '1');
define('CLICK_LABEL_NO', '2');
define('MOBILE_LABEL_YES', '1');
define('MOBILE_LABEL_NO', '2');


define('SETTING_IFEMPTY', 'ifempty');

define('SETTING_IFERROR', 'iferror');

define('SETTING_KEEP', 'keep');

define('SETTING_ARRAY', 'array');

define('SETTING_HIDDEN', 'hidden');
define('SETTING_HIDDEN_ROUTING', 'hiddenrouting');
define('SETTING_HIDDEN_PAPER_VERSION', 'hiddenpaperversion');
define('SETTING_HIDDEN_TRANSLATION', 'hiddentranslations');
define('SETTING_SCREENDUMPS', 'screendumps');
define('SETTING_PARADATA', 'paradata');
define('SETTING_DATA_ENCRYPTION_KEY', 'dataencryptionkey');
define('SETTING_OUTPUT_ENCRYPTED', 'outputencrypted');
define('SETTING_OUTPUT_SETOFENUMERATED', 'outputseofenumerated');
define('SETTING_OUTPUT_VALUELABEL_WIDTH', 'outputvalueLabelwidth');
define('SETTING_OUTPUT_OPTIONS', 'outputoptions');
define('SETTING_SECTION', 'section');

define('SETTING_ANSWERTYPE', 'answertype');
define('SETTING_ANSWERTYPE_CUSTOM', 'answertypecustom');
define('SETTING_MINIMUM_RANGE', 'minimum');
define('SETTING_MAXIMUM_RANGE', 'maximum');
define('SETTING_OTHER_RANGE', 'othervalues');

define('SETTING_INPUT_MASK', 'inputmask');
define('SETTING_INPUT_MASK_CUSTOM', 'inputmaskcustom');
define('SETTING_INPUT_MASK_ENABLED', 'inputmaskenabled');
define('SETTING_INPUT_MASK_PLACEHOLDER', 'inputmaskplaceholder');
define('SETTING_INPUT_MASK_CALLBACK', 'inputmaskcallback');
define('SETTING_KEYBOARD_BINDING_ENABLED', 'keyboardbindingenabled');
define('SETTING_KEYBOARD_BINDING_NEXT', 'keyboardbindingnext');
define('SETTING_KEYBOARD_BINDING_BACK', 'keyboardbindingback');
define('SETTING_KEYBOARD_BINDING_DK', 'keyboardbindingdk');
define('SETTING_KEYBOARD_BINDING_RF', 'keyboardbindingrf');
define('SETTING_KEYBOARD_BINDING_NA', 'keyboardbindingna');
define('SETTING_KEYBOARD_BINDING_UPDATE', 'keyboardbindingupdate');
define('SETTING_KEYBOARD_BINDING_REMARK', 'keyboardbindingremark');
define('SETTING_KEYBOARD_BINDING_CLOSE', 'keyboardbindingclose');
define('SETTING_DKRFNA', 'individualdkrfna');
define('SETTING_DKRFNA_SINGLE', 'individualdkrfnasingle');
define('SETTING_DKRFNA_INLINE', 'individualdkrfnainline');
define('SETTING_TIMEOUT', 'timeout');
define('SETTING_TIMEOUT_LENGTH', 'timeoutlength');
define('SETTING_TIMEOUT_ALIVE_BUTTON', 'timeoutalivebutton');
define('SETTING_TIMEOUT_LOGOUT_BUTTON', 'timeoutlogoutbutton');
define('SETTING_TIMEOUT_LOGOUT', 'timeoutlogouturl');
define('SETTING_TIMEOUT_REDIRECT', 'timeoutredirecturl');
define('SETTING_TIMEOUT_TITLE', 'timeouttitle');

define('SETTING_COMPARISON_EQUAL_TO', 'comparisonequalto');
define('SETTING_COMPARISON_NOT_EQUAL_TO', 'comparisonnotequalto');
define('SETTING_COMPARISON_GREATER_EQUAL_TO', 'comparisongreaterequalto');
define('SETTING_COMPARISON_GREATER', 'comparisongreater');
define('SETTING_COMPARISON_SMALLER_EQUAL_TO', 'comparisonsmallerequalto');
define('SETTING_COMPARISON_SMALLER', 'comparisonsmaller');
define('SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE', 'comparisonequaltonocase');
define('SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE', 'comparisonnotequaltonocase');

define('SETTING_MINIMUM_LENGTH', 'minimumlength');
define('SETTING_MAXIMUM_LENGTH', 'maximumlength');
define('SETTING_MINIMUM_OPEN_LENGTH', 'minimumlength');
define('SETTING_MAXIMUM_OPEN_LENGTH', 'maximumlength');
define('SETTING_MINIMUM_WORDS', 'minimumwords');
define('SETTING_MAXIMUM_WORDS', 'maximumwords');
define('SETTING_MINIMUM_SELECTED', 'minimumselected');
define('SETTING_EXACT_SELECTED', 'exactselected');
define('SETTING_MAXIMUM_SELECTED', 'maximumselected');
define('SETTING_INVALIDSUB_SELECTED', 'invalidsubselected');
define('SETTING_INVALID_SELECTED', 'invalidselected');
define('SETTING_MINIMUM_REQUIRED', 'minimumrequired');
define('SETTING_MAXIMUM_REQUIRED', 'maximumrequired');
define('SETTING_MINIMUM_CALENDAR', 'minimumcalendar');
define('SETTING_MAXIMUM_CALENDAR', 'maximumcalendar');
define('SETTING_PATTERN', 'pattern');
define('SETTING_MINIMUM_RANKED', 'minimumranked');
define('SETTING_EXACT_RANKED', 'exactranked');
define('SETTING_MAXIMUM_RANKED', 'maximumranked');


define('SETTING_BACK_BUTTON', 'backbutton');

define('SETTING_NEXT_BUTTON', 'nextbutton');

define('SETTING_DK_BUTTON', 'dkbutton');

define('SETTING_RF_BUTTON', 'rfbutton');
define('SETTING_NA_BUTTON', 'nabutton');
define('SETTING_UPDATE_BUTTON', 'updatebutton');
define('SETTING_REMARK_BUTTON', 'remarkbutton');
define('SETTING_REMARK_SAVE_BUTTON', 'remarksavebutton');
define('SETTING_CLOSE_BUTTON', 'closebutton');

define('SETTING_BACK_BUTTON_LABEL', 'backbuttonlabel');
define('SETTING_NEXT_BUTTON_LABEL', 'nextbuttonlabel');
define('SETTING_DK_BUTTON_LABEL', 'dkbuttonlabel');
define('SETTING_RF_BUTTON_LABEL', 'rfbuttonlabel');
define('SETTING_UPDATE_BUTTON_LABEL', 'updatebuttonlabel');
define('SETTING_NA_BUTTON_LABEL', 'nabuttonlabel');
define('SETTING_REMARK_BUTTON_LABEL', 'remarkbuttonlabel');
define('SETTING_REMARK_SAVE_BUTTON_LABEL', 'remarksavebuttonlabel');
define('SETTING_CLOSE_BUTTON_LABEL', 'closebuttonlabel');

define('SETTING_QUESTION_ALIGNMENT', 'questionalignment');
define('SETTING_ANSWER_ALIGNMENT', 'answeralignment');
define('SETTING_ANSWER_FORMATTING', 'answerformatting');
define('SETTING_HEADER_FORMATTING', 'tableheaderformatting');
define('SETTING_HEADER_ALIGNMENT', 'tableheaderalignment');
define('SETTING_HEADER_FIXED', 'tableheaderfixed');
define('SETTING_HEADER_SCROLL_DISPLAY', 'tableheaderscrolldisplay');
define('SETTING_FOOTER_DISPLAY', 'tablefooterdisplay');
define('SETTING_QUESTION_FORMATTING', 'questionformatting');
define('SETTING_BUTTON_ALIGNMENT', 'buttonalignment');
define('SETTING_BUTTON_FORMATTING', 'buttonformatting');

define('SETTING_PROGRESSBAR_SHOW', 'progressbarshow');
define('SETTING_PROGRESSBAR_TYPE', 'progressbartype');
define('SETTING_PROGRESSBAR_FILLED_COLOR', 'progressbarfilledcolor');
define('SETTING_PROGRESSBAR_REMAIN_COLOR', 'progressbarremaincolor');
define('SETTING_PROGRESSBAR_VALUE', 'progressbarvalue');
define('SETTING_PROGRESSBAR_WIDTH', 'progressbarwidth');

define('SETTING_FILLTEXT', 'filltext');
define('SETTING_FILLCODE', 'fillcode');
define('SETTING_CHECKTEXT', 'checktext');
define('SETTING_CHECKCODE', 'checkcode');


define('SETTING_SPINNER', 'spinner');
define('SPINNER_YES', 1);
define('SPINNER_NO', 2);
define('SETTING_SPINNER_TYPE', 'spinnertype');
define('SPINNER_TYPE_VERTICAL', 1);
define('SPINNER_TYPE_HORIZONTAL', 2);
define('SETTING_SPINNER_UP', 'spinnerup');
define('SETTING_SPINNER_DOWN', 'spinnerdown');
define('SETTING_SPINNER_STEP', 'spinnerstep');
define('SETTING_TEXTBOX_MANUAL', 'textboxmanual');
define('MANUAL_YES', 1);
define('MANUAL_NO', 2);

define('SETTING_KNOB_ROTATION', 'knobrotation');
define('SETTING_SLIDER_INCREMENT', 'sliderincrement');
define('SETTING_SLIDER_ORIENTATION', 'sliderorientation');
define('SETTING_SLIDER_TOOLTIP', 'slidertooltip');
define('SETTING_SLIDER_FORMATER', 'sliderformater');
define('SETTING_SLIDER_PRESELECTION', 'sliderpreselection');
define('SETTING_SLIDER_TEXTBOX', 'slidertextbox');
define('SETTING_SLIDER_TEXTBOX_LABEL', 'slidertextboxlabel');
define('SETTING_SLIDER_TEXTBOX_POSTTEXT', 'slidertextboxposttext');
define('SETTING_SLIDER_LABELS', 'sliderlabels');
define('SETTING_SLIDER_LABEL_PLACEMENT', 'sliderlabelplacement');
define('SETTING_ERROR_PLACEMENT', 'errorplacement');
define('SETTING_EMPTY_MESSAGE', 'emptymessage');
define('SETTING_LOGIN_ERROR', 'loginerror');

define('SETTING_ERROR_MESSAGE_RANGE', 'errormessagerange');

define('SETTING_ERROR_MESSAGE_INTEGER', 'errormessageinteger');

define('SETTING_ERROR_MESSAGE_DOUBLE', 'errormessagedouble');

define('SETTING_ERROR_MESSAGE_PATTERN', 'errormessagepattern');

define('SETTING_ERROR_MESSAGE_MINIMUM_LENGTH', 'errormessageminlength');

define('SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH', 'errormessagemaxlength');

define('SETTING_ERROR_MESSAGE_MINIMUM_WORDS', 'errormessageminwords');

define('SETTING_ERROR_MESSAGE_MAXIMUM_WORDS', 'errormessagemaxwords');

define('SETTING_ERROR_MESSAGE_MINIMUM_SELECT', 'errormessageminselect');

define('SETTING_ERROR_MESSAGE_MAXIMUM_SELECT', 'errormessagemaxselect');

define('SETTING_ERROR_MESSAGE_EXACT_SELECT', 'errormessageexactselect');

define('SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT', 'errormessageinvalidsubselect');

define('SETTING_ERROR_MESSAGE_INVALID_SELECT', 'errormessageinvalidselect');

define('SETTING_ERROR_MESSAGE_MINIMUM_RANK', 'errormessageminrank');

define('SETTING_ERROR_MESSAGE_MAXIMUM_RANK', 'errormessagemaxrank');

define('SETTING_ERROR_MESSAGE_EXACT_RANK', 'errormessageexactrank');


define('SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED', 'errorminimumrequired');
define('SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED', 'errormaximumrequired');
define('SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR', 'errormessagemaximumcalendar');
define('SETTING_ERROR_MESSAGE_EXACT_REQUIRED', 'errorexactrequired');
define('SETTING_ERROR_MESSAGE_INCLUSIVE', 'errorinclusive');
define('SETTING_ERROR_MESSAGE_EXCLUSIVE', 'errorexclusive');
define('SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED', 'erroruniquerequired');
define('SETTING_ERROR_MESSAGE_SAME_REQUIRED', 'errorsamerequired');

define('SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED', 'errorinlineminimumrequired');
define('SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED', 'errorinlinemaximumrequired');
define('SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_CALENDAR', 'errorinlinemessagemaximumcalendar');
define('SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED', 'errorinlineexactrequired');
define('SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE', 'errorinlineinclusive');
define('SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE', 'errorinlineexclusive');
define('SETTING_ERROR_MESSAGE_INLINE_ANSWERED', 'errorinlineanswered');

define('SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED', 'enumentered');
define('SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED', 'setofenumentered');

define('SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO', 'errorcomparisonequalto');
define('SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO', 'errorcomparisonnotequalto');
define('SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO', 'errorcomparisongreaterequalto');
define('SETTING_ERROR_MESSAGE_COMPARISON_GREATER', 'errorcomparisongreater');
define('SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO', 'errorcomparisonsmallerequalto');
define('SETTING_ERROR_MESSAGE_COMPARISON_SMALLER', 'errorcomparisonsmaller');
define('SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE', 'errorcomparisonequaltoignorecase');
define('SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE', 'errorcomparisonnotequaltoignorecase');


define('SETTING_ID', 'id');
define('SETTING_JAVASCRIPT_WITHIN_ELEMENT', 'elementjavascript');
define('SETTING_JAVASCRIPT_WITHIN_PAGE', 'pagejavascript');
define('SETTING_STYLE_WITHIN_ELEMENT', 'elementstyle');
define('SETTING_STYLE_WITHIN_PAGE', 'pagestyle');
define('SETTING_FOLLOW_TYPE', 'settingfollowtype');
define('SETTING_FOLLOW_GENERIC', 'settingfollowgeneric');
define('SETTING_TYPE', 'type');

define('SETTING_PLACEHOLDER', 'placeholder');
define('SETTING_PRETEXT', 'pretext');
define('SETTING_POSTTEXT', 'posttext');
define('SETTING_HOVERTEXT', 'hovertext');

define('SETTING_DATE_FORMAT', 'settingdateformat');
define('SETTING_TIME_FORMAT', 'settingtimeformat');
define('SETTING_DATETIME_FORMAT', 'settingdatetimeformat');
define('SETTING_DATE_DEFAULT_VIEW', 'settingdatedefaultview');
define('SETTING_DATETIME_COLLAPSE', 'settingdatetimecollapse');
define('SETTING_DATETIME_SIDE_BY_SIDE', 'settingdatetimesidebyside');

define('DATE_COLLAPSE_YES', '1');
define('DATE_COLLAPSE_NO', '2');
define('DATE_SIDE_BY_SIDE_YES', '1');
define('DATE_SIDE_BY_SIDE_NO', '2');

define('SETTING_SURVEY_TEMPLATE', 'surveytemplate');
define('SETTING_SURVEY_CHANGE_TEMPLATE', 'surveytemplatechange');
define('SETTING_GROUP_NAME', 'groupname');
define('SETTING_GROUP_TEMPLATE', "template");
define('SETTING_GROUP_XI_TEMPLATE', "xitemplate");
define('SETTING_GROUP_CUSTOM_TEMPLATE', 'customtemplate');
define('SETTING_GROUP_EXCLUSIVE', "exclusive");
define('SETTING_GROUP_INCLUSIVE', "inclusive");
define('SETTING_GROUP_MINIMUM_REQUIRED', "minimumrequired");
define('SETTING_GROUP_MAXIMUM_REQUIRED', "maximumrequired");
define('SETTING_GROUP_EXACT_REQUIRED', "exactrequired");
define('SETTING_GROUP_UNIQUE_REQUIRED', "uniquerequired");
define('SETTING_GROUP_SAME_REQUIRED', "samerequired");
define('SETTING_GROUP_TYPE', 'grouptype');
define('SETTING_GROUP_TABLE_ID', 'tableid');
define('SETTING_GROUP_TABLE_STRIPED', 'tablestriped');

define('SETTING_GROUP_TABLE_BORDERED', 'tablebordered');

define('SETTING_GROUP_TABLE_CONDENSED', 'tablecondensed');
define('SETTING_GROUP_TABLE_HOVERED', 'tablehovered');
define('SETTING_TABLE_MOBILE', 'tablemobile');
define('SETTING_TABLE_MOBILE_LABELS', 'tablemobilelabels');


define('SETTING_INLINE_EXCLUSIVE', "inlineexclusive");
define('SETTING_INLINE_INCLUSIVE', "inlineinclusive");
define('SETTING_INLINE_MINIMUM_REQUIRED', "inlineminimumrequired");
define('SETTING_INLINE_MAXIMUM_REQUIRED', "inlinemaximumrequired");
define('SETTING_INLINE_EXACT_REQUIRED', "inlineexactrequired");

define('SETTING_USER_MODE' , 'usermode');
define('SETTING_USER_LANGUAGES', 'userlanguages');
define('SETTING_USER_MODES', 'usermodes');
define('SETTING_USER_SURVEYS', 'usersurveys');
define('SETTING_USER_SURVEY', 'usersurvey');

define('SETTING_VALIDATE_ASSIGNMENT', 'assignmentvalidation');
define('VALIDATE_ASSIGNMENT_YES', 1);
define('VALIDATE_ASSIGNMENT_NO', 2);
define('VALID_ASSIGNMENT', 1);
define('INVALID_ASSIGNMENT', 2);

define('SETTING_APPLY_CHECKS', 'applychecks');
define('APPLY_CHECKS_YES', 1);
define('APPLY_CHECKS_NO', 2);

define('SETTING_MULTICOLUMN_QUESTIONTEXT', 'multicolumnquestiontext');
define('SETTING_COMBOBOX_DEFAULT', 'comboboxdefault');

/* placeholders */
define('PLACEHOLDER_TEXTFIELD', '$textfield$');
define('PLACEHOLDER_PATTERN', '$pattern$');

define('PLACEHOLDER_MINIMUM', '$minimum$');
define('PLACEHOLDER_MAXIMUM', '$maximum$');
define('PLACEHOLDER_OTHERVALUES', '$othervalues$');
define('PLACEHOLDER_MINIMUM_LENGTH', '$minimumlength$');

define('PLACEHOLDER_MAXIMUM_LENGTH', '$maximumlength$');

define('PLACEHOLDER_MINIMUM_WORDS', '$minimumwords$');

define('PLACEHOLDER_MAXIMUM_WORDS', '$maximumwords$');

define('PLACEHOLDER_MINIMUM_RANKED', '$minimumranked$');

define('PLACEHOLDER_MAXIMUM_RANKED', '$maximumranked$');

define('PLACEHOLDER_EXACT_RANKED', '$exactranked$');


define('PLACEHOLDER_MINIMUM_SELECTED', '$minimumselected$');

define('PLACEHOLDER_MAXIMUM_SELECTED', '$maximumselected$');

define('PLACEHOLDER_EXACT_SELECTED', '$exactselected$');

define('PLACEHOLDER_INVALIDSUBSET_SELECTED', '$invalidsubsetselected$');

define('PLACEHOLDER_INVALIDSET_SELECTED', '$invalidsetselected$');

define('PLACEHOLDER_MINIMUM_REQUIRED', '$minimumrequired$');
define('PLACEHOLDER_MAXIMUM_REQUIRED', '$maximumrequired$');
define('PLACEHOLDER_EXACT_REQUIRED', '$exactrequired$');
define('PLACEHOLDER_MAXIMUM_CALENDAR', '$maximumdates$');

define('PLACEHOLDER_INLINE_MINIMUM_REQUIRED', '$minimumrequired$');
define('PLACEHOLDER_INLINE_MAXIMUM_REQUIRED', '$maximumrequired$');
define('PLACEHOLDER_INLINE_EXACT_REQUIRED', '$exactrequired$');
define('PLACEHOLDER_ENUMERATED_OPTION', '$option');
define('PLACEHOLDER_ENUMERATED_TEXT', '$text');
define('PLACEHOLDER_ENUMERATED_CODE', '$code');
define('PLACEHOLDER_ERROR_ANSWER', '#answer');
define('PLACEHOLDER_ERROR_ANSWER_VALUE', '#answer_value');

/* interview status survey engine */
define('INTERVIEW_NOTCOMPLETED', 0);
define('INTERVIEW_COMPLETED', 1);
define('INTERVIEW_LOCKED', 1);
define('INTERVIEW_UNLOCKED', 2);


/* ajax loading */
define('POST_PARAM_AJAX_LOAD', "ajaxload");
define('AJAX_LOAD', 2);
define('POST_PARAM_NOAJAX', "noajax");
define('NOAJAX', 2);
define('POST_PARAM_SURVEY_AJAX', 'surveyajax');
define('SURVEY_AJAX_CALL', 1);
define('POST_PARAM_SMS_AJAX', "ajax");
define('SMS_AJAX_CALL', 'smsajax');

/* answer and setting defaults */
define('PATTERN_WHITESPACE', '(?:\s+)?');
define('PATTERN_WHITESPACE_MULTIPLE', '(?:\s+)');
define('PATTERN_NONWHITESPACE', '([^\s|(|!=><;:#]+)');
define('PATTERN_CASE_INSENSITIVE', '(?i)');
define('PATTERN_WORDBREAK', '\b');
define('PATTERN_BREAKSTART', PATTERN_NONWHITESPACE . PATTERN_WHITESPACE); 
define('PATTERN_BREAKEND', PATTERN_WHITESPACE . PATTERN_WORDBREAK);
define('PATTERN_ALPHANUMERIC', '([\[\]a-z_\-0-9\+\*\%\/ ,]+)');
define('PATTERN_EQUALTO', '==');
define('PATTERN_NOTEQUALTO', '(<>|!=)');
define('LOGICAL_TRUE', 'true');
define('LOGICAL_AND', ' AND ');
define('LOGICAL_OR', ' OR ');
define('LOGICAL_NOT', 'NOT');
define('LOGICAL_IN', ' IN ');
define('ANSWER_DK', 'DK');
define('ANSWER_RF', 'RF');
define('ANSWER_NA', 'NA');

define('ANSWER_EMPTY', 'EMPTY');

define('ANSWER_RESPONSE', 'RESPONSE');
define('ANSWER_NONRESPONSE', 'NONRESPONSE');
define('ANSWER_RANGE_MINIMUM', 0);

define('ANSWER_RANGE_MAXIMUM', PHP_INT_MAX);
define('ANSWER_PRIMKEY_LENGTH', 20);
define('ANSWER_OPEN_MIN_LENGTH', 0);
define('ANSWER_OPEN_MAX_LENGTH', 10000);
define('ANSWER_STRING_MIN_LENGTH', 0);
define('ANSWER_STRING_MAX_LENGTH', 500);

define('ANSWER_OPEN_MIN_WORDS', 0);

define('ANSWER_OPEN_MAX_WORDS', 5000);

define('ANSWER_CALENDAR_MAXSELECTED', 100000000);

define('ANSWER_PATTERN', '');



/* error checking */

define('ERROR_CHECK_REQUIRED', "required");

define('ERROR_CHECK_PATTERN', "pattern");

define('ERROR_CHECK_EMPTY', "empty");

define('ERROR_CHECK_NOTEMPTY', "notempty");

define('ERROR_CHECK_MINLENGTH', "minlength");

define('ERROR_CHECK_MAXLENGTH', "maxlength");

define('ERROR_CHECK_RANGELENGTH', "rangelength");

define('ERROR_CHECK_MIN', "min");

define('ERROR_CHECK_MAX', "max");

define('ERROR_CHECK_RANGE', "range");
define('ERROR_CHECK_RANGE_CUSTOM', 'rangecustom');
define('ERROR_CHECK_EMAIL', "email");

define('ERROR_CHECK_URL', "url");

define('ERROR_CHECK_DATE', "date");

define('ERROR_CHECK_DATEISO', "dateISO");

define('ERROR_CHECK_NUMBER', "number");

define('ERROR_CHECK_INTEGER', "integer");

define('ERROR_CHECK_DIGITS', "digits");

define('ERROR_CHECK_EQUALTO', "equalTo");

define('ERROR_CHECK_NOTEQUALTO', "notEqualTo");

define('ERROR_CHECK_ALPHANUMERIC', "alphanumeric");

define('ERROR_CHECK_ZIPCODEUS', "zipcodeUS");

define('ERROR_CHECK_LETTERSONLY', "lettersonly");

define('ERROR_CHECK_MAXWORDS', "maxWords");

define('ERROR_CHECK_MINWORDS', "minWords");

define('ERROR_CHECK_RANGEWORDS', "rangeWords");

define('ERROR_CHECK_MINSELECTED', "minimumselected");

define('ERROR_CHECK_EXACTSELECTED', "exactselected");

define('ERROR_CHECK_MAXSELECTED', "maximumselected");

define('ERROR_CHECK_INVALIDSUBSELECTED', "invalidsubselected");

define('ERROR_CHECK_INVALIDSELECTED', "invalidselected");

define('ERROR_CHECK_MINSELECTEDDROPDOWN', "minimumselecteddropdown");

define('ERROR_CHECK_EXACTSELECTEDDROPDOWN', "exactselecteddropdown");

define('ERROR_CHECK_MAXSELECTEDDROPDOWN', "maximumselecteddropdown");

define('ERROR_CHECK_INVALIDSUBSELECTEDDROPDOWN', "invalidsubselecteddropdown");

define('ERROR_CHECK_INVALIDSELECTEDDROPDOWN', "invalidselecteddropdown");

define('ERROR_CHECK_MINRANKED', "minimumranked");

define('ERROR_CHECK_EXACTRANKED', "exactranked");

define('ERROR_CHECK_MAXRANKED', "maximumranked");

define('ERROR_CHECK_INLINE_EXCLUSIVE', "inlineexclusive");
define('ERROR_CHECK_INLINE_INCLUSIVE', "inlineinclusive");
define('ERROR_CHECK_INLINE_MINREQUIRED', "inlineminimumrequired");
define('ERROR_CHECK_INLINE_MAXREQUIRED', "inlinemaximumrequired");
define('ERROR_CHECK_INLINE_EXACTREQUIRED', "inlineexactrequired");
define('ERROR_CHECK_INLINE_ANSWERED', 'inlineanswered');
define('ERROR_CHECK_EXCLUSIVE', "exclusive");
define('ERROR_CHECK_INCLUSIVE', "inclusive");
define('ERROR_CHECK_MINREQUIRED', "minimumrequired");
define('ERROR_CHECK_MAXREQUIRED', "maximumrequired");
define('ERROR_CHECK_EXACTREQUIRED', "exactrequired");
define('ERROR_CHECK_UNIQUEREQUIRED', "uniquerequired");
define('ERROR_CHECK_SAMEREQUIRED', 'samerequired');
define('ERROR_CHECK_ENUMERATED_ENTERED', 'enumeratedentered');
define('ERROR_CHECK_SETOFENUMERATED_ENTERED', 'setofenumeratedentered');

define('ERROR_CHECK_COMPARISON_EQUAL_TO', 'numeric_equalto');
define('ERROR_CHECK_COMPARISON_NOT_EQUAL_TO', 'numeric_notequalto');
define('ERROR_CHECK_COMPARISON_GREATER_EQUAL_TO', 'numeric_greaterequalto');
define('ERROR_CHECK_COMPARISON_GREATER', 'numeric_greater');
define('ERROR_CHECK_COMPARISON_SMALLER_EQUAL_TO', 'numeric_smallerequalto');
define('ERROR_CHECK_COMPARISON_SMALLER', 'numeric_smaller');

define('ERROR_CHECK_SETOFENUM_COMPARISON_EQUAL_TO', 'setofenum_numeric_equalto');
define('ERROR_CHECK_SETOFENUM_COMPARISON_NOT_EQUAL_TO', 'setofenum_numeric_notequalto');
define('ERROR_CHECK_SETOFENUM_COMPARISON_GREATER_EQUAL_TO', 'setofenum_numeric_greaterequalto');
define('ERROR_CHECK_SETOFENUM_COMPARISON_GREATER', 'setofenum_numeric_greater');
define('ERROR_CHECK_SETOFENUM_COMPARISON_SMALLER_EQUAL_TO', 'setofenum_numeric_smallerequalto');
define('ERROR_CHECK_SETOFENUM_COMPARISON_SMALLER', 'setofenum_numeric_smaller');

define('ERROR_CHECK_COMPARISON_EQUAL_TO_STRING', 'string_equalto');
define('ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_STRING', 'string_notequalto');
define('ERROR_CHECK_COMPARISON_EQUAL_TO_STRING_IGNORE_CASE', 'string_equaltoignorecase');
define('ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_STRING_IGNORE_CASE', 'string_notequaltoignorecase');

define('ERROR_CHECK_COMPARISON_EQUAL_TO_DATETIME', 'datetime_equalto');
define('ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_DATETIME', 'datetime_notequalto');
define('ERROR_CHECK_COMPARISON_GREATER_EQUAL_TO_DATETIME', 'datetime_greaterequalto');
define('ERROR_CHECK_COMPARISON_GREATER_DATETIME', 'datetime_greater');
define('ERROR_CHECK_COMPARISON_SMALLER_EQUAL_TO_DATETIME', 'datetime_smallerequalto');
define('ERROR_CHECK_COMPARISON_SMALLER_DATETIME', 'datetime_smaller');

define('ERROR_CHECK_COMPARISON_EQUAL_TO_TIME', 'time_equalto');
define('ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_TIME', 'time_notequalto');
define('ERROR_CHECK_COMPARISON_GREATER_EQUAL_TO_TIME', 'time_greaterequalto');
define('ERROR_CHECK_COMPARISON_GREATER_TIME', 'time_greater');
define('ERROR_CHECK_COMPARISON_SMALLER_EQUAL_TO_TIME', 'time_smallerequalto');
define('ERROR_CHECK_COMPARISON_SMALLER_TIME', 'time_smaller');

define('ERROR_HARD', '1');
define('ERROR_SOFT', '2');

/* variable */

define('ANSWER_TYPE_STRING', '1');

define('ANSWER_TYPE_ENUMERATED', '2');

define('ANSWER_TYPE_SETOFENUMERATED', '3');

define('ANSWER_TYPE_INTEGER', '4');

define('ANSWER_TYPE_DOUBLE', '5');

define('ANSWER_TYPE_RANGE', '6');

define('ANSWER_TYPE_SLIDER', '7');

define('ANSWER_TYPE_DATE', '8');

define('ANSWER_TYPE_TIME', '9');

define('ANSWER_TYPE_DATETIME', '10');

define('ANSWER_TYPE_OPEN', '11');

define('ANSWER_TYPE_NONE', '12');

define('ANSWER_TYPE_SECTION', '13');

define('ANSWER_TYPE_CALENDAR', '14');

define('ANSWER_TYPE_DROPDOWN', '15');

define('ANSWER_TYPE_MULTIDROPDOWN', '16');

define('ANSWER_TYPE_RANK', '17');

define('ANSWER_TYPE_KNOB', '18');

define('ANSWER_TYPE_CUSTOM', '99');

define('USER_MODE_YES', 1);
define('USER_MODE_NO', 2);
define('ARRAY_ANSWER_YES', 1);
define('ARRAY_ANSWER_NO', 0);
define('KEEP_ANSWER_YES', 1);
define('KEEP_ANSWER_NO', 0);
define('OUTPUT_ENCRYPTED_YES', 1);
define('OUTPUT_ENCRYPTED_NO', 0);
define('HIDDEN_YES' , 0);
define('HIDDEN_NO' , 1);
define('IF_EMPTY_NOTALLOW', 1);
define('IF_EMPTY_ALLOW', 2);
define('IF_EMPTY_WARN', 3);
define('IF_ERROR_NOTALLOW', 1);
define('IF_ERROR_ALLOW', 2);
define('IF_ERROR_WARN', 3);
define('BUTTON_YES', 1);
define('BUTTON_NO', 2);
define('INLINE_YES', 1);
define('INLINE_NO', 2);
define('GROUP_YES', 1);
define('GROUP_NO', 2);
define('GROUP_MAIN', 1);
define('GROUP_SUB', 2);
define('TABLE_YES', 1);
define('TABLE_NO', 2);
define('ENUMERATED_YES', 1);
define('ENUMERATED_NO', 2);
define('SECTIONHEADER_YES', 1);
define('SECTIONHEADER_NO', 2);
define('SECTIONFOOTER_YES', 1);
define('SECTIONFOOTER_NO', 2);
define('ENUM_FOOTER_YES', 1);
define('ENUM_FOOTER_NO', 2);

define('REENTRY_SAME_SCREEN', 1);
define('REENTRY_NEXT_SCREEN', 2);
define('REENTRY_FIRST_SCREEN', 3);
define('REENTRY_FROM_START', 4);
define('REENTRY_NO_REENTRY', 5);
define('REENTRY_SAME_SCREEN_REDO_ACTION', 6);

define('AFTER_COMPLETION_FIRST_SCREEN', 1);
define('AFTER_COMPLETION_LAST_SCREEN', 2);
define('AFTER_COMPLETION_FROM_START', 3);
define('AFTER_COMPLETION_NO_REENTRY', 4);
define('AFTER_COMPLETION_LAST_SCREEN_REDO', 5);
define('PRELOAD_REDO_NO', 1);
define('PRELOAD_REDO_YES', 2);
define('DATA_KEEP_ONLY_NO', 1);
define('DATA_KEEP_ONLY_YES', 2);
define('DATA_KEEP_NO', 1);
define('DATA_KEEP_YES', 2);
define('DATA_INPUTMASK_NO', 1);
define('DATA_INPUTMASK_YES', 2);
define('DATA_SKIP_NO', 1);
define('DATA_SKIP_YES', 2);
define('DATA_TIMINGS_CUTOFF', 301);
define('STORE_LOCATION_INTERNAL', 1);
define('STORE_LOCATION_BOTH', 2);
define('STORE_LOCATION_EXTERNAL', 3);
define('STORE_EXTERNAL_GET', 1);
define('STORE_EXTERNAL_SET', 2);

define('PROGRESSBAR_NO', 1);
define('PROGRESSBAR_PERCENT', 2);
define('PROGRESSBAR_BAR', 3);
define('PROGRESSBAR_ALL', 4);
define('PROGRESSBAR_FILLED_COLOR', '');
define('PROGRESSBAR_REMAIN_COLOR', '');
define('PROGRESSBAR_WIDTH', 300);
define('PROGRESSBAR_WHOLE', 1);
define('PROGRESSBAR_SECTION', 2);
define('ACCESS_RETURN_YES', 1);
define('ACCESS_RETURN_NO', 2);
define('LANGUAGE_CHANGE_PROGRAMMATIC_ALLOWED', 1);
define('LANGUAGE_CHANGE_RESPONDENT_ALLOWED', 2);
define('LANGUAGE_CHANGE_NOTALLOWED', 3);
define('LANGUAGE_REENTRY_YES', 1);
define('LANGUAGE_REENTRY_NO', 2);
define('LANGUAGE_BACK_YES', 1);
define('LANGUAGE_BACK_NO', 2);

define('MODE_CHANGE_PROGRAMMATIC_ALLOWED', 1);
define('MODE_CHANGE_RESPONDENT_ALLOWED', 2);
define('MODE_CHANGE_NOTALLOWED', 3);
define('MODE_REENTRY_YES', 1);
define('MODE_REENTRY_NO', 2);
define('MODE_BACK_YES', 1);
define('MODE_BACK_NO', 2);

define('TEMPLATE_CHANGE_PROGRAMMATIC_ALLOWED', 1);
define('TEMPLATE_CHANGE_RESPONDENT_ALLOWED', 2);
define('TEMPLATE_CHANGE_NOTALLOWED', 3);

define('SECTION_BASE', 'Base');
define('ORIENTATION_HORIZONTAL', 1);
define('ORIENTATION_VERTICAL', 2);
define('ORIENTATION_CUSTOM', 3);
define('ORDER_OPTION_FIRST', 1);
define('ORDER_LABEL_FIRST', 2);
define('TOOLTIP_YES', 1);
define('TOOLTIP_NO', 2);
define('TOOLTIP_ALWAYS', 2);
define('TEXTBOX_YES', 1);
define('TEXTBOX_NO', 2);
define('DEFAULT_INCREMENT', 1);
define('SPLIT_COLUMNS_YES', 1);
define('SPLIT_COLUMNS_NO', 2);
define('RANK_COLUMN_ONE', 1);
define('RANK_COLUMN_TWO', 2);
define('SLIDER_PRESELECTION_YES', 1);
define('SLIDER_PRESELECTION_NO', 2);
define('MULTI_QUESTION_YES', 1);
define('MULTI_QUESTION_NO', 2);


define('ERROR_PLACEMENT_WITH_QUESTION', 1);
define('ERROR_PLACEMENT_AT_TOP', 2);
define('ERROR_PLACEMENT_AT_BOTTOM', 3);
define('ERROR_PLACEMENT_WITH_QUESTION_TOOLTIP', 4);

/* translation */

define('TRANSLATION_VARIABLE', 1);

define('TRANSLATION_TYPE', 2);



/* database */

define('DB_MYSQL', 1);
define('DB_SQLITE', 2);

define('MYSQL_BINDING_INTEGER', "i");

define('MYSQL_BINDING_STRING', "s");

define('MYSQL_BINDING_BLOB', "b");



//Survey entry possiblities

define('LOGIN_ANONYMOUS', 1);

define('LOGIN_DIRECT', 2);

define('LOGIN_LOGINCODE', 3);



//System entry possibilites

define('USCIC_SURVEY', 1);

define('USCIC_SMS', 2);

define('USCIC_CATI', 3);


/* data export */
define('STATA_TYPE_INTEGER', 1);
define('STATA_TYPE_DOUBLE', 2);
define('STATA_TYPE_SHORT', 3);
define('STATA_TYPE_STRING', 4);

define('STATA_DATAFORMAT_SHORT', 252);
define('STATA_DATAFORMAT_DOUBLE', 255);

/* survey interview type */
define('PANEL_HOUSEHOLD', 1);
define('PANEL_RESPONDENT', 2);
define('PANEL_TRACKING_YES', 1);
define('PANEL_TRACKING_NO', 2);

/* user types */
define('USER_INTERVIEWER', 0);
define('USER_SUPERVISOR', 1);
define('USER_TRANSLATOR', 2);
define('USER_RESEARCHER', 3);
define('USER_SYSADMIN', 4);
define('USER_NURSE', 5);
define('USER_CATIINTERVIEWER', 6);
define('USER_TESTER', 7);

/* user sub types */
define('USER_NURSE_MAIN', 1);
define('USER_NURSE_LAB', 2);
define('USER_NURSE_FIELD', 3);
define('USER_NURSE_VISION', 4);
define('USER_SYSADMIN_MAIN', 1);
define('USER_SYSADMIN_ADMIN', 2);

/* SEND RECEIVE */
define ('SEND_RECEIVE_USB', 1);
define ('SEND_RECEIVE_INTERNET', 2);
define ('SEND_RECEIVE_EXPORTSQL', 3);
define ('SEND_RECEIVE_WORKONSERVER', 4);

define ('DEVICE_ALL', 1);
define ('DEVICE_TABLET', 2);


define('TEMPLATE_NAME', 'displayquestion');
define('TEMPLATE_BASIC', 'displayquestion_0');
define('TEMPLATE_UAS', 'displayquestion_1');
define('TEMPLATE_MTEENS', 'displayquestion_2');
define('TEMPLATE_MINIMAL', 'displayquestion_3');

define('ISSUE_REPORTED', 1);
define('ISSUE_INPROGRESS', 2);
define('ISSUE_RESOLVED', 3);

define('OUTPUT_AGGREGATE_NUMBEROFBRACKETS', 5);

define('DATABASE_LOCALHOST', "localhost");
define('DATABASE_MYSQL_PORT', "3306");

define('CONFIGURATION_DATABASE', "database");
define('CONFIGURATION_DATABASE_SERVER', "server");
define('CONFIGURATION_DATABASE_PORT', "port");
define('CONFIGURATION_DATABASE_USER', "user");
define('CONFIGURATION_DATABASE_PASSWORD', "password");
define('CONFIGURATION_DATABASE_NAME', "name");
define('CONFIGURATION_DATABASE_TYPE', "type");
define('CONFIGURATION_DATABASE_SURVEY', "survey");

define('CONFIGURATION_GENERAL', "general");
define('CONFIGURATION_GENERAL_STARTUP', "startup");
define('CONFIGURATION_GENERAL_DEVICE', "device");

define('CONFIGURATION_LOGGING', "logging");
define('CONFIGURATION_LOGGING_PARAMS', 'params');
define('CONFIGURATION_LOGGING_TIMINGS', "timings");
define('CONFIGURATION_LOGGING_ACTIONS', "actions");
define('CONFIGURATION_LOGGING_TABSWITCH', "tabswitch");
define('CONFIGURATION_LOGGING_PARADATA', "paradata");
define('CONFIGURATION_LOGGING_MOUSE', "mouse");

define('CONFIGURATION_ENCRYPTION', "encryption");
define('CONFIGURATION_ENCRYPTION_LOGINCODES', 'logincodes');
define('CONFIGURATION_ENCRYPTION_ADMIN', "admin");
define('CONFIGURATION_ENCRYPTION_PERSONAL', "personal");
define('CONFIGURATION_ENCRYPTION_REMARK', "remark");
define('CONFIGURATION_ENCRYPTION_SYSADMIN', "sysadmin");
define('CONFIGURATION_ENCRYPTION_CONTACTREMARK', "contactremark");
define('CONFIGURATION_ENCRYPTION_CONTACTNAME', "contactname");
define('CONFIGURATION_ENCRYPTION_COMMUNICATION_CONTENT', "communicationcontent");
define('CONFIGURATION_ENCRYPTION_COMMUNICATION_COMPONENT', "communicationcomponent");
define('CONFIGURATION_ENCRYPTION_COMMUNICATION_ACCESS', "communicationaccess");
define('CONFIGURATION_ENCRYPTION_COMMUNICATION_UPLOAD', "communicationupload");
define('CONFIGURATION_ENCRYPTION_COMMUNICATION_AJAX', "communicationajax");
define('CONFIGURATION_ENCRYPTION_TESTER', "tester");
define('CONFIGURATION_ENCRYPTION_CALENDAR', "calendar");
define('CONFIGURATION_ENCRYPTION_PICTURE', "picture");
define('CONFIGURATION_ENCRYPTION_ACTION_PARAMS', "actionparams");
define('CONFIGURATION_ENCRYPTION_DIRECT', "direct");
define('CONFIGURATION_ENCRYPTION_LAB', "lab");
define('CONFIGURATION_ENCRYPTION_FILE', "file");
define('CONFIGURATION_ENCRYPTION_DATA', 'data');

define('CONFIGURATION_DATETIME', "datetime");
define('CONFIGURATION_DATETIME_TIMEZONE', 'timezone');
define('CONFIGURATION_DATETIME_USFORMAT_SMS', 'smsformat');
define('CONFIGURATION_DATETIME_USFORMAT_SURVEY', 'surveyformat');
define('CONFIGURATION_DATETIME_MINUTES_SMS', 'smsminutes');
define('CONFIGURATION_DATETIME_MINUTES_SURVEY', 'surveyminutes');
define('CONFIGURATION_DATETIME_SECONDS_SMS', 'smsseconds');
define('CONFIGURATION_DATETIME_SECONDS_SURVEY', 'surveyseconds');

define('CONFIGURATION_SAMPLE', "sample");
define('CONFIGURATION_SAMPLE_PANEL', "panel");
define('CONFIGURATION_SAMPLE_TRACKING', "tracking");
define('CONFIGURATION_SAMPLE_PROXYCODE', "proxycode");
define('CONFIGURATION_SAMPLE_PROXYCONTACT', "proxycontact");
define('CONFIGURATION_SAMPLE_COMMUNICATION', "communication");
define('CONFIGURATION_SAMPLE_FILELOCATION', 'filelocation');
define('CONFIGURATION_SAMPLE_ALLOW_COMMUNICATION', 'allowcommunication');
define('CONFIGURATION_SAMPLE_ALLOW_UPLOAD', 'allowupload');

define('CONFIGURATION_SESSION', 'session');
define('CONFIGURATION_SESSION_WARN', 'sessionwarn');
define('CONFIGURATION_SESSION_TIMEOUT', 'sessiontimewarn');
define('CONFIGURATION_SESSION_LOGOUT', 'sessionlogout');
define('CONFIGURATION_SESSION_REDIRECT', 'sessionredirect');
define('CONFIGURATION_SESSION_PING', 'sessionping');

define('CONFIGURATION_PERFORMANCE', 'performance');
define('CONFIGURATION_PERFORMANCE_PREPARE_QUERIES', 'performancepreparequeries');
define('CONFIGURATION_PERFORMANCE_UNSERIALIZE', 'performanceunserialize');
define('CONFIGURATION_PERFORMANCE_DATA_FROM_STATE', 'performancedatafromstate');
define('CONFIGURATION_PERFORMANCE_USE_DATARECORDS', 'performanceusedatarecords');
define('CONFIGURATION_PERFORMANCE_USE_LOCKING', 'performanceuselocking');
define('CONFIGURATION_PERFORMANCE_USE_DYNAMIC_MINIFY', 'performanceusedynamicminify');
define('CONFIGURATION_PERFORMANCE_USE_TRANSACTIONS', 'performanceusetransactions');
define('CONFIGURATION_PERFORMANCE_USE_ACCESSIBLE', "performanceaccessible");

/* export settings */
define('SETTING_EXPORT_TYPE', 'exporttype');
define('SETTING_EXPORT_HISTORY', 'exporthistory');
define('SETTING_EXPORT_CREATE', 'exportcreate');
define('EXPORT_HISTORY_YES', 1);
define('EXPORT_HISTORY_NO', 2);
define('EXPORT_CREATE_YES', 1);
define('EXPORT_CREATE_NO', 2);
define('EXPORT_TYPE_SQL', 1);
define('EXPORT_TYPE_SERIALIZE', 2);
define('EXPORT_PLACEHOLDER_TABLE', '$importtablename$');
define('EXPORT_PLACEHOLDER_SUID', '$importsuid$');
define('EXPORT_PLACEHOLDER_URID', '$importurid$');
define('EXPORT_COLUMN_SUID', 'suid');
define('EXPORT_COLUMN_URID', 'urid');
define('EXPORT_FILE_NUBIS', '.nubis');
define('EXPORT_FILE_BLAISE_BLA', '.bla');
define('EXPORT_FILE_BLAISE_INC', '.inc');
define('EXPORT_FILE_SQL', '.sql');
define('SMS_POST_EXPORTTYPE', 'smsexport');

/* import settings */
define('SMS_POST_IMPORTTYPE', 'smsimport');
define('SETTING_IMPORT_TYPE', 'importtype');
define('SETTING_IMPORT_AS', 'importas');
define('SETTING_IMPORT_SERVER', 'databaseServer');
define('SETTING_IMPORT_DATABASE', 'databaseName');
define('SETTING_IMPORT_USER', 'databaseUsername');
define('SETTING_IMPORT_PASSWORD', 'databasePassword');
define('SETTING_IMPORT_TABLE', 'databaseTablename');
define('SETTING_IMPORT_TEXT', 'importtext');
define('EXPORT_DELIMITER', '~dsdfrfeterthbgf~');
define('EXPORT_SQL_DELIMITER', ';');
define('EXPORT_PLACEHOLDER_QUOTE', '~DOUBLEQUOTE~');
define('EXPORT_PLACEHOLDER_COMMA', '~COMMA~');
define('EXPORT_PLACEHOLDER_LINEBREAK', '~LINEBREAK~');
define('IMPORT_STATEMENT_INSERT', 'INSERT INTO');
define('IMPORT_STATEMENT_INSERT_VALUES', 'VALUES');

/* survey devices */
define('SURVEY_DEVICE_PC', "1");
define('SURVEY_DEVICE_TABLET', "2");
define('SURVEY_DEVICE_PHONE', "3");

/* prefixing options */
define('PREFIXING_FULL', 1);
define('PREFIXING_FULL_IF_BRACKET', 2);
define('PREFIXING_BRACKET_ONLY', 3);

/* fill options */
define('FILL_NO_SPACE_INSERT', 1);
define('FILL_SPACE_INSERT_BEFORE', 2);
define('FILL_SPACE_INSERT_AFTER', 3);
define('FILL_SPACE_INSERT_AROUND', 4);

?>