$(document).ready(function(){
  //unobtrusively add the context menu, so that users without js don't see it
  addContextMenu();
  
  var searchBuffer='';
  // defines whether the click was on the contextmenu or not
  var contextMenu = false;
  var contextMenuSelector = '#contextmenu';
  var contextMenuElement = $(contextMenuSelector);
  
  /**
   * Adds the html for the context menu to the body.
   */
  function addContextMenu(){
    var contextMenuElement = '<ul id="contextmenu" class="contextmenu">' + 
            '<li><a class="menu">Google-Suchwörter löschen</a></li>' +
            '<li><a id="googleSearch" class="menu">Google Suche</a></li>' +
          '</div>';
    $('body').append(contextMenuElement);
  }
  
  $('#contextmenu li:first-child').click(deleteSearchWords);
  $('#googleSearch').click(googleSearch);
  
  
  //to make it possible to show the contextmenu only on certain elements, 
  //we only use it when the class show-contextmenu is present
  $('.show-contextmenu').bind('contextmenu', showCustomContextmenu);

  //we probably only need mouseup, because then we know that the selection is finished
  $('.show-contextmenu').bind('mouseup', clickHandler);
  $('.show-contextmenu').click(function(){
      contextMenuElement.hide();
  });

  //mouse enter should have better performance then mousemove, because it should only get called once
  contextMenuElement.mouseenter(function(){
      contextMenu = true;
  });
  
  contextMenuElement.mouseout(function(){
      contextMenu = false;
  });

  function showCustomContextmenu(ev)
  {  
    if (searchBuffer != '') {
        // if searchBuffer is not empty, it means that the user has selected a word
        // => show our context menu.
        x = (document.all) ? window.event.x + document.body.scrollLeft : ev.pageX;
        y = (document.all) ? window.event.y + document.body.scrollTop : ev.pageY;
        var scrollTop = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop;
        var scrollLeft = document.body.scrollLeft ? document.body.scrollLeft : document.documentElement.scrollLeft;

        contextMenuElement.css({
          'left': ev.clientX + scrollLeft + 'px',
          'top': ev.clientY + scrollTop + 'px',
          'display': 'block'
        });

        // avoid showing default contextMenu
        return false;
    } else {
        //@todo if we place the show-contextmenu class carefully, I would say it would be better for the UI,
        //if we don't show the normal contextmenu at all on those elements, to don't confuse the user
      
        // if searchBuffer is empty, show normal context menu.
        return true;
    }
  }

  function clickHandler(event)
  {
      // if click on contextmenu or 'right' click, we do not need to save the selection
      // in the searchBuffer
      if (!contextMenu && event.which === 1) {
        
          var selectedText = getSelectedText();
          //shouldn't be possible to get undefined or ' ' now, because the 
          //function always returns '' and is trimmed now
          if (selectedText != ''){
              searchBuffer += ' ' + selectedText;
              updateGoogleSearchText();
          }
      }
  }

  function getSelectedText()
  {
      var text = '';

      if (window.getSelection)
      {
          text = window.getSelection().toString();
      }
      else if (document.getSelection)
      {
          text = document.getSelection();
      }
      else if (document.selection)
      {
          text = document.selection.createRange().text;
      }

      return $.trim(text);
  }

  function copyToClipboard(s) {
    if (window.clipboardData && clipboardData.setData) {
      clipboardData.setData('text', s);
    }
  }

  function deleteSearchWords()
  {
      searchBuffer = "";
      updateGoogleSearchText();
  }

  function googleSearch()
  {
      cleanSearchBuffer();
      window.open('http://www.google.de/search?q='+searchBuffer, '_newTab');
      searchBuffer = '';
      updateGoogleSearchText();
  }

  function updateGoogleSearchText()
  {
      document.getElementById('googleSearch').innerHTML = "GoogleSuche nach: '" + searchBuffer + "'";
  }

  /**
   * @todo does this really do anything? replacing empty string with empty string seems somehow unnecessary
   */
  function cleanSearchBuffer()
  {
    searchBuffer = searchBuffer.replace(' ',' ');
    searchBuffer = searchBuffer.replace(' ',' ');
  }
});