<?php

namespace Surcouf\Cookbook;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Surcouf\Cookbook\Mail\SmtpConf;

if (!defined('CORE2'))
  exit;

class Mail {

  private $mailprovider;

  public function __construct() {
    $this->mailprovider = new PHPMailer(DEBUG);
    $this->mailprovider->isSMTP();
    $this->mailprovider->Host = SmtpConf::SMTP_HOST;
    $this->mailprovider->SMTPAuth = (!is_null(SmtpConf::SMTP_USER));
    $this->mailprovider->Username = SmtpConf::SMTP_USER;
    $this->mailprovider->Password = SmtpConf::SMTP_PASSWORD;
    $this->mailprovider->SMTPSecure = SmtpConf::SMTP_SECURE;
    $this->mailprovider->Port = SmtpConf::SMTP_PORT;
    $this->mailprovider->setFrom(SmtpConf::SMTP_FROM_MAIL, SmtpConf::SMTP_FROM_NAME);
    $this->mailprovider->isHTML(true);
  }

  public function send(string $toName, string $toMail, string $subject, array $content, array &$response) : bool {
    global $Controller, $twig;
    $body = $twig->render('mails/body.html.twig', $content);
    $this->mailprovider->addAddress($toMail, $toName);
    $this->mailprovider->Subject = $subject;
    $this->mailprovider->Body = $body;
    try {
      $this->mailprovider->send();
      return true;
    } catch (Exception $e) {
      $response = $Controller->Config()->getResponseArray(210);
      $response['message'] .= $this->mailprovider->ErrorInfo;
      return false;
    }
  }

}
