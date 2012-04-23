<?php

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"color.css\" />";
define("DELIMITER", " ");
define("BREAKLINE", "<br/>");

function compareText($documents, $min_run_length) {
    $documents_len = array();

    $final_match_list = array();
    $match_table = array();
    $docs = sizeof($documents);

    for ($doc_idx = 0; $doc_idx < $docs; $doc_idx++) {
        $doc = $documents[$doc_idx];
        $tokens = sizeof($doc) - $min_run_length + 1;
        $doc_len = sizeof($doc);
        $documents_len[$doc_idx] = $doc_len;

        // If the word is smaller than than the min_run_length, do not analyse it
        if ($tokens <= 0) {
            continue;
        }

        $min_token_idx = 0;

        for ($token_idx = 0; $token_idx < $tokens; $token_idx++) {
            $match = array_slice($doc, $token_idx, $min_run_length);
            $match_loc = array($doc_idx, $token_idx);
            $match_tag = implode(" ", $match);

            if (array_key_exists($match_tag, $match_table)) {
                if ($token_idx >= $min_token_idx) {
                    $best_match = array($doc_idx, $token_idx, -1, 0, 0);
                    $matches = $match_table[$match_tag];
                    $nr_matches = sizeof($matches);
                    for ($idx = 0; $idx < $nr_matches; $idx++) {
                        $match_peer = $matches[$idx];
                        $peer_doc_idx = $match_peer[0];
                        $peer_doc = $documents[$peer_doc_idx];
                        $peer_token_idx = $match_peer[1] + $min_run_length;
                        $peer_len = $documents_len[$peer_doc_idx];
                        $our_token_idx = $token_idx + $min_run_length;

                        if ($peer_doc_idx == $doc_idx) {
                            continue;
                        }

                        while ($peer_token_idx < $peer_len
                        && $our_token_idx < $doc_len
                        && $peer_doc[$peer_token_idx] == $doc[$our_token_idx]) {
                            $peer_token_idx++;
                            $our_token_idx++;
                        }

                        $len = $our_token_idx - $token_idx;
                        if ($len > $best_match[4]) {
                            $best_match[2] = $match_peer[0];
                            $best_match[3] = $match_peer[1];
                            $best_match[4] = $len;
                        }
                    }

                    if ($best_match[2] != -1) {
                        array_push($final_match_list, $best_match);
                        $min_token_idx = $token_idx + $best_match[4];
                    }
                }
                array_push($match_table[$match_tag], $match_loc);
            } else {
                $match_table[$match_tag] = array($match_loc);
            }
        }
    }
    return $final_match_list;
}

//
///*$text1="Aus diesen Teilplänen für Beschaffung, Fertigung, Absatz, Investitionen, Forschung und Entwicklung, Personal, Finanzen und aus der Ergebnisplanung mit Planbilanzen sowie Plan-Gewinn- und Verlustrechnungen gewinnt der Vorstand die notwendige Zielorientierung für die Leitung der Gesellschaft. Somit gehört es zur Gesamtverantwortung aller Vorstandsmitglieder, sowohl eine strategische Mehljahresplanung als auch eine operative Einjahresplanung zu erstellen, die auf der Basis realistischer Prämissen die Budgetziele vorgibt [FN 241].";*/
//
//$text1="Die autonomistische Theorie RABELS hat in vielen Ländern Anhänger gefunden. [FN 19]. Jedoch war er sich selbst darüber im klaren, daß die Bildung von international gültigen Begriffen auf rechtsvergleichender Grundlage ein langer Weg mit erheblichen Schwierigkeiten ist. Auf den Einwand, der Richter werde kaum in der Lage sein, in jedem einzelnen Fall rechtsvergleichende Forschung auf breiter Grundlage vorzunehmen, hat RABEL schon 1931 in seinem Aufsatz geantwortet: „Von den Richtern dürfen wir nur empirische Beiträge erwarten, [Vergleiche des eigenen Rechts mit einzelnen fremden Rechten, in der Regel nur mit einem einzigen“. [FN 20]] [FN 19] Viele Jahre später hat RABEL die Ansichten seiner Anhänger in seinem Conflict of Laws I, S. 44ff., kurz zusammengefaßt niedergelegt.";
//
////$text1 = "test1 test2 test3 test4 test5 test6 test7 test8 test9 test10";// test1 test2 test3 test4 test5 test6 test7 test8 test9 test10";
//
///*$text2="Es gehört zu den wichtigsten Pflichten des Vorstands, sowohl eine - strategische - Mehrjahresplanung als auch eine - operative - Einjahresplanung zu erstellen, die auf der Basis realistischer Prämissen die Budgetziele vorgibt [FN 6]. Aus den Teilplänen für Beschaffung, Fertigung, Absatz, Investitionen, Forschung und Entwicklung, Personal, Finanzen und aus der Ergebnisplanung mit Planbilanzen sowie Plan-Gewinn- und Verlustrechnungen gewinnt der Vorstand die notwendige Zielorientierung für die Leitung der Gesellschaft.";*/
//
//$text2="Die autonomistische Theorie von Rabel hat in vielen Ländern Anhänger gefunden[FN 62]. Jedoch war Rabel selbst sich darüber im klaren, daß die Bildung von international gültigen Begriffen auf rechtsvergleichender Grundlage mit erheblichen Schwierigkeiten verbunden ist. Aber auf den Einwand, der Richter werde kaum in der Lage sein, in jedem einzelnen Fall rechts vergleichende Forschungen auf breiter Grundlage vorzunehmen, hat Rabel schon in seinem Aufsatz von 1931 geantwortet: „Von den Richtern dürfen wir nur empirische Beiträge erwarten, Vergleiche des eigenen Rechts mit einzelnen fremden Rechten, in der Regel nur mit einem einzigen.“ [FN 63]
// [FN 62] 62 Es seien erwähnt Beckett ...";
//
////$text2 = "test1 test2 test3 test4 test5a test6 test7 test8 test9";// test1 test2 test3 test4 test5 test6 test7 test8 test9 test10";
//
//
//var_dump($list1);
//echo BREAKLINE;
// documents must look like:
//[
//  ["c", "Ergebnis", "Aus", 113 mehr...], 
//  ["Es", "gehrt", "zu", 79 mehr...]
//]


function compare($left, $right, $minlength) {
    $listLeft = explode(DELIMITER, $left);
    $listRight = explode(DELIMITER, $right);
    $documents = array(array_values($listLeft), array_values($listRight));

    $comparedTexts = compareText($documents, 4);
    return markTexts($comparedTexts,$listLeft,$listRight);
}

function markTexts($comparedTexts,$list1,$list2) {
    /* Colors have css classes "fragmark1" to "fragmark9" */
    $col = 0;
    $nr_col = 9;
    for ($i = 0; $i < sizeof($comparedTexts); $i++) {
        $res = $comparedTexts[$i];
        $list1[$res[3]] = "<span class=\"fragmark-$col\">" . $list1[$res[3]];
        $list1[$res[3] + $res[4] - 1] = $list1[$res[3] + $res[4] - 1] . "</span>";

        $list2[$res[1]] = "<span class=\"fragmark-$col\">" . $list2[$res[1]];
        $list2[$res[1] + $res[4] - 1] = $list2[$res[1] + $res[4] - 1] . "</span>";

        $col = ($col + 1) % $nr_col;
    }
    $newlist1 = implode(" ", $list1);
    $newlist2 = implode(" ", $list2);

    return "<div style='float:left; width: 500px;margin-right:20px;'>" . $newlist1 . "</div>" . "<div>" . $newlist2 . "</div>" ;
}

?> 