<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Unplagged_Mailer{

  /**
   * Sends a registration mail to a specific user to verify the users email address.
   * 
   * @Application_Model_User $user The user the mail sent to.
   * 3
   * @return Whether the mail was sent or not.
   */
  public static function sendRegistrationMail(Application_Model_User $user){
    $config = Zend_Registry::get('config');

    $bodyText = 'Thanks for your registration.' . "\r" . "\n" . "\r" . "\n";
    $bodyText .= 'Please click the following link, to verify your account: ' . "\r" . "\n";
    $bodyText .= $config->link->accountVerification . $user->getVerificationHash() . "\r" . "\n";

    $bodyText .= Unplagged_Mailer::getFooter();

    $mail = new Zend_Mail('utf-8');
    $mail->setBodyText($bodyText);
    $mail->setFrom($config->default->senderEmail, $config->default->senderName);
    $mail->addTo($user->getEmail());
    $mail->setSubject($config->default->portalName . ' Registration verification required');

    $mail->send();

    return true;
  }

  /**
   * Sends a registration mail to a specific user to verify the users email address.
   * 
   * @Application_Model_User $user The user the mail sent to.
   * 
   * @return Whether the mail was sent or not.
   */
  public static function sendActivationMail(Application_Model_User $user){
    $config = Zend_Registry::get('config');

    $bodyText = 'Thanks for verifying your account.' . "\r" . "\n" . "\r" . "\n";
    $bodyText .= 'You now can use our website. ' . "\r" . "\n";
    
    $bodyText .= Unplagged_Mailer::getFooter();

    $mail = new Zend_Mail('utf-8');
    $mail->setBodyText($bodyText);
    $mail->setFrom($config->default->senderEmail, $config->default->senderName);
    $mail->addTo($user->getEmail());
    $mail->setSubject($config->default->portalName . ' Account successfully verified');
    $mail->send();

    return true;
  }
  
    /**
   * Sends a password recovery mail to a specific user.
   * 
   * @Application_Model_User $user The user the mail sent to.
   * 
   * @return Whether the mail was sent or not.
   */
  public static function sendPasswordRecoveryMail(Application_Model_User $user){
    $config = Zend_Registry::get('config');

    $bodyText = 'You or someone else started a password recovery for the account associated with' . "\r" . "\n";
    $bodyText .= 'this E-Mail address. If it wasn\'t you, simply ignore this mail. ' . "\r" . "\n" . "\r" . "\n";
    
    $bodyText .= 'Otherwise click the following link: ' . "\r" . "\n";
    $bodyText .= $config->link->passwordRecovery . $user->getVerificationHash() . "\r" . "\n" . "\r" . "\n";

    $bodyText .= 'Username: ' . $user->getUsername() . "\r" . "\n";

    $bodyText .= Unplagged_Mailer::getFooter();

    $mail = new Zend_Mail('utf-8');
    $mail->setBodyText($bodyText);
    $mail->setFrom($config->default->senderEmail, $config->default->senderName);
    $mail->addTo($user->getEmail());
    $mail->setSubject($config->default->portalName . ' Password recovery');
    $mail->send();

    return true;
  }

  /**
   * Sends a detection report finished mail.
   * 
   * Application_Model_Document_Page_DetectionReport $reoirt The report that was created.
   * 
   * @return Whether the mail was sent or not.
   */
  public static function sendDetectionReportAvailable(Application_Model_Document_Page_DetectionReport &$report){
    $config = Zend_Registry::get('config');

    $documentTitle = $report->getPage()->getDocument()->getTitle();
    $pageNumber = $report->getPage()->getPageNumber();
    $toEmail = $report->getUser()->getEmail();

    $bodyText = 'There is a plagiarism detection report available.' . "\r" . "\n" . "\r" . "\n";
    $bodyText .= 'Document. ' . $documentTitle . "\r" . "\n";
    $bodyText .= 'Page. ' . $pageNumber . "\r" . "\n";
    
    $bodyText .= Unplagged_Mailer::getFooter();

    $mail = new Zend_Mail('utf-8');
    $mail->setBodyText($bodyText);
    $mail->setFrom($config->default->senderEmail, $config->default->senderName);
    $mail->addTo($toEmail);
    $mail->setSubject($config->default->portalName . '  Plagiarism Detection Report available');
    $mail->send();

    return true;
  }
  
  private static function getFooter() {
    $config = Zend_Registry::get('config');
    
    $footerText = "\r" . "\n";
    $footerText .= 'Your team of ' . "\r" . "\n";
    $footerText .= $config->default->senderName;
    
    return $footerText;
  }

}

?>
