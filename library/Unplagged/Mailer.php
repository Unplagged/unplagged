<?php

/**
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 
 */
class Unplagged_Mailer{

  private $language;
  private $templatePath;
  private $templateName;

  public function __construct($templateName, $language = 'en'){
    $this->language = $language;
    $this->templateName = $templateName;
    $this->templatePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'emails' . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR;
    
    //check if we have the directory, so that we can at least provide english as the default
    if(!is_dir($this->templatePath)){
      $this->templatePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'emails' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR;
    }
  }

  /**
   * Sends a registration mail to a specific user to verify the users email address.
   * 
   * @Application_Model_User $user The user the mail sent to.
   * 
   * @return Whether the mail was sent or not.
   * @deprecated
   */
  public static function sendRegistrationMail(Application_Model_User $user){
    $config = Zend_Registry::get('config');

    $html = new Zend_View();
    $html->assign('verificationLink', $config->default->applicationUrl . $config->link->accountVerification . $user->getVerificationHash());

    $html->setScriptPath(APPLICATION_PATH . '/views/emails/de/plain/');
    $bodyText = $html->render('registration.phtml');

    $bodyText .= Unplagged_Mailer::getFooter();

    $mail = new Zend_Mail('utf-8');
    $mail->setBodyText($bodyText);
    $mail->setFrom($config->default->senderEmail, $config->default->senderName);
    $mail->addTo($user->getEmail());
    $mail->setSubject($config->default->portalName . ' Registration verification required');

    $mail->send();

    return true;
  }

  public function sendMail(Application_Model_User $user, $subject){
    $config = Zend_Registry::get('config');

    $mailView = new Zend_View();
    $mailView->assign('verificationLink', $config->default->applicationUrl . $config->link->accountVerification . $user->getVerificationHash());
    $mailView->assign('sender', $config->default->senderName);
    $mailView->assign('recipient', $user->getUsername());
    
    $bodyText = $this->getBodyContent($mailView);;
    $bodyHtml = $this->getBodyContent($mailView, 'html');

    $mail = new Zend_Mail('utf-8');
    $mail->setBodyText($bodyText);
    $mail->setBodyHtml($bodyHtml);
    $mail->setFrom($config->default->senderEmail, $config->default->senderName);
    $mail->addTo($user->getEmail());
    $mail->setSubject($subject);

    $mail->send();

    return true;
  }

  /**
   * Tries to load the specified type of body contentn from a template file.
   * @param type $mailView
   * @param type $type
   * @return type 
   */
  private function getBodyContent($mailView, $type = 'plain'){
    $bodyHtml = '';
    if(file_exists($this->templatePath . $type . DIRECTORY_SEPARATOR . $this->templateName)){
      $mailView->setScriptPath($this->templatePath . $type . DIRECTORY_SEPARATOR);
      $bodyHtml = $mailView->render($this->templateName);
    }
    
    return $bodyHtml;
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

  public static function sendPasswordRecoveryMail(Application_Model_User $user){
    $config = Zend_Registry::get('config');

    $bodyText = 'You or someone else started a password recovery for the account associated with' . "\r" . "\n";
    $bodyText .= 'this E-Mail address. If it wasn\'t you, simply ignore this mail. ' . "\r" . "\n" . "\r" . "\n";

    $bodyText .= 'Otherwise click the following link: ' . "\r" . "\n";
    $bodyText .= $config->default->applicationUrl . $config->link->passwordRecovery . $user->getVerificationHash() . "\r" . "\n" . "\r" . "\n";

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

  private static function getFooter(){
    $config = Zend_Registry::get('config');

    $footerText = "\r" . "\n";
    $footerText .= 'Your team of ' . "\r" . "\n";
    $footerText .= $config->default->senderName;

    return $footerText;
  }

}
?>
