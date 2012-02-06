var x=0; 
var y=0; 
var searchBuffer='';

// defines wether the click was on the contextmenu or not
var contextMenu = false;

document.body.oncontextmenu = rightClick;
document.body.onclick = clickHandler;

var contextMenuId = 'contextmenu';
document.getElementById(contextMenuId).onmouseover = function(){    
    contextMenu = true;    
};
document.getElementById(contextMenuId).onmouseout = function(){
    contextMenu = false;    
};

function rightClick(ev)
{    
    if (searchBuffer != '') {
        // if searchBuffer is not empty, it means that the user has selected a word
        // => show our context menu.
        x = (document.all) ? window.event.x + document.body.scrollLeft : ev.pageX; 
        y = (document.all) ? window.event.y + document.body.scrollTop : ev.pageY; 
        var scrollTop = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop;
        var scrollLeft = document.body.scrollLeft ? document.body.scrollLeft : document.documentElement.scrollLeft;
    
        document.getElementById(contextMenuId).style.left = ev.clientX + scrollLeft + 'px'; 
        document.getElementById(contextMenuId).style.top = ev.clientY + scrollTop + 'px'; 
    
        document.getElementById(contextMenuId).style.display='block';
    
        // avoid showing default contextMenu
        return false;
    } else {
        // if searchBuffer is empty, show normal context menu.
        return true;
    }
}

function clickHandler() 
{ 
    document.getElementById(contextMenuId).style.display = "none";
    
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

function cleanSearchBuffer() 
{
    searchBuffer = searchBuffer.replace('   ',' ');
    searchBuffer = searchBuffer.replace('  ',' ');    
}