<?php

namespace Surcouf\PhpArchive\Mail;

if (!defined('CORE2'))
  exit;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Account {

  private $login, $name, $password;
  private $imap, $smtp;
  private $sendcode, $sendmessage, $mailerror;

  private $sendto = array(), $sendcc = array(), $sendfn, $sendsubj, $sendbody, $sendfile;

  function __construct($data) {
    $this->login = $data['account']['login'];
    $this->name = $data['account']['name'];
    $this->password = $data['account']['password'];
    $this->imap = new Server($data['imap']);
    $this->smtp = new Server($data['smtp']);
    /*$box = imap_open('{'.$this->imap->getServer().':'.$this->imap->getPort().'/ssl}', $this->login, $this->password);
    var_dump($box);
    var_dump(imap_num_msg($box));
    if ($box !== false)
      imap_close($box);

    exit;*/
  }

  public function getDataArray() : array {
    return array(
      'account' => array(
        'name' => $this->name,
        'login' => $this->login,
        'password' => $this->password,
      ),
      'imap' => array(
        'server' => $this->imap->getServer(),
        'port' => $this->imap->getPort(),
      ),
      'smtp' => array(
        'server' => $this->smtp->getServer(),
        'port' => $this->smtp->getPort(),
      )
    );
  }

  public function getLastErrorcode() {
    return $this->sendcode;
  }

  public function getLastErrormessage() {
    return $this->sendmessage;
  }

  public function getDebugSendData() {
    return array($this->sendto, $this->sendcc, $this->sendfn, $this->sendsubj, $this->sendbody, $this->sendfile, $this->sendcode, $this->sendmessage);
  }

  public function sendMailWithAttachment($to, $cc, $fn, $subj, $body, $file) {
    if (!$this->sendMailWithAttachment_prepareTo($to)) {
      $this->sendcode = 400;
      $this->sendmessage = 'Error validating email receipients';
      return false;
    }
    if (!$this->sendMailWithAttachment_prepareCc($cc)) {
      $this->sendcode = 401;
      $this->sendmessage = 'Error validating email receipients';
      return false;
    }
    if (!$this->sendMailWithAttachment_prepareFile($file, $fn)) {
      $this->sendcode = 402;
      $this->sendmessage = 'Error preparing file information';
      return false;
    }
    if ($subj == '') {
      $this->sendcode = 403;
      $this->sendmessage = 'Subject is empty';
      return false;
    } else {
      $this->sendsubj = $subj;
    }
    if ($body == '') {
      $this->sendcode = 403;
      $this->sendmessage = 'Body is empty';
      return false;
    } else {
      $this->sendbody = $body;
    }
    if (!$this->sendMail()) {
      $this->sendcode = 404;
      $this->sendmessage = $this->mailerror;
      return false;
    }
    return true;
  }

  private function sendMail() {
    $mail = new PHPMailer(true);
    try {
      //Server settings
      $mail->SMTPDebug = SMTP::DEBUG_OFF;
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();
      $mail->Host       = $this->smtp->getServer();
      $mail->SMTPAuth   = true;
      $mail->Username   = $this->login;
      $mail->Password   = $this->password;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port       = $this->smtp->getPort();

      //Recipients
      $mail->setFrom($this->name);
      for ($t=0; $t<count($this->sendto); $t++) {
        $mail->addAddress($this->sendto[$t]);
      }
      for ($c=0; $c<count($this->sendcc); $c++) {
        $mail->addCC($this->sendcc[$c]);
      }

      if (!is_null($this->sendfile)) {
        if (!is_null($this->sendfn) && $this->sendfn != '')
          $mail->addAttachment($this->sendfile->getPath(), $this->sendfn);
        else
          $mail->addAttachment($this->sendfile->getPath());
      }

      // Content
      $mail->isHTML(false);
      $mail->Subject = $this->sendsubj;
      $mail->Body    = $this->sendbody;

      $mail->send();
      return true;
    } catch (Exception $e) {
      $this->mailerror = $mail->ErrorInfo;
      return false;
    }
  }

  private function sendMailWithAttachment_checkRecipient($mailadr) {
    return filter_var($mailadr, FILTER_VALIDATE_EMAIL);
  }

  private function sendMailWithAttachment_prepareCc($data) {
    if (is_array($data)) {
      for($i=0; $i<count($data); $i++) {
        $res = $this->sendMailWithAttachment_checkRecipient(trim($data[$i]));
        if ($res === false)
          return false;
        $this->sendcc[] = $res;
      }
      return true;
    } else {
      if ($data === '') {
        return true;
      } else if (strpos($data, ',') !== false) {
        return $this->sendMailWithAttachment_prepareTo(explode(',', $data));
      } else if (strpos($data, ';') !== false) {
        return $this->sendMailWithAttachment_prepareTo(explode(';', $data));
      } else {
        $res = $this->sendMailWithAttachment_checkRecipient(trim($data));
        if ($res === false)
          return false;
        $this->sendcc[] = $res;
        return true;
      }
    }
  }

  private function sendMailWithAttachment_prepareFile($fileobj, $filename) {
    if (is_null($fileobj))
      return false;
    if (!$fileobj->stillExists())
      return false;
    if (!$fileobj->getExtensionObject()->allowMailsend())
      return false;
    if ($filename == '')
      $filename = $fileobj->getName();
    $this->sendfn = $filename;
    $this->sendfile = $fileobj;
    return true;
  }

  private function sendMailWithAttachment_prepareTo($data) {
    if (is_array($data)) {
      for($i=0; $i<count($data); $i++) {
        $res = $this->sendMailWithAttachment_checkRecipient(trim($data[$i]));
        if ($res === false)
          return false;
        $this->sendto[] = $res;
      }
      return true;
    } else {
      if ($data === '') {
        return false;
      } else if (strpos($data, ',') !== false) {
        return $this->sendMailWithAttachment_prepareTo(explode(',', $data));
      } else if (strpos($data, ';') !== false) {
        return $this->sendMailWithAttachment_prepareTo(explode(';', $data));
      } else {
        $res = $this->sendMailWithAttachment_checkRecipient(trim($data));
        if ($res === false)
          return false;
        $this->sendto[] = $res;
        return true;
      }
    }
  }

}
