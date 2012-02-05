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
class Application_Form_Case_Modify extends Zend_Form{

  /**
   * Creates the form to create a new case.
   * 
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/case/create");

    $nameElement = new Zend_Form_Element_Text('name');
    $nameElement->setLabel("Name");
    $nameElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $nameElement->addValidator('stringLength', false, array(2, 64));
    $nameElement->setRequired(true);

    $aliasElement = new Zend_Form_Element_Text('alias');
    $aliasElement->setLabel("Pseudonym");
    $aliasElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $aliasElement->addValidator('stringLength', false, array(2, 64));
    $aliasElement->setAttrib('maxLength', 64);
    $aliasElement->setRequired(false);

    $collaborator = new Zend_Form_Element_Text('collaborator');
    /** @todo validator doesn't exist, and required is probably not necessary */
    //$collaborator->addValidator('Member', false, array("min" => "2", "name" => "collaborator", "skipUsersFrom" => array()));
    //$collaborator->setRequired(true);
    $collaborator->setLabel('Insert the names of your reviewers.');
    $default_reviewers["readonly"] = (($this->_readonly) ? "true" : "false");
    /* foreach($article->getReviewers() as $user)
      {
      $default_reviewers[$user->getId()] = "true";
      } */
    $collaborator->setDecorators(array(array('ViewScript', array(
          'viewScript'=>'user/_element.phtml',
          'callBack'=>'/user/autocomplete-names',
          'default'=>$default_reviewers,
          'skipAlsoUsers'=>array()
      ))));

    $tags = new Zend_Form_Element_Text('tags');
    $tags->setLabel('Insert tags for your article.');
    $default_tags["readonly"] = (($this->_readonly) ? "true" : "false");
    /* foreach($case->getTags() as $tag)
      {
      $default_tags[$tag->getId()] = "true";
      } */
    $tags->setDecorators(array(array('ViewScript', array(
          'viewScript'=>'tag/_element.phtml',
          'callBack'=>'/tag/autocomplete-titles',
          'default'=>$default_tags
      ))));

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $nameElement
      , $aliasElement
      , $tags
      , $collaborator
    ));

    $this->addDisplayGroup(array('name', 'alias', 'tags', 'collaborator')
        , 'credentialGroup'
        , array('legend'=>'Case creation')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }

}
?>