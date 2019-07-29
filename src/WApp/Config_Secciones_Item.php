<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WApp;


class Config_Secciones_Item {
  public $id;
  public $title;
  public $folder;
  public $link;
  public $logo;
  public $uploads_default;
  public $uploads_dir;

  function __construct($id_section, $title) {
    $this->id    = $id_section;
    $this->title = $title;
  }

  public function logo($logo) {
    $this->logo = $logo;
  }
}
