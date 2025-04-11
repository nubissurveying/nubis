<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Display {

    var $config;
    private $chrmap;

    function __construct() {

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        $this->chrmap = array(
            // Windows codepage 1252
            "\xC2\x82", // U+0082⇒U+201A single low-9 quotation mark
            "\xC2\x84", // U+0084⇒U+201E double low-9 quotation mark
            "\xC2\x8B", // U+008B⇒U+2039 single left-pointing angle quotation mark
            "\xC2\x91", // U+0091⇒U+2018 left single quotation mark
            "\xC2\x92", // U+0092⇒U+2019 right single quotation mark
            "\xC2\x93", // U+0093⇒U+201C left double quotation mark
            "\xC2\x94", // U+0094⇒U+201D right double quotation mark
            "\xC2\x9B", // U+009B⇒U+203A single right-pointing angle quotation mark
            // Regular Unicode     // U+0022 quotation mark (")
            // U+0027 apostrophe     (')
            "\xC2\xAB", // U+00AB left-pointing double angle quotation mark
            "\xC2\xBB", // U+00BB right-pointing double angle quotation mark
            "\xE2\x80\x98", // U+2018 left single quotation mark
            "\xE2\x80\x99", // U+2019 right single quotation mark
            "\xE2\x80\x9A", // U+201A single low-9 quotation mark
            "\xE2\x80\x9B", // U+201B single high-reversed-9 quotation mark
            "\xE2\x80\x9C", // U+201C left double quotation mark
            "\xE2\x80\x9D", // U+201D right double quotation mark
            "\xE2\x80\x9E", // U+201E double low-9 quotation mark
            "\xE2\x80\x9F", // U+201F double high-reversed-9 quotation mark
            "\xE2\x80\xB9", // U+2039 single left-pointing angle quotation mark
            "\xE2\x80\xBA", // U+203A single right-pointing angle quotation mark
        );
    }

    function defaultDisplayOverviewAddressColumns() {
        return array('address1_dec' => Language::labelAdress1(), 'address2_dec' => Language::labelAdress2(), 'city_dec' => Language::labelCity(), 'zip_dec' => Language::labelZip(), 'state_dec' => Language::labelState());
    }

    function defaultDisplayInfoAddressColumns() {
        return array('address1_dec' => Language::labelAdress1(), 'address2_dec' => Language::labelAdress2(), 'city_dec' => Language::labelCity(), 'zip_dec' => Language::labelZip(), 'state_dec' => Language::labelState());
    }

    function defaultDisplayInfo2AddressColumns() {
        return array('telephone1_dec' => Language::labelTelephone(), 'telephone2_dec' => Language::labelTelephone2(), 'email_dec' => Language::labelEmail());
    }

    function showHeader($title, $style = '', $fastload = false) {
        /* FOR NO CACHING
         * <meta http-equiv="cache-control" content="max-age=0" />
          <meta http-equiv="cache-control" content="no-cache" />
          <meta http-equiv="expires" content="0" />
          <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
          <meta http-equiv="pragma" content="no-cache" />
         */
        $returnStr = ' 
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="shortcut icon" href="images/favicon.ico">

    <title>' . $title . '</title>';

        if (determinedJavascriptEnabled() == false) {
            $returnStr .= '<noscript><meta http-equiv="refresh" content="0; URL=' . getURL() . '/nojavascript.php"></noscript>';
        }
        $returnStr .= '
    <!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="bootstrap/dist/css/bootstrap.min.css">

    <!-- Custom scripts and styles for this template -->';
        if ($fastload == false) {
            $returnStr .= '<script type="text/javascript" charset="utf-8" language="javascript" src="bootstrap/assets/js/jquery.js"></script>';
        }
        $returnStr .= '
    ' . $style . '

<script type="text/javascript">
    if(typeof window.history.pushState == \'function\') {
        window.history.pushState({}, "Hide", "index.php");
    }    
</script>
      
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="bootstrap/assets/js/html5shiv.js"></script>
      <script src="bootstrap/assets/js/respond.min.js"></script>
    <![endif]-->
    
    <script src="js/hover-dropdown.js"></script>
    <script type="text/javascript" src="js/tooltip.js"></script>
    <script type="text/javascript" src="js/popover.js"></script>    
    <script type="text/javascript" src="js/modal.js"></script>
    ';

        $returnStr .= '</head>
                    <body>
                    ';
        return $returnStr;
    }

    function showSurveyHeader($title, $style = '', $extra = '') {
        /* FOR NO CACHING
         * <meta http-equiv="cache-control" content="max-age=0" />
          <meta http-equiv="cache-control" content="no-cache" />
          <meta http-equiv="expires" content="0" />
          <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
          <meta http-equiv="pragma" content="no-cache" />
         */
        $returnStr = ' 
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="shortcut icon" href="images/favicon.ico">

    <title>' . $title . '</title>';

        // REMOVED TO ALWAYS ASSUME JAVASCRIPT
        //if (determinedJavascriptEnabled() == false) {
        //    $returnStr .= '<noscript><meta http-equiv="refresh" content="0; URL=' . getURL() . '/nojavascript.php"></noscript>';
        //}
        $returnStr .= '<script type="text/javascript" charset="utf-8" language="javascript" src="bootstrap/assets/js/jquery.js"></script>';
        $returnStr .= '<link href="css/uscic.css" type="text/css" rel="stylesheet">';
        $returnStr .= '
    <!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="bootstrap/dist/css/bootstrap.min.css">

    <!-- Custom scripts and styles for this template -->';
        $returnStr .= '
    ' . $style . '

<script type="text/javascript">' . minifyScript('
    if(typeof window.history.pushState == \'function\') {
        window.history.pushState({}, "Hide", "index.php");
    }') . '    
</script>
      
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="bootstrap/assets/js/html5shiv.js"></script>
      <script src="bootstrap/assets/js/respond.min.js"></script>
    <![endif]-->
    ';
        $returnStr .= '</head>
                    <body>
                    ';
        /*
         * 
         */
        return $returnStr;
    }

    function showSurveyFooter($extra = '') {
        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }
        $returnStr = '
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->';
        /* $returnStr .= ' <script src="js/hover-dropdown.js"></script>
          <script type="text/javascript" src="js/tooltip.js"></script>
          <script type="text/javascript" src="js/popover.js"></script>
          <script type="text/javascript" src="js/modal.js"></script>';
         */
        $returnStr .= '<script src="bootstrap/dist/js/bootstrap.min.js"></script>'; // needed for bootstrap-select
        $returnStr .= $extra;
        if (dbConfig::defaultDevice() == DEVICE_TABLET) {

            $returnStr .= '<script type="text/javascript">';

            $str = 'if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {';


            $str .= '$( ".btn" ).removeClass("btn").addClass("btn-lg");';
            $str .= '$( "#searchbutton" ).removeClass("btn-lg").addClass("btn");';

            $str .= '$("input[type=radio]").addClass("form-control");';
            $str .= '$("input[type=radio]").css("width", "50px");';
            $str .= '$( ".uscic-radio" ).css("font-size", "26px");';
            $str .= '$( ".uscic-radio" ).css("border", "1px dotted gray");';

            $str .= '$("input[type=checkbox]").addClass("form-control");';
            $str .= '$("input[type=checkbox]").css("width", "50px");';
            $str .= '$( ".uscic-checkbox" ).css("font-size", "26px");';
            $str .= '$( ".uscic-checkbox" ).css("border", "1px dotted gray");';

            $str .= '}';
            $returnStr .= minifyScript($str);
            $returnStr .= '</script>';
        }

        if (Config::useAccessible()) {
            $returnStr .= '</main>';
            $returnStr .= "<footer role='contentinfo'></footer>";
        }

        $returnStr .= '</body></html>';
        return $returnStr;
    }

