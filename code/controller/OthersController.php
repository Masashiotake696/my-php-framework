<?php

class OthersController extends BaseController {
  public function action() {
    $this->db->exec("INSERT INTO tests (testint, testchar5) VALUES (111, 'aa')");
  }
}
