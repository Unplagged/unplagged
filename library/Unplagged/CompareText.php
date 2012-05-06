<?php

class Unplagged_CompareText{

  private static $delimiter = " ";
  private static $lineBreaks = array("<br />", "<br>", "\n");

  public static function compare($listLeft, $listRight, $minlength, $inputType = 'array'){
    // remove line breaks – replace them by spaces
    if($inputType == 'array'){
      $tmpLeft = implode(Unplagged_CompareText::$delimiter, $listLeft);
      $tmpRight = implode(Unplagged_CompareText::$delimiter, $listRight);
    }

    $wordsLeft = explode(Unplagged_CompareText::$delimiter, $tmpLeft);
    $wordsRight = explode(Unplagged_CompareText::$delimiter, $tmpRight);

    $documents = array(array_values($wordsLeft), array_values($wordsRight));

    $comparedTexts = Unplagged_CompareText::compareText($documents, $minlength);

    return Unplagged_CompareText::getMarkedTexts($comparedTexts, $wordsLeft, $wordsRight, $listLeft, $listRight);
  }

  private static function compareText($documents, $min_run_length){
    $documents_len = array();

    $final_match_list = array();
    $match_table = array();
    $docs = sizeof($documents);

    for($doc_idx = 0; $doc_idx < $docs; $doc_idx++){
      $doc = $documents[$doc_idx];
      $tokens = sizeof($doc) - $min_run_length + 1;
      $doc_len = sizeof($doc);
      $documents_len[$doc_idx] = $doc_len;

      // If the word is smaller than the min_run_length, do not analyse it
      if($tokens <= 0){
        continue;
      }

      $min_token_idx = 0;

      for($token_idx = 0; $token_idx < $tokens; $token_idx++){
        $match = array_slice($doc, $token_idx, $min_run_length);
        $match_loc = array($doc_idx, $token_idx);
        $match_tag = implode(" ", $match);

        if(array_key_exists($match_tag, $match_table)){
          if($token_idx >= $min_token_idx){
            $best_match = array($doc_idx, $token_idx, -1, 0, 0);
            $matches = $match_table[$match_tag];
            $nr_matches = sizeof($matches);
            for($idx = 0; $idx < $nr_matches; $idx++){
              $match_peer = $matches[$idx];
              $peer_doc_idx = $match_peer[0];
              $peer_doc = $documents[$peer_doc_idx];
              $peer_token_idx = $match_peer[1] + $min_run_length;
              $peer_len = $documents_len[$peer_doc_idx];
              $our_token_idx = $token_idx + $min_run_length;

              if($peer_doc_idx == $doc_idx){
                continue;
              }

              while($peer_token_idx < $peer_len
              && $our_token_idx < $doc_len
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
              array_push($final_match_list, $best_match);
              $min_token_idx = $token_idx + $best_match[4];
            }
          }
          array_push($match_table[$match_tag], $match_loc);
        }else{
          $match_table[$match_tag] = array($match_loc);
        }
      }
    }
    return $final_match_list;
  }

  private static function getMarkedTexts($comparedTexts, $wordsLeft, $wordsRight, &$listLeft, &$listRight){
    // Colors have css classes "fragmark1" to "fragmark9"
    $col = 0;
    $nr_col = 9;
    
    for($i = 0; $i < sizeof($comparedTexts); $i++){
      $res = $comparedTexts[$i];
      $wordsLeft[$res[3]] = "<span class=\"fragmark-$col\">" . $wordsLeft[$res[3]];
      $wordsLeft[$res[3] + $res[4] - 1] = $wordsLeft[$res[3] + $res[4] - 1] . "</span>";

      $wordsRight[$res[1]] = "<span class=\"fragmark-$col\">" . $wordsRight[$res[1]];
      $wordsRight[$res[1] + $res[4] - 1] = $wordsRight[$res[1] + $res[4] - 1] . "</span>";

      $col = ($col + 1) % $nr_col;
    }
    
    // At this point the text is exploded word by word, now the line breaks have to be added again, as in the original document
    $resultLeft = Unplagged_CompareText::addLinebreaks($listLeft, $wordsLeft);
    $resultRight = Unplagged_CompareText::addLinebreaks($listRight, $wordsRight);

    return array("left"=>$resultLeft, "right"=>$resultRight);
  }

  /**
   * Adds line numbers and linebreaks again to the result.
   * 
   * @param type $originalLines
   * @param type $originalWords
   * 
   * @return array marked lines 
   */
  private function addLinebreaks($originalLines, $originalWords){
    $result = array();
    $offset = 0;

    $openInNextLine = '';
    foreach($originalLines as $lineNumber=>$lineContent){
      $wordsInLineCount = count(explode(" ", $lineContent));

      $words = array();
      for($i = $offset; $i < $offset + $wordsInLineCount; $i++){
        $words[] = $originalWords[$i];
      }

      $lineContent = $openInNextLine . implode(" ", $words);
      
      // close opened fragmark tags and determine which tags were closed that have to be opened in next line
      $lineContent = Unplagged_CompareText::closeTags($lineContent, $openInNextLine);

      $result[$lineNumber] = $lineContent;
      $offset += $wordsInLineCount;
    }

    return $result;
  }

  /**
   * close all open xhtml tags at the end of the string
   *
   * @param string $html
   * @return string
   * @author Milian <mail@mili.de>
   */
  private function closeTags($html, &$openInNextLine){
    $openInNextLine = '';
    $closedHtml = '';

    #put all opened tags into an array
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $resultTags);
    preg_match_all('#<(span(?: .*))?(?<![/|/ ])>#iU', $html, $resultTagsWithAttributes);

    $openedtags = $resultTags[1];   #put all closed tags into an array
    preg_match_all('#</([a-z]+)>#iU', $html, $resultTags);
    $closedtags = $resultTags[1];
    $len_opened = count($openedtags);

    // all tags are closed
    if(count($closedtags) == $len_opened){
      return $html;
    }

    $openedtags = array_reverse($openedtags);

    # close tags
    for($i = 0; $i < $len_opened; $i++){
      if(!in_array($openedtags[$i], $closedtags)){
        $openInNextLine .= '<' . $resultTagsWithAttributes[1][$i] . '>';
        $closedHtml .= '</span>';
      }else{
        unset($closedtags[array_search($openedtags[$i], $closedtags)]);
      }
    } return $html . $closedHtml;
  }

}

?> 