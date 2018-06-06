<?php

class OthersController extends DatabaseController {
  public function action() {
    $this->db->exec("INSERT INTO tests (testint, testchar5) VALUES (111, 'aa')");
    $this->db->exec("INSERT INTO tests (testint, testchar5) VALUES ('aaaa', 'aa')");
    $this->db->exec("INSERT INTO tests (testint, testchar5) VALUES (222, 'aa')");
  }
}
