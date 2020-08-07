<?php

namespace Surcouf\PhpArchive\Mail;

if (!defined('CORE2'))
  exit;

class Server {

  private $host, $port;

  function __construct($data) {
    $this->host = $data['server'];
    $this->port = intval($data['port']);
  }

  function getPort() {
    return $this->port;
  }

  function getServer() {
    return $this->host;
  }

}
