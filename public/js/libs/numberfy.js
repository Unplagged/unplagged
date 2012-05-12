(function($) { $.fn.numberfy = function() {

	//prep - Opera currently not supported
	if (navigator.appName == 'Opera' || navigator.appName == 'Microsoft Internet Explorer') return false;

	//prep - line numbers column styling
	var column_BG = '#999', column_text = '#eaeaea';

	//for each textarea in selector...
	$(this).filter('textarea').each(function() {

		//wrap each in a new DIV container (allows easy position of line numbers elements relative to textarea's left edge)
		var container;
		$(this).appendTo(container = $('<div>').addClass('lineNumberfier').css({position: 'relative', height: $(this)[0].offsetHeight}).insertBefore($(this)));

		//get certain line-affecting styles of the textarea that we will need to use later on
		var lineAffectingStyles = {};
		var temp = ['font-size', 'font-family', 'line-height', 'text-indent', 'font-weight', 'text-decoration'];
		for (var i=0; i<temp.length; i++) lineAffectingStyles[temp[i]] = $(this).css(temp[i]);

		//create a 'clone' of this textarea to act as its measurer.
		var measurer = $('<p>').appendTo('body').css({visibility: 'hidden', background: '#ddd', position: 'absolute'}).appendTo('body');
		$(this).data({measurer: measurer});

		//textarea and measurer must have same wor-wrap, white-space and overflow-y CSS so width readings are reliable
		$(this).add(measurer).css({overflowY: 'scroll', wordWrap: 'break-word', whiteSpace: 'pre-wrap'});

		//disable any native support for textarea resizing
		$(this).css('resize', 'none');

		//set up the line numbers col and position it to left of textarea
		var col = $('<div>').css({background: column_BG, color: column_text, width: 30, position: 'absolute', overflow: 'hidden', left: 0, top: 0, height: $(this).height()}).addClass('lineNumsCol').prependTo($(this).parent());

		//line nums col needs to copy all line- and X/Y box-affecting styls of textarea
		var css = {}, thiss = $(this);
		$.each(['paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft', 'borderTop', 'borderRight', 'borderBottom', 'borderLeft'], function(key, val) { css[val] = thiss.css(val); });
		col.css(lineAffectingStyles).css(css);

		//measurer needs same line- and X box-affecting styles as textarea
		measurer.css(lineAffectingStyles).css({width: $(this).width(), paddingLeft: $(this).css('padding-left'), paddingRight: $(this).css('padding-right')});

		//Firefox pads slightly to left of scrollbar. Account for this in measurer.
		if (navigator.appVersion.indexOf('Firefox') != -1) measurer.css({width: '-='+parseInt($(this).css('fontSize')) / 2});

		//pad container left by width of line numbers col, so no overlap between col and textarea
		container.css('padding-left', col[0].offsetWidth);

		//on load, and on keyup in this textarea, set lines
		doLines($(this));
		$(this).keyup(function() { doLines($(this)); });

		//on textarea scroll, also scroll line nums col in tandem
		$(this).scroll(function() { col[0].scrollTop = $(this)[0].scrollTop; });

	});

	//main func - do lines (on load, and updated for each keypress)
	function doLines(el) {

		var col = el.prev();

		//exit if no content in textarea
		if (!el.val()) return;

		//empty line numbers in preparation for re-doing them
		col.empty();

		//how many lines in textarea's current value?
		//Since empty lines don't influence height in most HTML elements, convert these to have a &nbsp;
		var lines = el.val().replace(/\n($|\n)/gm, '\n&nbsp;$1').split(/\n/g);

		//gather line heights by putting each inside this textarea's corresponding measurer el and reading its expanded height
		var lineHeights = [];
		for (var i=0; i<lines.length; i++) {
			el.data('measurer').text(lines[i]);
			lineHeights.push(el.data('measurer')[0].offsetHeight);
		}

		//now insert line numbers using heights data
		for (var i=0, len = lineHeights.length; i<len; i++) {
			var heights_upToThisLine = lineHeights.slice(0, i);
			var YPos = (function() { var b = 0; for (var i in heights_upToThisLine) b += heights_upToThisLine[i]; return b; })() + 2;
			col.append($('<span>', {text: i+1}).css({position: 'absolute', top: YPos}));
		}

		//update scroll top of line nums col
		col[0].scrollTop = el[0].scrollTop;

	}

} })(jQuery);