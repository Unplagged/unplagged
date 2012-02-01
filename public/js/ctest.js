   width = 60, height = 100, status = 0; 
    spacer = "&nbsp;&nbsp;";
    onfocus = "onfocus='if(this.blur)this.blur()'"; 
    
	document.write("<style type='text/css'>" +
	               "a.menu {text-decoration:none;font: 12px Verdana;}" +
				   "a.menu:link,a.menu:visited {text-decoration:none;color:#000000}" +
				   "a.menu:hover,a.menu:active {text-decoration:none;color:#000000}" +
				   "hr.menu {border:1px;height:1px;background-color:black;color:white}</style>" +
				   "<div id='menu' style='position:absolute;top:-250;left:0;z-index:100'>" +
				   "<table cellpadding='5' cellspacing='0' width='" + width + "' height='" + height + "' style='border-style:outset;border-width:1;border-color:black;background-color:lightblue'>" +
				   "<tr><td><a class='menu' href='javascript:history.back()'" + onfocus + ">&nbsp;Back</a></td></tr>" +
				   "<tr><td><a class='menu' href='javascript:history.forward()'" + onfocus + ">&nbsp;Forward</a></td></tr>" +
				   "<tr><td><hr class='menu'><a class='menu' href='javascript:location.reload()'" + onfocus + ">&nbsp;Update</a></td></tr>" +
				   "<tr><td><a class='menu' href='javascript:viewSource()'" + onfocus + ">&nbsp;Sourcecode</a></td></tr>" +
				   "<tr><td><a class='menu' href='javascript:print()'" + onfocus + ">&nbsp;Print</a></td></tr>" +
				   "</table></div>");
				   
    
    document.oncontextmenu = showMenu;
    //document.onclick=closecontextmenu;
    //window.onload=closecontextmenu;
    document.onmouseup = closecontextmenu;


function showMenu(e) {
   // if(ie8) {
        if(event.clientX > width) 
		    xPos = event.clientX - width + document.body.scrollLeft;
        else 
		    xPos = event.clientX + document.body.scrollLeft;

		if(event.clientY > height) 
		    yPos = event.clientY - height + document.body.scrollTop;
        else 
	        yPos = event.clientY + document.body.scrollTop;

    document.getElementById("menu").style.left = xPos;
    document.getElementById("menu").style.top = yPos;
    status = 1;
    return false;
}

function hideMenu(e) {
    if(status == 1 && ((ie8 && event.button == 1) || (nn && e.which == 1))) {
        setTimeout("document.getElementById('menu').style.top=-250", 250);
        status = 0;
   }
}

function closecontextmenu() 
{ 
	document.getElementById('menu').style.display = "none"; 
}

function viewSource() {
    var w = window.open("view-source:" + window.location,'','resizable=1,scrollbars=1');
}

/*var x=0; 
var y=0; 
function contextmenu(e) 
{ 
	document.getElementById('conmenu').style.display = "block"; 

	x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX; 
	y = (document.all) ? window.event.y + document.body.scrollTop : e.pageY; 

	document.getElementById('conmenu').style.left = x; 
	document.getElementById('conmenu').style.top = y; 
	return false;
} 
function closecontextmenu() 
{ 
	document.getElementById('conmenu').style.display = "none"; 
}
function openNewWindow() {
	var w=window.open(window.location,'','resizable=1,scrollbars=1,status=1,location=1,menubar=1,toolbar=1');
}

document.write("<style>a.menu:link,a.menu:visited {text-decoration:none;color:#F0F8FF}"+
"a.menu:hover,a.menu:active {text-decoration:none;color: white;}</style>"+
"<div id='conmenu' style='position:absolute;top:-250;left:0;z-index:100'>"+
"<table cellpadding='5' cellspacing='0' style='border-style:outset;border-width:1;border-color:#3a6c96;background-color:#4682B4'>"+
"<tr><td><a class='menu' href='javascript:history.back()'> Zurück </a></td></tr>"+
"<tr><td><a class='menu' href='javascript:history.forward()'> Vorwärts </a></td></tr>"+
"<tr><td><hr class='menu'><a class='menu' href='javascript:location.reload()'> Aktualisieren </a></td></tr>"+
"<tr><td><a class='menu' href='javascript:print()'> Drucken </a></td></tr>"+
"<tr><td><hr class='menu'><a class='menu' href='javascript:openNewWindow()'> Neues Fenster </a></td></tr>"+
"</table>"+
"</div>");*/
//document.onclick=closecontextmenu;
//window.onreload=closecontextmenu;
//-->
/*function click (e) {
  if (!e)
    e = window.event;
  if ((e.type && e.type == "contextmenu") || (e.button && e.button == 2) || (e.which && e.which == 3)) {
      document.write("<style>a.menu:link,a.menu:visited {text-decoration:none;color:#F0F8FF}"+
"a.menu:hover,a.menu:active {text-decoration:none;color: white;}</style>"+
"<div id='conmenu' style='position:absolute;top:-250;left:0;z-index:100'>"+
"<table cellpadding='5' cellspacing='0' style='border-style:outset;border-width:1;border-color:#3a6c96;background-color:#4682B4'>"+
"<tr><td><a class='menu' href='javascript:history.back()'> Zurück </a></td></tr>"+
"<tr><td><a class='menu' href='javascript:history.forward()'> Vorwärts </a></td></tr>"+
"<tr><td><hr class='menu'><a class='menu' href='javascript:location.reload()'> Aktualisieren </a></td></tr>"+
"<tr><td><a class='menu' href='javascript:print()'> Drucken </a></td></tr>"+
"<tr><td><hr class='menu'><a class='menu' href='javascript:openNewWindow()'> Neues Fenster </a></td></tr>"+
"</table>"+
"</div>");
    if (window.opera)
      window.alert("Sorry: Diese Funktion ist deaktiviert.");
    return false;
  }
}
if (document.layers)
document.captureEvents(Event.MOUSEDOWN);
document.onmousedown = click;
document.oncontextmenu = click;*/