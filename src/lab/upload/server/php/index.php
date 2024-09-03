<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

require_once("../../../../constants.php");
require_once("../../../../functions.php");
require_once("../../../../dbConfig.php");

$_SESSION['SYSTEM_ENTRY'] = USCIC_SMS;
$loaded = dbConfig::load("../../../../conf.php");
require_once("../../../../config.php");
require_once("../../../../globals.php");

if (Config::allowUpload() == false) {
    exit;
}

if (decryptC(loadvar("k"), Config::smsComponentKey()) != Config::uploadAccessKey()) {
    exit;
}

$options = array(
    'delete_type' => 'POST',
    'db_host' => Config::dbServer(),
    'db_user' => Config::dbUser(),
    'db_pass' => Config::dbPassword(),
    'db_name' => Config::dbName(),
    'db_table' => Config::dbSurveyData() . '_files'
);

error_reporting(E_ALL | E_STRICT);
require_once('UploadHandler.php');

class CustomUploadHandler extends UploadHandler {

    protected function initialize() {
        $this->db = new mysqli(
                $this->options['db_host'], $this->options['db_user'], $this->options['db_pass'], $this->options['db_name']
        );
        parent::initialize();
        $this->db->close();
    }

    protected function handle_form_data($file, $index) {
        $file->title = @$_REQUEST['title'][$index];
        $file->description = @$_REQUEST['description'][$index];
    }

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
        //    $file = parent::handle_file_upload($uploaded_file, $name, $size, $type, $error, $index, $content_range);
        $file = new \stdClass();

        $file->name = $name;
        $file->size = $size;
//         $file->content = json_encode($uploaded_file);
        $file->content = str_replace(array('"', '\\'), "", $uploaded_file);
        if (file_exists($file->content)) {
            $test = file_get_contents($file->content);
            $file->description = strlen($test);
        }
        $file->labbarcode = $_GET['labbarcode'];
        $file->urid = $_GET['urid'];

        //require_once('functions.php');
        if (empty($file->error)) {
            //AES_ENCRYPT(content, "basbas")
            $sql = 'INSERT INTO `' . $this->options['db_table']
                    . '` (`name`, `size`, `urid`, `labbarcode`, `description`, `content`)'
                    . ' VALUES (?, ?, ?, ?, ?, aes_encrypt(?, "' . Config::filePictureKey() . '"))';
            $query = $this->db->prepare($sql);
            $query->bind_param(
                    'siisss', $file->name, $file->size, $file->urid, $file->labbarcode, $file->description, $test
            );
            $query->execute();
            $file->id = $this->db->insert_id;
        }
        return $file;
    }

    protected function set_additional_file_properties($file) {
        parent::set_additional_file_properties($file);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $sql = 'SELECT `id`, `type`, `title`, `description` FROM `'
                    . $this->options['db_table']; //.'` WHERE `name`=?';
            $query = $this->db->prepare($sql);
            // $query->bind_param('s', $file->name);
            $query->execute();
            $query->bind_result(
                    $id, $type, $title, $description
            );
            while ($query->fetch()) {
                $file->id = $id;
                //   $file->type = $type;
                $file->title = $title;
                $file->description = $description;
            }
        }
    }

    public function delete($print_response = true) {
        $response = parent::delete(false);
        foreach ($response as $name => $deleted) {
            if ($deleted) {
                $sql = 'DELETE FROM `'
                        . $this->options['db_table'] . '` WHERE `name`=?';
                $query = $this->db->prepare($sql);
                $query->bind_param('s', $name);
                $query->execute();
            }
        }
        return $this->generate_response($response, $print_response);
    }

}

$upload_handler = new CustomUploadHandler($options);

?>
