<?php

$TGroupTemplate = new Template('TGroupTemplate', TEMPLATE_GROUP, 'grouptemplate', '');
$TGroupTemplate_NoWrap = new Template('TGroupTemplate_NoWrap', TEMPLATE_GROUP, '', array(
    1 => '<div class="nowrap"><ul class="list-inline"><repeat><li><question /></li></repeat></ul></div>',
        ));
$TQuestionTemplate = new Template('TQuestionTemplate', TEMPLATE_QUESTION, 'questiontemplate', '');
$TQuestionTemplate_NoWrap = new Template('TQuestionTemplate_NoWrap', TEMPLATE_QUESTION, '', array(
    1 => '<li><errorMessage /></li><li><questionText /></li><li><answerOption />',
        ));
$TTextTemplate = new Template('TTextTemplate', TEMPLATE_TYPE, 'texttemplate', '');
$TTextTemplate_NoWrap = new Template('TTextTemplate_NoWrap', TEMPLATE_TYPE, '', array(
    1 => '<div class="input-group" style="<style />"><beforeHintText /><input type=text style="<style />" class="form-control" name="<questionName />" id="<idName />" value="<value />"><afterHintText /></div></li><li><dkrfInput /></li><li><dkButton /></li><li> <rfButton /></li>',
        ));
$TEnumeratedTemplate = new Template('TEnumeratedTemplate', TEMPLATE_TYPE, 'enumeratedtemplate', '');
$TEnumeratedTemplate_NoWrap = new Template('TEnumeratedTemplate_NoWrap', TEMPLATE_TYPE, '', array(
    1 => '<li><repeat><div class="<inputType />"><label><input type="<inputType />" name="<questionName />" id="<idName />" value="<optionKey />"<checked />><optionText /></label> <div id="specify<questionName /><optionKey />"><specifyOption /></div></div></repeat></li><li><dkrfInput /></li><li><dkButton /></li><li><rfButton /></li><li>',
        ));
$TSelectTemplate = new Template('TSelectTemplate', TEMPLATE_TYPE, 'selecttemplate', '');
$TSelectTemplate_NoWrap = new Template('TSelectTemplate_NoWrap', TEMPLATE_TYPE, '', array(
    1 => '<div class="input-group" style="<style />"><beforeHintText /><select class="form-control" name="<questionName />" id="<idName />"><option value="">Select</option><repeat><div class="<inputType />"><label><option value="<optionKey />"<checked />><optionText /></option></label></div></repeat></select><afterHintText /></div></li><li><dkrfInput /></li><li><dkButton /></li><li> <rfButton /></li>',
        ));
$TOpenTemplate = new Template('TOpenTemplate', TEMPLATE_TYPE, 'opentemplate', '');
$TEnumeratedOneOptionTemplate_NoWrap = new Template('TEnumeratedOneOptionTemplate_NoWrap', TEMPLATE_TYPE, '', '<specifyOpenDiv /><div class="<inputType />"><label><input type="<inputType />" name="<questionName />" id="<idName />" value="<optionKey />"<checked />><optionText /></label> <div id="specify<questionName /><optionKey />"><specifyOption /></div></div><specifyCloseDiv />');

$TYesNoTemplate = new Template('TYesNoTemplate', TEMPLATE_TYPE, '', array(
    1 => 'some top text <table class="table"> <tr><td></td><td align=center><b>Yes</b></td><td align=center><b>No</b></td></tr> <repeat> <question /> </repeat> </table> some botton text',
        ));

$TRowTemplate = new Template('TRowTemplate', TEMPLATE_TYPE, '', array(
    1 => '<td align=center><div class="radio"><label><input type="radio" name="<questionName />" value="<optionKey />"<checked />></label></div></td>',
        ));

$TStandardSingleQuestionGroup = new Template('TStandardSingleQuestionGroup', TEMPLATE_GROUP, 'questiontemplate', array(
    1 => '<question />',
    2 => '<question />',
        ));
$TNumberOrRangeGroupTemplate = new Template('TNumberOrRangeGroupTemplate', TEMPLATE_GROUP, 'questiontemplate', array(
    1 => '<question 0 />
<question 1 />
<question 2 /> ~ <question 3 />',
        ));
$T3ColumsTableGroup = new Template('T3ColumsTableGroup', TEMPLATE_GROUP, 'questiontemplate', array(
    1 => '<table width=100%><tr><td><question 0 /></td><td><question 1 /></td><td><question 2 /></td><td><question 3 /></td></tr></table>',
        ));
?>