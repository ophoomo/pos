<?php
    class Filesystem {

        public $path;

        private $file;
        private $name;

        public function setFile($file) {
            $this->file = $file;
            $this->name = $this->generate_name_file();
        }

        public function getName() {
            return $this->name;
        }

        public function upload() {
            $move = move_uploaded_file($this->file['tmp_name'], $this->path."/".$this->name);
            if (!$move) {
                return false;
            }
            return true;
        }

        public function delete($name) {
            unlink($this->path."/".$name);
        }

        private function generate_name_file() {
            $ext = pathinfo($this->file['name'], PATHINFO_EXTENSION);
            return date("Y-m-d")."_".uniqid().".".$ext;
        }

    }
?>