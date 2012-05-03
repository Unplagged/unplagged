<?php

class Unplagged_CompareText{

  private static $delimiter = " ";
  private static $lineBreaks = array("<br />", "<br>", "\n");

  public static function compare($left, $right, $minlength, $outputType = "html"){

    $left = str_replace(Unplagged_CompareText::$lineBreaks, "____ ", $left);
    $right = str_replace(Unplagged_CompareText::$lineBreaks, "____ ", $right);

    $listLeft = explode(Unplagged_CompareText::$delimiter, $left);
    $listRight = explode(Unplagged_CompareText::$delimiter, $right);
    $documents = array(array_values($listLeft), array_values($listRight));

    $comparedTexts = Unplagged_CompareText::compareText($documents, $minlength);

    if($outputType == "plain"){
      return Unplagged_CompareText::getMarkedTexts($comparedTexts, $listLeft, $listRight);
    }
    
    return Unplagged_CompareText::getHtml($comparedTexts, $listLeft, $listRight);
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

      // If the word is smaller than than the min_run_length, do not analyse it
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

  private static function getHtml($comparedTexts, $list1, $list2){
    /* Colors have css classes "fragmark1" to "fragmark9" */
    $col = 0;
    $nr_col = 9;
    for($i = 0; $i < sizeof($comparedTexts); $i++){
      $res = $comparedTexts[$i];
      $list1[$res[3]] = "<span class=\"fragmark-$col\">" . $list1[$res[3]];
      $list1[$res[3] + $res[4] - 1] = $list1[$res[3] + $res[4] - 1] . "</span>";

      $list2[$res[1]] = "<span class=\"fragmark-$col\">" . $list2[$res[1]];
      $list2[$res[1] + $res[4] - 1] = $list2[$res[1] + $res[4] - 1] . "</span>";

      $col = ($col + 1) % $nr_col;
    }
    $newlist1 = str_replace("____", "<br />", implode(" ", $list1));
    $newlist2 = str_replace("____", "<br />", implode(" ", $list2));

    return "<div class=\"diff clearfix\"><div class=\"src-wrapper\">" . $newlist1 . "</div>" . "<div class=\"src-wrapper\">" . $newlist2 . "</div></div>";
  }

  private static function getMarkedTexts($comparedTexts, $list1, $list2){
    /* Colors have css classes "fragmark1" to "fragmark9" */
    $col = 0;
    $nr_col = 9;
    for($i = 0; $i < sizeof($comparedTexts); $i++){
      $res = $comparedTexts[$i];
      $list1[$res[3]] = "<span class=\"fragmark-$col\">" . $list1[$res[3]];
      $list1[$res[3] + $res[4] - 1] = $list1[$res[3] + $res[4] - 1] . "</span>";

      $list2[$res[1]] = "<span class=\"fragmark-$col\">" . $list2[$res[1]];
      $list2[$res[1] + $res[4] - 1] = $list2[$res[1] + $res[4] - 1] . "</span>";

      $col = ($col + 1) % $nr_col;
    }
    $newlist1 = str_replace("____", "<br />", implode(" ", $list1));
    $newlist2 = str_replace("____", "<br />", implode(" ", $list2));

    return array("left"=>$newlist1, "right"=>$newlist2);
  }

}

?> 