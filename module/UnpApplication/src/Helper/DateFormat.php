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
namespace UnpApplication\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper that can be used to format dates.
 */
class DateFormat extends AbstractHelper{

  /**
   * Creates an easily readable version of the relation between two dates.
   * 
   * The second parameter is mostly for testing purposes to avoid problems 
   * with the undeterminism of dates.
   * 
   * @param \DateTime $date A date in the past.
   * @param \DateTime $compareTo A more current date from which the difference should
   * be calculated
   * @return string
   */
  public function expiredTime(\DateTime $date, \DateTime $compareTo = null){
    if(!$compareTo){
      $compareTo = new \DateTime('now');
    }
    $dateInterval = $date->diff($compareTo, true);

    if($dateInterval->y > 0){
      return sprintf($this->view->translatePlural('a year ago', '%s years ago', $dateInterval->y), $dateInterval->y);
    }
    if($dateInterval->m > 0){
      return sprintf($this->view->translatePlural('a month ago', '%s months ago', $dateInterval->m), $dateInterval->m);
    }
    if($dateInterval->d > 0){
      $weeks = floor($dateInterval->d / 7);
      if($weeks > 0){
        return sprintf($this->view->translatePlural('a week ago', '%s weeks ago', $weeks), $weeks);
      }
      return sprintf($this->view->translatePlural('yesterday at %2$s', '%1$s days ago at %2$s', $dateInterval->d),
                      $dateInterval->d, $date->format('H:i'));
    }
    if($dateInterval->h > 0){
      return sprintf($this->view->translatePlural('an hour ago', '%s hours ago', $dateInterval->h), $dateInterval->h);
    }
    if($dateInterval->i > 0){
      return sprintf($this->view->translatePlural('a minute ago', '%s minutes ago', $dateInterval->i), $dateInterval->i);
    }
    if($dateInterval->s > 0){
      return $this->view->translate('a few seconds ago');
    }

    return $this->view->translate('Now');
  }

}