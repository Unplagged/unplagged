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
 * This class can be used to find and highlight similarities in text corpora. It basically uses a variant
 * of an algorithm for the 
 * {@link 
 * http://en.wikipedia.org/wiki/Longest_common_substring_problemhttp://en.wikipedia.org/wiki/Longest_common_substring_problem 
 * longest common substring} problem, but with words instead of characters as the basis.
 * 
 * It needs two lists of lines as input data that should be compared.
 * 
 * It is based on the work done by {@link http://de.vroniplag.wikia.com/ VroniPlag} user "Marcusb". The original 
 * javascript sources can be found under {@link http://de.vroniplag.wikia.com/wiki/MediaWiki:Fragcolors/code.js}
 * (last accessed Nov. 9th 2012).
 */
class Unplagged_CompareText{

  private $wordSimilarityMinimum;
  private $markerCount;
  private $originalLeftSide;
  private $originalRightSide;

  /**
   * @param int $wordSimilarityMinimum The count of words which are minimally needed for the 
   * matches to be marked as a similarity.
   * @param int $markerCount The number of different markers to highlight the text, repeat afterwards. 
   */
  public function __construct($wordSimilarityMinimum = 3, $markerCount = 9){
    $this->wordSimilarityMinimum = $wordSimilarityMinimum;
    $this->markerCount = $markerCount;
  }

  /**
   * Takes two arrays of lines and compares them.
   * 
   * @param array $linesLeft
   * @param array $linesRight
   * @return type
   */
  public function compare(array $listLeft, array $listRight){
    $this->originalLeftSide = $listLeft;
    $this->originalRightSide = $listRight;

    $wordsLeft = $this->buildSingleWordsArray($listLeft);
    $wordsRight = $this->buildSingleWordsArray($listRight);
    $matchList = $this->compareText($wordsLeft, $wordsRight);

    return $this->getMarkedTexts($matchList, $wordsLeft, $wordsRight);
  }

  private function removePunctuationMarks($string = ''){
    return preg_replace("/[^a-zA-Z0-9]+/", "", $string);
  }

  /**
   * Builds a continuously indexed array of all words from the 
   * given array of lines.
   * 
   * @param array $lines
   * @return array
   */
  private function buildSingleWordsArray(array $lines){
    //foreach($lines as $key=> $line){
    //  $lines[$key] = $this->removePunctuationMarks($line);
    //}

    $concatenatedLines = implode(' ', $lines);

    return array_values(explode(' ', $concatenatedLines));
  }

  /**
   * Uses a dynamic programming algorithm to compute an array of matches of the two texts. 
   * 
   * @param array $wordsLeft
   * @param array $wordsRight
   * @return array
   */
  private function compareText($wordsLeft, $wordsRight){
    $documents = array($wordsLeft, $wordsRight);
    $documents_len = array();

    //the resulting matches defined by the starting point in the documents and the length
    $matchesList = array();
    //matrix MxN with M=sizeof(wordsLeft) N = sizeof($wordsRight)
    $matchTable = array();

    foreach($documents as $documentIndex=> $doc){
      $wordCount = sizeof($doc);
      $tokens = $wordCount - $this->wordSimilarityMinimum + 1;

      if($tokens > 0){
        $documents_len[$documentIndex] = $wordCount;
        $min_token_idx = 0;

        for($token_idx = 0; $token_idx < $tokens; $token_idx++){
          $match = array_slice($doc, $token_idx, $this->wordSimilarityMinimum);
          $match_loc = array($documentIndex, $token_idx);
          $match_tag = implode(' ', $match);

          if(array_key_exists($match_tag, $matchTable)){
            if($token_idx >= $min_token_idx){
              $best_match = array($documentIndex, $token_idx, -1, 0, 0);
              $matches = $matchTable[$match_tag];
              
              foreach($matches as $match_peer){
                $peer_doc_idx = $match_peer[0];
                $peer_doc = $documents[$peer_doc_idx];
                $peer_token_idx = $match_peer[1] + $this->wordSimilarityMinimum;
                $peer_len = $documents_len[$peer_doc_idx];
                $our_token_idx = $token_idx + $this->wordSimilarityMinimum;

                if($peer_doc_idx == $documentIndex){
                  continue;
                }

                while($peer_token_idx < $peer_len
                && $our_token_idx < $wordCount
                && $peer_doc[$peer_token_idx] == $doc[$our_token_idx]){
                  $peer_token_idx++;
                  $our_token_idx++;
                }

                $len = $our_token_idx - $token_idx;
                if($len > $best_match[4]){
                  $best_match[2] = $match_peer[0];
                  $best_match[3] = $match_peer[1];
                  $best_match[4] = $len;
                }
              }

              if($best_match[2] != -1){
                array_push($matchesList, $best_match);
                $min_token_idx = $token_idx + $best_match[4];
              }
            }
            array_push($matchTable[$match_tag], $match_loc);
          }else{
            $matchTable[$match_tag] = array($match_loc);
          }
        }
      }
    }
    return $matchesList;
  }

  /**
   * Adds html tags to both of the compared texts in order to highlight the similarities.
   * 
   * @param type $textMatches
   * @param type $wordsLeft
   * @param type $wordsRight
   * @return type
   */
  private function getMarkedTexts($textMatches, $wordsLeft, $wordsRight){
    foreach($textMatches as $markIndex=> $match){
      $markClasses = 'fragmark fragmark-' . $markIndex % $this->markerCount;
      $wordsLeft = $this->wrapMatchInSpanTag($wordsLeft, $match[3], $match[4], $markClasses);
      $wordsRight = $this->wrapMatchInSpanTag($wordsRight, $match[1], $match[4], $markClasses);
    }

    // At this point the text is exploded word by word, now the line breaks have to be added again, 
    // as in the original documents
    $resultLeft = $this->readdLinebreaks($this->originalLeftSide, $wordsLeft);
    $resultRight = $this->readdLinebreaks($this->originalRightSide, $wordsRight);

    return array('left'=>$resultLeft, 'right'=>$resultRight);
  }

  /**
   * Takes an array of words and adds span tags in the appropriate places
   * 
   * @param array $words
   * @param type $matchStart
   * @param type $matchLength
   * @param type $markClasses
   * @return string
   */
  private function wrapMatchInSpanTag(array $words, $matchStart, $matchLength, $markClasses){
    $words[$matchStart] = '<span class="' . $markClasses . '">' . $words[$matchStart];
    $lastWordPosition = $matchStart + $matchLength - 1;
    $words[$lastWordPosition] = $words[$lastWordPosition] . '</span>';

    return $words;
  }

  /**
   * Adds line numbers and linebreaks back to the result.
   * 
   * @param array $originalLines
   * @param array $originalWords
   * @return array An array containing the original lines with html marks for the similarities.
   */
  private function readdLinebreaks(array $originalLines, array $originalWords){
    $markedLines = array();
    //the start index of the next line
    $offset = 0;

    $openInNextLine = '';
    foreach($originalLines as $lineNumber=> $lineContent){
      $wordsInLine = count(explode(" ", $lineContent));

      $words = array();
      for($i = $offset; $i < $offset + $wordsInLine; $i++){
        $words[] = $originalWords[$i];
      }

      $lineContent = $openInNextLine . implode(" ", $words);

      // close opened fragmark tags and determine which tags were closed that have to be opened in next line
      $closeTagsResult = $this->addClosingTags($lineContent);
      $openInNextLine = $closeTagsResult[1];

      $markedLines[$lineNumber] = $closeTagsResult[0];
      $offset += $wordsInLine;
    }

    return $markedLines;
  }

  /**
   * Closes all open html tags at the end of the given line. The closed tags need to be reopened 
   * on the next line, in order to have valid html. This is necessary if for example the lines 
   * are displayed in a list tag, so that no invalid html occurs.
   *
   * Based on work by Milian <mail@mili.de>.
   * 
   * @param string $line
   * @return array An array containing the line with appended closing tags if necessary and the tags to be 
   * opened on the next line.
   */
  private function addClosingTags($line){
    $openingTags = $this->findOpeningHtmlTags($line);
    $closingTags = array_reverse($this->findClosingHtmlTags($line));

    $openInNextLine = '';
    if(count($closingTags) < count($openingTags)){
      $openedTagsWithAttributes = $this->findOpeningHtmlTags($line, true);
      //store the tags that need to be reopened on the next line

      foreach($openingTags as $i=> $openedTag){
        if(!in_array($openedTag, $closingTags)){
          //no matching tag was found, so it spans over to the next line 
          //-> close here, reopen on the next line with same attributes
          $openInNextLine .= '<' . $openedTagsWithAttributes[$i] . '>';
          $line .= '</' . $openedTag . '>';
        }else{
          //remove the closing tag that just matched, because it already closed one tag
          unset($closingTags[array_search($openedTag, $closingTags)]);
        }
      }
    }

    return array($line, $openInNextLine);
  }

  /**
   * @param string $line A line of text that should be searched for opening html tags.
   * @param bool $withAttributes Indicating whether the tags should be with attributes or just the name.
   * @return array An array containing all opening tags that were found inside the line.
   */
  private function findOpeningHtmlTags($line, $withAttributes = false){
    $tags = array();

    if($withAttributes){
      preg_match_all('#<(span(?: .*))?(?<![/|/ ])>#iU', $line, $tags);
    }else{
      preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $line, $tags);
    }

    return $tags[1];
  }

  /**
   * @param string $line A line of text that should be searched for closing html tags.
   * @return array An array containing all closing tags that were found inside the line.
   */
  private function findClosingHtmlTags($line){
    $closedTags = array();
    preg_match_all('#</([a-z]+)>#iU', $line, $closedTags);

    return $closedTags[1];
  }

}