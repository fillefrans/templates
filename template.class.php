<?php


  class Template {

    private   $filename = "";
    private   $basename = "";
    private   $longname = "";

    // the raw template string
    private   $raw      = "";
    private   $rendered = "";
    private   $data     = array();

    private   $width    = 0;
    private   $height   = 0;



    public function __construct ($filename) {

      if(!file_exists($filename)) {
        return;
      }

      $this->filename = $filename;
      $this->longname = str_replace(".html", "", TemplateList::folderToKey($filename));

      $this->basename = basename($filename, ".html");

      $this->raw = file_get_contents($filename);

      $this->init();

    }


    private function init () {
      $adSize = Template::getSizeFromFilename($this->basename);

      $this->width  = $adSize['w'];
      $this->height = $adSize['h'];

      if(count($adSize) >= 2) {
        $this->data['adwidth']  = $adSize['w'];
        $this->data['adheight'] = $adSize['h'];
      } 

      if(!isset($this->data['imagesrc'])) {
        // default image
        $this->data['imagesrc'] = "assets/images/test.jpg";
      }

    }





    /**
     *    utility functions
     * 
     */

    private static function getSizeFromFilename ($filename) {
      if( strpos($filename, 'x') === false ) {
        return null;
      }

      $size = array();

      $nameParts = explode("x", $filename);

      $size['w'] = intval($nameParts[0], 10);
      $size['h'] = intval($nameParts[1], 10);

      return $size;
    }


    private static function toRegex ($keyArray) {

      if(!is_array($keyArray)) {
        return null;
      }

      $i = count($keyArray)-1;

      while($i >= 0) {
        // regex to match {key*}
        $keyArray[$i] = "/\{(" . $keyArray[$i] . ")([^}]*)\}/";
        $i--;
      }
      return $keyArray;
    }



    public function render ($contents = null, $showsource = false, $toString = false) {

      $this->rendered = "";

      if($contents && is_array($contents)) {
        foreach ($contents as $key => $value) {
          $this->data[$key] = $value;
        }
      }


      $keys   = Template::toRegex(array_keys($this->data));
      $values = array_values($this->data);

      $this->rendered = preg_replace($keys, $values, $this->raw);

      if($toString === true) {
        return $this->rendered;
      }
      else {
        print("<p /><a name='{$this->longname}' href='#{$this->longname}'>{$this->basename}</a>\n");
        print($this->rendered);
      }

      if($showsource) {
        $this->showSource();
      }

    }


    public function showSource ($toString = false) {

      if($toString === true) {
        return $this->raw;
      }
      else {
        print(
              "<div style='width:{$this->width}px;text-align:center;'><a name='#{$this->longname}-source' href='#{$this->longname}-source'>source</a>" . 
              "</div>" . '<textarea id="'. $this->longname .'-source" class="sourcecode">' . $this->raw . '</textarea>' .
              "<div id='". $this->longname ."-editor'></div>"
              );
      }
    }

  }


?>