//remove the get parameters: only for html 5 (http://stackoverflow.com/questions/13789231/remove-get-parameter-in-url-after-processing-is-finishednot-using-post-php)



    function showHeaderNoJavascript($title, $style = '') {
        $returnStr = ' 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/favicon.ico">

    <title>' . $title . '</title>';

        $returnStr .= '
    <!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="bootstrap/dist/css/bootstrap.min.css">

    <!-- Custom styles for this template -->
    ' . $style . '
  </head>
  <body>
';
        return $returnStr;
    }

    function displayNoJavascript() {
        $returnStr = $this->showHeaderNoJavascript("Attention", '<link type="text/css" href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<div id="wrap">';
        $returnStr .= '<div class="container">';
        $returnStr .= '<div class="panel panel-default">
                        <div class="panel-body">';

        $returnStr .= '<noscript>This site is optimized to work with JavaScript! If you wish to have the best available site experience, please enable JavaScript.</noscript>';
        $returnStr .= ' Please just click \'Next\' to continue.<br/><br/>';
        $returnStr .= '<form id="form" role="form" method="post" action="index.php">       

                <noscript>
                <input type="hidden" name="js_enabled" value="1" />
                </noscript>
                <input type="hidden" name="js_chosen" value="1" />';

        $returnStr .= '<div class="panel-footer text-center">';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonJavascriptContinue() . '">';
        $returnSt .= '</div>';
        $returnStr .= '</form>';


        $returnSt .= '</div></div></div>';
        $returnStr .= "</body></html>";
        echo $returnStr;
    }

    function showFooter($fastLoad = true, $extra = '') {
        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }
        $returnStr = '
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->';
        if ($fastLoad) {
            $returnStr .= '<script src="bootstrap/assets/js/jquery.js"></script>';
        }
        $returnStr .= '<script src="bootstrap/dist/js/bootstrap.min.js"></script>';
        $returnStr .= $extra;
        if (dbConfig::defaultDevice() == DEVICE_TABLET) {
            //WOULD NEED A dbConfig check here!!

            $returnStr .= '<script type="text/javascript">';

            $str = 'if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {';


            $str .= '$( ".btn" ).removeClass("btn").addClass("btn-lg");';
            $str .= '$( "#searchbutton" ).removeClass("btn-lg").addClass("btn");';

            $str .= '$("input[type=radio]").addClass("form-control");';
            $str .= '$("input[type=radio]").css("width", "50px");';
            $str .= '$( ".uscic-radio" ).css("font-size", "26px");';
            $str .= '$( ".uscic-radio" ).css("border", "1px dotted gray");';

            $str .= '$("input[type=checkbox]").addClass("form-control");';
            $str .= '$("input[type=checkbox]").css("width", "50px");';
            $str .= '$( ".uscic-checkbox" ).css("font-size", "26px");';
            $str .= '$( ".uscic-checkbox" ).css("border", "1px dotted gray");';

            $str .= '}';
            $returnStr .= minifyScript($str);
            $returnStr .= '</script>';
        }

        $returnStr .= '</body></html>';
        return $returnStr;
    }

    function displayOptionsSidebar($selector, $name, $page = "sysadmin.search") {
        $returnStr = '';
        if (!isRegisteredScript("js/sidr/jquery.sidr.js")) {
            registerScript('js/sidr/jquery.sidr.js');
            $returnStr .= getScript('js/sidr/jquery.sidr.js');
        }
        if (!isRegisteredScript("js/sidr/jquery.sidr.light.css")) {
            registerScript('js/sidr/jquery.sidr.light.css');
            $returnStr .= getCSS('js/sidr/jquery.sidr.light.css');
        }

        $returnStr .= '<script type="text/javascript" >
                $(document).ready(function() {
                $(\'#' . $selector . '\').sidr( {
                 displace: false,
                 name: \'' . $name . '\'
                });
                ';

        if (isset($_SESSION['SEARCH']) && $_SESSION['SEARCH'] == SEARCH_OPEN_YES) {
            $returnStr .= " var term = '" . $_SESSION['SEARCHTERM'] . "';
                            var r = '" . setSessionsParamString(array("page" => $page)) . "';
                            var url = '';

                            // Send the data using post
                            var posting = $.post( url, { r: r, search: term, updatesessionpage: 2 } );

                            // Put the results in a div
                            posting.done(function( data ) {
                            $( '#optionssidebar' ).empty().append( $( data ));
                            });";
            $returnStr .= '$.sidr(\'open\', \'optionssidebar\');';
        }
        $returnStr .= "});</script>";
        return $returnStr;
    }

    function displayComboBox($css = true, $survey = null) {
        $str = '';
        if (!isRegisteredScript("js/bootstrap-select/bootstrap-select-min.js")) {
            registerScript('js/bootstrap-select/bootstrap-select-min.js');
            $str .= getScript("js/bootstrap-select/bootstrap-select-min.js");
        }
        if ($css && !isRegisteredScript("css/bootstrap-select.min.css")) {
            registerScript('css/bootstrap-select.min.css');
            $str .= getCSS("css/bootstrap-select.min.css");
        }
        if ($survey) {
            $label = $survey->getComboBoxNothingLabel();
        } else {
            $label = Language::labelDropdownNothing();
        }
        $str .= minifyScript('<script type="text/javascript">
                    $(document).ready(function(){
                    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
                      $(".selectpicker").selectpicker({
                            mobile: true,
                            noneSelectedText : \'' . str_replace("'", "", $label) . '\'}
                        );                      
                      }
                    else {
                      $(".selectpicker").selectpicker({
                            noneSelectedText : \'' . str_replace("'", "", $label) . '\'}
                        );
                    }
                  });
                  </script>');
        return $str;
    }

    function displayComboBoxCSS() {
        $str = '';
        if (!isRegisteredScript("css/bootstrap-select.min.css")) {
            registerScript('css/bootstrap-select.min.css');
            $str .= getCSS("css/bootstrap-select.min.css");
        }
        return $str;
    }

    function displayColorPicker() {
        $str = '';
        if (!isRegisteredScript("js/colorpicker/bootstrap-colorpicker.js")) {
            registerScript('js/colorpicker/bootstrap-colorpicker.js');
            $str .= getScript("js/colorpicker/bootstrap-colorpicker.js");
        }
        if (!isRegisteredScript("js/colorpicker/bootstrap-colorpicker.css")) {
            registerScript('js/colorpicker/bootstrap-colorpicker.css');
            $str .= getCSS("js/colorpicker/bootstrap-colorpicker.css");
        }
        $str .= '<script>
                    $(document).ready(function(){
                        $(".colorpicker").colorpicker();
                    });
                </script>';
        return $str;
    }

    function displayPopoverScript() {
        $returnStr = '<script type=text/javascript>
            
        function showPopover() {
            var $this = $(this);

            // Currently hovering popover
            $this.data("hoveringPopover", true);

            // If its still waiting to determine if it can be hovered, dont allow other handlers
            if ($this.data("waitingForPopoverTO")) {
                e.stopImmediatePropagation();
            }
         }
         
        function hidePopover() {
   
            var $this = $(this);

            // If timeout was reached, allow hide to occur
            if ($this.data("forceHidePopover")) {
                $this.data("forceHidePopover", false);
                return true;
            }

            // Prevent other `hide` handlers from executing
            e.stopImmediatePropagation();

            // Reset timeout checker
            clearTimeout($this.data("popoverTO"));

            // No longer hovering popover
            $this.data("hoveringPopover", false);

            // Flag for `show` event
            $this.data("waitingForPopoverTO", true);

            // In 500ms, check to see if the popover is still not being hovered
            $this.data("popoverTO", setTimeout(function () {
                // If not being hovered, force the hide
                if (!$this.data("hoveringPopover")) {
                    $this.data("forceHidePopover", true);
                    $this.data("waitingForPopoverTO", false);
                    $this.popover("hide");
                }
            }, 500));

            // Stop default behavior
            return false;
        }
        </script>';

        return $returnStr;
    }

    function displayPopover($selector, $content) {
        $returnStr = "<script type=text/javascript>$('" . $selector . "').popover({
                container: '" . $selector . "',
                animation: false,
                content: '" . str_replace("'", "\'", $content) . "'
                }).on({
                    show: showPopover,
                    hide: hidePopover
                    });
                    </script>";
        return $returnStr;
    }

    function displayValidation($paradata, $errors = array(), $externalonly = array(), $checkerror = true, $checkempty = true, $placement = ERROR_PLACEMENT_WITH_QUESTION) {

        $rulestringerror = "";
        $rulestringempty = "";
        if ($paradata == true) {
            $errormapping = "";
            $errorcodes = Common::errorCodes();
        }

        /* error checking */
        if ($checkerror) {
            if (sizeof($errors) > 0) {
                if ($paradata == true) {
                    $errormapping = "var mapping = { errors: [] };\r\n";
                }
                $rulestringerror .= "rules: {\r\n";
                foreach ($errors as $error) {
                    $name = $error->getVariableName();

                    if (contains($name, "[")) {
                        $name = "'" . $name . "'";
                    }
                    $rulestringerror .= $name . ": {\r\n";
                    $local = "";
                    $err = $error->getErrorChecks();
                    foreach ($err as $e) {

                        // not empty check
                        if ($e->getType() != ERROR_CHECK_REQUIRED) {

                            /* not pattern check */
                            if ($e->getType() != ERROR_CHECK_PATTERN) {
                                $local .= $e->getType() . ": " . $e->getValue() . ",\r\n";
                            } else {
                                $value = $e->getValue();

                                /* not pattern given, then assume validator function */
                                if (!startsWith($value, '/')) {
                                    $local .= $e->getValue() . ": true,\r\n";
                                } else {                                    
                                    $local .= $e->getType() . ": " . str_replace("/","",$e->getValue()) . ",\r\n";
                                }
                            }

                            // add to mapping
                            if ($paradata == true) {
                                $code = 999;
                                if (isset($errorcodes[$e->getType()])) {
                                    $code = $errorcodes[$e->getType()];
                                }

                                // external only, then don't store any incorrect answers
                                if (inArray(getBasicName($error->getRealVariableName()), $externalonly)) {
                                    $errormapping .= "mapping.errors.push( {name: '" . $error->getVariableName() . "-" . $this->stripNonAscii(str_replace("\n", "", str_replace("'", "\'", strip_tags($e->getMessage())))) . "', store: '2', value: '" . $code . "'});\r\n";
                                } else {
                                    $errormapping .= "mapping.errors.push( {name: '" . $error->getVariableName() . "-" . $this->stripNonAscii(str_replace("\n", "", str_replace("'", "\'", strip_tags($e->getMessage())))) . "', store: '1', value: '" . $code . "'});\r\n";
                                }
                            }
                        }
                    }
                    $local = substr(trim($local), 0, strlen(trim($local)) - 1) . "\r\n}, \r\n";
                    $rulestringerror .= $local;

                    // add to overal mapping
                }
                $rulestringerror = substr(trim($rulestringerror), 0, strlen(trim($rulestringerror)) - 1) . "\r\n}, \r\n";
            }
        }

        /* empty checking */
        if ($checkempty) {
            if (sizeof($errors) > 0) {
                if ($paradata == true) {
                    if ($errormapping == "") {
                        $errormapping = "var mapping = { errors: []};\r\n";
                    }
                }
                $rulestringempty .= "rules: {\r\n";
                foreach ($errors as $error) {
                    $name = $error->getVariableName();
                    if (contains($name, "[")) {
                        $name = "'" . $name . "'";
                    }
                    $rulestringempty .= $name . ": {\r\n";
                    $local = "";
                    $err = $error->getErrorChecks();
                    foreach ($err as $e) {

                        // empty check
                        if ($e->getType() == ERROR_CHECK_REQUIRED) {
                            $local .= $e->getType() . ": " . $e->getValue() . ",\r\n";

                            // add to mapping
                            if ($paradata == true) {
                                $code = 999;
                                if (isset($errorcodes[$e->getType()])) {
                                    $code = $errorcodes[$e->getType()];
                                }

                                if (inArray(getBasicName($error->getRealVariableName()), $externalonly)) {
                                    $errormapping .= "mapping.errors.push( {name: '" . $error->getVariableName() . "-" . $this->stripNonAscii(str_replace("\n", "", str_replace("'", "\'", strip_tags($e->getMessage())))) . "', store: '2', value: '" . $code . "'});\r\n";
                                } else {
                                    $errormapping .= "mapping.errors.push( {name: '" . $error->getVariableName() . "-" . $this->stripNonAscii(str_replace("\n", "", str_replace("'", "\'", strip_tags($e->getMessage())))) . "', store: '1', value: '" . $code . "'});\r\n";
                                }
                            }
                        }
                    }
                    $local = substr(trim($local), 0, strlen(trim($local)) - 1) . "\r\n}, \r\n";
                    $rulestringempty .= $local;
                }
                $rulestringempty = substr(trim($rulestringempty), 0, strlen(trim($rulestringempty)) - 1) . "\r\n}, \r\n";
            }
        }
        $finalstr = "";
        if (!isRegisteredScript("js/validation/jquery.validate-min.js")) {
            $finalstr .= getScript("js/validation/jquery.validate-min.js");
            $finalstr .= getScript("js/validation/jquery.validate.additional.js");
        }
        $str = '<script type="text/javascript">';

        // add error mapping if logging paradata
        if ($paradata == true) {
            $str .= $errormapping;
            if ($errormapping != "") {
                $str .= "function lookupCode(name, search, answer) {                                        
                            $.each(mapping.errors, function(i, v) {                                                        
                                if (v.name == search) {
                                    if (answer != '' & v.store == 1) {    
                                        logParadata(v.value + ':' + name + ':' + answer);
                                    }
                                    else {                                    
                                        logParadata(v.value + ':' + name);
                                    }
                                }
                            });
                        }";
            }
        }

        // code for handling answer display in error messages
        $str .= 'function getDisplayValueError(el) {';
        $str .= "var name = $(el).attr('name');";
        $str .= 'if ($(el).is(":radio")) {';
        $str .= "var target = $('input[name=\"' + name + '\"]:checked');";
        $str .= "var label = $('label[for=\"' + $(target).attr('id') + '\"]').text();";
        $str .= 'return label;';
        $str .= '}';
        $str .= 'else if ($(el).is(":checkbox")) {';
        $str .= 'var labels = [];';
        $str .= "$.each($('input[name=\"' + name + '\"]:checked'), function(){
                    var label = $('label[for=\"' + $(this).attr('id') + '\"]').text();
                    labels.push(label.trim());
                });";
        $str .= "return labels.join(', ');";
        $str .= '}';
        $str .= 'else if ($(el).is("select")) {';
        $str .= 'var at = $(el).attr("multiple");';
        $str .= 'if (typeof attr !== typeof undefined && attr !== false) {';
        $str .= 'var labels = [];';
        $str .= "$('#' + $(el).attr('id') + ' option:selected').each(function () {
                    var label = $(this).text();
                    labels.push(label.trim());
                });";
        $str .= "return labels.join(', ');";
        $str .= '}';
        $str .= 'else {';
        $str .= "return $('select[name=\"' + name + '\"] option:selected').text();";
        $str .= '}';
        $str .= '}';
        $str .= 'return $(el).val().trim();'; // default: integer, double, open, string, range, knob, slider, calendar, time picker, date picker, date/time picker
        $str .= '}';

        $str .= 'function getValueError(el) {';
        $str .= "var name = $(el).attr('name');";
        $str .= 'if ($(el).is(":radio")) {';
        $str .= "return $('input[name=\"' + name + '\"]:checked').val();";
        $str .= '}';
        $str .= 'else if ($(el).is(":checkbox")) {';
        $str .= 'var values = [];';
        $str .= "$.each($('input[name=\"' + name + '\"]:checked'), function(){
                    values.push($(this).val());
                });";
        $str .= "return values.join(', ');";
        $str .= '}';
        $str .= 'else if ($(el).is("select")) {';
        $str .= 'var at = $(el).attr("multiple");';
        $str .= 'if (typeof attr !== typeof undefined && attr !== false) {';
        $str .= 'var values = [];';
        $str .= "$('#' + $(el).attr('id') + ' option:selected').each(function () {
                    var value = $(this).val();
                    values.push(value.trim());
                });";
        $str .= "return values.join(', ');";
        $str .= '}';
        $str .= 'else {';
        $str .= "return $('select[name=\"' + name + '\"]').val();";
        $str .= '}';
        $str .= '}';

        $str .= 'return $(el).val().trim();'; // default: integer, double, open, string, range, knob, slider, calendar, time picker, date picker, date/time picker
        $str .= '}';

        $str .= 'String.prototype.replaceAll = function(strReplace, strWith) {
    // See http://stackoverflow.com/a/3561711/556609
    var esc = strReplace.replace(/[-\/\\^$*+?.()|[\]{}]/gi, \'\\$&\');
    var reg = new RegExp(esc, \'ig\');    
    return this.replace(reg, strWith);
};';

        $str .= '
                 
             function clearForm() {';

        if ($placement == ERROR_PLACEMENT_AT_TOP || $placement == ERROR_PLACEMENT_AT_BOTTOM) {
            $str .= '$(\'#uscic-errors\').empty();';
        } else {
            $str .= '$(\'div.uscic-answer\').removeClass(\'has-errors\');
                $(\'div.uscic-answer\').removeClass(\'has-warning\'); 
                $(\'tr.has-warning\').removeAttr(\'style\');  
                $(\'tr.has-warning\').removeClass(\'has-warning\');
                $(\'div.uscic-answer\').removeAttr(\'style\');                                
                $(\':input\').removeClass(\'empty-error\');
                $(\':input\').removeClass(\'error-error\');';
        }

        $str .= '}
             
             var validator;    
';

        /* define error placement */
        $errorplacement = '';
        $errorplacement1 = '';
        if ($placement == ERROR_PLACEMENT_WITH_QUESTION) {
            $errorplacement .= 'showErrors: function(errorMap, errorList) {';
            $errorplacement .= '$.each(errorList, function (index, error) {';
            $errorplacement .= 'if ($(error.element).attr("data-validation-empty") == 3) {';
            $errorplacement .= 'var name = $(error.element).attr("name");';
            $errorplacement .= '$("[name=\'" + name + "\']").addClass("ignore-empty")';
            $errorplacement .= '}';
            if ($paradata == true) {
                $errorplacement .= 'lookupCode($(error.element).attr("name"), $(error.element).attr("name") + "-" + error.message, $(error.element).val());';
            }

            // replace any value placeholders
            $errorplacement .= 'var inputs = $(":input[name^=\'answer\']");';
            $errorplacement .= 'for (cnt = 0; cnt <= inputs.length; cnt++) {';
            $errorplacement .= 'if (error.message.toLowerCase().indexOf("' . PLACEHOLDER_ERROR_ANSWER . '" + (cnt+1) + "#") > -1) {';
            $errorplacement .= 'error.message = error.message.replaceAll("' . PLACEHOLDER_ERROR_ANSWER . '" + (cnt+1) + "#", getDisplayValueError(inputs[cnt]));';
            $errorplacement .= "}";
            $errorplacement .= 'if (error.message.toLowerCase().indexOf("' . PLACEHOLDER_ERROR_ANSWER_VALUE . '" + (cnt+1) + "#") > -1) {';
            $errorplacement .= 'error.message = error.message.replaceAll("' . PLACEHOLDER_ERROR_ANSWER_VALUE . '" + (cnt+1) + "#", getValueError(inputs[cnt]));';
            $errorplacement .= "}";
            $errorplacement .= "}";

            $errorplacement .= '});';
            $errorplacement .= 'this.defaultShowErrors(); },';

            $errorplacement .= 'errorElement: \'p\',
                            errorClass: \'help-block uscic-help-block\',
                            errorPlacement: function(error, element) {  ';
            $errorplacement .= '
                                if ($(element).closest(\'div.uscic-answer\').hasClass(\'has-errors\') === false) {
                                   $(element).closest(\'div.uscic-answer\').addClass(\'has-errors\');
                                }
                                if ($(element).hasClass(\'uscic-radio-table\') === true) {
                                     error.insertAfter($(element).closest(\'tr\').first().children(\'td\').children(\'div\').first());
                                }                                
                                else if ($(element).hasClass(\'uscic-radio-horizontal-table\') === true) {
                                     error.insertAfter($(element).closest(\'table\').first());
                                }
                                else if ($(element).hasClass(\'uscic-checkbox-horizontal-table\') === true) {
                                    error.insertAfter($(element).closest(\'table\').first());
                                }
                                else if ($(element).hasClass(\'uscic-checkbox-table\') === true) {
                                     error.insertAfter($(element).closest(\'tr\').first().children(\'td\').children(\'div\').first());
                                }
                                else if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for text fields in a table row
                                    error.insertAfter($(element).closest(\'tr\').first().children(\'td\').children(\'div\').first());
                                }
                                else {                                
                                   error.insertAfter($(element).closest(\'div.uscic-answer\').children().last());            
                                }
                           }';
            $errorplacement1 = str_replace("ignore-empty", "ignore-error", str_replace("data-validation-empty", "data-validation-error", $errorplacement));
        } else if ($placement == ERROR_PLACEMENT_AT_TOP || $placement == ERROR_PLACEMENT_AT_BOTTOM) {
            $errorplacement .= 'showErrors: function(errorMap, errorList) {
                        $(\'#uscic-errors\').empty();
                        $(\'#uscic-errors\').addClass(\'has-warning has-errors\');
                        var str = "";
                        $.each(errorList, function (index, error) {
                            //var $element = $(error.element);
                            if ($(error.element).attr("data-validation-empty") == 3) {
                                var name = $(error.element).attr("name");
                                $("[name=\'" + name + "\']").addClass("ignore-empty");
                            }';

            $errorplacement .= 'var inputs = $(":input[name^=\'answer\']");';
            $errorplacement .= 'var msg = error.message;';
            $errorplacement .= 'for (cnt = 0; cnt <= inputs.length; cnt++) {';
            $errorplacement .= 'if (msg.toLowerCase().indexOf("' . PLACEHOLDER_ERROR_ANSWER . '" + (cnt+1) + "#") > -1) {';
            $errorplacement .= 'msg = msg.replaceAll("' . PLACEHOLDER_ERROR_ANSWER . '" + (cnt+1) + "#", getDisplayValueError(inputs[cnt]));';
            $errorplacement .= "}";
            $errorplacement .= 'if (msg.toLowerCase().indexOf("' . PLACEHOLDER_ERROR_ANSWER_VALUE . '" + (cnt+1) + "#") > -1) {';
            $errorplacement .= 'msg = msg.replaceAll("' . PLACEHOLDER_ERROR_ANSWER_VALUE . '" + (cnt+1) + "#", getValueError(inputs[cnt]));';
            $errorplacement .= "}";
            $errorplacement .= "}";

            $errorplacement .= 'str = str + "<p class=\'help-block uscic-help-block\'>" + msg + "</p>";';
            if ($paradata == true) {
                $errorplacement .= 'lookupCode($(error.element).attr("name"), $(error.element).attr("name") + "-" + error.message, $(error.element).val());';
            }
            $errorplacement .= '
                        });
                        str = str;
                        $(\'#uscic-errors\').append(str);
                    }';
            $errorplacement1 .= 'showErrors: function(errorMap, errorList) {
                        $(\'#uscic-errors\').addClass(\'has-warning has-errors\');
                        var str = "";
                        $.each(errorList, function (index, error) {
                            if ($(error.element).attr("data-validation-error") == 3) {
                                var name = $(error.element).attr("name");
                                $("[name=\'" + name + "\']").addClass("ignore-error");
                            }
                            var $element = $(error.element);';

            $errorplacement1 .= 'var inputs = $(":input[name^=\'answer\']");';
            $errorplacement1 .= 'var msg = error.message;';
            $errorplacement1 .= 'for (cnt = 0; cnt <= inputs.length; cnt++) {';
            $errorplacement1 .= 'if (msg.toLowerCase().indexOf("' . PLACEHOLDER_ERROR_ANSWER . '" + (cnt+1) + "#") > -1) {';
            $errorplacement1 .= 'msg = msg.replaceAll("' . PLACEHOLDER_ERROR_ANSWER . '" + (cnt+1) + "#", getDisplayValueError(inputs[cnt]));';
            $errorplacement1 .= "}";
            $errorplacement1 .= 'if (msg.toLowerCase().indexOf("' . PLACEHOLDER_ERROR_ANSWER_VALUE . '" + (cnt+1) + "#") > -1) {';
            $errorplacement1 .= 'msg = msg.replaceAll("' . PLACEHOLDER_ERROR_ANSWER_VALUE . '" + (cnt+1) + "#", getValueError(inputs[cnt]));';
            $errorplacement1 .= "}";
            $errorplacement1 .= "}";

            $errorplacement1 .= 'str = str + "<p class=\'help-block uscic-help-block\'>" + msg + "</p>";';
            if ($paradata == true) {
                $errorplacement1 .= 'lookupCode($(error.element).attr("name"), $(error.element).attr("name") + "-" + error.message, $(error.element).val());';
            }
            $errorplacement1 .= '
                                });
                        str = str;
                        $(\'#uscic-errors\').append(str);
                    }';
        }

        /* add empty checking function */
        if ($checkempty) {
            $str .= 'function validateFormEmpty() {   
                $(\'form\').removeData(\'validator\');                
                    
    $(\'form\').validate({  ' . $rulestringempty . ' 
        ignore: ".ignore-empty, .dkrfna, :hidden:not(.ranker, .selectpicker, .bootstrapslider, .knob, #calendardiv)", // for selectpicker bootstrap plugin, bootstrap-slider, and jquery knob plugin
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            highlight: function(element) {                            
                $(element).addClass(\'empty-error\');
                $(element).closest(\'div.uscic-answer\').addClass(\'has-warning\');
                if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for multi column setup in a table row                
                    $(element).closest(\'tr\').addClass(\'has-warning\');
                    $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                }
                else if ($(element).is(\':checkbox\')) { // for checkboxes to add the highlighting                
                    if ($(element).hasClass(\'uscic-checkbox-table\') === true) {
                        $(element).closest(\'tr\').addClass(\'has-warning\');
                        $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                    else if ($(element).hasClass(\'uscic-checkbox-horizontal-table\') === true) {
                        $(element).closest(\'table\').parent(\'div.uscic-answer\').addClass(\'has-warning\');
                        $(element).closest(\'table\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                    else {
                        $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }    
                }                
                else if ($(element).is(\':radio\')) { // for radio buttons to add the highlighting
                    if ($(element).hasClass(\'uscic-radio-table\') === true ) {                    
			$(element).closest(\'tr\').addClass(\'has-warning\');
                        $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                    else if ($(element).hasClass(\'uscic-radio-horizontal-table\') === true) {
                        $(element).closest(\'table\').parent(\'div.uscic-answer\').addClass(\'has-warning\');
                        $(element).closest(\'table\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                    else {
                        $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                }
                else if ($(element).is(\'select\')) { // for select picker to add the highlighting                
                    $(element).next().children().first().attr(\'style\', \'border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                }
                else if ($(element).is(\':text\')) { // for sliders and textboxes to add the highlighting
                    if ($(element).hasClass(\'bootstrapslider\') === true) {   
                        $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    } 
                    else if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for text fields in a table row
			$(element).closest(\'tr\').addClass(\'has-warning\');
                        $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                }
                else if ($(element).hasClass(\'ranker\')) { // for ranker to add the highlighting 
                    $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                } 
            },
            unhighlight: function(element) {
                $(element).removeClass(\'empty-error\');                
                if ($(element).closest(\'div.uscic-answer\').hasClass(\'has-errors\') === false) {
                    $(element).closest(\'div.uscic-answer\').removeClass(\'has-warning\');
                    
                    if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for text fields in a table row
                        $(element).closest(\'tr\').removeAttr(\'style\');
                    }
                    else if ($(element).is(\':checkbox\')) { // for checkboxes to remove the highlighting                
                        if ($(element).hasClass(\'uscic-checkbox-table\') === true) {
                            if ($(element).closest(\'tr\').hasClass(\'has-warning\') === false) {
                                $(element).closest(\'tr\').removeAttr(\'style\');
                            }  
                        }
                        else if ($(element).hasClass(\'uscic-checkbox-horizontal-table\') === true) {
                            if ($(element).closest(\'table\').parent(\'div.uscic-answer\').hasClass(\'has-warning\') === false) {
                                $(element).closest(\'table\').removeAttr(\'style\');
                            }  
                        }
                        else {
                            $(element).closest(\'div.uscic-answer\').removeAttr(\'style\');
                        }
                    }
                    else if ($(element).is(\':radio\')) { // for checkboxes to remove the highlighting                
                        if ($(element).hasClass(\'uscic-radio-table\') === true) {
                            if ($(element).closest(\'tr\').hasClass(\'has-warning\') === false) {
                                $(element).closest(\'tr\').removeAttr(\'style\');
                            }    
                        }
                        else if ($(element).hasClass(\'uscic-radio-horizontal-table\') === true) {
                            if ($(element).closest(\'table\').parent(\'div.uscic-answer\').hasClass(\'has-warning\') === false) {
                                $(element).closest(\'table\').removeAttr(\'style\');
                            }  
                        }
                        else {
                            $(element).closest(\'div.uscic-answer\').removeAttr(\'style\');
                        }    
                    }
                    else if ($(element).is(\'select\')) { // for select picker to remove the highlighting                
                        $(element).next().children().first().removeAttr(\'style\');
                    }
                    else if ($(element).is(\':text\')) { // for checkboxes to add the highlighting                                    
                        if ($(element).hasClass(\'bootstrapslider\') === true) {                        
                            $(element).closest(\'div.uscic-answer\').removeAttr(\'style\');                            
                        }    
                        else if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for text fields in a table row
                            if ($(element).closest(\'tr\').hasClass(\'has-warning\') === false) {;
                                $(element).closest(\'tr\').removeAttr(\'style\');
                            }    
                        }
                    }
                    else if ($(element).hasClass(\'ranker\')) { // for ranker to add the highlighting                       
                        $(element).closest(\'div.uscic-answer\').first().removeAttr(\'style\');
                    } 
                }
            },';

            $str .= $errorplacement;


            $str .= '});    
        return $(\'form\').valid();
        }';
        }

        /* add error checking function */
        if ($checkerror) {

            $str .= 'function validateFormError() {                
    $(\'form\').removeData(\'validator\');        
    $(\'form\').validate({  ' . $rulestringerror . ' 
        ignore: ":hidden:not(.ranker, .selectpicker, .bootstrapslider, .knob), #calendardiv, .empty-error, .ignore-error, .dkrfna", // for selectpicker bootstrap plugin; we dont ignore dkrfna here so we check dk/rf/naed answers
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            highlight: function(element) {
                $(element).addClass(\'error-error\');
                $(element).closest(\'div.uscic-answer\').addClass(\'has-warning\');
                
                if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for multi column setup in a table row                
                    $(element).closest(\'tr\').addClass(\'has-warning\');
                    $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                }
                else if ($(element).is(\':checkbox\')) { // for checkboxes to add the highlighting                
                    if ($(element).hasClass(\'uscic-checkbox-table\') === true) {
                        $(element).closest(\'tr\').addClass(\'has-warning\');
                        $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }  
                    else if ($(element).hasClass(\'uscic-checkbox-horizontal-table\') === true) {
                        $(element).closest(\'table\').parent(\'div.uscic-answer\').addClass(\'has-warning\');
                        $(element).closest(\'table\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                    else {
                        $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }    
                }
                else if ($(element).is(\':radio\')) { // for radio buttons to add the highlighting
                    if ($(element).hasClass(\'uscic-radio-table\') === true) {
			$(element).closest(\'tr\').addClass(\'has-warning\');
                        $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                    else if ($(element).hasClass(\'uscic-radio-horizontal-table\') === true) {
                        $(element).closest(\'table\').parent(\'div.uscic-answer\').addClass(\'has-warning\');
                        $(element).closest(\'table\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                    else {
                        $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                }
                else if ($(element).is(\'select\')) { // for select picker to add the highlighting                
                    $(element).next().children().first().attr(\'style\', \'border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                } 
                else if ($(element).is(\':text\')) { // for checkboxes to add the highlighting
                    if ($(element).hasClass(\'bootstrapslider\') === true) {                        
                        $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');                        
                    } 
                    else if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for text fields in a table row
			$(element).closest(\'tr\').addClass(\'has-warning\');
                        $(element).closest(\'tr\').attr(\'style\', \'padding: 0.5em; border: 3px solid; border-color: ' . Config::errorColor() . '; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                    }
                }
                else if ($(element).hasClass(\'ranker\')) { // for ranker to add the highlighting                  
                    $(element).closest(\'div.uscic-answer\').attr(\'style\', \'padding: 0.5em; border: 1px solid; border-color: ' . Config::errorColor() . '3; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;\');
                } 
            },
            unhighlight: function(element) {
                $(element).removeClass(\'error-error\');
                if ($(element).closest(\'div .uscic-answer\').hasClass(\'has-errors\') === false && $(element).closest(\'div .uscic-answer\').hasClass(\'has-warning\') === false) {
                    $(element).closest(\'div.uscic-answer\').removeClass(\'has-warning\');
                    
                    if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for text fields in a table row
                        if ($(element).closest(\'tr\').hasClass(\'has-warning\') === false) {;
                            $(element).closest(\'tr\').removeAttr(\'style\');
                        }    
                    }
                    else if ($(element).is(\':checkbox\')) { // for checkboxes to remove the highlighting                
                        if ($(element).hasClass(\'uscic-checkbox-table\') === true) {
                            if ($(element).closest(\'tr\').hasClass(\'has-warning\') === false) {;
                                $(element).closest(\'tr\').removeAttr(\'style\');
                            }    
                        }
                        else if ($(element).hasClass(\'uscic-checkbox-horizontal-table\') === true) {
                            if ($(element).closest(\'table\').parent(\'div.uscic-answer\').hasClass(\'has-warning\') === false) {
                                $(element).closest(\'table\').removeAttr(\'style\');
                            }  
                        }
                        else {
                            $(element).closest(\'div.uscic-answer\').removeAttr(\'style\');
                        }
                    }
                    else if ($(element).is(\':radio\')) { // for checkboxes to remove the highlighting                
                        if ($(element).hasClass(\'uscic-radio-table\') === true) {
                            if ($(element).closest(\'tr\').hasClass(\'has-warning\') === false) {
                                $(element).closest(\'tr\').removeAttr(\'style\');
                            }    
                        }
                        else if ($(element).hasClass(\'uscic-radio-horizontal-table\') === true) {
                            if ($(element).closest(\'table\').parent(\'div.uscic-answer\').hasClass(\'has-warning\') === false) {
                                $(element).closest(\'table\').removeAttr(\'style\');
                            }  
                        }
                        else {
                            $(element).closest(\'div.uscic-answer\').removeAttr(\'style\');
                        }    
                    }
                    else if ($(element).is(\'select\')) { // for select picker to remove the highlighting                
                        $(element).next().children().first().removeAttr(\'style\');
                    }
                    else if ($(element).is(\':text\')) { // for checkboxes to add the highlighting                                    
                        if ($(element).hasClass(\'bootstrapslider\') === true) {                        
                            $(element).closest(\'div.uscic-answer\').removeAttr(\'style\');                            
                        }
                        else if ($(element).closest(\'td\') && $(element).closest(\'td\').hasClass(\'uscic-table-row-cell-multicolumn\')) { // for text fields in a table row
                            $(element).closest(\'tr\').removeAttr(\'style\');
                        }
                    }
                    else if ($(element).hasClass(\'ranker\')) { // for ranker to add the highlighting                       
                        $(element).closest(\'div.uscic-answer\').first().removeAttr(\'style\');
                    } 
                }
            },';

            $str .= $errorplacement1;


            $str .= '});    
        
            // validate for errors                        
            var result = $(\'form\').valid();
        
            // force showing of all error blocks since jquery validator hides the empty ones!
            // find all inputs with empty-error class, get name and show p block that matches in its for attr
            $(document).find(\'.empty-error\').each(function(element) {
                var name = $(this).attr(\'name\');
                if($(this).is("select") && $(this).is("[multiple]")) {
                   name = $(this).attr(\'name\').replace("[","").replace("]",""); 
                }
                $(\'p[for="\' + name + \'"]\').css(\'display\', \'block\');
            });
            return result;
        }        
';
            
        }
        
        if (($checkempty || $checkerror)) {
            if (Config::useAccessible()) {        
                $str .= 'function validateAccessible() {';
                $str .= // force showing of all error blocks since jquery validator hides the empty ones!
            // find all inputs with empty-error class, get name and show p block that matches in its for attr
            '$(document).find(\'.empty-error\').each(function(element) {
                var name = $(this).attr(\'name\');
                if($(this).is("select") && $(this).is("[multiple]")) {
                   name = $(this).attr(\'name\').replace("[","").replace("]",""); 
                }
                $(\'p[for="\' + name + \'"]\').attr(\'role\', \'alert\');
                $(\'p[for="\' + name + \'"]\').attr(\'aria-live\', \'assertive\');
            });
            }';
            }
            
            $str .= 'function scrollToError() {
                            var up = 10;
                            var firsterror = $(\'.uscic-help-block\').first();
                            if (!firsterror || !firsterror.offset()) {
                                return;
                            }
                            var scroll = -1;
                            if (firsterror.parents(".uscic-answer").length) {
                                var question = firsterror.parents(".uscic-answer").first().prev();
                                if (question.attr("uscic-texttype") == "question") {
                                    scroll = (question.offset().top) - up;
                                }
                                else {
                                    scroll = firsterror.parents(".uscic-answer").offset().top - up;
                                }
                            }
                            else if (firsterror.parents(".uscic-table-row-question-cell-enumerated").length) {
                                var question = firsterror.parents(".uscic-table-row-question-cell-enumerated").first().children().first();
                                scroll = (question.offset().top) - up;                                
                            }
                            else {
                                scroll = (firsterror.offset().top) - up;
                            }
                            if (scroll < 0) {
                                return;
                            }
                            if (scroll > -1) {
                                $(\'html, body\').animate({
                                    scrollTop: scroll
                                },500);
                            }    
                        }';
        }
        
        $str .= '</script>';
        return $finalstr . minifyScript($str);
    }

    function displaySlider($variable, $var, $name, $id, $value, $minimum, $maximum, $errors, $qa, $inlineclass, $step = 1, $tooltip = "show", $orientation = "horizontal", $dkrfna = "", $linkedto = "", $legend = "", $width = "400px", $height = "40px", $formater = ' return value;') {

        $returnStr = '';
        if (!isRegisteredScript("js/modernizr.js")) {
            registerScript('js/modernizr.js');
            $returnStr = getScript("js/modernizr.js");
        }

        // with pre-select
        if ($var->getSliderPreSelection() == SLIDER_PRESELECTION_YES) {
        
            if (!isRegisteredScript("js/bootstrap-slider/bootstrap-slider/bootstrap-slider.min.js")) {
                registerScript('js/bootstrap-slider/bootstrap-slider.min.js');
                $returnStr .= getScript("js/bootstrap-slider/bootstrap-slider.min.js");
            }
        }
        else {
            
            if (!isRegisteredScript("js/bootstrap-slider/bootstrap-slider/bootstrap-slider-no-preselection.min.js")) {
                registerScript('js/bootstrap-slider/bootstrap-slider-no-preselection.min.js');
                $returnStr .= getScript("js/bootstrap-slider/bootstrap-slider-no-preselection.min.js");
            }
            
        }

        if (!isRegisteredScript("js/bootstrap-slider/bootstrap-slider.min.css")) {
            registerScript('js/bootstrap-slider/css/bootstrap-slider.min.css');
            $returnStr .= getCSS("js/bootstrap-slider/bootstrap-slider.min.css");
        }
        
        if ($width == "") {
            $width = "400px";
        }
        if ($height == "") {
            $height = "40px";
        }
        if ($formater == "") {
            $formater = "return value;";
        }

        $str = '<script type="text/javascript">
                        $( document ).ready(function() {
                            $(\'#' . $id . '\').slider({ 
                          formatter: function(value) {' . $formater . '} });              
                        ';

        // if not numeric, then show without value
        if (!is_numeric($value)) {
            $value = "";
        }
        
        if ($value == "") {
            $str .= "$('#" . $id . "').val(''); document.getElementById('" . $id . "').value='';";
        }

        $str .= '});
                       </script>';
        
        // if no value, do this on window load, so the slider is there
        // this is to center tooltip if always showing and no initial value
        //if ($value == "") {
        //    $str .= '$( window ).load(function() {';
        //    $str .= '$(".tooltip").css("left", "50%");';
        //    $str .= '});';
        //}
        
        $returnStr .= minifyScript($str);
        $dkrfnaclass = "";
        if ($dkrfna != "") {
            if ($this->engine->isDKAnswer($variable)) {
                $dkrfnaclass = "dkrfna";
            } else if ($this->engine->isRFAnswer($variable)) {
                $dkrfnaclass = "dkrfna";
            } else if ($this->engine->isNAAnswer($variable)) {
                $dkrfnaclass = "dkrfna";
            }
        }

        $startlabel = '<label id="label_' . $id . '" for="' . $id . '">';
        $endlabel = '</label>';
        if (Config::useAccessible() && $linkedto != "") {
            $startlabel = "";
            $endlabel = "";
        }
        $role = "";
        if (Config::useAccessible() && $legend != "") {
            $role = ' aria-labelledby="' . $legend . '" ';
        }

        if ($orientation == "horizontal") {
            $returnStr .= '<div id="' . $id . '_slid" class="form-group uscic-formgroup' . $inlineclass . '">';


            // add labeling
            $labels = $this->engine->replaceFills($var->getSliderLabels());
            $labelStr = '';
            if ($labels != "") {
                global $survey;
                $labelvar = $survey->getVariableDescriptiveByName($labels);
                $labels = $labelvar->getOptions();
                if (sizeof($labels) > 0) {

                    // NEW WAY TO ADD:
                    // <input id="ex13" type="text" data-slider-ticks="[0, 100, 200, 300, 400]" data-slider-ticks-snap-bounds="30" data-slider-ticks-labels='["$0", "$100", "$200", "$300", "$400"]'/>

                    $labelStr .= '
                                <table role="presentation" id="' . $id . '_labels" class="slider_labels">
                                    <tr>';
                    for ($i = 0; $i < sizeof($labels); $i++) {
                        $option = $labels[$i];
                        if ($i == 0) {
                            $labelStr .= '<td id="' . $id . '_labels' . $i . '" style="width: 20%; text-align: left;">' . $this->engine->replaceFills($option["label"]) . '</td>';
                        } else if (($i + 1) == sizeof($labels)) {
                            $labelStr .= '<td id="' . $id . '_labels' . $i . '" style="width: 20%; text-align: right;">' . $this->engine->replaceFills($option["label"]) . '</td>';
                        } else {
                            $labelStr .= '<td id="' . $id . '_labels' . $i . '" style="width: 20%; text-align: center;">' . $this->engine->replaceFills($option["label"]) . '</td>';
                        }
                    }
                    $labelStr .= '</tr>       
                                </table>
                           ';
                }
            }

            if ($var->getSliderLabelPlacement() == SLIDER_LABEL_PLACEMENT_TOP) {
                $returnStr .= $labelStr;
            }

            $minimumlabel = $minimum;
            $maximumlabel = $maximum;
            if ($labels != "") {
                $minimumlabel = "";
                $maximumlabel = "";
            }
            $returnStr .= '<div id="' . $id . '_sliderdiv" class="uscic-' . $orientation . '-slider' . $inlineclass . ' ' . $qa . '">
                                <label>
                                <span style="font-weight: bold; padding-right: 10px;">' . $minimumlabel . '</span><input ' . $role . $linkedto . ' id="' . $id . '" ' . $errors . ' class="bootstrapslider ' . $dkrfnaclass . '" type="text" name=' . $name . ' value="' . addslashes($value) . '" data-slider-min="' . $minimum . '" data-slider-max="' . $maximum . '" data-slider-step="' . $step . '" data-slider-value="' . addslashes($value) . '" data-slider-orientation="' . $orientation . '" data-slider-selection="after" data-slider-tooltip="' . $tooltip . '"><span style="font-weight: bold; padding-left: 10px;">' . $maximumlabel . '</span><br/>
                                    </label>';

            if ($var->getSliderLabelPlacement() == SLIDER_LABEL_PLACEMENT_BOTTOM) {
                $returnStr .= $labelStr;
            }

            $returnStr .= '</div>'; // end class=uscic div

            if ($var->isTextbox()) {
                $pretext = $this->engine->getFill($variable, $var, SETTING_SLIDER_TEXTBOX_LABEL);
                $pretextmin = $pretext;
                $pretext = '<span class="input-group-addon uscic-inputaddon-pretext">' . $this->applyFormatting($pretext, $var->getAnswerFormatting()) . '</span>';
                $inputgroupstart = '<div class="input-group uscic-inputgroup-pretext">';
                $inputgroupend = "</div>";
                $posttext = $this->engine->getFill($variable, $var, SETTING_SLIDER_TEXTBOX_POSTTEXT);
                $posttextmin = $posttext;
                $posttext = '<div class="input-group-addon uscic-inputaddon-posttext">' . $this->applyFormatting($posttext, $var->getAnswerFormatting()) . '</div>';
                $style = "";
                if ($qa == "text-center") {
                    $style = "style='display: block; margin-left: 40%; margin-right: 40%;'";
                } else if ($qa == "text-right") {
                    $style = "style='display: block; margin-left: 80%; margin-right: 0%;'";
                }

                $readonly = "";
                if ($var->isSpinner() == true) {

                    if ($var->isTextboxManual() == false) {
                        $readonly = " readonly ";
                    }

                    $mintext = '';
                    $maxtext = '';
                    $minimum = $this->engine->getFill($variable, $var, SETTING_MINIMUM_RANGE);
                    if ($minimum == "" || !is_numeric($minimum)) {
                        $minimum = ANSWER_RANGE_MINIMUM;
                    }
                    $mintext = 'min: ' . $minimum . ',';
                    $maximum = $this->engine->getFill($variable, $var, SETTING_MAXIMUM_RANGE);
                    if ($maximum == "" || !is_numeric($maximum)) {
                        $maximum = ANSWER_RANGE_MAXIMUM;
                    }
                    $maxtext = 'max: ' . $maximum . ',';

                    $spinnertype = '';
                    if ($var->getSpinnerType() == SPINNER_TYPE_VERTICAL) {
                        $spinnertype = 'verticalbuttons: true,';
                    }
                    if (!isRegisteredScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js")) {
                        registerScript('js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js');
                        $returnStr .= getScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js");
                    }
                    if (!isRegisteredScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css")) {
                        registerScript('js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css');
                        $returnStr .= getCSS("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css");
                    }

                    if (!is_numeric($step)) {
                        $step = 1;
                    }
                    $decimals = strlen(substr(strrchr($step, "."), 1));
                    $returnStr .= '<style>
                                            span.input-group-btn {
                                                width: 0%;
                                            } 
                                            button.bootstrap-touchspin-down {
                                                top: auto;
                                            }
                                            </style>
                                            <script type="text/javascript">
                                            $(document).ready(function() {
                                                var i = $("#' . $id . '_textbox").TouchSpin({
                                                    ' . $mintext . 
                            '' . 
                            $maxtext . '' . 
                            $spinnertype . '                                                                                                            
                                                    step: ' . $step . ',
                                                    verticalupclass: "' . str_replace('"', '&#34;', $var->getSpinnerUp()) . '",
                                                    verticaldownclass: "' . str_replace('"', '&#34;', $var->getSpinnerDown()) . '",
                                                    decimals: ' . $decimals . ',
                                                    prefix: "' . str_replace('"', '&#34;', $pretextmin) . '",
                                                    postfix: "' . str_replace('"', '&#34;', $posttextmin) . '"
                                                });
                                                
                                                i.on("change", function() {
                                                    $("#' . $id . '_textbox").keyup();
                                                });
                                            });    
                                                                                        
                                            </script>';

                    $pretext = "";
                    $posttext = "";
                }

                $mask = "integer";
                $m = "\"'alias': '" . $mask . "'\"";
                $placeholder = $this->engine->getFill($variable, $var, SETTING_INPUT_MASK_PLACEHOLDER);
                $textmask = "data-inputmask=" . $m . " data-inputmask-placeholder='" . $placeholder . "'";
                $returnStr .= '<div id="' . $id . '_textboxdiv" ' . $style . ' class="uscic-horizontal-slider-textbox ' . $qa . '">' . $inputgroupstart . $pretext . '
                                <input ' . $role . $readonly . ' id="' . $id . '_textbox" autocomplete="off" ' . $textmask . ' class="form-control uscic-form-control uscic-slider-' . $orientation . '" type=text value="' . addslashes($value) . '">                                    
                                    ' . $posttext . $inputgroupend . '</div>
                                </div>
                                ';
                $str = "<script type=text/javascript>";
                $getvalue = 'parseFloat($(this).val())';
                if ($step > 1) {
                    $getvalue = 'Math.round(parseFloat($(this).val())/' . $step . ') * ' . $step;
                }

                // this is to hide handle if not showing value on no selection
                if ($var->getSliderPreSelection() == SLIDER_PRESELECTION_YES) {
                    $extrahide = "";
                }
                else {
                    $extrahide = '$("#' . $id . '_slid .min-slider-handle").addClass(\'round hide\');';
                }
                $str .= '$("#' . $id . '_textbox").keyup(
                                    function(event) {
                                        var x = $("#' . $id . '").slider();
                                        x.slider(\'setValue\', ' . $getvalue . ');                                        
                                        if ($(this).val() == "") {
                                            $("#' . $id . '").val(""); document.getElementById("' . $id . '").value="";
                                            //$(".tooltip").css("left", "50%"); // this is to center tooltip if always chosen
                                            ' . $extrahide . '
                                        }
                                        //else { // this is to hide handle if not showing initial value
                                            //$(".min-slider-handle").removeClass(\'hide\'); // this is to hide handle if not showing initial value
                                        //} // this is to hide handle if not showing initial value
                                        $("#' . $id . '_textbox").trigger("slideStopCustom");
                                    });
                                    
                                $("#' . $id . '_textbox").change(
                                    function(event) {
                                        var x = parseFloat($("#' . $id . '").slider("getAttribute", "max"));                                        
					var y = parseFloat($("#' . $id . '").slider("getAttribute", "min"));                                        
                                        if ($(this).val() == "") {
                                            $("#' . $id . '").val(""); document.getElementById("' . $id . '").value="";
                                        }
					else if (' . $getvalue . ' > x) {
                                            //$("#' . $id . '").val(x); 
                                            //document.getElementById("' . $id . '").value=x;
                                            //$(this).val(x);
					}
					else if (' . $getvalue . ' < y) {
                                            //$("#' . $id . '").val(y);
                                            //document.getElementById("' . $id . '").value=y;
                                            //$(this).val(y);
					}
					$("#' . $id . '_textbox").trigger("slideStopCustom"); 	
                                    });
        

                                $("#' . $id . '").on(\'slideStop\', function(slideEvt) {
                                        $("#' . $id . '_textbox").val(slideEvt.value);
                                            $("#' . $id . '_textbox").val(slideEvt.value);
                                            $("#' . $id . '_textbox").trigger("slideStopCustom");
                                            $("#' . $id . '_textbox").change();    
                                });    
                                ';
                
                // this handles tick display
                $str .= "$( window ).resize(function() {
                            var x = $('#" . $id . "').slider();                            
                            x.refresh({ useCurrentValue: true }); // old: x.slider('refresh'); 
                        });";
                
                $str .= "</script>";
                $returnStr .= minifyScript($str);
            } else {
                $returnStr .= "</div>";
            }
            $returnStr .= $dkrfna;
        } else {
            $returnStr .= '<div class="form-group uscic-formgroup' . $inlineclass . '">';
            $returnStr .= '<table role="presentation" id="' . $id . '_slidertable" class="uscic-' . $orientation . '-slider' . $inlineclass . ' ' . $qa . '"><tr><td align=middle>' . $minimum . '</td></tr><tr><td align=middle>
                <input ' . $role . $linkedto . ' class="bootstrapslider ' . $dkrfnaclass . '" id="' . $id . '" ' . $errors . ' style="width: ' . $width . '; height: ' . $height . ';" type="text" name=' . $name . ' value="' . addslashes($value) . '" data-slider-min="' . $minimum . '" data-slider-max="' . $maximum . '" data-slider-step="' . $step . '" data-slider-value="' . addslashes($value) . '" data-slider-orientation="' . $orientation . '" data-slider-selection="after" data-slider-tooltip="' . $tooltip . '"></td></tr><tr><td align=middle><b>' . $maximum . '</b></td></tr></table>';

            if ($var->isTextbox()) {
                $pretext = $this->engine->getFill($variable, $var, SETTING_SLIDER_TEXTBOX_LABEL);
                $pretext = '<span class="input-group-addon uscic-inputaddon-pretext">' . $this->applyFormatting($pretext, $var->getAnswerFormatting()) . '</span>';
                $inputgroupstart = '<div class="input-group uscic-inputgroup-pretext">';
                $inputgroupend = "</div>";
                $posttext = $this->engine->getFill($variable, $var, SETTING_SLIDER_TEXTBOX_POSTTEXT);
                $posttext = '<div class="input-group-addon uscic-inputaddon-posttext">' . $this->applyFormatting($posttext, $var->getAnswerFormatting()) . '</div>';

                $style = "";
                if ($qa == "text-center") {
                    $style = "style='display: block; margin-left: 40%; margin-right: 40%;'";
                } else if ($qa == "text-right") {
                    $style = "style='display: block; margin-left: 80%; margin-right: 0%;'";
                }

                $readonly = "";
                if ($var->isSpinner() == true) {

                    if ($var->isTextboxManual() == false) {
                        $readonly = " readonly ";
                    }

                    $mintext = '';
                    $maxtext = '';
                    $minimum = $this->engine->getFill($variable, $var, SETTING_MINIMUM_RANGE);
                    if ($minimum == "" || !is_numeric($minimum)) {
                        $minimum = ANSWER_RANGE_MINIMUM;
                    }
                    $mintext = 'min: ' . $minimum . ',';
                    $maximum = $this->engine->getFill($variable, $var, SETTING_MAXIMUM_RANGE);
                    if ($maximum == "" || !is_numeric($maximum)) {
                        $maximum = ANSWER_RANGE_MAXIMUM;
                    }
                    $maxtext = 'max: ' . $maximum . ',';

                    $spinnertype = '';
                    if ($var->getSpinnerType() == SPINNER_TYPE_VERTICAL) {
                        $spinnertype = 'verticalbuttons: true,';
                    }
                    if (!isRegisteredScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js")) {
                        registerScript('js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js');
                        $returnStr .= getScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js");
                    }
                    if (!isRegisteredScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css")) {
                        registerScript('js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css');
                        $returnStr .= getCSS("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css");
                    }

                    if (!is_numeric($step)) {
                        $step = 1;
                    }
                    $decimals = strlen(substr(strrchr($step, "."), 1));
                    $returnStr .= '<style>
                                            span.input-group-btn {
                                                width: 0%;
                                            } 
                                            button.bootstrap-touchspin-down {
                                                top: auto;
                                            }
                                            </style>
                                            <script type="text/javascript">
                                            $(document).ready(function() {
                                                var i = $("#' . $id . '_textbox").TouchSpin({
                                                    ' . $mintext .
                            $maxtext .
                            $spinnertype . '                                                                                                            
                                                    step: ' . $step . ',
                                                    verticalupclass: "' . str_replace('"', '&#34;', $var->getSpinnerUp()) . '",
                                                    verticaldownclass: "' . str_replace('"', '&#34;', $var->getSpinnerDown()) . '",
                                                    decimals: ' . $decimals . ',
                                                    prefix: "' . str_replace('"', '&#34;', $pretextmin) . '",
                                                    postfix: "' . str_replace('"', '&#34;', $posttextmin) . '"
                                                });
                                                
                                                i.on("change", function() {
                                                    $("#' . $id . '_textbox").keyup();
                                                });
                                            });    
                                                                                        
                                            </script>';

                    $pretext = "";
                    $posttext = "";
                }

                $mask = "integer";
                $m = "\"'alias': '" . $mask . "'\"";
                $placeholder = $this->engine->getFill($variable, $var, SETTING_INPUT_MASK_PLACEHOLDER);
                $textmask = "data-inputmask=" . $m . " data-inputmask-placeholder='" . $placeholder . "'";
                $returnStr .= '<div id="' . $id . '_textboxdiv" ' . $style . ' class="uscic-vertical-slider-textbox ' . $qa . '">' . $inputgroupstart . $pretext . '
                                <input ' . $role . $readonly . ' id="' . $id . '_textbox" autocomplete="off" ' . $textmask . ' class="form-control uscic-form-control" type=text value="' . addslashes($value) . '">                                    
                                    ' . $posttext . $inputgroupend . '</div>
                                </div>
                                ';
                $str = "<script type=text/javascript>";

                $getvalue = 'parseFloat($(this).val())';
                if ($step > 1) {
                    $getvalue = 'Math.round(parseFloat($(this).val())/' . $step . ') * ' . $step;
                }

                $str .= '$("#' . $id . '_textbox").keyup(
                                    function(event) {
                                        var x = $("#' . $id . '").slider();
                                        x.slider(\'setValue\', ' . $getvalue . ');
                                        
                                        if ($(this).val() == "") {
                                            $("#' . $id . '").val(""); document.getElementById("' . $id . '").value="";                                                
                                        }
                                    });
                                    
                                $("#' . $id . '_textbox").change(
                                    function(event) {
                                        var x = parseFloat($("#' . $id . '").slider("getAttribute", "max"));                                        
					var y = parseFloat($("#' . $id . '").slider("getAttribute", "min"));                                        
                                        if ($(this).val() == "") {
                                            $("#' . $id . '").val(""); 
                                            document.getElementById("' . $id . '").value="";
                                        }
					else if (' . $getvalue . ' > x) {
                                            $("#' . $id . '").val(x); 
                                            document.getElementById("' . $id . '").value=x;
                                            //$(this).val(x);
					}
					else if (' . $getvalue . ' < y) {
                                            $("#' . $id . '").val(y);
                                            document.getElementById("' . $id . '").value=y;
                                            //$(this).val(y);
					}
					$("#' . $id . '_textbox").trigger("slideStopCustom"); 	
                                    });
                                    
                                $("#' . $id . '").on(\'slideStop\', function(slideEvt) {                                    
                                        $("#' . $id . '_textbox").val(slideEvt.value);
                                        $("#' . $id . '_textbox").trigger("slideStopCustom");
                                        $("#' . $id . '_textbox").change();    
                                });    
                                ';
                $str .= "</script>";
                $returnStr .= minifyScript($str);
            } else {
                $returnStr .= "</div>";
            }
            $returnStr .= $dkrfna;
        }
        return $returnStr;
    }

    function displayKnob($variable, $var, $name, $id, $value, $minimum, $maximum, $errors, $qa, $inlineclass, $step = 1, $dkrfna = "", $linkedto = "", $legend = "", $rotation = "clockwise", $width = "400px", $height = "40px") {

        $returnStr = '';
        if (!isRegisteredScript("js/roundslider/roundslider.min.js")) {
            registerScript('js/roundslider/roundslider.min.js');
            $returnStr .= getScript("js/roundslider/roundslider.min.js");
        }

        if (!isRegisteredScript("js/roundslider/roundslider.min.css")) {
            registerScript('js/roundslider/roundslider.min.css');
            $returnStr .= getCss("js/roundslider/roundslider.min.css");
        }

        $tmp = "";
        if ($var->isTextbox()) {
            $tmp = "change: updateTextbox,";
        }
        $increment = $var->getIncrement();
        if ($increment == "") {
            $increment = "1";
        }

        /* tooltip formatting
         * 
         * tooltipFormat: "tooltipVal2",
         * function tooltipVal2(args) {
          return "$ " + args.value;
          }
         */

        $str = '<script type="text/javascript">
                        $( document ).ready(function() {
                            $(\'#' . $id . '\').roundSlider({
    radius: 70,
    circleShape: "full",
    width: 20,
    min: ' . $minimum . ',
    max: ' . $maximum . ', 
    step: ' . $increment . ',
    ' . $tmp . '
    sliderType: "min-range"
});              
                        ';

        if ($value == "") {
            $str .= "$('#" . $id . "').val(''); document.getElementById('" . $id . "').value='';";
        }

        $str .= '});
                       </script>';
        $returnStr .= minifyScript($str);
        $dkrfnaclass = "";
        if ($dkrfna != "") {
            if ($this->engine->isDKAnswer($variable)) {
                $dkrfnaclass = "dkrfna";
            } else if ($this->engine->isRFAnswer($variable)) {
                $dkrfnaclass = "dkrfna";
            } else if ($this->engine->isNAAnswer($variable)) {
                $dkrfnaclass = "dkrfna";
            }
        }

        // jquery know: <input ' . $linkedto . ' id="' . $id . '" ' . $errors . ' class="knob ' . $dkrfnaclass . '" type="text" name=' . $name . ' value="' . addslashes($value) . '" data-min="' . $minimum . '" data-max="' . $maximum . '" data-step="' . $step . '" data-value="' . addslashes($value) . '" data-rotation="' . $rotation . '">
        $startlabel = '<label id="label_' . $id . '" for="' . $id . '">';
        $endlabel = '</label>';
        if (Config::useAccessible() && $linkedto != "") {
            $startlabel = "";
            $endlabel = "";
        }
        $role = "";
        if (Config::useAccessible() && $legend != "") {
            $role = ' aria-labelledby="' . $legend . '" ';
        }


        $returnStr .= '<div id="' . $id . '_knob" class="form-group uscic-formgroup' . $inlineclass . '">';
        $returnStr .= '<div id="' . $id . '_knobdiv" class="uscic-knob' . $inlineclass . ' ' . $qa . '">
                                ' . $startlabel . '
                                <input ' . $role . $linkedto . ' id="' . $id . '" ' . $errors . ' class="knob ' . $dkrfnaclass . '" type="text" name=' . $name . ' value="' . addslashes($value) . '" data-step="' . $step . '" data-value="' . addslashes($value) . '">
                                    ' . $endlabel;

        $returnStr .= '</div>'; // end class=uscic div

        if ($var->isTextbox()) {
            $pretext = $this->engine->getFill($variable, $var, SETTING_SLIDER_TEXTBOX_LABEL);
            $pretextmin = $pretext;
            $pretext = '<span class="input-group-addon uscic-inputaddon-pretext">' . $this->applyFormatting($pretext, $var->getAnswerFormatting()) . '</span>';
            $inputgroupstart = '<div class="input-group uscic-inputgroup-pretext">';
            $inputgroupend = "</div>";
            $posttext = $this->engine->getFill($variable, $var, SETTING_SLIDER_TEXTBOX_POSTTEXT);
            $posttextmin = $posttext;
            $posttext = '<div class="input-group-addon uscic-inputaddon-posttext">' . $this->applyFormatting($posttext, $var->getAnswerFormatting()) . '</div>';
            $style = "";
            if ($qa == "text-center") {
                $style = "style='display: block; margin-left: 40%; margin-right: 40%; margin-top: 10px;'";
            } else if ($qa == "text-right") {
                $style = "style='display: block; margin-left: 80%; margin-right: 0%; margin-top: 10px;'";
            } else {
                $style = "style='margin-top: 10px;'";
            }

            $readonly = "";
            if ($var->isSpinner() == true) {

                if ($var->isTextboxManual() == false) {
                    $readonly = " readonly ";
                }

                $mintext = '';
                $maxtext = '';
                $minimum = $this->engine->getFill($variable, $var, SETTING_MINIMUM_RANGE);
                if ($minimum == "" || !is_numeric($minimum)) {
                    $minimum = ANSWER_RANGE_MINIMUM;
                }
                $mintext = 'min: ' . $minimum . ',';
                $maximum = $this->engine->getFill($variable, $var, SETTING_MAXIMUM_RANGE);
                if ($maximum == "" || !is_numeric($maximum)) {
                    $maximum = ANSWER_RANGE_MAXIMUM;
                }
                $maxtext = 'max: ' . $maximum . ',';

                $spinnertype = '';
                if ($var->getSpinnerType() == SPINNER_TYPE_VERTICAL) {
                    $spinnertype = 'verticalbuttons: true,';
                }
                if (!isRegisteredScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js")) {
                    registerScript('js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js');
                    $returnStr .= getScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.js");
                }
                if (!isRegisteredScript("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css")) {
                    registerScript('js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css');
                    $returnStr .= getCSS("js/bootstrap-touchspin-master/dist/jquery.bootstrap-touchspin.min.css");
                }

                if (!is_numeric($step)) {
                    $step = 1;
                }
                $decimals = strlen(substr(strrchr($step, "."), 1));
                $returnStr .= '<style>
                                            span.input-group-btn {
                                                width: 0%;
                                            } 
                                            button.bootstrap-touchspin-down {
                                                top: auto;
                                            }
                                            </style>
                                            <script type="text/javascript">
                                            $(document).ready(function() {
                                                var i = $("#' . $id . '_textbox").TouchSpin({
                                                    ' . $mintext .
                        $maxtext .
                        $spinnertype . '                                                                                                            
                                                    step: ' . $step . ',
                                                    verticalupclass: "' . str_replace('"', '&#34;', $var->getSpinnerUp()) . '",
                                                    verticaldownclass: "' . str_replace('"', '&#34;', $var->getSpinnerDown()) . '",
                                                    decimals: ' . $decimals . ',
                                                    prefix: "' . str_replace('"', '&#34;', $pretextmin) . '",
                                                    postfix: "' . str_replace('"', '&#34;', $posttextmin) . '"
                                                });
                                                
                                                i.on("change", function() {
                                                    $("#' . $id . '_textbox").keyup();
                                                });
                                            });    
                                                                                        
                                            </script>';

                $pretext = "";
                $posttext = "";
            }

            $mask = "integer";
            $m = "\"'alias': '" . $mask . "'\"";
            $placeholder = $this->engine->getFill($variable, $var, SETTING_INPUT_MASK_PLACEHOLDER);
            $textmask = "data-inputmask=" . $m . " data-inputmask-placeholder='" . $placeholder . "'";
            $returnStr .= '<div id="' . $id . '_textboxdiv" ' . $style . ' class="uscic-knob-textbox ' . $qa . '">' . $inputgroupstart . $pretext . '
                                <input ' . $role . $readonly . ' id="' . $id . '_textbox" autocomplete="off" ' . $textmask . ' class="form-control uscic-form-control uscic-knob-' . $rotation . '" type=text value="' . addslashes($value) . '">                                    
                                    ' . $posttext . $inputgroupend . '</div>
                                </div>
                                ';
            $str = "<script type=text/javascript>";
            $getvalue = 'parseFloat($(this).val())';
            if ($step > 1) {
                $getvalue = 'Math.round(parseFloat($(this).val())/' . $step . ') * ' . $step;
            }

            $str .= '$("#' . $id . '_textbox").keyup(
                                    function(event) {
                                        $("#' . $id . '").roundSlider("setValue", ' . $getvalue . ');                       
                                        if ($(this).val() == "") {
                                            $("#' . $id . '").val(""); document.getElementById("' . $id . '").value="";
                                        }
                                    });
                                    
                                $("#' . $id . '_textbox").change(
                                    function(event) {
                                        var x = parseFloat(' . $maximum . ');                                        
					var y = parseFloat(' . $minimum . ');                                        
                                        if ($(this).val() == "") {
                                            $("#' . $id . '").val(""); document.getElementById("' . $id . '").value="";
                                        }
					else if (' . $getvalue . ' > x) {
                                            $("#' . $id . '").val(x); 
                                            document.getElementById("' . $id . '").value=x;
                                            //$(this).val(x);
					}
					else if (' . $getvalue . ' < y) {
                                            $("#' . $id . '").val(y);
                                            document.getElementById("' . $id . '").value=y;
                                            //$(this).val(y);
					}	
                                    });
        

                                function updateTextbox(knobEvt) {
                                    $("#' . $id . '_textbox").val(knobEvt.value);
                                    $("#' . $id . '_textbox").val(knobEvt.value);
                                    $("#' . $id . '_textbox").change();    
                                }    
                                ';
            $str .= "</script>";
            $returnStr .= minifyScript($str);
        } else {
            $returnStr .= "</div>";
        }
        $returnStr .= $dkrfna;


        return $returnStr;
    }

    function displayDateTimePicker($name, $id, $default = '', $language = "en", $pickdate = 'true', $picktime = 'true', $ushourformat = "true", $seconds = "true", $minutes = "true", $inlineclass = "", $inlinestyle = "", $inlinejavascript = "", $customformat = "", $errorstring = "", $dkrfna = "", $variable = "", $linkedto = "", $legend = "", $defaultview = "", $collapse = DATE_COLLAPSE_YES, $sidebyside = DATE_SIDE_BY_SIDE_NO) {

        if ($language != "en") {
            $language = "en"; // TODO: FIGURE OUT WHICH OTHER ONES ARE SUPPORTED AND HOW TO CALL THEM
        }
        $icon = 'glyphicon-calendar';
        $class = "uscic-datetime";
        if ($pickdate == "true" && $picktime == "true") {
            if ($ushourformat == "true") {
                if ($seconds == "true") {
                    $format = "YYYY-MM-DD hh:mm:ss A";
                } else {
                    $format = "YYYY-MM-DD hh:mm A";
                }
            } else {
                $format = "YYYY-MM-DD HH:mm:ss";
            }
        } else if ($pickdate == "true") {
            $class = "uscic-date";
            $format = "YYYY-MM-DD";
        } else if ($picktime == "true") {
            $class = "uscic-time";
            $icon = 'glyphicon-time';
            if ($ushourformat == "true") {
                if ($seconds == "true") {
                    $format = "hh:mm:ss A";
                } else {
                    $format = "hh:mm A";
                }
            } else {
                if ($seconds == "true") {
                    $format = "HH:mm:ss";
                } else {
                    $format = "HH:mm";
                }
            }
        }
        
        $sec = '';
        $min = '';
        if ($seconds == "true") {
            //$sec = 'useSeconds: \'true\',';
        }
        if ($minutes == "true") {
            //$min = 'useMinutes: \'true\',';
        }

        if ($customformat != "") {
            $format = $customformat;
            if (contains($customformat, "ss") == false) {
                $sec = "";
            }
            if (contains($customformat, "mm") == false) {
                $min = "";
            }
        }

        if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SURVEY) {
            $dkrfnaclass = "";
            if ($dkrfna != "") {
                if ($this->engine->isDKAnswer($variable)) {
                    $dkrfnaclass = "dkrfna";
                } else if ($this->engine->isRFAnswer($variable)) {
                    $dkrfnaclass = "dkrfna";
                } else if ($this->engine->isNAAnswer($variable)) {
                    $dkrfnaclass = "dkrfna";
                }
            }
        }

        $returnStr = '';
        if (!isRegisteredScript("js/datetimepicker/moment-min.js")) {
            registerScript('js/datetimepicker/moment-min.js');
            $returnStr .= getScript("js/datetimepicker/moment-min.js");
        }
        if (!isRegisteredScript("js/datetimepicker/bootstrap-datetimepicker-min.js")) {
            registerScript('js/datetimepicker/bootstrap-datetimepicker-min.js');
            $returnStr .= getScript("js/datetimepicker/bootstrap-datetimepicker-min.js");
        }
        if (!isRegisteredScript("css/bootstrap-datetimepicker.min.css")) {
            registerScript('css/bootstrap-datetimepicker.min.css');
            $returnStr .= getCSS("css/bootstrap-datetimepicker.min.css");
        }

        /* in survey, then check for input masking */
        $inputmasking = '';
        if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SURVEY) {
            global $survey;
            $var = $survey->getVariableDescriptiveByName($variable);
            if ($var->isInputMaskEnabled()) {
                $inputmasking = $this->getDateTimePickerInputMasking($variable, $var);
            }
        }
        
        // set default view
        if ($default != "") {
            $defaultview = $default;
        }
        
        // no selected value and no default view specified, then use current day
        if ($defaultview == "") {
            $defaultview = date("Y-m-d", time());
        }
        
        $extraview = "";
        
        // use default view if picking date
        if ($pickdate == true) {
            $extraview = 'viewDate: moment("' . $defaultview . '"), useCurrent: false, ';
        }
        
        $both = "";
        if ($pickdate == true && $picktime == true) {
            if ($collapse == DATE_COLLAPSE_NO) {
                $cp = "false";
            } 
            else {
                $cp = "true";
            }
            if ($sidebyside == DATE_SIDE_BY_SIDE_NO) {
                $st = "false";
            } 
            else {
                $st = "true";
            }
            $both = 'sideBySide: ' . $st . ', collapse: ' . $cp . ', ';
        }

        $role = "";
        if (Config::useAccessible() && $legend != "") {
            $role = ' aria-labelledby="' . $legend . '" ';
        }    

//$extraview = "";
        // bootstrap date/time picker version 4
        $returnStr .= '<div class=\'input-group date ' . $class . '\' id=\'' . $id . 'div\'>
            <div class="input-group uscic-inputgroup-posttext">
            <input ' . $role . $linkedto . ' ' . $errorstring . ' ' . $inlinestyle . ' ' . $inlinejavascript . ' autocomplete="off" type=\'text\' class="form-control uscic-form-control ' . $dkrfnaclass . ' ' . $inlineclass . '" value="' . $default . '" id="' . $id . '" name="' . $name . '"/>
            <div class="input-group-addon uscic-inputaddon-posttext"><span class="glyphicon ' . $icon . '"></span>
            </div></div>' . $dkrfna . '
        </div>';

        if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SURVEY) {
            $returnStr .= '<script type="text/javascript">' . minifyScript('
                $(function () {
                    $(\'#' . $id . '\').datetimepicker({' . $both . $extraview . 'locale: \'' . $language . '\', ' . 'format: \'' . $format . '\'' . $inputmasking . '});                                    
                    $(\'#' . $id . '\').attr("readonly","true");
                });') . '                    
            </script>';
        } else {
            $returnStr .= '<script type="text/javascript">
                $(function () {
                    $(\'#' . $id . '\').datetimepicker({locale: \'' . $language . '\', ' . 'format: \'' . $format . '\'' . $inputmasking . '});                
                });        
            </script>';
        }
        return $returnStr;
    }

    function getDateTimePickerInputMasking($variable, $var) {

        $inputmasking = '';
        $eq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
        $enableddates = '';
        $at = $var->getAnswerType();

        // date/datetime picker
        if (inArray($at, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {

            if ($eq != "") {
                $dates = explode(SEPARATOR_COMPARISON, $eq);
                foreach ($dates as $d) {
                    if (strtotime($d) != false) { // date string
                        if ($enableddates == '') {
                            $enableddates = '"' . date("Y-m-d H:m:s", strtotime($d)) . '"';
                        } else {
                            $enableddates .= ',"' . date("Y-m-d H:m:s", strtotime($d)) . '"';
                        }
                    }
                }
                if ($enableddates != "") {
                    $inputmasking .= ', enabledDates: [' . $enableddates . ']';
                }
            }

            $disableddates = '';
            $neq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
            if ($neq != "") {
                $dates = explode(SEPARATOR_COMPARISON, $neq);
                foreach ($dates as $d) {
                    if (strtotime($d) != false) { // date string
                        if ($disableddates == '') {
                            $disableddates = '"' . date("Y-m-d H:m:s", strtotime($d)) . '"';
                        } else {
                            $disableddates .= ',"' . date("Y-m-d H:m:s", strtotime($d)) . '"';
                        }
                    }
                }
                if ($disableddates != "") {
                    $inputmasking .= ', disabledDates: [' . $disableddates . ']';
                }
            }

            // check for minimum dates
            $mindate = "";
            $gr = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_GREATER));
            if ($gr != "") {
                $dates = explode(SEPARATOR_COMPARISON, $gr);
                foreach ($dates as $d) {
                    if (strtotime($d) != false) { // date string
                        if ($mindate == '') {
                            $mindate = date("Y-m-d H:i:s", strtotime($d . "+1 day"));
                        } else {
                            if (strtotime($d) > strtotime($mindate)) {
                                $mindate = date("Y-m-d H:i:s", strtotime($d . "+1 day"));
                            }
                        }
                    }
                }
            }
            $geq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
            if ($geq != "") {
                $dates = explode(SEPARATOR_COMPARISON, $geq);
                foreach ($dates as $d) {
                    if (strtotime($d) != false) { // date string
                        if ($mindate == '') {
                            $mindate = date("Y-m-d H:i:s", strtotime($d));
                        } else {
                            if (strtotime($d) > strtotime($mindate)) {
                                $mindate = date("Y-m-d H:i:s", strtotime($d));
                            }
                        }
                    }
                }
            }
            if ($mindate != '') {
                $inputmasking .= ', minDate: moment("' . $mindate . '")';
            }

            // check for maximum dates
            $maxdate = '';
            $sm = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
            if ($sm != "") {
                $dates = explode(SEPARATOR_COMPARISON, $sm);
                foreach ($dates as $d) {
                    if (strtotime($d) != false) { // date string
                        if ($maxdate == '') {
                            $maxdate = date("Y-m-d H:i:s", strtotime($d . "-1 day"));
                        } else {
                            if (strtotime($d) < strtotime($mindate)) {
                                $maxdate = date("Y-m-d H:i:s", strtotime($d . "-1 day"));
                            }
                        }
                    }
                }
            }

            $seq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
            if ($seq != "") {
                $dates = explode(SEPARATOR_COMPARISON, $seq);
                foreach ($dates as $d) {
                    if (strtotime($d) != false) { // date string
                        if ($maxdate == '') {
                            $maxdate = date("Y-m-d H:i:s", strtotime($d));
                        } else {
                            if (strtotime($d) < strtotime($mindate)) {
                                $maxdate = date("Y-m-d H:i:s", strtotime($d));
                            }
                        }
                    }
                }
            }
            if ($maxdate != '') {
                $inputmasking .= ', maxDate: moment("' . $maxdate . '")';
            }
        }
        // time picker
        else {

            // get all hours equal to
            $allhours = array();
            if ($eq != "") {
                $times = explode(SEPARATOR_COMPARISON, $eq);
                foreach ($times as $d) {
                    if (is_numeric($d)) {
                        $allhours[] = $d;
                    }
                }
            } else {
                // add all hours                
                for ($i = 1; $i < 25; $i++) {
                    $allhours[] = $i;
                }
            }

            // exclude hours not equal to
            $neq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
            if ($neq != "") {
                $times = explode(SEPARATOR_COMPARISON, $neq);
                foreach ($times as $d) {
                    if (is_numeric($d)) {
                        if (inArray($d, $allhours)) {
                            unset($allhours[array_search($d, $allhours)]);
                        }
                    }
                }
            }

            // check for minimum hours
            $gr = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_GREATER));
            if ($gr != "") {
                $times = explode(SEPARATOR_COMPARISON, $gr);
                foreach ($times as $d) {
                    if (is_numeric($d)) { // date string
                        $key = array_search($d + 1, $allhours);
                        if ($key) {
                            $allhours = array_splice($allhours, $key);
                        }
                    }
                }
            }
            $geq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
            if ($geq != "") {
                $times = explode(SEPARATOR_COMPARISON, $geq);
                foreach ($times as $d) {
                    if (is_numeric($d)) { // date string
                        $key = array_search($d, $allhours);
                        if ($key) {
                            $allhours = array_splice($allhours, $key);
                        }
                    }
                }
            }

            // check for maximum hours
            $maxdate = '';
            $sm = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
            if ($sm != "") {
                $times = explode(SEPARATOR_COMPARISON, $sm);
                foreach ($times as $d) {
                    if (is_numeric($d)) { // date string
                        $key = array_search($d, $allhours);
                        if ($key) {
                            array_splice($allhours, $key);
                        }
                    }
                }
            }

            $seq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
            if ($seq != "") {
                $times = explode(SEPARATOR_COMPARISON, $seq);
                foreach ($times as $d) {
                    if (is_numeric($d)) { // date string
                        $key = array_search($d + 1, $allhours);
                        if ($key) {
                            array_splice($allhours, $key);
                        }
                    }
                }
            }
            $inputmasking .= ', enabledHours: [' . implode(",", $allhours) . ']';
        }

        // return result
        return $inputmasking;
    }

    function displayCalendar($id = "calendar", $type = USCIC_SMS) {
        $returnStr = "";
        if ($type == USCIC_SMS) {
            $returnStr .= '<div class="page-header" style="padding-bottom: 1px; margin: 5px 0 20px;">';
            $returnStr .= '
		<div class="pull-right form-inline">
			<div class="btn-group">
				<button class="btn btn-primary" data-calendar-nav="prev"><< Prev</button>
				<button class="btn" data-calendar-nav="today">Today</button>
				<button class="btn btn-primary" data-calendar-nav="next">Next >></button>
			</div>
			<div class="btn-group">
				<button class="btn btn-default" data-calendar-view="year">Year</button>
				<button class="btn btn-default active" data-calendar-view="month">Month</button>
				<button class="btn btn-default" data-calendar-view="week">Week</button>
				<button class="btn btn-default" data-calendar-view="day">Day</button>
			</div>
		</div>	
                <h3></h3>
	</div>
        ';
        } else {
            $returnStr = '<div class="page-header" style="padding-bottom: 1px; margin: 5px 0 20px;">';

            $returnStr .= '<div class="pull-right form-inline">
			<div class="btn-group">
				<button type=button class="btn btn-primary" data-calendar-nav="prev"><< Prev</button>
				<button type=button class="btn" data-calendar-nav="today">Today</button>
				<button type=button class="btn btn-primary" data-calendar-nav="next">Next >></button>
			</div>
			<div class="btn-group">
				<button type=button class="btn btn-default" data-calendar-view="year">Year</button>
				<button type=button class="btn btn-default active" data-calendar-view="month">Month</button>
			</div>
		</div>	<h3></h3>	
	</div>
       
        ';
        }

        $returnStr .= '

<!--		<div class="col-md-9">-->
			<div id="' . $id . '"></div>
	<!--	</div>-->';

        // this needs to happen here, since calender.js relies on tooltip.js
        if (!isRegisteredScript("js/tooltip.js")) {
            registerScript('js/tooltip.js');
            $returnStr .= '<script type="text/javascript" src="js/tooltip.js"></script>';
        }

        if (!isRegisteredScript("js/underscore-min.js")) {
            registerScript('js/underscore-min.js');
            $returnStr .= getScript("js/underscore-min.js");
        }
        if (!isRegisteredScript("js/jstz.min.js")) {
            registerScript('js/jstz.min.js');
            $returnStr .= getScript("js/jstz.min.js");
        }

        // also in header, but has to be here, otherwise calendar.js fails
        if (!isRegisteredScript("js/tooltip.js")) {
            registerScript('js/tooltip.js');
            $returnStr .= getScript("js/tooltip.js");
        }

        if (!isRegisteredScript("js/calendar-min.js")) {
            registerScript('js/calendar-min.js');
            $returnStr .= getScript("js/calendar-min.js");
        }

        if ($type == USCIC_SMS) {
            $_SESSION[CONFIGURATION_ENCRYPTION_CALENDAR] = encryptC(Config::calendarKey(), Config::smsComponentKey()); //set key to allow access to calendar in SMS via events.json/index.php
            if (!isRegisteredScript("js/app.js")) {
                registerScript('js/app.js');
                $returnStr .= getScript("js/app.js");
            }
        } else {
            if (!isRegisteredScript("js/appsurvey.js")) {
                registerScript('js/appsurvey.js');
                $returnStr .= getScript("js/appsurvey.js");
            }
        }
        return $returnStr;
    }

    function displayHeaderForTable($title, $message = '') {
        $extramin = 0;
        if ($message != '') {
            $extramin = 90;
        }
        $header = $this->displayDataTablesScripts();

        if (!isRegisteredScript("css/DT_bootstrap.min.css")) {
            registerScript('css/DT_bootstrap.min.css');
            $header .= getCSS("css/DT_bootstrap.min.css");
        }
        if (!isRegisteredScript("bootstrap/css/sticky-footer-navbar.min.css")) {
            registerScript('bootstrap/css/sticky-footer-navbar.min.css');
            $header .= getCSS("bootstrap/css/sticky-footer-navbar.min.css");
        }
        if (!isRegisteredScript("js/DT_bootstrap.min.js")) {
            registerScript('js/DT_bootstrap.min.js');
            $header .= getScript("js/DT_bootstrap.min.js");
        }

        // $.fn.dataTable.moment( "MMM DD, YYYY - HH:mm:ss" ); this line can be used to hook in ordering of date/time columns using momnet.js...
        // for formatting see http://momentjs.com
        $header .= '<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$(\'#example\').dataTable({"bFilter": false, "bLengthChange": false, "iDisplayLength": Math.round(($(window).height() - 60 - 51 - 36 - 160 - ' . $extramin . ')/40)});
			} );
		  </script>';
        return $this->showHeader($title, $header);
    }

    function displayHeaderForTableAndSideBar($title, $message = '') {
        $extramin = 0;
        if ($message != '') {
            $extramin = 90;
        }
        $header = $this->displayDataTablesScripts();

        if (!isRegisteredScript("css/DT_bootstrap.min.css")) {
            registerScript('css/DT_bootstrap.min.css');
            $header .= getScript("css/DT_bootstrap.min.css");
        }
        if (!isRegisteredScript("css/uscicadmin.css")) {
            registerScript('css/uscicadmin.css');
            $header .= getCSS("css/uscicadmin.css");
        }
        if (!isRegisteredScript("bootstrap/css/sticky-footer-navbar.min.css")) {
            registerScript('bootstrap/css/sticky-footer-navbar.min.css');
            $header .= getCSS("bootstrap/css/sticky-footer-navbar.min.css");
        }
        $header .= '<script type="text/javascript" charset="utf-8">
			$(document).ready(function () {
					if ($("[rel=tooltip]").length) {
						  $("[rel=tooltip]").tooltip();
					}
			});
			</script>';
        if (!isRegisteredScript("js/DT_bootstrap.min.js")) {
            registerScript('js/DT_bootstrap.min.js');
            $header .= getScript("js/DT_bootstrap.min.js");
        }
        $header .= '<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$(\'#example\').dataTable({"bFilter": false, "bLengthChange": false, "iDisplayLength": Math.round(($(window).height() - 60 - 51 - 36 - 160 - ' . $extramin . ')/40)});
			} );
      </script>
';
        return $this->showHeader($title, $header);
    }

    function showRespondentsTable($respondents, $refpage = 'interviewer') {
        $returnStr = '

<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
	<thead>
		<tr>
			<th>' . Language::labelRespondentIdentifier() . '</th>
			<th>' . Language::labelRespondentName() . '</th>';
        $columns = Language::defaultDisplayOverviewAddressColumns();

        foreach ($columns as $column) {
            $returnStr .= '<th>' . $column . '</th>';
        }
        $returnStr .= '<th>' . Language::labelRespondentLastContact() . '</th>
			<th>' . Language::labelRespondentStatus() . '</th>
			<th>' . Language::labelRespondentRefusal() . '</th>';
        
        $user = new User($_SESSION["URID"]);
        if ($user->getUserType() == USER_SUPERVISOR) {
            $returnStr .= '<th>' . Language::labelHouseholdInterviewer() . '</th>';
        }
        
	$returnStr .= '	</tr>
	</thead>
	<tbody>';
        foreach ($respondents as $respondent) {
            $selurid = $respondent->getUrid();
            $uridtext = Language::labelUnassigned();
            if ($selurid > 0) {                
                $us = new User($selurid);
                $uridtext = $us->getName();
            }
            $returnStr .= '<tr>';
            $returnStr .= '<td>' . setSessionParamsHref(array('page' => $refpage . '.info', 'primkey' => $respondent->getPrimkey()), $respondent->getPrimkey()) . '</td>';
            $returnStr .= '<td>' . $respondent->getFirstname() . ' ' . $respondent->getLastname() . '</td>';

            foreach ($columns as $key => $column) {
                $returnStr .= '<td>' . $respondent->getDataByField($key) . '</td>';
            }
            $returnStr .= '<td>' . $this->displayLastContact($respondent) . '</td>';
            $returnStr .= '<td>' . $this->displayStatus($respondent) . '</td>';
            $returnStr .= '<td>' . $this->displayRefusal($respondent) . '</td>';
            if ($user->getUserType() == USER_SUPERVISOR) {
                $returnStr .= '<td>' . $uridtext . '</td>'; //don't display iwer for members in hh
            }
            $returnStr .= '</tr>';
        }

        $returnStr .= '</tbody></table>';
        return $returnStr;
    }

    function showHouseholdsTable($households, $refpage = '') {
        // use 'example2' as table id, so we don't apply data tables sorting
        $returnStr = '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
	<thead>
		<tr>
			<th>' . Language::labelHouseholdIdentifier() . '</th>
			<th>' . Language::labelHouseholdName() . '</th>';

        $columns = Language::defaultDisplayOverviewAddressColumns();
        foreach ($columns as $column) {
            $returnStr .= '<th>' . $column . '</th>';
        }
        $returnStr .= '          <th>' . Language::labelHouseholdLastContact() . '</th>
			<th>' . Language::labelHouseholdStatus() . '</th>
			<th>' . Language::labelHouseholdRefusal() . '</th>';
        
        $user = new User($_SESSION["URID"]);
        if ($user->getUserType() == USER_SUPERVISOR) {
            $returnStr .= '<th>' . Language::labelHouseholdInterviewer() . '</th>';
        }
	$returnStr .= ' </tr>
	</thead>
	<tbody>';

        foreach ($households as $household) {
            $selurid = $household->getUrid();
            $uridtext = Language::labelUnassigned();
            if ($selurid > 0) {                
                $us = new User($selurid);
                $uridtext = $us->getName();
            }
            $returnStr .= '<tr>';
            $returnStr .= '<td>' . setSessionParamsHref(array('page' => $refpage . 'interviewer.household.info', 'primkey' => $household->getPrimkey()), $household->getPrimkey()) . '</td>';
            $returnStr .= '<td>' . $household->getName() . '</td>';
            foreach ($columns as $key => $column) {
                $returnStr .= '<td>' . $household->getDataByField($key) . '</td>';
            }
            $returnStr .= '<td><div data-toggle="tooltip" data-placement="top" title="' . $this->displayLastContactText($household) . '">' . $this->displayLastContact($household) . '</div></td>';
            $returnStr .= '<td>' . $this->displayStatus($household) . '</td>';
            $returnStr .= '<td>' . $this->displayRefusal($household) . '</td>';
            if ($user->getUserType() == USER_SUPERVISOR) {
                $returnStr .= '<td>' . $uridtext . '</td>'; //don't display iwer for members in hh
            }
            $returnStr .= '</tr>';
            $respondents = $household->getSelectedRespondentsWithFinFamR();
            foreach ($respondents as $respondent) {
                $bgcolor = 'style="background: #ecf4ff;"';
                $returnStr .= '<tr>';
                $returnStr .= '<td ' . $bgcolor . ' align=right>' . setSessionParamsHref(array('page' => $refpage . 'interviewer.respondent.info', 'primkey' => $respondent->getPrimkey()), $respondent->getPrimkey()) . '</td>';
                $returnStr .= '<td ' . $bgcolor . ' align=right><b>' . $respondent->getName() . '<b></td>';
                foreach ($columns as $key => $column) {
                    $returnStr .= '<td ' . $bgcolor . '></td>'; //don't display for members in hh
                    // $returnStr .= '<td>' . $household->getDataByField($key) . '</td>';
                }
                $returnStr .= '<td ' . $bgcolor . '><div data-toggle="tooltip" data-placement="top" title="' . $this->displayLastContactText($respondent) . '">' . $this->displayLastContact($respondent) . '</div></td>';
                $returnStr .= '<td ' . $bgcolor . '>' . $this->displayStatus($respondent) . '</td>';
                $returnStr .= '<td ' . $bgcolor . '>' . $this->displayRefusal($respondent) . '</td>';
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $returnStr .= '<td ' . $bgcolor . '></td>'; //don't display iwer for members in hh
                }
                $returnStr .= '</tr>';
            }
        }

        $returnStr .= '</tbody></table>';
        return $returnStr;
    }

    function displayLastContact($respondent) {
        $contact = $respondent->getLastContact();
        if ($contact == null) {
            return Language::labelNone();
        } else {
            return $contact->getCode();
        }
    }

    function displayLastContactText($respondent) {
        $contact = $respondent->getLastContact();
        if ($contact == null) {
            return Language::labelNone();
        } else {
            return $contact->getText();
        }
    }

    function displayStatus($respondent) {
        $statusCodes = Language::labelStatus();
        if (isset($statusCodes[$respondent->getStatus()])) {
            return $statusCodes[$respondent->getStatus()];
        }
        return '-';
    }

    function displayRefusal($respondent) {
        if ($respondent->isRefusal()) {
            return Language::labelYes();
        }
        return Language::labelNo();
    }

    function displayWarning($message, $id = "") {
        $idtext = "";
        if ($id != "") {
            $idtext = "id=" . $id;
        }
        return '<div ' . $idtext . ' class="alert alert-warning">' . $message . '</div>';
    }

    function displayError($message, $id = "") {
        $idtext = "";
        if ($id != "") {
            $idtext = "id=" . $id;
        }
        return '<div class="alert alert-danger">' . $message . '</div>';
    }

    function displaySuccess($message, $id = "") {
        $idtext = "";
        if ($id != "") {
            $idtext = "id=" . $id;
        }
        return '<div class="alert alert-success">' . $message . '</div>';
    }

    function displayInfo($message, $id = "") {
        $idtext = "";
        if ($id != "") {
            $idtext = "id=" . $id;
        }
        return '<div class="alert alert-info">' . $message . '</div>';
    }

    function displayModesAdmin($name, $id, $value, $multiple = "", $list = "", $onchange = "") {
        $returnStr = $this->displayComboBox();
        $tag = "";
        if ($multiple != "") {
            $tag = "[]";
        }
        $returnStr .= '<select ' . $onchange . ' ' . $multiple . ' id="' . $id . '" name="' . $name . $tag . '" class="form-control selectpicker show-tick">';
        $modes = Common::surveyModes();
        ksort($modes);
        $values = explode("~", $value);
        $modelist = explode("~", $list);
        $icons = array(MODE_CAPI => "data-icon='glyphicon glyphicon-user'", MODE_CATI => "data-icon='glyphicon glyphicon-earphone'", MODE_CASI => "data-icon='glyphicon glyphicon-globe'", MODE_CADI => "data-icon='glyphicon glyphicon-pencil'");
        foreach ($modes as $k => $mode) {
            if (trim($list) == "" || inArray($k, $modelist)) {
                $selected = "";
                if (inArray($k, $values)) {
                    $selected = "SELECTED";
                }
                $icon = $icons[$k];
                $returnStr .= "<option $icon $selected value=" . $k . ">" . $mode . "</option>";
            }
        }
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayModesChange($current = "") {
        $returnStr = "<select class='form-control selectpicker show-tick' name=" . SETTING_CHANGE_MODE . ">";
        $selected = array(MODE_CHANGE_PROGRAMMATIC_ALLOWED => "", MODE_CHANGE_NOTALLOWED => "", MODE_CHANGE_RESPONDENT_ALLOWED => "");
        $selected[$current] = "selected";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[MODE_CHANGE_NOTALLOWED] . " value=" . MODE_CHANGE_NOTALLOWED . ">" . Language::optionsModeChangeNotAllowed() . "</option>";
        $returnStr .= "<option " . $selected[MODE_CHANGE_PROGRAMMATIC_ALLOWED] . " value=" . MODE_CHANGE_PROGRAMMATIC_ALLOWED . ">" . Language::optionsModeChangeProgrammaticAllowed() . "</option>";
        $returnStr .= "<option " . $selected[MODE_CHANGE_RESPONDENT_ALLOWED] . " value=" . MODE_CHANGE_RESPONDENT_ALLOWED . ">" . Language::optionsModeChangeRespondentAllowed() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayModeReentry($current = "") {
        $returnStr = "<select class='form-control selectpicker show-tick' name=" . SETTING_REENTRY_MODE . ">";
        $selected = array(MODE_REENTRY_YES => "", MODE_REENTRY_NO => "");
        $selected[$current] = "selected";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[MODE_REENTRY_NO] . " value=" . MODE_REENTRY_NO . ">" . Language::optionsModeReentryNo() . "</option>";
        $returnStr .= "<option " . $selected[MODE_REENTRY_YES] . " value=" . MODE_REENTRY_YES . ">" . Language::optionsModeReentryYes() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayModeBack($current = "") {
        $returnStr = "<select class='form-control selectpicker show-tick' name=" . SETTING_BACK_MODE . ">";
        $selected = array(MODE_BACK_YES => "", MODE_BACK_NO => "");
        $selected[$current] = "selected";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[MODE_BACK_NO] . " value=" . MODE_BACK_NO . ">" . Language::optionsModeBackNo() . "</option>";
        $returnStr .= "<option " . $selected[MODE_BACK_YES] . " value=" . MODE_BACK_YES . ">" . Language::optionsModeBackYes() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayLanguagesAdmin($name, $id, $value, $flags = true, $country = true, $showdefault = true, $multiple = "", $list = "") {
        $languages = Language::getLanguagesArray();
        $returnStr = $this->displayComboBox();
        $values = explode("~", $value);
        $tag = "";
        if ($multiple != "") {
            $tag = "[]";
        }
        $returnStr .= '<select ' . $multiple . ' id="' . $id . '" name="' . $name . $tag . '" class="form-control selectpicker show-tick">';
        $languagelist = explode("~", $list);
        foreach ($languages as $lang) {
            if (trim($list) == "" || inArray($lang["value"], $languagelist)) {
                $text = $lang["name"];
                if ($country) {
                    if ($lang["countryfull"] != "") {
                        $text .= "(" . $lang["countryfull"] . ")";
                    }
                }
                $flagtext = "";
                if ($flags) {
                    $flagtext = 'data-icon="bfh-flag-' . $lang["country"] . '"';
                }
                $selected = "";
                if (inArray($lang["value"], $values)) {
                    $selected = "SELECTED";
                }
                $default = "";
                if ($showdefault == true && $lang["value"] == getDefaultSurveyLanguage()) {
                    $default = " (default)";
                }
                $returnStr .= '<option ' . $selected . ' value="' . $lang["value"] . '" ' . $flagtext . '>' . $text . $default . '</option>';
            }
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayLanguagesChange($current = "") {
        $returnStr = "<select class='form-control selectpicker show-tick' name=" . SETTING_CHANGE_LANGUAGE . ">";
        $selected = array(LANGUAGE_CHANGE_PROGRAMMATIC_ALLOWED => "", LANGUAGE_CHANGE_NOTALLOWED => "", LANGUAGE_CHANGE_RESPONDENT_ALLOWED => "");
        $selected[$current] = "selected";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[LANGUAGE_CHANGE_NOTALLOWED] . " value=" . LANGUAGE_CHANGE_NOTALLOWED . ">" . Language::optionsLanguageChangeNotAllowed() . "</option>";
        $returnStr .= "<option " . $selected[LANGUAGE_CHANGE_PROGRAMMATIC_ALLOWED] . " value=" . LANGUAGE_CHANGE_PROGRAMMATIC_ALLOWED . ">" . Language::optionsLanguageChangeProgrammaticAllowed() . "</option>";
        $returnStr .= "<option " . $selected[LANGUAGE_CHANGE_RESPONDENT_ALLOWED] . " value=" . LANGUAGE_CHANGE_RESPONDENT_ALLOWED . ">" . Language::optionsLanguageChangeRespondentAllowed() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayLanguageReentry($current = "") {
        $returnStr = "<select class='form-control selectpicker show-tick' name=" . SETTING_REENTRY_LANGUAGE . ">";
        $selected = array(LANGUAGE_REENTRY_YES => "", LANGUAGE_REENTRY_NO => "");
        $selected[$current] = "selected";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[LANGUAGE_REENTRY_NO] . " value=" . LANGUAGE_REENTRY_NO . ">" . Language::optionsLanguageReentryNo() . "</option>";
        $returnStr .= "<option " . $selected[LANGUAGE_REENTRY_YES] . " value=" . LANGUAGE_REENTRY_YES . ">" . Language::optionsLanguageReentryYes() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayLanguageBack($current = "") {
        $returnStr = "<select class='form-control selectpicker show-tick' name=" . SETTING_BACK_LANGUAGE . ">";
        $selected = array(LANGUAGE_BACK_YES => "", LANGUAGE_BACK_NO => "");
        $selected[$current] = "selected";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[LANGUAGE_BACK_NO] . " value=" . LANGUAGE_BACK_NO . ">" . Language::optionsLanguageBackNo() . "</option>";
        $returnStr .= "<option " . $selected[LANGUAGE_BACK_YES] . " value=" . LANGUAGE_BACK_YES . ">" . Language::optionsLanguageBackYes() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayIsArray($current = "", $type = -1) {
        $returnStr = "<select id=arraydrop class='form-control selectpicker show-tick' name='" . SETTING_ARRAY . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ARRAY_ANSWER_YES => "", ARRAY_ANSWER_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[ARRAY_ANSWER_NO] . " value=" . ARRAY_ANSWER_NO . ">" . Language::optionsArrayNo() . "</option>";
        $returnStr .= "<option " . $selected[ARRAY_ANSWER_YES] . " value=" . ARRAY_ANSWER_YES . ">" . Language::optionsArrayYes() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayIsKeep($current = "", $type = -1) {
        $returnStr = "<select id=keepdrop class='form-control selectpicker show-tick' name='" . SETTING_KEEP . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", KEEP_ANSWER_YES => "", KEEP_ANSWER_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[KEEP_ANSWER_NO] . " value=" . KEEP_ANSWER_NO . ">" . Language::optionsKeepNo() . "</option>";
        $returnStr .= "<option " . $selected[KEEP_ANSWER_YES] . " value=" . KEEP_ANSWER_YES . ">" . Language::optionsKeepYes() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayHidden($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", HIDDEN_YES => "", HIDDEN_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[HIDDEN_NO] . " value=" . HIDDEN_NO . ">" . Language::optionsHiddenNo() . "</option>";
        $returnStr .= "<option " . $selected[HIDDEN_YES] . " value=" . HIDDEN_YES . ">" . Language::optionsHiddenYes() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayScreendumps($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", SCREENDUMPS_YES => "", SCREENDUMPS_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SCREENDUMPS_YES] . " value=" . SCREENDUMPS_YES . ">" . Language::optionsScreendumpsYes() . "</option>";
        $returnStr .= "<option " . $selected[SCREENDUMPS_NO] . " value=" . SCREENDUMPS_NO . ">" . Language::optionsScreendumpsNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayStoreLocation($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select id=" . $name . " class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", STORE_LOCATION_INTERNAL => "", STORE_LOCATION_BOTH => "", STORE_LOCATION_EXTERNAL => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[STORE_LOCATION_INTERNAL] . " value=" . STORE_LOCATION_INTERNAL . ">" . Language::optionsStoreInternal() . "</option>";
        $returnStr .= "<option " . $selected[STORE_LOCATION_BOTH] . " value=" . STORE_LOCATION_BOTH . ">" . Language::optionsStoreBoth() . "</option>";
        $returnStr .= "<option " . $selected[STORE_LOCATION_EXTERNAL] . " value=" . STORE_LOCATION_EXTERNAL . ">" . Language::optionsStoreExternal() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayDataInputMask($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATA_INPUTMASK_YES => "", DATA_INPUTMASK_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[DATA_INPUTMASK_YES] . " value=" . DATA_INPUTMASK_YES . ">" . Language::optionsDataInputMaskYes() . "</option>";
        $returnStr .= "<option " . $selected[DATA_INPUTMASK_NO] . " value=" . DATA_INPUTMASK_NO . ">" . Language::optionsDataInputMaskNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayDataKeepOnly($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATA_KEEP_ONLY_YES => "", DATA_KEEP_ONLY_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[DATA_KEEP_ONLY_YES] . " value=" . DATA_KEEP_ONLY_YES . ">" . Language::optionsDataKeepOnlyYes() . "</option>";
        $returnStr .= "<option " . $selected[DATA_KEEP_ONLY_NO] . " value=" . DATA_KEEP_ONLY_NO . ">" . Language::optionsDataKeepOnlyNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayDataKeep($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATA_KEEP_YES => "", DATA_KEEP_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[DATA_KEEP_YES] . " value=" . DATA_KEEP_YES . ">" . Language::optionsDataKeepYes() . "</option>";
        $returnStr .= "<option " . $selected[DATA_KEEP_NO] . " value=" . DATA_KEEP_NO . ">" . Language::optionsDataKeepNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayDataSkip($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATA_SKIP_YES => "", DATA_SKIP_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[DATA_SKIP_YES] . " value=" . DATA_SKIP_YES . ">" . Language::optionsDataSkipYes() . "</option>";
        $returnStr .= "<option " . $selected[DATA_SKIP_NO] . " value=" . DATA_SKIP_NO . ">" . Language::optionsDataSkipNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displaySetOfEnumeratedOutput($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATA_KEEP_YES => "", DATA_KEEP_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SETOFENUMERATED_DEFAULT] . " value=" . SETOFENUMERATED_DEFAULT . ">" . Language::optionsDataSetOfEnumeratedDefault() . "</option>";
        $returnStr .= "<option " . $selected[SETOFENUMERATED_BINARY] . " value=" . SETOFENUMERATED_BINARY . ">" . Language::optionsDataSetOfEnumeratedBinary() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayValueLabelWidth($name, $current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATA_KEEP_YES => "", DATA_KEEP_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[VALUELABEL_WIDTH_SHORT] . " value=" . VALUELABEL_WIDTH_SHORT . ">" . Language::optionsValueLabelWidthShort() . "</option>";
        $returnStr .= "<option " . $selected[VALUELABEL_WIDTH_FULL] . " value=" . VALUELABEL_WIDTH_FULL . ">" . Language::optionsValueLabelWidthFull() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displaySections($name, $current, $suid, $ignore = "", $multiple = '') {
        $survey = new Survey($suid);
        $sections = $survey->getSections();
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        foreach ($sections as $section) {
            $selected = "";
            if ($current == $section->getSeid()) {
                $selected = "SELECTED";
            }
            if ($section->getSeid() != $ignore) {
                $returnStr .= "<option " . $selected . " value=" . $section->getSeid() . ">" . $section->getName() . "</option>";
            }
        }
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displaySurveys($name, $id, $current, $ignore = "", $multiple = "", $onchange = "", $all = false) {
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys($all);
        $returnStr = "<select $onchange $multiple class='selectpicker show-tick' name=$name id=$id>";
        $current = explode("~", $current);
        foreach ($surveys as $survey) {
            if ($survey->getSuid() != $ignore) {
                $selected = "";
                if (inArray($survey->getSuid(), $current)) {
                    $selected = "SELECTED";
                }
                $returnStr .= "<option " . $selected . " value=" . $survey->getSuid() . ">" . $survey->getName() . "</option>";
            }
        }
        $returnStr .= "</select>";
        return $returnStr;
    }
    
    function displaySurveysNoSelect($name, $id, $current, $ignore = "", $multiple = "", $onchange = "") {
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr = "<select $onchange $multiple class='selectpicker show-tick' name=$name id=$id>";
        $returnStr .= "<option value=''>" . Language::labelPleaseSelect() . "</option>";
        foreach ($surveys as $survey) {
            if ($survey->getSuid() != $ignore) {
                $selected = "";
                if ($survey->getSuid() == $current) {
                    $selected = "SELECTED";
                }
                $returnStr .= "<option " . $selected . " value=" . $survey->getSuid() . ">" . $survey->getName() . "</option>";
            }
        }
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayIfEmpty($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", IF_EMPTY_ALLOW => "", IF_EMPTY_NOTALLOW => "", IF_EMPTY_WARN => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[IF_EMPTY_ALLOW] . " value=" . IF_EMPTY_ALLOW . ">" . Language::optionsIfEmptyAllow() . "</option>";
        $returnStr .= "<option " . $selected[IF_EMPTY_NOTALLOW] . " value=" . IF_EMPTY_NOTALLOW . ">" . Language::optionsIfEmptyNotAllow() . "</option>";
        $returnStr .= "<option " . $selected[IF_EMPTY_WARN] . " value=" . IF_EMPTY_WARN . ">" . Language::optionsIfEmptyWarn() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayIfError($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", IF_ERROR_ALLOW => "", IF_ERROR_NOTALLOW => "", IF_ERROR_WARN => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[IF_ERROR_ALLOW] . " value=" . IF_ERROR_ALLOW . ">" . Language::optionsIfErrorAllow() . "</option>";
        $returnStr .= "<option " . $selected[IF_ERROR_NOTALLOW] . " value=" . IF_ERROR_NOTALLOW . ">" . Language::optionsIfErrorNotAllow() . "</option>";
        $returnStr .= "<option " . $selected[IF_ERROR_WARN] . " value=" . IF_ERROR_WARN . ">" . Language::optionsIfErrorWarn() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayInputMasks($name, $current = "") {
        $returnStr = "<select id=$name class='selectpicker show-tick' name=" . $name . ">";
        $returnStr .= "<option value=''>" . Language::optionsInputMaskNone() . "</option>";
        $selected = array();
        $inputmasks = Common::surveyInputMasks();
        foreach ($inputmasks as $k => $v) {
            $selected[$k] = "";
            if ($current == $k) {
                $selected[$k] = "selected";
            }
            $returnStr .= "<option " . $selected[$k] . " value=" . $k . ">" . $v . "</option>";
        }
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayInputMaskEnabled($current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . SETTING_INPUT_MASK_ENABLED . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", INPUT_MASK_YES => "", INPUT_MASK_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[INPUT_MASK_YES] . " value=" . INPUT_MASK_YES . ">" . Language::optionsInputMaskYes() . "</option>";
        $returnStr .= "<option " . $selected[INPUT_MASK_NO] . " value=" . INPUT_MASK_NO . ">" . Language::optionsInputMaskNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayManual($current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . SETTING_TEXTBOX_MANUAL . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", MANUAL_YES => "", MANUAL_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[MANUAL_YES] . " value=" . MANUAL_YES . ">" . Language::optionsManualYes() . "</option>";
        $returnStr .= "<option " . $selected[MANUAL_NO] . " value=" . MANUAL_NO . ">" . Language::optionsManualNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displaySpinner($current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . SETTING_SPINNER . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", SPINNER_YES => "", SPINNER_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SPINNER_YES] . " value=" . SPINNER_YES . ">" . Language::optionsSpinnerYes() . "</option>";
        $returnStr .= "<option " . $selected[SPINNER_NO] . " value=" . SPINNER_NO . ">" . Language::optionsSpinnerNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displaySpinnerType($current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . SETTING_SPINNER_TYPE . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", SPINNER_TYPE_VERTICAL => "", SPINNER_TYPE_HORIZONTAL => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SPINNER_TYPE_VERTICAL] . " value=" . SPINNER_TYPE_VERTICAL . ">" . Language::optionsSpinnerVertical() . "</option>";
        $returnStr .= "<option " . $selected[SPINNER_TYPE_HORIZONTAL] . " value=" . SPINNER_TYPE_HORIZONTAL . ">" . Language::optionsSpinnerHorizontal() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayHeaderFixed($current = "", $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=" . SETTING_HEADER_FIXED . ">";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", TABLE_YES => "", TABLE_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[TABLE_YES] . " value=" . TABLE_YES . ">" . Language::optionsHeaderFixedYes() . "</option>";
        $returnStr .= "<option " . $selected[TABLE_NO] . " value=" . TABLE_NO . ">" . Language::optionsHeaderFixedNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayAlignment($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ALIGN_LEFT => "", ALIGN_RIGHT => "", ALIGN_JUSTIFIED => "", ALIGN_CENTER => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[ALIGN_LEFT] . " value=" . ALIGN_LEFT . ">" . Language::optionsAlignmentLeft() . "</option>";
        $returnStr .= "<option " . $selected[ALIGN_RIGHT] . " value=" . ALIGN_RIGHT . ">" . Language::optionsAlignmentRight() . "</option>";
        $returnStr .= "<option " . $selected[ALIGN_JUSTIFIED] . " value=" . ALIGN_JUSTIFIED . ">" . Language::optionsAlignmentJustified() . "</option>";
        $returnStr .= "<option " . $selected[ALIGN_CENTER] . " value=" . ALIGN_CENTER . ">" . Language::optionsAlignmentCenter() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayFormatting($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select multiple class='selectpicker show-tick' name='" . $name . "[]'>";
        $current = explode("~", $current);

        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", FORMATTING_BOLD => "", FORMATTING_ITALIC => "", FORMATTING_UNDERLINED => "");
        foreach ($current as $c) {
            $selected[$c] = "selected";
        }

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[FORMATTING_BOLD] . " value=" . FORMATTING_BOLD . ">" . Language::optionsFormattingBold() . "</option>";
        $returnStr .= "<option " . $selected[FORMATTING_ITALIC] . " value=" . FORMATTING_ITALIC . ">" . Language::optionsFormattingItalic() . "</option>";
        $returnStr .= "<option " . $selected[FORMATTING_UNDERLINED] . " value=" . FORMATTING_UNDERLINED . ">" . Language::optionsFormattingUnderlined() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }
    
    function displayCollapse($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATE_COLLAPSE_YES => "", DATE_COLLAPSE_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[DATE_COLLAPSE_YES] . " value=" . DATE_COLLAPSE_YES . ">" . Language::optionsDataCollapseYes() . "</option>";
        $returnStr .= "<option " . $selected[DATE_COLLAPSE_NO] . " value=" . DATE_COLLAPSE_NO . ">" . Language::optionsDataCollapseNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }
    
    function displaySideBySide($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", DATE_SIDE_BY_SIDE_YES => "", DATE_SIDE_BY_SIDE_NO => "");
        $selected[$current] = "selected";        

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[DATE_SIDE_BY_SIDE_YES] . " value=" . DATE_SIDE_BY_SIDE_YES . ">" . Language::optionsDataSideBySideYes() . "</option>";
        $returnStr .= "<option " . $selected[DATE_SIDE_BY_SIDE_NO] . " value=" . DATE_SIDE_BY_SIDE_NO . ">" . Language::optionsDataSideBySideNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayErrorPlacement($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ERROR_PLACEMENT_AT_BOTTOM => "", ERROR_PLACEMENT_AT_TOP => "", ERROR_PLACEMENT_WITH_QUESTION => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[ERROR_PLACEMENT_WITH_QUESTION] . " value=" . ERROR_PLACEMENT_WITH_QUESTION . ">" . Language::optionsErrorPlacementWithQuestion() . "</option>";
        $returnStr .= "<option " . $selected[ERROR_PLACEMENT_AT_BOTTOM] . " value=" . ERROR_PLACEMENT_AT_BOTTOM . ">" . Language::optionsErrorPlacementAtBottom() . "</option>";
        $returnStr .= "<option " . $selected[ERROR_PLACEMENT_AT_TOP] . " value=" . ERROR_PLACEMENT_AT_TOP . ">" . Language::optionsErrorPlacementAtTop() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayButton($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", BUTTON_YES => "", BUTTON_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[BUTTON_YES] . " value=" . BUTTON_YES . ">" . Language::optionsButtonYes() . "</option>";
        $returnStr .= "<option " . $selected[BUTTON_NO] . " value=" . BUTTON_NO . ">" . Language::optionsButtonNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayButtonLabel($name, $current, $readonly = '') {
        if ($readonly != "") {
            $name .= "_ignore";
        }
        return "<input type=text $readonly class='form-control autocompletebasic' name='" . $name . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($current, ENT_QUOTES)) . "'>";
    }

    function displaySpinnerStep($name, $current, $readonly = '') {
        if ($readonly != "") {
            $name .= "_ignore";
        }
        return "<input type=text $readonly class='form-control autocompletebasic' name='" . $name . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($current, ENT_QUOTES)) . "'>";
    }

    function displaySpinnerUp($name, $current, $readonly = '') {
        if ($readonly != "") {
            $name .= "_ignore";
        }
        return "<input type=text $readonly class='form-control autocompletebasic' name='" . $name . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($current, ENT_QUOTES)) . "'>";
    }

    function displaySpinnerDown($name, $current, $readonly = '') {
        if ($readonly != "") {
            $name .= "_ignore";
        }
        return "<input type=text $readonly class='form-control autocompletebasic' name='" . $name . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($current, ENT_QUOTES)) . "'>";
    }

    function displayProgressbar($name, $current, $generic = false, $type = -1) {

        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", PROGRESSBAR_NO => "", PROGRESSBAR_PERCENT => "", PROGRESSBAR_BAR => "", PROGRESSBAR_ALL => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[PROGRESSBAR_NO] . " value=" . PROGRESSBAR_NO . ">" . Language::optionsProgressBarNo() . "</option>";
        $returnStr .= "<option " . $selected[PROGRESSBAR_PERCENT] . " value=" . PROGRESSBAR_PERCENT . ">" . Language::optionsProgressBarPercent() . "</option>";
        $returnStr .= "<option " . $selected[PROGRESSBAR_BAR] . " value=" . PROGRESSBAR_BAR . ">" . Language::optionsProgressBarBar() . "</option>";
        $returnStr .= "<option " . $selected[PROGRESSBAR_ALL] . " value=" . PROGRESSBAR_ALL . ">" . Language::optionsProgressBarAll() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayProgressbarType($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", PROGRESSBAR_NO => "", PROGRESSBAR_PERCENT => "", PROGRESSBAR_BAR => "", PROGRESSBAR_ALL => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[PROGRESSBAR_WHOLE] . " value=" . PROGRESSBAR_WHOLE . ">" . Language::optionsProgressBarWhole() . "</option>";
        $returnStr .= "<option " . $selected[PROGRESSBAR_SECTION] . " value=" . PROGRESSBAR_SECTION . ">" . Language::optionsProgressBarSection() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayAccessTypes($current) {
        $returnStr = "<select class='selectpicker show-tick' name=" . SETTING_ACCESS_TYPE . ">";
        $selected = array(LOGIN_ANONYMOUS => "", LOGIN_DIRECT => "", LOGIN_LOGINCODE);
        $selected[$current] = "selected";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[LOGIN_ANONYMOUS] . " value=" . LOGIN_ANONYMOUS . ">" . Language::optionsAccessTypeAnonymous() . "</option>";
        $returnStr .= "<option " . $selected[LOGIN_DIRECT] . " value=" . LOGIN_DIRECT . ">" . Language::optionsAccessTypeDirect() . "</option>";
        $returnStr .= "<option " . $selected[LOGIN_LOGINCODE] . " value=" . LOGIN_LOGINCODE . ">" . Language::optionsAccessTypeLogincode() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayAccessReturn($current) {
        $returnStr = "<select class='selectpicker show-tick' name='" . SETTING_ACCESS_RETURN . "'>";
        $selected = array(ACCESS_RETURN_YES => "", ACCESS_RETURN_NO => "");
        $selected[$current] = "selected";
        $returnStr .= "<option " . $selected[ACCESS_RETURN_YES] . " value=" . ACCESS_RETURN_YES . ">" . Language::optionsAccessReturnYes() . "</option>";
        $returnStr .= "<option " . $selected[ACCESS_RETURN_NO] . " value=" . ACCESS_RETURN_NO . ">" . Language::optionsAccessReturnNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayValidateAssignment($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", VALIDATE_ASSIGNMENT_YES => "", VALIDATE_ASSIGNMENT_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[VALIDATE_ASSIGNMENT_YES] . " value=" . VALIDATE_ASSIGNMENT_YES . ">" . Language::optionsValidateYes() . "</option>";
        $returnStr .= "<option " . $selected[VALIDATE_ASSIGNMENT_NO] . " value=" . VALIDATE_ASSIGNMENT_NO . ">" . Language::optionsValidateNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayMultiColumnQuestionText($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", MULTI_QUESTION_YES => "", MULTI_QUESTION_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[MULTI_QUESTION_YES] . " value=" . MULTI_QUESTION_YES . ">" . Language::multiColumnQuestiontextYes() . "</option>";
        $returnStr .= "<option " . $selected[MULTI_QUESTION_NO] . " value=" . MULTI_QUESTION_NO . ">" . Language::multiColumnQuestiontextNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }
    
    function displayFooterDisplay($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", ENUM_FOOTER_YES => "", ENUM_FOOTER_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[ENUM_FOOTER_YES] . " value=" . ENUM_FOOTER_YES . ">" . Language::optionsFooterDisplayYes() . "</option>";
        $returnStr .= "<option " . $selected[ENUM_FOOTER_NO] . " value=" . ENUM_FOOTER_NO . ">" . Language::optionsFooterDisplayNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayApplyChecks($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", APPLY_CHECKS_YES => "", APPLY_CHECKS_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[APPLY_CHECKS_YES] . " value=" . APPLY_CHECKS_YES . ">" . Language::optionsApplyChecksYes() . "</option>";
        $returnStr .= "<option " . $selected[APPLY_CHECKS_NO] . " value=" . APPLY_CHECKS_NO . ">" . Language::optionsApplyChecksNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayExclusive($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", GROUP_YES => "", GROUP_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[GROUP_YES] . " value=" . GROUP_YES . ">" . Language::optionsGroupYes() . "</option>";
        $returnStr .= "<option " . $selected[GROUP_NO] . " value=" . GROUP_NO . ">" . Language::optionsGroupNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayInclusive($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", GROUP_YES => "", GROUP_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }        
        //$returnStr .= "<option></option>";
        $returnStr .= "<option " . $selected[GROUP_YES] . " value=" . GROUP_YES . ">" . Language::optionsGroupYes() . "</option>";
        $returnStr .= "<option " . $selected[GROUP_NO] . " value=" . GROUP_NO . ">" . Language::optionsGroupNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayStriped($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", GROUP_YES => "", GROUP_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[GROUP_YES] . " value=" . GROUP_YES . ">" . Language::optionsGroupYes() . "</option>";
        $returnStr .= "<option " . $selected[GROUP_NO] . " value=" . GROUP_NO . ">" . Language::optionsGroupNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayMobileLabels($name, $current, $generic = false) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", MOBILE_LABEL_YES => "", MOBILE_LABEL_NO => "");
        $selected[$current] = "selected";
        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        $returnStr .= "<option " . $selected[MOBILE_LABEL_YES] . " value=" . MOBILE_LABEL_YES . ">" . Language::optionsGroupYes() . "</option>";
        $returnStr .= "<option " . $selected[MOBILE_LABEL_NO] . " value=" . MOBILE_LABEL_NO . ">" . Language::optionsGroupNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayOrientation($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ORIENTATION_HORIZONTAL => "", ORIENTATION_VERTICAL => "");
        $selected[$current] = "selected";

        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        }
        $returnStr .= "<option " . $selected[ORIENTATION_HORIZONTAL] . " value=" . ORIENTATION_HORIZONTAL . ">" . Language::optionsSliderOrientationHorizontal() . "</option>";
        $returnStr .= "<option " . $selected[ORIENTATION_VERTICAL] . " value=" . ORIENTATION_VERTICAL . ">" . Language::optionsSliderOrientationVertical() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayRotation($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", KNOB_ROTATION_CLOCKWISE => "", KNOB_ROTATION_ANTICLOCKWISE => "");
        $selected[$current] = "selected";

        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        }
        $returnStr .= "<option " . $selected[KNOB_ROTATION_CLOCKWISE] . " value=" . KNOB_ROTATION_CLOCKWISE . ">" . Language::optionsKnobRotationClockwise() . "</option>";
        $returnStr .= "<option " . $selected[KNOB_ROTATION_ANTICLOCKWISE] . " value=" . KNOB_ROTATION_ANTICLOCKWISE . ">" . Language::optionsKnobRotationAntiClockwise() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayEnumeratedTemplate($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select id=$name class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ORIENTATION_HORIZONTAL => "", ORIENTATION_VERTICAL => "", ORIENTATION_CUSTOM => "");
        $selected[$current] = "selected";

        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        }
        $returnStr .= "<option " . $selected[ORIENTATION_HORIZONTAL] . " value=" . ORIENTATION_HORIZONTAL . ">" . Language::optionsOrientationHorizontal() . "</option>";
        $returnStr .= "<option " . $selected[ORIENTATION_VERTICAL] . " value=" . ORIENTATION_VERTICAL . ">" . Language::optionsOrientationVertical() . "</option>";

        if ($generic || $type > 0) {
            $returnStr .= "<option " . $selected[ORIENTATION_CUSTOM] . " value=" . ORIENTATION_CUSTOM . ">" . Language::optionsOrientationCustom() . "</option>";
        }
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayEnumeratedOrder($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ORDER_LABEL_FIRST => "", ORDER_OPTION_FIRST => "");
        $selected[$current] = "selected";

        if ($generic) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
        }
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        }
        $returnStr .= "<option " . $selected[ORDER_OPTION_FIRST] . " value=" . ORDER_OPTION_FIRST . ">" . Language::optionsEnumeratedOrderOptionFirst() . "</option>";
        $returnStr .= "<option " . $selected[ORDER_LABEL_FIRST] . " value=" . ORDER_LABEL_FIRST . ">" . Language::optionsEnumeratedOrderLabelFirst() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayRankColumn($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", RANK_COLUMN_ONE => "", RANK_COLUMN_TWO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[RANK_COLUMN_ONE] . " value=" . RANK_COLUMN_ONE . ">" . Language::optionsRankColumnOne() . "</option>";
        $returnStr .= "<option " . $selected[RANK_COLUMN_TWO] . " value=" . RANK_COLUMN_TWO . ">" . Language::optionsRankColumnTwo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayEnumeratedLabel($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='$name'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ENUMERATED_LABEL_LABEL_ONLY => "", ENUMERATED_LABEL_LABEL_CODE => "", ENUMERATED_LABEL_LABEL_CODE_VALUELABEL => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }

        $returnStr .= "<option " . $selected[ENUMERATED_LABEL_INPUT_ONLY] . " value=" . ENUMERATED_LABEL_INPUT_ONLY . ">" . Language::optionsEnumeratedInputOnly() . "</option>";
        $returnStr .= "<option " . $selected[ENUMERATED_LABEL_LABEL_ONLY] . " value=" . ENUMERATED_LABEL_LABEL_ONLY . ">" . Language::optionsEnumeratedLabelOnly() . "</option>";
        $returnStr .= "<option " . $selected[ENUMERATED_LABEL_LABEL_CODE] . " value=" . ENUMERATED_LABEL_LABEL_CODE . ">" . Language::optionsEnumeratedLabelCode() . "</option>";
        $returnStr .= "<option " . $selected[ENUMERATED_LABEL_LABEL_CODE_VALUELABEL] . " value=" . ENUMERATED_LABEL_LABEL_CODE_VALUELABEL . ">" . Language::optionsEnumeratedLabelAll() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayEnumeratedLabelRank($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='$name'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ENUMERATED_LABEL_LABEL_ONLY => "", ENUMERATED_LABEL_LABEL_CODE => "", ENUMERATED_LABEL_LABEL_CODE_VALUELABEL => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }

        //$returnStr .= "<option " . $selected[ENUMERATED_LABEL_INPUT_ONLY] . " value=" . ENUMERATED_LABEL_INPUT_ONLY . ">" . Language::optionsEnumeratedInputOnly() . "</option>";
        $returnStr .= "<option " . $selected[ENUMERATED_LABEL_LABEL_ONLY] . " value=" . ENUMERATED_LABEL_LABEL_ONLY . ">" . Language::optionsEnumeratedLabelOnly() . "</option>";
        $returnStr .= "<option " . $selected[ENUMERATED_LABEL_LABEL_CODE] . " value=" . ENUMERATED_LABEL_LABEL_CODE . ">" . Language::optionsEnumeratedLabelCode() . "</option>";
        $returnStr .= "<option " . $selected[ENUMERATED_LABEL_LABEL_CODE_VALUELABEL] . " value=" . ENUMERATED_LABEL_LABEL_CODE_VALUELABEL . ">" . Language::optionsEnumeratedLabelAll() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayEnumeratedTextBox($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='$name'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", TEXTBOX_YES => "", TEXTBOX_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[TEXTBOX_YES] . " value=" . TEXTBOX_YES . ">" . Language::optionsEnumeratedTextboxYes() . "</option>";
        $returnStr .= "<option " . $selected[TEXTBOX_NO] . " value=" . TEXTBOX_NO . ">" . Language::optionsEnumeratedTextboxNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }
    
    function displaySetOfEnumeratedRanking($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='$name'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", SETOFENUMERATED_RANKING_YES => "", SETOFENUMERATED_RANKING_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SETOFENUMERATED_RANKING_YES] . " value=" . SETOFENUMERATED_RANKING_YES . ">" . Language::optionsSetOfEnumeratedRankingYes() . "</option>";
        $returnStr .= "<option " . $selected[SETOFENUMERATED_RANKING_NO] . " value=" . SETOFENUMERATED_RANKING_NO . ">" . Language::optionsSetOfEnumeratedRankingNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayClickLabel($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='$name'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", CLICK_LABEL_YES => "", CLICK_LABEL_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[CLICK_LABEL_YES] . " value=" . CLICK_LABEL_YES . ">" . Language::optionsClickLabelYes() . "</option>";
        $returnStr .= "<option " . $selected[CLICK_LABEL_NO] . " value=" . CLICK_LABEL_NO . ">" . Language::optionsClickLabelNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayTextBox($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", TEXTBOX_YES => "", TEXTBOX_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[TEXTBOX_YES] . " value=" . TEXTBOX_YES . ">" . Language::optionsSliderTextboxYes() . "</option>";
        $returnStr .= "<option " . $selected[TEXTBOX_NO] . " value=" . TEXTBOX_NO . ">" . Language::optionsSliderTextboxNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayTooltip($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", TOOLTIP_YES => "", TOOLTIP_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[TOOLTIP_ALWAYS] . " value=" . TOOLTIP_ALWAYS . ">" . Language::optionsTooltipAlways() . "</option>";
        $returnStr .= "<option " . $selected[TOOLTIP_YES] . " value=" . TOOLTIP_YES . ">" . Language::optionsTooltipYes() . "</option>";
        $returnStr .= "<option " . $selected[TOOLTIP_NO] . " value=" . TOOLTIP_NO . ">" . Language::optionsTooltipNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }
    
    function displaySliderMarker($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", SLIDER_PRESELECTION_YES => "", SLIDER_PRESELECTION_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SLIDER_PRESELECTION_YES] . " value=" . SLIDER_PRESELECTION_YES . ">" . Language::optionsMarkerYes() . "</option>";
        $returnStr .= "<option " . $selected[SLIDER_PRESELECTION_NO] . " value=" . SLIDER_PRESELECTION_NO . ">" . Language::optionsMarkerNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displaySliderPlacement($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", TEXTBOX_YES => "", TEXTBOX_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SLIDER_LABEL_PLACEMENT_TOP] . " value=" . SLIDER_LABEL_PLACEMENT_TOP . ">" . Language::optionsSliderLabelsTop() . "</option>";
        $returnStr .= "<option " . $selected[SLIDER_LABEL_PLACEMENT_BOTTOM] . " value=" . SLIDER_LABEL_PLACEMENT_BOTTOM . ">" . Language::optionsSliderLabelsBottom() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayEnumeratedSplit($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", ENUMERATED_YES => "", ENUMERATED_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[ENUMERATED_YES] . " value=" . ENUMERATED_YES . ">" . Language::optionsEnumeratedYes() . "</option>";
        $returnStr .= "<option " . $selected[ENUMERATED_NO] . " value=" . ENUMERATED_NO . ">" . Language::optionsEnumeratedNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayTextSettingValue($text) {
        if ($text == SETTING_FOLLOW_GENERIC) {
            return "";
        }
        if ($text == SETTING_FOLLOW_TYPE) {
            return "";
        }
        return $text;
    }

    function displaySectionHeader($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", SECTIONHEADER_YES => "", SECTIONHEADER_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SECTIONHEADER_YES] . " value=" . SECTIONHEADER_YES . ">" . Language::optionsSectionHeaderYes() . "</option>";
        $returnStr .= "<option " . $selected[SECTIONHEADER_NO] . " value=" . SECTIONHEADER_NO . ">" . Language::optionsSectionHeaderNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displaySectionFooter($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name=$name>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", SECTIONFOOTER_YES => "", SECTIONFOOTER_NO => "");
        $selected[$current] = "selected";

        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[SECTIONFOOTER_YES] . " value=" . SECTIONFOOTER_YES . ">" . Language::optionsSectionFooterYes() . "</option>";
        $returnStr .= "<option " . $selected[SECTIONFOOTER_NO] . " value=" . SECTIONFOOTER_NO . ">" . Language::optionsSectionFooterNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    /* RADIO BUTTON/CHECK BOX SCRIPTS */

    function displayRadioSwitch($css = true) {
        $returnStr = "";
        if (!isRegisteredScript("js/switch/bootstrap-switch-min.js")) {
            registerScript('js/switch/bootstrap-switch-min.js');
            $returnStr .= getScript("js/switch/bootstrap-switch-min.js");
        }
        if ($css && !isRegisteredScript("js/switch/bootstrap-switch.min.css")) {
            registerScript('js/switch/bootstrap-switch.min.css');
            $returnStr .= getCSS("js/switch/bootstrap-switch.min.css");
        }
        $returnStr .= minifyScript("<script type='text/javascript'>
                            $( document ).ready(function() {
                                $('input.bootstrapswitch').bootstrapSwitch();
                            });
                          </script>");
        return $returnStr;
    }

    function displayRadioSwitchCSS() {
        $returnStr = "";
        if (!isRegisteredScript("js/switch/bootstrap-switch.css")) {
            registerScript('js/switch/bootstrap-switch.css');
            $returnStr .= getCSS("js/switch/bootstrap-switch.css");
        }
        return $returnStr;
    }

    function displayRadioButtonScript($var, $target, $tablecell = false) {

        $returnStr = '<script type="text/javascript">$( document ).ready(function() {';

        // not allowing deselect
        if (Config::allowRadioButtonUnselect() == false) {
            if ($tablecell) {
                $returnStr .= '$("#cell' . $target . '").mousedown(function (e){   
                    
                                                        if ($("#' . $target . '").prop("disabled") == true) {
                                                            return;
                                                        }
                                                        $("#' . $target . '").prop("checked", true);
                                                        $("#' . $target . '").change();
                                                        return false;
                                                    });';
            }
        }
        // allowing deselect
        else {
            $returnStr .= 'var radioChecked' . $target . ';
                                                    
                                                    $("#' . $target . '").mousedown(function (e) {                                                        
                                                            if ($(this).prop("checked") == true) {
                                                                radioChecked' . $target . ' = true;                                                              
                                                            }  
                                                            else {
                                                                radioChecked' . $target . ' = false;
                                                            }
                                                            return true;
                                                     });
                                                        
                                                    $("#' . $target . '").click(function (e) {                                                         
                                                            if (radioChecked' . $target . ') {                                                                
                                                                $(this).prop("checked", false);
                                                             } else {
                                                                $(this).prop("checked", true);
                                                             }                                                             
                                                     });
                                                     
                                                   $("label[for=\'' . $target . '\']").mousedown(function (e){
                                                       
                                                        if ($("#' . $target . '").prop("disabled") == true) {
                                                                return;
                                                        }
                                                            
                                                        if ($(e.target).hasClass("uscic-radiobutton")) {                                                            
                                                                
                                                        }
                                                        else {
                                                            return true; // inline field OR individual dk/rf/na, so prevent click
                                                        }
                                                        
                                                        
                                                        radioChecked' . $target . ' = $(\'#' . $target . '\').prop(\'checked\');
                                                        if (radioChecked' . $target . ') {                                                                
                                                                $("#' . $target . '").prop("checked", false);
                                                             } else {
                                                                $("#' . $target . '").prop("checked", true);
                                                             }
                                                             $("#' . $target . '").change();
                                                             return false; // prevent bubbling to table cell level
                                                        });

                                                    ';

            // if in table
            if ($tablecell) {
                $returnStr .= '$("#cell' . $target . '").mousedown(function (e){ 
                    
                                                            if ($("#' . $target . '").prop("disabled") == true) {
                                                                return;
                                                            }
                                                            
                                                            radioChecked' . $target . ' = $(\'#' . $target . '\').prop(\'checked\');                                               
                                                                if (radioChecked' . $target . ') {                                                                
                                                                    $("#' . $target . '").prop("checked", false);
                                                                 } else {
                                                                    $("#' . $target . '").prop("checked", true);
                                                                 }
                                                                 $("#' . $target . '").change();
                                                                     return true;
                                                            });';

                if ($var->isEnumeratedClickLabel()) {
                    $returnStr .= '$("#cellheader' . $target . '").mousedown(function (e){  
                        
                                                            if ($("#' . $target . '").prop("disabled") == true) {
                                                                return;
                                                            }
                                                            
                                                            radioChecked' . $target . ' = $(\'#' . $target . '\').prop(\'checked\');                                               
                                                                if (radioChecked' . $target . ') {                                                                
                                                                    $("#' . $target . '").prop("checked", false);
                                                                 } else {
                                                                    $("#' . $target . '").prop("checked", true);
                                                                 }
                                                                 $("#' . $target . '").change();
                                                                     return false;
                                                            });';
                }
            }
        }
        $returnStr .= '});</script>';

        return minifyScript($returnStr);
    }

    function displayCheckBoxUnchecking($mainid, $invalidsub) {
        if ($invalidsub == "") {
            return "";
        }
        $returnStr = "";
        $uncheck = array();

        // determine incompatible sets
        $sets = explode(SEPARATOR_COMPARISON, $invalidsub);
        foreach ($sets as $set) {
            $setarray = explode(",", $set);

            // skip if for example only '1': no counterpart specified
            if (sizeof($setarray) != 2) {
                continue;
            } else {
                $first = $setarray[0];
                $second = $setarray[1];
                $uncheck[$first] = $second;
                $uncheck[$second] = $first;
            }
        }

        // process
        $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){                        
                        ";
        foreach ($uncheck as $k => $v) {
            $karray = explode("-", $k); // in case of range
            if (sizeof($karray) == 1) {
                $karray[1] = $karray[0];
            }
            $varray = explode("-", $v); // in case of range
            if (sizeof($varray) == 1) {
                $varray[1] = $varray[0];
            }

            /*
             * function checkBoxes(obj) {if (obj.value != 5) { var checked = false; $('input[type=checkbox]').each(function () { if (this.value != 5 && this.checked) { checked = true; } });  if (checked == true) {$('input[type=checkbox][value="5"]').prop('checked', false); $('input[type=checkbox][value="5"]').change(); }  }  else { $('input[type=checkbox]').each(function () { if (this.value != 5) {this.checked = false; $(this).change();}});  }} 
             * 
             * 
             */
            $uncheckcode = "";
            for ($i = trim($varray[0]); $i <= trim($varray[1]); $i++) {
                $uncheckcode .= "$('#" . $mainid . "_" . $i . "').prop('checked', false);\r\n";
                $uncheckcode .= "$('#" . $mainid . "_" . $i . "').change();\r\n";
            }


            for ($i = trim($karray[0]); $i <= trim($karray[1]); $i++) {
                $returnStr .= "$('#" . $mainid . "_" . $i . "').change( function(e) {\r\n
                                    if ($(this).prop('checked') == true ";

                // handle range
                for ($j = trim($karray[0]); $j <= trim($karray[1]); $j++) {
                    if ($i != $j) {
                        $returnStr .= " && $('#" . $mainid . "_" . $j . "').prop('checked') == true";
                    }
                }
                $returnStr .= ") {\r\n " . $uncheckcode . " }
                                });\r\n";
            }

            // reverse
            $uncheckcode = "";
            for ($i = trim($karray[0]); $i <= trim($karray[1]); $i++) {
                $uncheckcode .= "$('#" . $mainid . "_" . $i . "').prop('checked', false);\r\n";
                $uncheckcode .= "$('#" . $mainid . "_" . $i . "').change();\r\n";
            }


            for ($i = trim($varray[0]); $i <= trim($varray[1]); $i++) {
                $returnStr .= "$('#" . $mainid . "_" . $i . "').change( function(e) {\r\n
                                    if ($(this).prop('checked') == true ";

                // handle range
                for ($j = trim($varray[0]); $j <= trim($varray[1]); $j++) {
                    if ($i != $j) {
                        $returnStr .= " && $('#" . $mainid . "_" . $j . "').prop('checked') == true";
                    }
                }

                $returnStr .= ") {\r\n " . $uncheckcode . " }
                                });\r\n";
            }
        }
        $returnStr .= "});
            </script>";
        return minifyScript($returnStr);
    }
    
    function displayMultiDropdownUnchecking($mainid, $answer, $invalidsub) {
        if ($invalidsub == "") {
            return "";
        }
        $returnStr = "";
        $uncheck = array();

        // determine incompatible sets
        $sets = explode(SEPARATOR_COMPARISON, $invalidsub);
        foreach ($sets as $set) {
            $setarray = explode(",", $set);

            // skip if for example only '1': no counterpart specified
            if (sizeof($setarray) != 2) {
                continue;
            } else {
                $first = $setarray[0];
                $second = $setarray[1];
                $uncheck[$first] = $second;
                $uncheck[$second] = $first;
            }
        }

        // process
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= "var inconsistent = [];\r\n";
        $returnStr .= "var previousvalues = '" . $answer . "';\r\n";
        
        // add inconsistent combinations
        foreach ($uncheck as $k => $v) {
            $karray = explode("-", $k); // in case of range
            if (sizeof($karray) == 1) {
                $karray[1] = $karray[0];
            }
            $varray = explode("-", $v); // in case of range
            if (sizeof($varray) == 1) {
                $varray[1] = $varray[0];
            }
            
            $uncheckcode = $v;
            $conditions = $k;
            $returnStr .= "inconsistent['" . $k . "'] = '" . $v . "';\r\n";
        }
        
        $returnStr .= "$('#" . $mainid . "').change( function(e) {\r\n;
                            var values = $(this).val();\r\n
                                    
                            // no previous values selected, so nothing to uncheck\r\n
                            if (previousvalues == '') {\r\n
                                previousvalues = '' + values.join('" . SEPARATOR_SETOFENUMERATED . "');\r\n
                                return;\r\n
                            }\r\n
                                    
                            // no current values selected, so nothing to uncheck\r\n
                            if (!values || values == '' || values.length == 0) {\r\n
                                return;\r\n
                            }\r\n
                            
                            // clear all currently selected values\r\n
                            $('#" . $mainid . " option').removeAttr('selected');\r\n
                            $('#" . $mainid . " option').prop('aria-checked', false);\r\n
                            
                            // create array of previous values
                            previousvalues = previousvalues.split('" . SEPARATOR_SETOFENUMERATED . "');\r\n
                                
                            // less selected now than before, so uncheck\r\n
                            //if (values.length < previousvalues.length) {\r\n
                            //    previousvalues = '' + values.join('" . SEPARATOR_SETOFENUMERATED . "');\r\n
                            //    return;\r\n
                            //}\r\n
                            
                            // find out which value was added\r\n
                            var newvalue = '';\r\n
                            for (cnt = 0; cnt < values.length; cnt++) {\r\n
                            
                                var val = '' + values[cnt];\r\n
                                if (previousvalues.indexOf(val) == -1) {\r\n
                                    newvalue = val;\r\n
                                    break;\r\n
                                }\r\n
                                
                            }\r\n        
                            
                            // go through inconsistencies\r\n
                            var toremove = [];\r\n
                            for(let i in inconsistent) {\r\n
                            
                                var list = i.split('-');\r\n
                                var unselect = inconsistent[i];\r\n
                                var remove = 1;\r\n
                                var withnew = 2;\r\n
                                for (cnt1 = 0; cnt1 < list.length; cnt1++) {\r\n 
                                
                                    var checkval = list[cnt1];\r\n
                                    if (checkval == newvalue) {\r\n
                                        withnew = 1; // listing that contains the newly selected value\r\n
                                    }\r\n
                                    if (values.indexOf(checkval) == -1) {\r\n
                                        remove = 2; // do not need to remove\r\n
                                        break;\r\n
                                    }\r\n
                                }    \r\n                             
                                
                                // we need toremove\r\n
                                if (withnew == 1 & remove == 1) {\r\n
                                    var t = unselect.split('-');\r\n
                                    for (cnt2 = 0; cnt2 < t.length; cnt2++) {\r\n
                                        toremove.push(t[cnt2]);  \r\n                                      
                                    }\r\n
                                }\r\n
                            }\r\n
                            
                            // create new list of values\r\n
                            var toselect = [];\r\n
                            for (cnt3 = 0; cnt3 < values.length; cnt3++) {\r\n
                                var vl = values[cnt3];\r\n
                                if (toremove.indexOf(vl) == -1) {\r\n
                                    toselect.push(vl);\r\n
                                    $('#" . $mainid . "_' + vl).prop('selected', true);\r\n
                                    $('#" . $mainid . "_' + vl).prop('aria-checked', true);\r\n
                                }\r\n
                            }\r\n
                            
                            // update previous values\r\n
                            previousvalues = '' + toselect.join('" . SEPARATOR_SETOFENUMERATED . "');\r\n
                                
                            // update display\r\n
                            $('#" . $mainid . "').selectpicker('render');\r\n
                            ";
        
        $returnStr .= "});
            </script>";
        return minifyScript($returnStr);
    }

    /* INLINE SCRIPT FUNCTIONS */

    function displayAutoFocusScript($id) {
        return '<script type=text/javascript>' . minifyScript('$( document ).ready(function() {$(\'#' . $id . '\').click(function(event) { $(\'#' . $id . '\').trigger("dblclick"); event.preventDefault(); return false;});});') . '</script>';
    }

    function displayAutoSelectScript($id, $variable, $targetid, $inputtype, $value, $inlineanswertype) {
        if (!inArray($inputtype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            return "";
        }
        $type = "radio";
        if ($inputtype == ANSWER_TYPE_SETOFENUMERATED) {
            $type = "checkbox";
        }
        $returnStr = "";

        if (inArray($inlineanswertype, array(ANSWER_TYPE_OPEN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_DOUBLE))) {
            $returnStr .= "$('#" . $id . "').keyup(function(){if (this.value != '')";
        } else if (inArray($inlineanswertype, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME))) {
            $returnStr .= "$('#" . $id . "').on(\"dp.change\", function(e) {if (this.value != '')";
        } else if (inArray($inlineanswertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            // TODO: $returnStr .= "$('#" . $id . "').change(function(){if (this.value != '')";
        } else if (inArray($inlineanswertype, array(ANSWER_TYPE_SLIDER))) {
            $returnStr .= "$('#" . $id . "').change(function(){if (this.value != '')";
        } else if (inArray($inlineanswertype, array(ANSWER_TYPE_KNOB))) {
            // TODO KNOB
            $returnStr .= "$('#" . $id . "').change(function(){if (this.value != '')";
        } else {
            $returnStr .= "$('#" . $id . "').change(function(){if (this.value != '')";
        }
        $returnStr .= '{$(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"][value="' . $value . '"]\').prop("checked", true);} else { $(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"][value="' . $value . '"]\').prop("checked", false);} $(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"]\').change(); });';

        $returnStr .= "$('input[name=\"" . $variable . "_dkrfna\"]').on('switchChange.bootstrapSwitch', function(event, state) {";
        $returnStr .= 'if ($("input[name=\'' . $variable . '_dkrfna\']:checked").val()) {                        
                        $(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"][value="' . $value . '"]\').prop("checked", true);                        
                            $(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"]\').change();
                    }
                    else {
                    
                        // currently selected, then deselect
                       if ($(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"][value="' . $value . '"]\').prop("checked") == true) {
                            $(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"][value="' . $value . '"]\').prop("checked", false);
                            $(\'input[type="' . $type . '"][id="' . $targetid . '_' . $value . '"]\').change();
                            // no change event --> if dk/rf/na was selected, then 
                       }
                    }                    
                    
                    });';

        return "<script type='text/javascript'>" . minifyScript($returnStr) . "</script>";
    }

    /* PARADATA HANDLING */

    function displayParadataScripts($paradata) {

        //only for surveys
        if ($_SESSION['SYSTEM_ENTRY'] != USCIC_SURVEY) {
            return;
        }

        // check config
        if ($paradata == false) {
            return;
        }

        // http://greensock.com/forums/topic/9059-cross-browser-to-detect-tab-or-window-is-active-so-animations-stay-in-sync-using-html5-visibility-api/
        if (!isRegisteredScript("js/TabWindowVisibilityManager.min.js")) {
            registerScript('js/TabWindowVisibilityManager.min.js');
            $returnStr = getScript('js/TabWindowVisibilityManager.min.js');
        }
        if (!isRegisteredScript("js/datetimepicker/moment-min.js")) {
            registerScript('js/datetimepicker/moment-min.js');
            $returnStr .= getScript("js/datetimepicker/moment-min.js");
        }
        //if (!isRegisteredScript("js/zip/lzstring.js")) {
        //    registerScript('js/zip/lzstring.js');
        //    $returnStr .= '<script type=text/javascript src="js/zip/lzstring.js"></script>';
        //}        
        $params = array(POST_PARAM_DEFAULT_LANGUAGE => getDefaultSurveyLanguage(), POST_PARAM_DEFAULT_MODE => getDefaultSurveyMode(), POST_PARAM_RGID => $this->engine->getRgid(), POST_PARAM_LANGUAGE => getSurveyLanguage(), POST_PARAM_MODE => getSurveyMode(), SESSION_PARAM_TEMPLATE => getSurveyTemplate(), POST_PARAM_VERSION => getSurveyVersion(), POST_PARAM_STATEID => $this->engine->getStateId(), POST_PARAM_DISPLAYED => urlencode(serialize($this->engine->getDisplayNumbers())), POST_PARAM_PRIMKEY => $this->engine->getPrimaryKey(), POST_PARAM_SUID => $this->engine->getSuid());
        $r = setSessionsParamString($params);
        $returnStr .= '<script type="text/javascript">';
        $str = '
            // bind listeners
            $(document).ready(function(){  ';
        if (Config::logParadataMouseMovement()) {

            $str .= '$("html").mousemove(function(event) {
                    window.mousex = event.pageX;
                    window.mousey = event.pageY;
                });';
        }
        
        $str .= '
                $("html").click(function(event){
                    var name = "";
                    if (event.target.name) {
                        name = event.target.name;
                    }
                    
                    logParadata("MC:"+event.pageX+":"+event.pageY+":"+event.which+":"+name);
                });

                $("html").keyup(function(event){                        
                    var name = "";
                    if (event.target.name) {
                        name = event.target.name;
                    }
                        
                    // target
                    if (name !== "") {
                        
                        // get node name
                        var element = event.target.nodeName.toLowerCase();
                            
                        // ignore textbox and textarea, handled below
                        if ((element === "input" && $(event.target).is("input:text")) || element === "textarea") {
                            // do nothing
                        }
                        else {                            
                            // log keystroke
                            logParadata("KE:"+event.key+":"+name);
                        }
                    }    
                    else {
                        logParadata("KE:"+event.key+":"+name);
                    }        
                });
                
                // function to get difference
		difference = function(value1, value2) {
                    var output = [];
                    for(i = 0; i < value2.length; i++) {
                        if(value1[i] !== value2[i]) {
                            output.push(value2[i]);
                        }
                    }
                    return output.join("");
                }

                var beforevalue = "";
                
                // filter here to only register keystrokes for actual text input elements  
                $("input[type=text], textarea").on("beforeinput", function(event){                    
                    beforevalue = event.target.value;
                });
                
                // log cut for actual text input elements
                $("input[type=text], textarea").on("cut", function(event){ 
                    var name = "";
                    if (event.target.name) {
                        name = event.target.name;
                    }
                    logParadata("CT:"+name);
                });
                
                // log paste for actual text input elements
                $("input[type=text], textarea").on("paste", function(event){ 
                    var name = "";
                    if (event.target.name) {
                        name = event.target.name;
                    }
                    logParadata("PS:"+name);
                });

                $("input[type=text], textarea").on("input", function(event){                        
                    var name = "";
                    if (event.target.name) {
                        name = event.target.name;
                    }

                    var newvalue = event.target.value;
                    var diff = difference(beforevalue, newvalue);
                    
                    // new value is shorter than old value, assume backspace
                    if (newvalue.length < beforevalue.length) {
                        var keycode = "backspace"; // backspace
                        var removed = beforevalue.length - newvalue.length;
                        
                        // log backspace events equal to number of deletions
                        // TODO: log what was deleted?
                        for (cnt = 0; cnt < removed; cnt++) {
                            logParadata("KE:"+keycode+":"+name);
                        }
                    }
                    else if (diff != "") {
                        
                        // one character added
                        if (diff.length == 1) {
                            var keycode = diff.charAt(0);
                            logParadata("KE:"+diff+":"+name);
                        }    
                        // multiple characters added (paste)
                        else if (diff.length > 1) {
                            for (cnt = 0; cnt < diff.length; cnt++) {
                                var keycode = diff.charAt(cnt);
                                logParadata("KE:"+keycode+":"+name);
                            }        
                        }
                    }                    

                });

            });';

        if (Config::logParadataMouseMovement()) {

            $str .= '
                window.mousex = 0;
                window.mousey = 0;
                window.lastx = window.mousex;
                window.lasty = window.mousey;
                function mousemov() {
                    if (window.lastx != window.mousex || window.lasty != window.mousey) {
                        logParadata("MM:"+window.mousex+":"+window.mousey);
                        window.lastx = window.mousex;
                        window.lasty = window.mousey;
                    }

                }
                window.onload=setInterval(mousemov, ' . Config::logParadataMouseMovementInterval() . '); // capture mouse movement every 5 seconds
                ';
        }

        $str .= '
            // compress function
            function compress(string) {
                return string;
                //return LZString.compressToUTF16(string);
            }
            
            // function to log paradata
            function logParadata(para) {
                //alert(para);
                $("#pid").val($("#pid").val() + "||" + compress(para + "=" + moment()));
                //alert($("#pid").val().length);
                // if length exceeds limit
                //if ($("#pid").val().length > 1024) {
                    //alert($("#pid").val().length);
                    //sendParadata($("#pid").val()); // send to server
                    //$("#pid").val(""); // reset
                //}
            }

            // function to send paradata to the server
            function sendParadata(paradata) {
                $.ajax({
                    type: "POST",
                    url: "ajax/index.php",
                    data: {k: "' . encryptC(Config::ajaxAccessKey(), Config::smsComponentKey()) . '",ajaxr: "' . $r . '", p: "storeparadata", ' . POST_PARAM_PARADATA . ': paradata},
                    async: true
                });
            }
                 
            var firedin = false;                
            var firedout = false; 
            $(window).TabWindowVisibilityManager({
                onFocusCallback: function(){
                        if (firedin == false) {
                            //document.title="visible";
                            logParadata("FI:");	                
                        }
                        firedin = true;
                        firedout = false;
                },
                onBlurCallback: function(){
                    if (firedout == false) {
                        //document.title="invisible";
                        logParadata("FO:");
                    }
                    firedout = true;    
                    firedin = false;
                }
            });';
        $returnStr .= minifyScript($str);
        $returnStr .= '</script>';
        return $returnStr;
    }

    /* COOKIE HANDLING */

    function displayCookieScripts() {
        if (!isRegisteredScript("js/cookie/jquery.cookie.js")) {
            registerScript('js/cookie/jquery.cookie.js');
            $returnStr = getScript("js/cookie/jquery.cookie.js");
        }
        if (!isRegisteredScript("js/cookie/uscic.cookie.js")) {
            registerScript('js/cookie/uscic.cookie.js');
            $returnStr .= getScript("js/cookie/uscic.cookie.js");
        }
        return $returnStr;
    }

    /*  TABLE MOBILE HANDLING */

    function displayTableSaw() {
        $returnStr = "";
        if (!isRegisteredScript("js/tablesaw/stackonly/tablesaw.stackonly.nubis.min.js")) {
            $returnStr .= getScript('js/tablesaw/stackonly/tablesaw.stackonly.nubis.min.js');
        }
        if (!isRegisteredScript("js/tablesaw/tablesaw-init.min.js")) {
            $returnStr .= getScript('js/tablesaw/tablesaw-init.min.js');
        }
        if (!isRegisteredScript("js/tablesaw/stackonly/tablesaw.stackonly.min.css")) {
            $returnStr .= getCSS("js/tablesaw/stackonly/tablesaw.stackonly.min.css");
        }
        return $returnStr;
    }

    /* DRAGGABLE */

    function displayDraggable() {
        $returnStr = "<script type='text/javascript'>(function($) {
            $.fn.drags = function(opt) {

                opt = $.extend({handle:\"\",cursor:\"move\"}, opt);

                if(opt.handle === \"\") {
                    var \$el = this;
                } else {
                    var \$el = this.find(opt.handle);
                }

                return \$el.css('cursor', opt.cursor).on(\"mousedown\", function(e) {
                    if(opt.handle === \"\") {
                        var \$drag = $(this).addClass('draggable');
                    } else {
                        var \$drag = $(this).addClass('active-handle').parent().addClass('draggable');
                    }
                    var z_idx = \$drag.css('z-index'),
                        drg_h = \$drag.outerHeight(),
                        drg_w = \$drag.outerWidth(),
                        pos_y = \$drag.offset().top + drg_h - e.pageY,
                        pos_x = \$drag.offset().left + drg_w - e.pageX;
                    \$drag.css('z-index', 1000).parents().on(\"mousemove\", function(e) {
                        $('.draggable').offset({
                            top:e.pageY + pos_y - drg_h,
                            left:e.pageX + pos_x - drg_w
                        }).on(\"mouseup\", function() {
                            $(this).removeClass('draggable').css('z-index', z_idx);
                        });
                    });
                    e.preventDefault(); // disable selection
                }).on(\"mouseup\", function() {
                    if(opt.handle === \"\") {
                        $(this).removeClass('draggable');
                    } else {
                        $(this).removeClass('active-handle').parent().removeClass('draggable');
                    }
                });

            }
        })(jQuery);</script>";
        return $returnStr;
    }

    /* AUTO COMPLETE */

    function displayAutoCompleteScripts($delimiters = array()) {  
        $returnStr = "";
        if (!isRegisteredScript("js/jquery-textcomplete/jquery.textcomplete.min.css")) {
            registerScript('js/jquery-textcomplete/jquery.textcomplete.min.css');
            $returnStr .= getCSS("js/jquery-textcomplete/jquery.textcomplete.min.css");
        }
        if (!isRegisteredScript("js/jquery-textcomplete/jquery.textcomplete-min.js")) {
            registerScript('js/jquery-textcomplete/jquery.textcomplete-min.js');
            $returnStr = getScript("js/jquery-textcomplete/jquery.textcomplete-min.js");
        }

        $returnStr .= "<script type=text/javascript>" . minifyScript("
                        var delimiter = '';
                        $(document).ready(function() {
                            var variables = [];
                            $.getJSON('index.php?p=sysadmin.autocomplete&" . POST_PARAM_SMS_AJAX . "=" . SMS_AJAX_CALL . "', function( data ) {
                                $.each( data, function( key, val ) {
                                  variables.push(val);
                                });
                            });
                            
                            // record delimiter
                            $('.autocomplete').keypress(function(event) {                             
                                if (event.which == 94 || event.which == 126 || event.which == 42 || event.which == 118) {
                                    delimiter = event.which;
                                }    
                                return true;
                            });
                            
                            $('.autocomplete').textcomplete([
                                { 
                                    match: /[\^~\*`](\w*)$/,
                                    search: function (term, callback) {                                                                               
                                        term = term.toLowerCase();
                                        callback($.map(variables, function (word) {
                                            return word.toLowerCase().indexOf(term) === 0 ? word : null;
                                        }));                                        
                                    },
                                    index: 1,
                                    replace: function (element) {                                         
                                        var delim = String.fromCharCode(delimiter);
                                        return delim + element;
                                    },
                                    cache: false,
                                    maxCount: 20
                                }
                            ]);
                            
                            // record delimiter
                            $('.autocompletebasic').keypress(function(event) {  
                                if (event.which == 94 || event.which == 42 || event.which == 118) {
                                    delimiter = event.which;
                                }    
                                return true;
                            });
                            
                            $('.autocompletebasic').textcomplete([
                                { 
                                    match: /[\^\*`](\w*)$/,
                                    search: function (term, callback) {                                                                               
                                        term = term.toLowerCase();
                                        callback($.map(variables, function (word) {
                                            return word.toLowerCase().indexOf(term) === 0 ? word : null;
                                        }));                                        
                                    },
                                    index: 1,
                                    replace: function (element) {  
                                        var delim = String.fromCharCode(delimiter);
                                        return delim + element;
                                    },
                                    cache: false,
                                    maxCount: 20
                                }
                            ]);
                        });") . "    
                    </script>";        
        return $returnStr;
    }

    /* ZIP */

    function displayZipScripts() {
        return;
        if (!isRegisteredScript("js/zip/lzstring.min.js")) {
            registerScript('js/zip/lzstring.min.js');
            $returnStr = getScript("js/zip/lzstring.min.js");
        }
        $returnStr .= '<script type="text/javascript">' . minifyScript('$(document).ready(function(){
                           unzip();
                        }); 
            
            function unzip() {                            
                $("*[data-zip]").each(function() {
                    var v = $(this).val();
                    var out = LZString.decompressFromBase64(v);
                    $(this).val(out);
                    document.getElementById($(this).attr("id")).value=out;
                });
            }            

            function zip() {                            
                $("*[data-zip]").each(function() {
                    var v = $(this).val();
                    var out = LZString.compressToBase64(v);
                    $(this).val(out);
                    document.getElementById($(this).attr("id")).value=out;
                });
            }') . '</script>';
        return $returnStr;
    }

    /* SESSION TIMEOUT */

    function displayTimeoutScripts() {

        global $survey, $engine;
        $returnStr = "";
        if (!isRegisteredScript("js/session/timeout-min.js")) {
            registerScript('js/session/timeout-min.js');
            $returnStr .= getScript("js/session/timeout-min.js");
        }

        $logouturl = $engine->replaceFills($survey->getTimeoutLogoutURL());
        if ($logouturl == "") {
            $logouturl = Config::sessionLogoutURL();
        }
        $logout = "";
        if ($logouturl != "") {
            $logout = "logoutUrl: '" . $logouturl . "',";
        }
        $aliveurl = Config::sessionAliveURL();
        $alive = "";
        if ($aliveurl != "") {
            $alive = "keepAliveUrl: '" . $aliveurl . "',";
        }
        $redirurl = $engine->replaceFills($survey->getTimeoutRedirectURL());
        if ($redirurl == "") {
            $redirurl = Config::sessionRedirectURL();
        }
        $redir = "";
        $length = $engine->replaceFills($survey->getTimeoutLength());
        if ($length == "") {
            $length = Config::sessionTimeout();
        }
        if ($redirurl != "") {
            $redir = "redirUrl: '" . $redirurl . "',";
            $redirafter = "redirAfter: " . $length * 1000;
        }
        $warnafter = ($length * 1000) * Config::sessionExpiredWarnPoint(); // warn after 60% of the time has passed
        $timeleft = ($length - ($length * Config::sessionExpiredWarnPoint())) / 60; // in minutes
        $message = Language::sessionExpiredMessage(round($timeleft));
        $alivebutton = $engine->replaceFills($survey->getTimeoutAliveButton());
        if ($alivebutton == "") {
            $alivebutton = Language::sessionExpiredKeepAliveButton();
        }
        $logoutbutton = $engine->replaceFills($survey->getTimeoutLogoutButton());
        if ($logoutbutton == "") {
            $logoutbutton = Language::sessionExpiredLogoutButton();
        }
        $title = $engine->replaceFills($survey->getTimeoutTitle());
        if ($title == "") {
            $title = Language::sessionExpiredTitle();
        }
        $ping = Config::sessionExpiredPingInterval();

        $returnStr .= "<script type='text/javascript'>" . minifyScript("
            $(document).ready(function(){
                $.sessionTimeout({
                    title: '$title',
                    keepAliveButton: '$alivebutton',
                    keepAliveInterval: $ping,    
                    logoutButton: '$logoutbutton',
                    message: '$message',
                    $alive
                    $logout
                    $redir                    
                    warnAfter: $warnafter,
                    $redirafter
                });
              });") . "  
            </script>";

        return $returnStr;
    }

    /* INPUT MASKING SCRIPT FUNCTIONS */

    function displayMaskingScripts($callback = "") {
        if (!isRegisteredScript("js/inputmasking/inputmask-min.js")) {
            registerScript('js/inputmasking/inputmask-min.js');
            $returnStr = getScript("js/inputmasking/inputmask-min.js");
        }
        if (!isRegisteredScript("js/inputmasking/numeric-min.js")) {
            registerScript('js/inputmasking/numeric-min.js');
            $returnStr .= getScript("js/inputmasking/numeric-min.js");
        }
        if (!isRegisteredScript("js/inputmasking/date-min.js")) {
            registerScript('js/inputmasking/date-min.js');
            $returnStr .= getScript("js/inputmasking/date-min.js");
        }

        if (!isRegisteredScript("js/inputmasking/regex-min.js")) {
            registerScript('js/inputmasking/regex-min.js');
            $returnStr .= getScript("js/inputmasking/regex-min.js");
        }
        if (!isRegisteredScript("js/inputmasking/uscic.min.js")) {
            registerScript('js/inputmasking/uscic.min.js');
            $returnStr .= getScript("js/inputmasking/uscic.min.js");
        }
        if (!isRegisteredScript("js/inputmasking/web-min.js")) {
            registerScript('js/inputmasking/web.js');
            $returnStr .= getScript("js/inputmasking/web-min.js");
        }

        /* NOTE: DISABLED FOR NOW FOR ANDROID Chrome and Firefox UNTIL RELEASE 38 COMES OUT. THEN ENABLE FOR RELEASE 38 IF WORKING THERE */
        // (user agent example for android: Mozilla/5.0 (Linux; Android 4.4.4; Nexus 5 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.131 Mobile Safari/537.36 =>)
        $returnStr .= minifyScript('<script type="text/javascript">$(document).ready(function(){
                          if(inputMaskingSupported() === true){
                            $(":input").inputmask();                 
                          }  
                        }); 
                        
function unmaskForm() {
    $("*[data-inputmask-unmask]").each(function() {
        var v = $(this).val();
        document.getElementById($(this).attr("id")).value = v;
        $(this).inputmask("remove"); // do this, because we dont call .submit() and so input mask is not removed otherwise
    });
}

function inputMaskingSupported() {
    var ua = navigator.userAgent;
    //var androidchrome = ua.match(new RegExp("android.*chrome.*", "i")) !== null;
    var androidchrome = ua.match(new RegExp("android.*chrome.*", "i")) !== null;    
    if (androidchrome) {    
        var bs = ua.match(/Chrome\/(\d+)/);        
        if (bs[1] < 38) { 
            return false;
        }
    }
    var androidfirefox = ua.match(new RegExp("android.*firefox.*", "i")) !== null;
    if (androidfirefox) {    
        return false;
    }
    var kindle = /Kindle/i.test(ua) || /Silk/i.test(ua) || /KFTT/i.test(ua) || /KFOT/i.test(ua) || /KFJWA/i.test(ua) || /KFJWI/i.test(ua) || /KFSOWI/i.test(ua) || /KFTHWA/i.test(ua) || /KFTHWI/i.test(ua) || /KFAPWA/i.test(ua) || /KFAPWI/i.test(ua);
    if(kindle) {
        var match = ua.match(/\bSilk\/([0-9]+)\b/);
        if (match[1] < 47) { // works silk 47 and higher 
            return false;
        }
    }
    return true;
}

function inputmaskCallbackError() {
' . $callback . '
}
                        </script>
                        ');
        return $returnStr;
    }

    /* DATA TABLES SCRIPT FUNCTIONS */

    function displayDataTablesScripts($extensions = array(), $css = true) {

        $returnStr = "";
        if (!isRegisteredScript("js/datatables/datatables.js")) {
            registerScript('js/datatables/datatables.js');
            $returnStr .= getScript("js/datatables/datatables.js");
        }

        if (!isRegisteredScript("js/datatables/extensions/date_sorting.js")) {
            registerScript('js/datatables/extensions/date_sorting.js');
            $returnStr .= getScript("js/datatables/extensions/date_sorting.js");
        }

        if (!isRegisteredScript("js/datetimepicker/moment-min.js")) {
            registerScript('js/datetimepicker/moment-min.js');
            $returnStr .= getScript("js/datetimepicker/moment-min.js");
        }

        if ($css && !isRegisteredScript("js/datatables/datatables.css")) {
            registerScript('js/datatables/datatables.css');
            $returnStr .= getCSS("js/datatables/datatables.css");
        }
        foreach ($extensions as $ext) {
            if (!isRegisteredScript("js/datatables/extensions/' . $ext . '.js")) {
                registerScript('js/datatables/extensions/' . $ext . '.js');
                $returnStr .= getScript("js/datatables/extensions/" . $ext . ".js");
            }
            if (strtoupper($ext) != strtoupper('rowreorder')) { // reorder has no associated css
                if ($css && !isRegisteredScript("js/datatables/extensions/' . $ext . '.css")) {
                    registerScript('js/datatables/extensions/' . $ext . '.css');
                    $returnStr .= getCSS("js/datatables/extensions/" . $ext . ".css");
                }
            } else {
                if (!isRegisteredScript("js/jqueryui/sortable.js")) {
                    registerScript('js/jqueryui/sortable.js');
                    $returnStr .= getScript("js/jqueryui/sortable.js");
                }
            }
        }

        /* https://datatables.net/forums/discussion/10437/fixedheader-column-headers-not-changing-on-window-resize/p1 */
        /* resize of header on window resize/empty/error */
        $returnStr .= '<script type="text/javascript">' . minifyScript('            
                        function resizeDataTables() {
                        $(\'div.dataTables_scrollBody table.dataTable\').each( function( index ) {
                        $(this).dataTable().fnAdjustColumnSizing();
                        });
                        }

                        $(window).on(\'resize\', function () {
                        resizeDataTables();
                        } );') . '
                        </script>';
        return $returnStr;
    }

    function displayDataTablesCSS($extensions = array()) {

        $returnStr = "";
        if (!isRegisteredScript("js/datatables/datatables.css")) {
            registerScript('js/datatables/datatables.css');
            $returnStr .= getCSS("js/datatables/datatables.css");
        }
        foreach ($extensions as $ext) {
            if (strtoupper($ext) != strtoupper('rowreorder')) { // reorder has no associated css
                if (!isRegisteredScript("js/datatables/extensions/' . $ext . '.css")) {
                    registerScript('js/datatables/extensions/' . $ext . '.css');
                    $returnStr .= getCSS("js/datatables/extensions/" . $ext . ".css");
                }
            }
        }
        return $returnStr;
    }

    /* KEYBOARD BINDING FUNCTIONS */

    function displayKeyBoardBinding($engine, $queryobject, $back) {
        $returnStr = "";
        if (!isRegisteredScript("js/hotkeys.js")) {
            registerScript('js/hotkeys.js');
            $returnStr = getScript("js/hotkeys.js");
        }

        $returnStr .= '<script type="text/javascript">';

        if ($back == true) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingBack()) . "', function(event){ $('#uscic-backbutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        if ($queryobject->getShowNextButton() == BUTTON_YES) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingNext()) . "', function(event){ $('#uscic-nextbutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        if ($queryobject->getShowDKButton() == BUTTON_YES) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingDK()) . "', function(){ $('#uscic-dkbutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        if ($queryobject->getShowRFButton() == BUTTON_YES) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingRF()) . "', function(){ $('#uscic-rfbutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        if ($queryobject->getShowNAButton() == BUTTON_YES) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingNA()) . "', function(){ $('#uscic-nabutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        if ($queryobject->getShowUpdateButton() == BUTTON_YES) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingUpdate()) . "', function(){ $('#uscic-updatebutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        if ($queryobject->getShowRemarkButton() == BUTTON_YES) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingRemark()) . "', function(){ $('#uscic-remarkbutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        if ($queryobject->getShowCloseButton() == BUTTON_YES) {
            $returnStr .= "$(document).bind('keypress', '" . $engine->replaceFills($queryobject->getKeyboardBindingClose()) . "', function(){ $('#uscic-closebutton').click(); event.preventDefault(); event.stopPropagation(); return false;} );";
        }

        $returnStr .= "</script>";
        return $returnStr;
    }

    function displayKeyBoardBindingDropdown($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", KEYBOARD_BINDING_YES => "", KEYBOARD_BINDING_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[KEYBOARD_BINDING_YES] . " value=" . KEYBOARD_BINDING_YES . ">" . Language::optionsKeyboardBindingYes() . "</option>";
        $returnStr .= "<option " . $selected[KEYBOARD_BINDING_NO] . " value=" . KEYBOARD_BINDING_NO . ">" . Language::optionsKeyboardBindingNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayTimeout($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' id='" . $name . "' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", TIMEOUT_YES => "", TIMEOUT_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[TIMEOUT_YES] . " value=" . TIMEOUT_YES . ">" . Language::optionsTimeoutYes() . "</option>";
        $returnStr .= "<option " . $selected[TIMEOUT_NO] . " value=" . TIMEOUT_NO . ">" . Language::optionsTimeoutNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayIndividualDKRFNA($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", INDIVIDUAL_DKRFNA_YES => "", INDIVIDUAL_DKRFNA_NO => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[INDIVIDUAL_DKRFNA_YES] . " value=" . INDIVIDUAL_DKRFNA_YES . ">" . Language::optionsIndividualDKRFNAYes() . "</option>";
        $returnStr .= "<option " . $selected[INDIVIDUAL_DKRFNA_NO] . " value=" . INDIVIDUAL_DKRFNA_NO . ">" . Language::optionsIndividualDKRFNANo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayAccessAfterCompletionReturn($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", AFTER_COMPLETION_NO_REENTRY => "", AFTER_COMPLETION_FIRST_SCREEN => "", AFTER_COMPLETION_LAST_SCREEN => "", AFTER_COMPLETION_LAST_SCREEN_REDO => "", AFTER_COMPLETION_FROM_START => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }

        $returnStr .= "<option " . $selected[AFTER_COMPLETION_NO_REENTRY] . " value=" . AFTER_COMPLETION_NO_REENTRY . ">" . Language::optionsAccessReTurnAfterCompletionNo() . "</option>";
        $returnStr .= "<option " . $selected[AFTER_COMPLETION_FROM_START] . " value=" . AFTER_COMPLETION_FROM_START . ">" . Language::optionsAccessReTurnAfterCompletionFromStart() . "</option>";
        $returnStr .= "<option " . $selected[AFTER_COMPLETION_FIRST_SCREEN] . " value=" . AFTER_COMPLETION_FIRST_SCREEN . ">" . Language::optionsAccessReTurnAfterCompletionFirst() . "</option>";
        $returnStr .= "<option " . $selected[AFTER_COMPLETION_LAST_SCREEN] . " value=" . AFTER_COMPLETION_LAST_SCREEN . ">" . Language::optionsAccessReTurnAfterCompletionLast() . "</option>";
        $returnStr .= "<option " . $selected[AFTER_COMPLETION_LAST_SCREEN_REDO] . " value=" . AFTER_COMPLETION_LAST_SCREEN_REDO . ">" . Language::optionsAccessReTurnAfterCompletionLastRedo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayAccessAfterCompletionPreload($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", PRELOAD_REDO_NO => "", PRELOAD_REDO_YES => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[PRELOAD_REDO_YES] . " value=" . PRELOAD_REDO_YES . ">" . Language::optionsPreloadRedoYes() . "</option>";
        $returnStr .= "<option " . $selected[PRELOAD_REDO_NO] . " value=" . PRELOAD_REDO_NO . ">" . Language::optionsPreloadRedoNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayAccessReentryPreload($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", PRELOAD_REDO_NO => "", PRELOAD_REDO_YES => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[PRELOAD_REDO_YES] . " value=" . PRELOAD_REDO_YES . ">" . Language::optionsPreloadRedoYes() . "</option>";
        $returnStr .= "<option " . $selected[PRELOAD_REDO_NO] . " value=" . PRELOAD_REDO_NO . ">" . Language::optionsPreloadRedoNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayUsers($users, $key, $name = 'uridsel', $none = false, $exclude = "") {
        $returnStr = "<select style='width:300px' class='form-control selectpicker show-tick' name='" . $name . "'>";
        if ($none) {
            $returnStr .= "<option value=-1>" . Language::labelNone() . "</option>";
        }

        foreach ($users as $user) {            
            if ($exclude == $user->getUrid()) {
                continue;
            }

            $selected = '';
            if ($key == $user->getUrid()) {
                $selected = "selected";
            }
            $returnStr .= "<option " . $selected . " value=" . $user->getUrid() . ">" . $user->getName() . "</option>";
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayUsersUpdate($users, $name = 'uridsel') {
        $returnStr = "<select style='width:300px' class='form-control selectpicker show-tick' multiple name='" . $name . "[]'>";
        $returnStr .= "<option selected value=-1>" . Language::labelAll() . "</option>";
        foreach ($users as $user) {
            $returnStr .= "<option value=" . $user->getUrid() . ">" . $user->getName() . "</option>";
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayAccessReentryAction($name, $current, $generic = false, $type = -1) {
        $returnStr = "<select class='selectpicker show-tick' name='" . $name . "'>";
        $selected = array(SETTING_FOLLOW_GENERIC => "", SETTING_FOLLOW_TYPE => "", REENTRY_FIRST_SCREEN => "", REENTRY_SAME_SCREEN => "", REENTRY_SAME_SCREEN_REDO_ACTION => "", REENTRY_NEXT_SCREEN => "", REENTRY_FROM_START => "", REENTRY_NO_REENTRY => "");
        $selected[$current] = "selected";
        if ($type > 0) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        } else {
            if ($generic) {
                $returnStr .= "<option " . $selected[SETTING_FOLLOW_GENERIC] . " value=" . SETTING_FOLLOW_GENERIC . ">" . Language::optionsFollowGeneric() . "</option>";
            }
        }
        $returnStr .= "<option " . $selected[REENTRY_NO_REENTRY] . " value=" . REENTRY_NO_REENTRY . ">" . Language::optionsAccessReentryActionNotAllowed() . "</option>";
        $returnStr .= "<option " . $selected[REENTRY_FROM_START] . " value=" . REENTRY_FROM_START . ">" . Language::optionsAccessReentryActionStart() . "</option>";
        $returnStr .= "<option " . $selected[REENTRY_FIRST_SCREEN] . " value=" . REENTRY_FIRST_SCREEN . ">" . Language::optionsAccessReentryActionFirst() . "</option>";
        $returnStr .= "<option " . $selected[REENTRY_SAME_SCREEN] . " value=" . REENTRY_SAME_SCREEN . ">" . Language::optionsAccessReentryActionSame() . "</option>";
        $returnStr .= "<option " . $selected[REENTRY_SAME_SCREEN_REDO_ACTION] . " value=" . REENTRY_SAME_SCREEN_REDO_ACTION . ">" . Language::optionsAccessReentryActionSameRedo() . "</option>";
        $returnStr .= "<option " . $selected[REENTRY_NEXT_SCREEN] . " value=" . REENTRY_NEXT_SCREEN . ">" . Language::optionsAccessReentryActionNext() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayAccessDevice($name, $current) {
        $returnStr = "<select multiple class='selectpicker show-tick' name='" . $name . "[]'>";
        $selected = array(SURVEY_DEVICE_PC => "", SURVEY_DEVICE_TABLET => "", SURVEY_DEVICE_PHONE => "");
        $arr = explode("~", $current);
        foreach ($arr as $a) {
            $selected[$a] = "selected";
        }

        $returnStr .= "<option " . $selected[SURVEY_DEVICE_PC] . " value=" . SURVEY_DEVICE_PC . ">" . Language::optionsAccessDevicePC() . "</option>";
        $returnStr .= "<option " . $selected[SURVEY_DEVICE_TABLET] . " value=" . SURVEY_DEVICE_TABLET . ">" . Language::optionsAccessDeviceTablet() . "</option>";
        $returnStr .= "<option " . $selected[SURVEY_DEVICE_PHONE] . " value=" . SURVEY_DEVICE_PHONE . ">" . Language::optionsAccessDevicePhone() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

    function displayButtonBinding($name, $current = '') {
        return "<input type=text class='form-control autocompletebasic' name='" . $name . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($current, ENT_QUOTES)) . "'>";
    }

    function displayOnSubmit($name, $current = '') {
        return "<input type=text class='form-control autocompletebasic' name='" . $name . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($current, ENT_QUOTES)) . "'>";
    }

    function disableForm() {
        $returnStr = "<script type='text/javascript'>" . minifyScript("
                            function checkForm() {
                                $('#sectiondiv :input').attr('disabled', true);
                                $('input[type=\"submit\"]').attr('disabled',true);                                
                             }  
                             $(document).ready(function(){
                                checkForm();
                             });") . "
                          </script>";
        return $returnStr;
    }

    function displayButtonToggling() {
        $returnStr = minifyScript("<script type='text/javascript'>
                            function disableButtons() {
                                $('button').attr('disabled', 'disabled');
                            }
                            function enableButtons() {                                    
                                $('button:not([delayedenable])').removeAttr('disabled');
                            }
                          </script>");
        return $returnStr;
    }

    function enableForm() {
        $returnStr = "<script type='text/javascript'>
                          function checkForm() {
                                                               
                           } 
                      </script>";
        return $returnStr;
    }

    function checkForm() {
        $returnStr = "";
        $active = checkUserAccess();
        if ($active) {
            $returnStr .= $this->disableForm();
        } else {
            $returnStr = $this->enableForm();
        }
        return $returnStr;
    }

    function bindAjax() {
        return "";
        $returnStr = "<script type='text/javascript'>
                        $(document).ready(function(){
                            
                            // http://stackoverflow.com/questions/1964839/jquery-please-wait-loading-animation
                            \$body = $('body');
                            $(document).on({
                                ajaxStart: function() { \$body.addClass('loading');  },
                                ajaxStop: function() { \$body.removeClass('loading'); }    
                            });
                            
                            $('#wrap').on('click', '#mainnavbar a',function(event){                            
                                  if (event.which != 1) {                                  
                                    return;
                                  }
                                  
                                  if ($(this).attr('target') == '_blank' || $(this).attr('" . POST_PARAM_NOAJAX . "') == " . NOAJAX . ") {
                                    return;
                                  }
                                  
                                  event.preventDefault();
                                  var url= this.href;
                                  if (url) {
                                    url = url + \"&" . POST_PARAM_AJAX_LOAD . "=" . AJAX_LOAD . "\";
                                  }
                                  else {
                                    url = 'index.php' + \"?" . POST_PARAM_AJAX_LOAD . "=" . AJAX_LOAD . "\";
                                  }
                                  $.get(url,{},function(response){ 
                                     $('#content').html($(response).contents());
                                     $('[data-hover=\"dropdown\"]').dropdownHover();  
                                  });
                                  return false;
                            });
                            
                            $('#wrap').on('click', '#content a[href]',function(event){
                                  
                                  if (event.which != 1) {                                  
                                    return;
                                  }   
                                  
                                  // http://stackoverflow.com/questions/1318076/jquery-hasattr-checking-to-see-if-there-is-an-attribute-on-an-element
                                  var oc = $(this).attr('onclick');
                                  if (typeof oc !== 'undefined' && oc !== false) {                                    
                                    return;
                                  }  
                                  event.preventDefault();
                                  var url= this.href;
                                  if (url) {
                                    url = url + \"&" . POST_PARAM_AJAX_LOAD . "=" . AJAX_LOAD . "\";
                                  }
                                  else {
                                    url = 'index.php' + \"?" . POST_PARAM_AJAX_LOAD . "=" . AJAX_LOAD . "\";
                                  }
                                  $.get(url,{},function(response){ 
                                     $('#content').html($(response).contents());
                                     $('[data-hover=\"dropdown\"]').dropdownHover();  
                                  });
                                  return false;
                            });
                            
                            $('#wrap').on('submit', '#content form' ,function(event){
                                  //  return;
                                  if ($(this).attr('target') == '_blank' || $(this).attr('" . POST_PARAM_NOAJAX . "') == " . NOAJAX . ") {
                                      return;
                                  }   
                                  event.preventDefault();
                                  
                                  var values = $(this).serialize();
                                  values += '&" . POST_PARAM_AJAX_LOAD . "=" . AJAX_LOAD . "';
                                  
                                  // Send the data using post
                                  var posting = $.post( $(this).attr('action'), values );
                                  
                                  posting.done(function( data ) {       
                                    $('#content').html( $( data ).html());
                                    $('[data-hover=\"dropdown\"]').dropdownHover();  
                                  }); 
                                  return false;
                                });	
                          });</script>
                        ";
        return $returnStr;
    }

    function displayRoutingErrorModal($section, $text) {
        $returnStr = "<script type='text/javascript' src='js/jqueryui/jquery-ui.js'></script>";
        $returnStr .= "<script type='text/javascript'>" . minifyScript("
                        $(document).ready(function() {
                            $('#errorsModal').drags({ 
                                handle: '.modal-header' 
                            });
                        });") . "   
                        </script>";
        $returnStr .= '<div class="modal fade" id="errorsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">' . Language::labelErrorsIn() . '\'' . $section->getName() . '\'</h4>
      </div>
      <div class="modal-body">';
        $returnStr .= $text;
        $returnStr .= '</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
';
        return $returnStr;
    }

    function showInputBox($name, $value, $edit) {

        if ($edit) {

            return '<input type="text" name="' . $name . '" class="form-control" value="' . convertHTLMEntities($value, ENT_QUOTES) . '" />';
        } else {

            return $value;
        }
    }

    function showActionBar($title, $input, $buttontext, $sessionparams, $javascript = '') {

        $content .= '<nav class="navbar navbar-default" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand">' . $title . '</a>
   </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">';


        $content .= '<form method="post" class="navbar-form navbar-left">';
        $content .= $sessionparams;
        //$content .= setSessionParamsPost(array('page' => 'supervisor.interviewer.respondent.reassign', 'primkey' => $respondentOrHousehold->getPrimkey()));
//          <form class="navbar-form navbar-left" role="search">
        $content .= '<div class="form-group">';
        //<input type="text" class="form-control" placeholder="Search">
        //$content .= '<select class="form-control"><option value=1>test</option></select>';

        $content .= $input; //$this->displayUsers($users, $respondentOrHousehold->getUrid());
        $content .= '</div>';
        $content .= '<button type="submit" class="btn btn-default"' . $javascript . '>' . $buttontext . '</button>';
        $content .= '</form>
        </div>
      </div>
</nav>';



        return $content;
    }

    function displayInterviewerDropDown($page, $urid = 1) {
        $returnStr = '';
        $returnStr .= '<div style="width:250px"><form method=post>';
        $returnStr .= setSessionParamsPost(array('page' => $page));

        $returnStr .= '<div class="input-group">';

        $returnStr .= $this->displayInterviewerSelect($urid);

        $returnStr .= '<span class="input-group-btn">';
        $returnStr .= '<input type=submit class="form-control" value="Go">';
        $returnStr .= '</span></div>';
        $returnStr .= '</form></div>';

        return $returnStr;
    }

    function displaySupervisorSelect($urid = "") {
        $returnStr = '<select name=selurid class="form-control" style="width:200px">';
        $selected = '';
        if (0 == $urid) {
            $selected = ' SELECTED';
        }
        $returnStr .= '<option value="' . 0 . '"' . $selected . '>' . 'Select supervisors' . '</option>';
        $users = new Users();
        $users = $users->getUsersByType(USER_SUPERVISOR);
        foreach ($users as $user) {
            $selected = '';
            if ($user->getUrid() == $urid) {
                $selected = ' SELECTED';
            }
            $returnStr .= '<option value="' . $user->getUrid() . '"' . $selected . '>' . $user->getUsername() . ': ' . $user->getName() . '</option>';
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayInterviewerSelect($urid = "") {
        $returnStr = '<select name=selurid class="form-control" style="width:200px">';
        $selected = '';
        if (0 == $urid) {
            $selected = ' SELECTED';
        }
        $returnStr .= '<option value="' . 0 . '"' . $selected . '>' . 'Select interviewer' . '</option>';
        $users = new Users();
        $user = new User($_SESSION['URID']);
        if ($user->getUserType() == USER_SUPERVISOR) {
            $users = $users->getUsersBySupervisor($user->getUrid());
        } elseif ($user->getUserType() == USER_SYSADMIN || $user->getUserType() == USER_RESEARCHER) {
            $users = $users->getUsersByType(USER_INTERVIEWER);
        } else {
            $users = array();
        }
        foreach ($users as $user) {
            $selected = '';
            if ($user->getUrid() == $urid) {
                $selected = ' SELECTED';
            }
            $returnStr .= '<option value="' . $user->getUrid() . '"' . $selected . '>' . $user->getUsername() . ': ' . $user->getName() . '</option>';
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayRespondentOrHousehold($rorh) {
        $returnStr = '<select name=rorh class="form-control" style="width:200px">';
        $selected = array('', '', '');
        $selected[$rorh] = ' SELECTED';
        $returnStr .= '<option value="1"' . $selected[1] . '>' . 'Household level' . '</option>';
        $returnStr .= '<option value="2"' . $selected[2] . '>' . 'Respondent level' . '</option>';

        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayContactType($ceid) {
        $returnStr = '<select name=ceid class="form-control" style="width:200px">';
        $selected = array('', '', '');
        $selected[$ceid] = ' SELECTED';
        $returnStr .= '<option value="1"' . $selected[1] . '>' . 'Interviewer codes' . '</option>';
        $returnStr .= '<option value="2"' . $selected[2] . '>' . 'Final codes' . '</option>';
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayPsus($puid, $showAll = false) {
        $returnStr = '<select class="form-control" name="puid">';
        $psus = new Psus();
        $psus = $psus->getPsus();
        $selected = array_fill(0, 500, '');
        $selected[$puid] = ' SELECTED';

        if ($showAll) {
            $returnStr .= '<option value="0"' . $selected[0] . '>' . 'All psus' . '</option>';
        }
        foreach ($psus as $psu) {
            $returnStr .= '<option value="' . $psu->getPuid() . '"' . $selected[$psu->getPuid()] . '>' . $psu->getName() . '</option>';
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displayCommunicationSelect($comm) {
        $selected = array_fill(0, 10, '');
        $selected[$comm] = ' SELECTED';
        $returnStr = '<select class="form-control" name="communication">';
        $returnStr .= '<option value=' . SEND_RECEIVE_USB . $selected[SEND_RECEIVE_USB] . '>' . Language::labelUSB() . '</option>';
        $returnStr .= '<option value=' . SEND_RECEIVE_INTERNET . $selected[SEND_RECEIVE_INTERNET] . '>' . Language::labelInternet() . '</option>';
        //$returnStr .= '<option value=' . SEND_RECEIVE_EXPORTSQL . $selected[SEND_RECEIVE_EXPORTSQL] . '>' . Language::labelExportAsSql() . '</option>';
        $returnStr .= '<option value=' . SEND_RECEIVE_WORKONSERVER . $selected[SEND_RECEIVE_WORKONSERVER] . '>' . Language::labelWorkOnServer() . '</option>';
        $returnStr .= '</select>';
        return $returnStr;
    }

    function displaySelectFromArray($inputArray, $inputSel, $name = 'arrayinput') {
        $selected = array_fill(0, 50, '');
        $selected[$inputSel] = ' SELECTED';
        $returnStr = $this->displayComboBox();
        $returnStr .= '<select class="selectpicker show-tick" id="' . $name . '" name="' . $name . '" style="width:300px" >';
        foreach ($inputArray as $key => $input) {
            $returnStr .= '<option value=' . $key . $selected[$key] . '>' . $input . '</option>';
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    function showModalForm($id, $text) {
        $returnStr .= '<div class="modal fade bs-example-modal-lg" id="' . $id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      </div>
      <div class="modal-body">
         <table width=100%" style="height:500px" ><tr><td valign=top>' . $text . ' 
         </td></tr></table>
      </div>
    </div>
  </div>
</div>        ';

        return $returnStr;
    }

    function displayPanelTypeFilter($paneltype = 0) {
        $returnStr = '';
        $active = array('', '', '', '', '', '', '', '');
        $active[$paneltype] = ' active';

        $returnStr .= '<input type="hidden" name="paneltype" id="paneltype" value="' . $paneltype . '">';

        $returnStr .= '<div id="filterselector" class="btn-group">
	  <button type="button" class="btn btn-default' . $active[1] . '" value=1>' . Language::labelHouseholds() . '</button>
	  <button type="button" class="btn btn-default' . $active[2] . '" value=2>' . Language::labelRespondents() . '</button>';
        $returnStr .= '</div>';

        $returnStr .= '<script>';
        $returnStr .= '$(\'#filterselector button\').click(function() {
		  $(\'#filterselector button\').addClass(\'active\').not(this).removeClass(\'active\');
		  $(\'#paneltype\').val("2");
		  if ($(this).val() == "1") {
		    $(\'#paneltype\').val("1");
		  }
 	  });';
        $returnStr .= '</script>';


        return $returnStr;
    }

    function ShowCommunicationServerOptions() {
        if (is_array(dbConfig::defaultCommunicationServer())) {
            $returnStr = '';
            $active = array('', '', '', '', '', '', '', '');
            if (loadvar('commserver') != '') {
                $_SESSION['COMMSERVER'] = loadvar('commserver');
            }
            $active[$_SESSION['COMMSERVER']] = ' active';

            $returnStr .= '<form method="post" id="hiddenform">';
            $returnStr .= setSessionParamsPost(array('page' => 'interviewer.sendreceive'));

            $returnStr .= '<input type="hidden" name="commserver" id="commserver" value="' . $paneltype . '">';

            $returnStr .= '<div id="commserverselector" class="btn-group">
  	    <button type="button" class="btn btn-default' . $active[0] . '" value=0>' . Language::labelCommServerLocal() . '</button>
	    <button type="button" class="btn btn-default' . $active[1] . '" value=1>' . Language::labelCommServerOutside() . '</button>';
            $returnStr .= '</div>';



            $returnStr .= '</form>';

            $returnStr .= '<br/>';


            $returnStr .= '<script>';
            $returnStr .= '$(\'#commserverselector button\').click(function() {
		  $(\'#commserverselector button\').addClass(\'active\').not(this).removeClass(\'active\');
		  $(\'#commserver\').val("0");
		  if ($(this).val() == "1") {
		    $(\'#commserver\').val("1");
		  }
                  $("#hiddenform").submit();            

 	  });';
            $returnStr .= '</script>';
        }

        return $returnStr;
    }

    function getTinyMCE($selector = "textarea.tinymce", $inline = 1, $editicon = '') {

        $returnStr = '';
        if (!isRegisteredScript("js/tinymce/tinymce.min.js")) {
            registerScript('js/tinymce/tinymce.min.js');
            $returnStr .= getScript("js/tinymce/tinymce.min.js");
        }
        if (!isRegisteredScript("js/tinymce/jquery.tinymce.min.js")) {
            registerScript('js/tinymce/jquery.tinymce.min.js');
            $returnStr .= getScript("js/tinymce/jquery.tinymce.min.js");
        }
        $returnStr .= '
            <script type="text/javascript">';

        // inline survey editing, then define load text function
        if ($inline > 1) {
            $returnStr .= 'function loadRealText() {
                var realtext = "";
                var ed = tinyMCE.activeEditor;
                var id = ed.id;
                var target = $("#" + id).attr("uscic-target");
                var texttype = $("#" + id).attr("uscic-texttype");
                var answercode = $("#" + id).attr("uscic-answercode"); 
                $.ajax({
                        type: "POST",
                        url: "' . setSessionParams(getSessionParams()) . '",
                        data: { ' . POST_PARAM_SMS_AJAX . ': "' . SMS_AJAX_CALL . '", p: "sysadmin.inline.getcontent", texttype: texttype, answercode: answercode, target: target },    
                        success: function(response){
                            ed.setContent(response + "' . $editicon . '");
                        }
                    });     
            }';
        }

        if ($inline == 1) {
            $returnStr .= 'var old = "";
               $( document ).ready(function() {
                
                /*$("textarea.tinymce").focusin(function() {
                    $(this).click();
                });*/
                                
                tinymce.init({  
                    valid_elements : "*[*]",
                    mode : "textareas",
                    selector: "' . $selector . '",    
                    menubar: "insert edit table format view tools",
                    setup: function(editor) {
                                editor.on("blur", function(e) {
                                    return;
                                });
                                editor.on("init", function(e) {
                                    tinyMCE.activeEditor.focus(); // does not work first time round
                                });
                
                                
                            },';
        }

        // editor
        // inline survey editing
        $save = '';
        $contextmenu = '';
        if ($inline > 1) {
            $returnStr .= '
                tinymce.init({
                mode : "textareas",
                selector: "' . $selector . '",    
                menubar: "insert edit table format view tools",';

            if ($inline == 2) {
                $returnStr .= '
                    valid_elements : "*[*]",';
            }            
            $contextmenu = 'contextmenu';
            $save = 'save';
            $contextmenu = '';
            $returnStr .= 'inline: true,
                            save_enablewhendirty: true,
                            save_onsavecallback: function() { ajaxSave(this);},
                            setup: function(editor) {
                                editor.on("focus", function(e) {
                                    loadRealText();
                                });                                
                            },
                        ';
        }

        $returnStr .= '    
        content_css : "css/tinymce.css",
        theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
        font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",
        force_br_newlines : false,
        force_p_newlines : false,
        forced_root_block: \'\',
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace code ' . $save . '",
            "insertdatetime media table ' . $contextmenu . ' paste"
        ],
        toolbar1: "insertfile save undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        toolbar2: "preview media | forecolor backcolor emoticons"
});';

        // inline survey editing
        if ($inline > 1) {
            $returnStr .= '
                function ajaxSave(ed) {
        ed.setProgressState(1); // Show progress 
        var id = ed.id;
        var texttype = $("#" + id).attr("uscic-texttype");
        var answercode = $("#" + id).attr("uscic-answercode");
        var target = $("#" + id).attr("uscic-target");
        $.ajax({
            type: "POST",
            url: "' . setSessionParams(getSessionParams()) . '",
            data: { ' . POST_PARAM_SMS_AJAX . ': "' . SMS_AJAX_CALL . '", p: "sysadmin.inline.editcontent", target: target, texttype: texttype, answercode: answercode, text: ed.getContent() },    
            success: function(msg){
                document.getElementById("navigation").value="' . PROGRAMMATIC_UPDATE . '"; 
                document.getElementById("form").submit();
            }
        });
        ed.setProgressState(0); // Show progress            
}
';
        }
        if ($inline == 1) {
            $returnStr .= '
                });';
        }

        $returnStr .= '</script>';
        return $returnStr;
    }

    function getCodeMirror($style = '') {

        if (!isRegisteredScript("js/codemirror/lib/codemirror.css")) {
            registerScript('js/codemirror/lib/codemirror.css');
            $returnStr = getCSS("js/codemirror/lib/codemirror.css");
        }
        if (!isRegisteredScript("js/codemirror/addon/dialog/dialog.css")) {
            registerScript('js/codemirror/addon/dialog/dialog.css');
            $returnStr .= getCSS("js/codemirror/addon/dialog/dialog.css");
        }
        if (!isRegisteredScript("js/codemirror/lib/codemirror.js")) {
            registerScript('js/codemirror/lib/codemirror.js');
            $returnStr .= getScript("js/codemirror/lib/codemirror.js");
        }
        if (!isRegisteredScript("js/codemirror/mode/xml/xml.js")) {
            registerScript('js/codemirror/mode/xml/xml.js');
            $returnStr .= getScript("js/codemirror/mode/xml/xml.js");
        }
        if (!isRegisteredScript("js/codemirror/addon/dialog/dialog.js")) {
            registerScript('js/codemirror/addon/dialog/dialog.js');
            $returnStr .= getScript("js/codemirror/addon/dialog/dialog.js");
        }
        if (!isRegisteredScript("js/codemirror/addon/search/searchcursor.js")) {
            registerScript('js/codemirror/addon/search/searchcursor.js');
            $returnStr .= getScript("js/codemirror/addon/search/searchcursor.js");
        }
        if (!isRegisteredScript("js/codemirror/addon/search/search.js")) {
            registerScript('js/codemirror/addon/search/search.js');
            $returnStr .= getScript("js/codemirror/addon/search/search.js");
        }
        if (!isRegisteredScript("js/codemirror/mode/nubis/nubis.js")) {
            registerScript('js/codemirror/mode/nubis/nubis.js');
            $returnStr .= getScript("js/codemirror/mode/nubis/nubis.js");
        }
        $returnStr .= '<style type="text/css">';
        $returnStr .= '    .CodeMirror {' . $style . ' border-top: 1px solid black; border-bottom: 1px solid black;}
                        dt {font-family: monospace; color: #666;}
                      </style>';
        return $returnStr;
    }

    function getDirtyForms() {

        $returnStr = '';
        if (!isRegisteredScript("js/dirtyform/lib/jquery.dirtyform.min.js")) {
            registerScript('js/dirtyform/jquery.dirtyform.min.js');
            $returnStr .= getScript("js/dirtyform/jquery.dirtyform.min.js");
            ;
        }

        if (!isRegisteredScript("js/dirtyform/lib/jquery.dirtyform.bootstrap.js")) {
            registerScript('js/dirtyform/jquery.dirtyform.bootstrap.js');
            $returnStr .= getScript("js/dirtyform/jquery.dirtyform.bootstrap.js");
        }

        if (isRegisteredScript("js/tinymce/tinymce.min.js")) {
            if (!isRegisteredScript("js/dirtyform/tinymce/jquery.dirtyforms.helpers.tinymce.min.js")) {
                registerScript('js/dirtyform/tinymce/jquery.dirtyforms.helpers.tinymce.min.js');
                $returnStr .= getScript("js/dirtyform/tinymce/jquery.dirtyforms.helpers.tinymce.min.js");
            }
        }

        $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function() {
                            $.DirtyForms.ignoreClass = 'dirtyignore';
                            $.DirtyForms.dialog.dialogID = 'uscic-dialog';
                            //$.DirtyForms.dialog.titleID = 'uscic-title';
                            $.DirtyForms.dialog.continueButtonClass = 'uscic-continue';
                            $.DirtyForms.dialog.cancelButtonClass = 'uscic-cancel';
                            $.DirtyForms.dialog.continueButtonText = '" . Language::buttonContinue() . "';
                            $.DirtyForms.dialog.cancelButtonText = '" . Language::buttonCancel() . "';
                            $('#editform').dirtyForms({});
                        });                       
                        </script>";

        $returnStr .= '<div id="uscic-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dirty-title">' .
                '<div class="modal-dialog" role="document">' .
                '<div class="modal-content panel-danger">' .
                '<div class="modal-header panel-heading">' .
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' .
                '<h3 class="modal-title" id="uscic-title"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . Language::labelUnsavedChanges() . '</h3>' .
                '</div>' .
                '<div class="modal-body panel-body">' . Language::labelUnsavedChangesMessage() . '</div>' .
                '<div class="modal-footer panel-footer">' .
                '<button type="button" class="uscic-continue btn btn-danger" data-dismiss="modal"></button>' .
                '<button type="button" class="uscic-cancel btn btn-default" data-dismiss="modal"></button>' .
                '</div>' .
                '</div>' .
                '</div>' .
                '</div>';
        return $returnStr;
    }

    function stripNonAscii($str) {

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        $str = $this->stripWordQuotes($str);

        // http://stackoverflow.com/questions/1176904/php-how-to-remove-all-non-printable-characters-in-a-string
        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str);
    }

    function stripWordQuotes($str) {

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        return str_replace($this->chrmap, "", $str);
    }
    
    function displaySelected($respondent, $showNone = true){
       if (!$showNone){ //show 'none'? or leave empty?
          if ($respondent->isSelected() == 0){
            return '';    
          }
        }
        if ($respondent->isSelected() == 1){
            return ' selected';
        }
        return ' -';
    }

    function displayMovedOut($respondent, $showNone = true) {
        if (!$showNone){ //show 'none'? or leave empty?
          if ($respondent->getMovedOut() == 0){
            return '';    
          }
        }
        $statusCodes = Language::labelMovedOutStatus();
        if (isset($statusCodes[$respondent->getMovedOut()])) {
            if ($respondent->getMovedOut() == 1){ //new hh, show location!
              return '<a href="#" data-toggle="modal" style="color:red" data-target="#myModal' . $respondent->getHhOrder() . '">' . $statusCodes[$respondent->getMovedOut()] . '</a>';
            }
            return $statusCodes[$respondent->getMovedOut()];
        }
        return '-';
    }


}

?>