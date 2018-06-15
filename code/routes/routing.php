<?php

$routing = [
  [
    'method' => 'GET',
    'path' => '/obachan',
    'controller' => 'ObachanController',
    'action' => 'show'
  ],
  [
    'method' => 'POST',
    'path' => '/obachan',
    'controller' => 'ObachanController',
    'action' => 'create'
  ],
  [
    'method' => 'GET',
    'path' => '/hasumin',
    'controller' => 'HasuminController',
    'action' => 'show'
  ],
  [
    'method' => 'GET',
    'path' => '/oba',
    'controller' => 'ObaController',
    'action' => 'show'
  ]
];
