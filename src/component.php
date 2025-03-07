<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Component extends NubisObject {

    protected $settings;
    private $runtime;

    function __construct() {
        $this->settings = array();
        $this->runtime = array();
    }

    /* settings functions */

    function addSettings($settings) {

        foreach ($settings as $setting) {

            $this->settings[strtoupper($setting->getName() . $setting->getMode() . $setting->getLanguage() . $setting->getObjectType())] = $setting;
        }
    }

    function addSetting($setting) {
        $ortype = $setting->getObjectType();
        $setting->setSuid($this->getSuid());
        $setting->setObject($this->getObjectName());
        $setting->setObjectType($this->getObjectType());
        $this->settings[strtoupper($setting->getName() . $setting->getMode() . $setting->getLanguage() . $ortype)] = $setting;
    }

    function addSettingByValues($name, $value, $mode, $language) {
        $setting = new Setting();
        $setting->setSuid($this->getSuid());
        $setting->setObject($this->getObjectName());
        $setting->setObjectType($this->getObjectType());
        $setting->setName($name);
        $setting->setLanguage($language);
        $setting->setMode($mode);
        $setting->setValue($value);
        $this->settings[strtoupper($name . $mode . $language . $this->getObjectType())] = $setting;
    }

    function getSettingWithIndex($name, $mode, $language, $objecttype) {
        $index = strtoupper($name . $mode . $language . $objecttype);

        if (isset($this->settings[$index])) {
            /* for admin pages always return the found setting (for editing) */
            if (isset($_SESSION['SYSTEM_ENTRY']) && $_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
                return $this->settings[$index];
            }
            // for survey pages
            else {
                return $this->settings[$index];
            }
        }
        return null;
    }

    function getSettingModeLanguage($name, $mode, $language, $type) {
        $setting = $this->getSettingWithIndex($name, $mode, $language, $type);        
        if ($setting) {  
            return $setting;
        }
        return new Setting(); // not found
    }

    function addRuntimeSetting($index, $setting) {
        if (isset($_SESSION['SYSTEM_ENTRY']) && $_SESSION['SYSTEM_ENTRY'] == USCIC_SURVEY) {
            $this->runtime[$index] = $setting;
        }
    }

    function getSetting($name, $default = true) {        
        $mode = getSurveyMode();                
        $language = getSurveyLanguage();
        $objecttype = $this->getObjectType();
        $index = strtoupper($name . $mode . $language . $objecttype);
        
        /* check runtime (retrieved before) if in survey */
        if (isset($_SESSION['SYSTEM_ENTRY']) && $_SESSION['SYSTEM_ENTRY'] == USCIC_SURVEY) {
            if (isset($this->runtime[$index])) {
                return $this->runtime[$index];
            }
        }

        $setting = $this->getSettingWithIndex($name, $mode, $language, $objecttype);
        if ($setting) {
            $this->addRuntimeSetting($index, $setting);
            return $setting;
        }

        // check for default value
        if ($default) {

            // first try by staying in the same mode but with the default language
            $setting = $this->getSettingWithIndex($name, $mode, getDefaultSurveyLanguage(), $objecttype);
            if ($setting) {
                $this->addRuntimeSetting($index, $setting);
                return $setting;
            }

            // next try with the default survey mode and the current language
            /* only do this for specific settings? */
            $setting = $this->getSettingWithIndex($name, getDefaultSurveyMode(), $language, $objecttype);
            if ($setting) {
                $this->addRuntimeSetting($index, $setting);
                return $setting;
            }

            // finally try with the default survey mode and the default language
            $setting = $this->getSettingWithIndex($name, getDefaultSurveyMode(), getDefaultSurveyLanguage(), $objecttype);
            if ($setting) {
                $this->addRuntimeSetting($index, $setting);
                return $setting;
            }
        }
        $setting = new Setting();
        $this->addRuntimeSetting($index, $setting);
        return $setting; // db failed
    }

    function getSettingValueDirect($name, $mode, $language) {

        $index = strtoupper($name . $mode . $language . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        // check for current mode/default language
        $index = strtoupper($name . $mode . getDefaultSurveyLanguage() . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        // should we break mode below to find a setting value???
        // check for default mode/current language
        $index = strtoupper($name . getDefaultSurveyMode() . $language . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        $index = strtoupper($name . getDefaultSurveyMode() . getDefaultSurveyLanguage() . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        return "";
    }
    
    function getSettingValueDirectAjax($name, $mode, $language, $defaultmode, $defaultlanguage) {

        $index = strtoupper($name . $mode . $language . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        // check for current mode/default language
        $index = strtoupper($name . $mode . $defaultlanguage . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        // should we break mode below to find a setting value???
        // check for default mode/current language
        $index = strtoupper($name . $defaultmode . $language . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        $index = strtoupper($name . $defaultmode . $defaultlanguage . $this->getObjectType());
        if (isset($this->settings[$index])) {
            return $this->settings[$index]->getValue();
        }

        return "";
    }

    function getSettingValue($name, $default = true) {
        return $this->getSetting($name, $default)->getValue();
    }

    function setSettingValue($name, $value) {

        /* determine index */
        $index = strtoupper($name . getSurveyMode() . getSurveyLanguage() . $this->getObjectType());

        /* don't store if follow generic/follow type (these are implicitly derived, 
         * so no need to store them explicitly). Also don't store if empty (if something should be empty
         * like a text, then enter &nbsp; for example as value).
         * If we had a value stored before, then we remove it now.
         */
        if (inArray($value, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            if (isset($this->settings[$index])) {
                $s = $this->settings[$index];
                if ($s != null) {
                    $s->remove();
                }
                unset($this->settings[$index]);
            }
            return;
        }

        /* update */
        if (isset($this->settings[$index])) {            
            $this->settings[$index]->setValue($value);
        }
        /* new */ else {

            /* create new */
            $setting = new Setting();
            $setting->setName($name);
            $setting->setSuid($this->getSuid());
            $setting->setObject($this->getObjectName());
            $setting->setObjectType($this->getObjectType());
            $setting->setLanguage(getSurveyLanguage());
            $setting->setMode(getSurveyMode());
            $setting->setValue($value);
            $this->settings[$index] = $setting;
        }
    }

    function hasSetting($name, $value) {

        if ($this->getSettingValue($name) == $value) {

            return true;
        }

        return false;
    }

    function getSettingsArray() {

        return $this->settings;
    }

    function getSettings() {
        global $db;
        $settings = array();
        $query = "select * from " . Config::dbSurvey() . "_settings where suid=" . prepareDatabaseString($this->getSuid()) . " and object=" . prepareDatabaseString($this->getObjectName()) . " and objecttype=" . prepareDatabaseString($this->getObjectType());
        if ($result = $db->selectQuery($query)) {
            if ($db->getNumberOfRows($result) > 0) {
                while ($row = $db->getRow($result)) {
                    $settings[] = new Setting($row);
                }
            }
        }        
        return $settings;
    }

    function setSettings($settings) {
        $this->settings = $settings;
    }
}

?>