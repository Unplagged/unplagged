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
 * 
 * @todo make multilingual
 */
class DateFormat extends AbstractHelper{

  public function expiredTime(\DateTime $date){
    $now = new \DateTime('now');
    $dateInterval = $date->diff($now, true);

    if($dateInterval->y > 0){
      return sprintf('%s years ago', $dateInterval->y);
    }
    if($dateInterval->m > 0){
      return sprintf('%s months ago', $dateInterval->m);
    }
    if($dateInterval->d > 0){
      $weeks = floor($dateInterval->d / 7);
      if($weeks > 0){
        return sprintf('%s weeks ago', $weeks);
      }
      if($dateInterval->d === 1){
        return sprintf('yesterday at %s', $date->format('H:i'));
      }
      return sprintf('%s days ago at %s', $dateInterval->d, $date->format('H:i'));
    }
    if($dateInterval->h > 0){
      return sprintf('%s hours ago', $dateInterval->h);
    }
    if($dateInterval->m > 0){
      return sprintf('%s minutes ago', $dateInterval->m);
    }

    return sprintf('just now');
  }

}