/**
 * This file is the place for all self-written scripts, as long as they are short enough
 * to be maintained here.
 * 
 * Please put everything at least into a self-executing function block to don't pollute
 * the global namespace.
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

$(document).ready(function(){
  
  
  $("#activity-stream .toggle-comments").click(function() {
    var more = $(this).closest(".activity").siblings('.activity-more');  
    var target = more.children(".comments");
    var loading = more.children(".comments-loading");
    var sourceId = more.children(".write-comment-box").children("input[name='sourceId']").val();
    
    if($(this).hasClass("shown")) {
      $(this).html("<span>Show comments</span>");
      $(this).removeClass("shown");
      
      more.slideUp(800, function() {
        target.html("");
      });
    } else {
      $(this).html("<span>Hide comments</span>");
      $(this).addClass("shown");
      more.show();
      target.hide();
      loading.slideDown(800, function() {
        $.post('/comment/list', {
          'source': sourceId,
          'format': 'json'
        }, function(data) {
        if(!data.errorcode) {
          target.html("");
          $.each(data, function() {
            addComment(this, target);
          });
          loading.slideUp(800, function() {
            target.slideDown(300);
          });
        } else {
        target.html('<div class="comment">' + data.message + '</div>');
          loading.slideUp(800, function() {
            target.slideDown(300);
          });
        }
        }, "json");
      });

    }
    return false;
  });
  
  $(".write-comment").click(function(){
    var source = $(this).closest(".write-comment-box").children("input[name='sourceId']");
    var text = $(this).closest(".write-comment-box").children("input[name='text']");

    var target = $(this).closest('.activity-more').children(".comments");
    if(text.val()) {
        $.post('/comment/create', {
          'source': source.val(),
          'text': text.val()
        }, function(data) {
          text.val("");
          addComment(data, target)
        }, "json");
    }
    return false;
  });
  
  function addComment(data, target) {
    var tpl = '<div class="comment">\n\
            <div class="image"><img class="avatar-small" src="' + data.author.avatar + '" /></div>\n\
            <div class="details">\n\
            <div class="title"><b>' + data.author.username + '</b> ' + data.text + '\n\
            <span class="date">' + data.created + '</span>\n\
            </div>\n\
            </div>\n\
            </div>';
            target.append(tpl);
  }
  
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
  
  var dropdownButton = '<span id="dropdown-button" class="arrow-up"></span>';
  $('header[role=toolbar]').append(dropdownButton);
  
  //collapse header line
  $('#dropdown-button').click(function(e) {
    var content = $('header[role=toolbar] .content');
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

$(function() {
   $('a.picture').lightBox();
});


