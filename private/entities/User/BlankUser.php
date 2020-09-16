<?php

namespace Surcouf\Cookbook\User;

use Surcouf\Cookbook\Mail;
use Surcouf\Cookbook\Helper\HashHelper;
use \DateTime;

if (!defined('CORE2'))
  exit;

class BlankUser extends User {

  public function __construct(string $firstname, string $lastname, string $username, string $email) {
    $this->firstname = $firstname;
    $this->lastname = $lastname;
    $this->mailadress = $email;
    $this->mailvalidationcode = HashHelper::generate_token(12);
    $this->name = $firstname.' '.$lastname;
    $this->passwordhash = '********';
    $this->registrationCompleted = new DateTime();
    $this->username = $username;
  }

  public function sendActivationMail(array &$response) : bool {
    global $Controller, $twig, $OUT;
    $mail = new Mail();
    $OUT['ActivationLink'] = $Controller->getLink('private:activation', $this->mailvalidationcode);
    $data = [
      'Headline' => $Controller->l('sendmail_registration_activationMail_title'),
      'Content' => $twig->render('mails/activation-mail.html.twig', $OUT),
    ];
    if ($mail->send($this->name, $this->mailadress, $Controller->l('sendmail_registration_activationMail_subject'),  $data, $response)) {
      return true;
    }
    return false;
  }

  public function save(array &$response) : bool {
    global $Controller;
    $result = $Controller->insertSimple('users',
      ['user_name', 'user_firstname', 'user_lastname', 'user_fullname', 'user_password', 'user_email', 'user_email_validation', 'user_registration_completed'],
      [$this->username, $this->firstname, $this->lastname, $this->name, $this->passwordhash, $this->mailadress, $this->mailvalidationcode, $this->registrationCompleted->format(DTF_SQL)]
    );
    if ($result > -1) {
      $this->id = $result;
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    $response['message'] = $Controller->dberror();
    return false;
  }

}
