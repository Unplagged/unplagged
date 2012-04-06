/**
 * This file is the place for all self-written scripts, as long as they are short enough
 * to be maintained here.
 * 
 * Please put everything at least into a self-executing function block to don't pollute
 * the global namespace.
 */

$(document).ready(function(){
  
  // current case auto completion and reset
  $('#current-case').focus(autoCompleteCurrentCase);
  $('#current-case-field .clear').click(resetCurrentCase);
  
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
  
  /**
   * Adds the html for the context menu to the body.
   */
  function addContextMenu(){
    var contextMenuElement = '<div id="contextmenu" style="display:none;position:absolute;top:-250;left:0;z-index:100;color:black">' + 
          '<table cellpadding="5" cellspacing="0" style="background-color:#40bfe8">' +
            '<tr><td><a class="menu" href="javascript:deleteSearchWords()"> Google-Suchwörter löschen </a></td></tr>' +
            '<tr><td><a id="googleSearch" class="menu" href="javascript:googleSearch()"> Google Suche </a></td></tr>' +
          '</table>' +
          '</div>';
    $('body').append(contextMenuElement);
  }
  //unobtrusively add the context menu, so that users without js don't see it
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

