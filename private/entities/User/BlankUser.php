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
    $this->name = $firstname.' '.$lastname;
    $this->mailadress = $email;
    $this->username = $username;
    $this->registrationCompleted = new DateTime();
  }

  public function sendActivationMail(array &$response) : bool {
    global $Controller, $twig, $OUT;
    $mail = new Mail();
    $this->mailvalidationcode = HashHelper::generate_token(12);
    $OUT['ActivationLink'] = $Controller->getLink('private:activation', $this->mailvalidationcode);
    $data = [
      'Headline' => $Controller->l('sendmail_registration_activationMail_title'),
      'Content' => $twig->render('mails/activation-mail.html.twig', $OUT),
    ];
    if ($mail->send($this->name, $this->mailadress, $Controller->l('sendmail_registration_activationMail_subject'),  $data, $response)) {
      $this->changes['user_email_validation'] = $this->mailvalidationcode;
      $Controller->updateDbObject($this);
      return true;
    }
    return false;
  }

  public function save(array &$response) : bool {
    global $Controller;
    $result = $Controller->insertSimple('users',
      ['user_name', 'user_firstname', 'user_lastname', 'user_fullname', 'user_password', 'user_email', 'user_registration_completed'],
      [$this->username, $this->firstname, $this->lastname, $this->name, '********', $this->mailadress, $this->registrationCompleted->format(DTF_SQL)]
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
