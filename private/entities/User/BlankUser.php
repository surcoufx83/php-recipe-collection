<?php

namespace Surcouf\Cookbook\User;

use Surcouf\Cookbook\Mail;
use Surcouf\Cookbook\Helper\HashHelper;
use \DateTime;

if (!defined('CORE2'))
  exit;

class BlankUser extends User {

  public function __construct(string $firstname, string $lastname, string $username, string $email) {
    $this->user_name = $username;
    $this->user_firstname = $firstname;
    $this->user_lastname = $lastname;
    $this->user_fullname = $firstname.' '.$lastname;
    $this->user_password = '********';
    $this->user_email = $email;
    $this->user_email_validation = HashHelper::generate_token(12);
    $this->user_registration_completed = new DateTime();
  }

  public function sendActivationMail(array &$response) : bool {
    global $Controller, $twig, $OUT;
    $mail = new Mail();
    $OUT['ActivationLink'] = $Controller->getLink('private:activation', $this->user_email_validation);
    $data = [
      'Headline' => $Controller->l('sendmail_registration_activationMail_title'),
      'Content' => $twig->render('mails/activation-mail.html.twig', $OUT),
    ];
    if ($mail->send($this->user_fullname, $this->user_email, $Controller->l('sendmail_registration_activationMail_subject'),  $data, $response)) {
      return true;
    }
    return false;
  }

  public function save(array &$response) : bool {
    global $Controller;
    $result = $Controller->insertSimple('users',
      ['user_name', 'user_firstname', 'user_lastname', 'user_fullname', 'user_password', 'user_email', 'user_email_validation', 'user_registration_completed'],
      [$this->user_name, $this->user_firstname, $this->user_lastname, $this->user_fullname, $this->user_password, $this->user_email, $this->user_email_validation, $this->user_registration_completed->format(DTF_SQL)]
    );
    if ($result > -1) {
      $this->user_id = $result;
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    $response['message'] = $Controller->dberror();
    return false;
  }

}
