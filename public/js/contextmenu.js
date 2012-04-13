var searchBuffer='';
// defines whether the click was on the contextmenu or not
var contextMenu = false;
var contextMenuId = 'contextmenu';

$(document).ready(function(){
  $(document).bind("contextmenu", rightClick);
  $(document).bind("click", clickHandler);

  $("#" + contextMenuId).onmouseover = function(){
    contextMenu = true;
  };
  $("#" + contextMenuId).onmouseout = function(){
    contextMenu = false;
  };
});

function rightClick(ev)
{
  if (searchBuffer != '') {
    // if searchBuffer is not empty, it means that the user has selected a word
    // => show our context menu.
    var scrollTop = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop;
    var scrollLeft = document.body.scrollLeft ? document.body.scrollLeft : document.documentElement.scrollLeft;
    
    $("#" + contextMenuId).css({ position: "absolute", left: ev.clientX + scrollLeft + 'px', top: ev.clientY + scrollTop + 'px' });
    $("#" + contextMenuId).show();
    
    // avoid showing default contextMenu
    return false;
  } else {
    // if searchBuffer is empty, show normal context menu.
    return true;
  }
}

function clickHandler()
{
  $("#" + contextMenuId).hide();
    
  // if click on contextmenu, we do not need to save the selection
  // in the searchBuffer
  if (!contextMenu) {
    var selectedText = getSelectedText();
    if (selectedText != '' && selectedText != null
      && selectedText != undefined && selectedText != ' '){
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
    text = window.getSelection();
  }
  else if (document.getSelection)
  {
    text = document.getSelection();
  }
  else if (document.selection)
  {
    text = document.selection.createRange().text;
  }

  return text;
}

function copyText(e)
{
  alert("copyText");
}
function markAll()
{
    
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
  $('#googleSearch').html("GoogleSuche nach: '" + searchBuffer + "'");
}

function cleanSearchBuffer()
{
  searchBuffer = searchBuffer.replace(' ',' ');
  searchBuffer = searchBuffer.replace(' ',' ');
}