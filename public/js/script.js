/**
 * @todo just as a sidenote, javascript functions should never be put into global context like below, but rather into a 
 * self executing function, this should definitely be changed later on
 */

function autoCompleteCurrentCase(e){   
  var inputField = $('#current-case');
  inputField.autocomplete({
    source: '/case/autocomplete-alias',
    select: function(e, element) {
      /* @todo: make sure to allow only selection of valid cases */
      $.post('/user/set-current-case', {
        'case': element.item.value
      }, function(data) {
      }, "json");
      inputField.val(element.item.label);
      return false;
    }
  });
}

function resetCurrentCase(e){   
  $.post('/user/reset-current-case', {}, function(data) {
        $('#current-case').val('');
      }, "json");
}

function addContextMenu(){
  var contextMenuElement = '<div id="contextmenu" style="display:none;position:absolute;top:-250;left:0;z-index:100;color:black">' + 
        '<table cellpadding="5" cellspacing="0" style="background-color:#40bfe8">' +
          '<tr><td><a class="menu" href="javascript:deleteSearchWords()"> Google-Suchwörter löschen </a></td></tr>' +
           '<tr><td><a id="googleSearch" class="menu" href="javascript:googleSearch()"> Google Suche </a></td></tr>' +
        '</table>' +
        '</div>';
  $('body').prepend(contextMenuElement);
}

$(document).ready(function(){
  // current case auto completion and reset
  $('#current-case').focus(autoCompleteCurrentCase);
  $('#current-case-field .clear').click(resetCurrentCase);
  
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
  
  addContextMenu();
  
  //wrap home menu button, so that icon gets shown
  var homeButton = $('#header .navigation .home');
  homeButton.wrapInner('<span class="ir"/>');
});

/**
 * The pagination plugin.
 */
$(function() {            
    $(".pagination a").live("click", function() {
        var href = $(this).attr("href");
        if(href) {
          var substr = href.split('/');
          var hash = substr[substr.length-2] + "/" + substr[substr.length-1];

          window.location.hash = hash;
        }
        return false;
    });
    
    $(window).bind('hashchange', function(){
        var newHash = window.location.hash.substring(1);
        
        if (newHash) {
            var substr = newHash.split('/');
            var hash = substr[substr.length-2] + "/" + substr[substr.length-1];
            
            var url = window.location.pathname;
            if(url.charAt(url.length-1) != '/') {
              url += '/';
            }
            url += hash;
            $("#main-wrapper").load(url + " #main");
        };
        
    });
    
    $(window).trigger('hashchange');
});

