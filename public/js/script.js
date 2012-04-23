/**
 * This file is the place for all self-written scripts, as long as they are short enough
 * to be maintained here.
 * 
 * Please put everything at least into a self-executing function block to don't pollute
 * the global namespace.
 */

$(document).ready(function(){
 
  // submit the case selection on change of the dropdown
  $('.case-settings-box select').chosen({allow_single_deselect: true}).change(function(){
    $(this).closest('form').submit();  
  });
  
  // if js is enabled we only want to open the menu on click, the other behaviour is
  // just a fallback for non-js users
  $('.dropdown-button').removeClass('hoverable').find('.button').live('click', function(){
    $(this).parent().toggleClass('hover');   
    return false;
  });
  
  $(".toggle-comments").click(function() {
    var targetId = $(this).attr("for");
    var target = $("#" + targetId);
    var comments = target.children(".comments");
    var loading = target.children(".comments-loading");
    var sourceId = target.children(".write-comment-box").children("input[name='sourceId']").val();

    if(target.is(':visible')) {
      $(this).html("<span class=\"comments-icon\">Show comments</span>");
      $(this).removeClass("expanded");
      
      target.slideUp(800, function() {
        comments.html("");
      });
    } else {
      $(this).html("<span class=\"comments-icon\">Hide comments</span>");
      $(this).addClass("expanded");
      target.show();
      comments.hide();
      loading.slideDown(800, function() {
        $.post('/comment/list', {
          'source': sourceId,
          'format': 'json'
        }, function(data) {
        if(!data.errorcode) {
          comments.html("");
          $.each(data, function() {
            addComment(this, comments);
          });
          loading.slideUp(800, function() {
            comments.slideDown(300);
          });
        } else {
        comments.html('<div class="comment">' + data.message + '</div>');
          loading.slideUp(800, function() {
            comments.slideDown(300);
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

    var target = $(this).closest('.comments-wrapper').children(".comments");
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
    var tpl = '<div class="comment">' +
            '<div class="image"><img class="avatar-small" src="' + data.author.avatar + '" /></div>' +
            '<div class="details">' +
            '<div class="title"><b>' + data.author.username + '</b> ' + data.text + 
            '<span class="date">' + data.created + '</span>' +
            '</div>' +
            '</div>' +
            '</div>';
            target.append(tpl);
  }
  
  //make dropdown out of the action icons
  function wrapActions(){
    var dropdownButton = $('<div class="dropdown-button" />');
    $('.action-list').addClass('').wrap(dropdownButton).parent().prepend('<button class="button">Select action<span class="arrow-down"></span></button>');
    $('.action-list a').each(function(){
      var currentAction = $(this);
      var title = currentAction.attr('title');
      currentAction.removeAttr('title');
      currentAction.append('<span>' + title + '</span>');
    });
  }
  wrapActions();
  
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
      $("#main-wrapper").load(url + " .main");
    };
        
  });
    
  $(window).trigger('hashchange');
});

$(function() {
   $('a.picture').lightBox();
});


