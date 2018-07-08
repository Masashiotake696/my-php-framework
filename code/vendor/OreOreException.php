<?php

class OreOreException extends Exception {
    public function __construct($message) {
        parent::__construct($message, 800);
    }
}
