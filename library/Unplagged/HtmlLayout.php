<?php

class Unplagged_HtmlLayout{

public static function htmlLayout($case, $note, $fragments){
  	
	$casename = 'Eine kritische Auseinandersetzung mit der Dissertation von Prof. Dr. : ' . $case;
	
	// html head
	$html = '<html>'.
		  '<style type="text/css">'.
		  'body {background-color:#d0e4fe;}h1{color:orange;text-align:center;}p{font-family:"Times New Roman";font-size:20px;}'.
		  '</style>';
	
	// html body
	$html .= '<body><p>Case name: ' . $casename .'</p>';
	
	// note
	$html .= '<p>Note: ' . $note .'</p>';
		  
	// iteration of fragments
	foreach($fragments as $fragment){
		
		// get bibtex infos
		$bibTexPlag = $fragment->getPlag()->getLineFrom()->getPage()->getDocument()->getBibTex();
		$bibTexSource = $fragment->getSource()->getLineFrom()->getPage()->getDocument()->getBibTex();
				
		// get page infos
		$pageFromPlag = $fragment->getPlag()->getLineFrom()->getPage()->getPageNumber();
		$pageToPlag = $fragment->getPlag()->getLineTo()->getPage()->getPageNumber();
		
		$pageFromSource = $fragment->getSource()->getLineFrom()->getPage()->getPageNumber();
		$pageToSource = $fragment->getSource()->getLineTo()->getPage()->getPageNumber();
		
		// get line infos
		$lineFromPlag = $fragment->getPlag()->getLineFrom()->getLineNumber();
		$lineToPlag = $fragment->getPlag()->getLineTo()->getLineNumber();
		
		$lineFromSource = $fragment->getSource()->getLineFrom()->getLineNumber();
		$lineToSource = $fragment->getSource()->getLineTo()->getLineNumber();
		
		
		$html .= '<p>Plag Bibtext: ' . $bibTexPlag .'</p>'.
				'<p>Source Bibtext' . $bibTexSource .'</p>';
		
		$html .= '<p> Page from: ' . $pageFromPlag . ' to:' . $pageToPlag . '</p>'.
				'<p> Page from: ' . $pageFromSource . ' to:' . $pageToSource . '</p>';
				
		
		$html .= '<p>Plagiarized Text </p>' . 
				'<p> Line from: ' . $lineFromPlag . ' to:' . $lineToPlag . '</p>'.
				'<p>Source Text </p>' . 
				'<p> Line from: ' . $lineFromSource . ' to:' . $lineToSource . '</p>';
		
		// // get fragment content
		$content = $fragment->getContent('array',true);
		$plagText = $content['plag'];
		$sourceText = $content['source'];
		
		foreach ($content['plag'] as $line){
			$html .= $line;
		}
		
		foreach ($content['source'] as $line){
			$html .= $line;
		}
		
	}
	
	// html foot
	$html .='</body></html>';
	
	return $html;
  }
  
 }
 ?>