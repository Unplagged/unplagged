$(document).ready(function(){
  //collapse header line
  $('#dropdown-button').click(function(e) {
    var content = $('#settings-panel .content');
    var button = $('#dropdown-button');

    content.toggle();
    
    if(content.is(":visible")) {
      button.addClass("arrow-up");
    } else {
      button.removeClass("arrow-up");
    }
  });
  
  //wrap home menu button, so that icon gets shown
  var homeButton = $('#header .navigation .home');
  homeButton.wrapInner('<span class="ir"/>');
});


