/**
 * This file is the place for all self-written scripts, as long as they are short enough
 * to be maintained here.
 * 
 * Please put everything at least into a self-executing function block to don't pollute
 * the global namespace.
 */

$(document).ready(function(){
  // lined textareas
  $("textarea").numberfy();
    
  // submit the case selection on change of the dropdown
  $('.case-settings-box select').chosen({
    allow_single_deselect: true
  }).change(function(){
    $(this).closest('form').submit();  
  });
  
  // if js is enabled we only want to open the menu on click, the other behaviour is
  // just a fallback for non-js users
  $('.dropdown-button').removeClass('hoverable').find('.button').live('click', toggleDropout);
  
  function toggleDropout(){
    closeDropouts($(this).parent());
    $(this).parent().toggleClass('hover');
    
    return false;
  }
  
  function closeDropouts(exclude){
    $('.dropdown-button').not(exclude).removeClass('hover');
  }
  
  $(document).live('click', function(){
    $('.dropdown-button').removeClass('hover');  
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
    ' <span class="date">' + data.created + '</span>' +
    '</div>' +
    '</div>' +
    '</div>';
    target.append(tpl);
  }
  
  //make dropdown out of the action icons
  function wrapActions(){
    var dropdownButton = $('<div class="dropdown-button left-dropout" />');
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
  var homeButton = $('header[role=banner] .navigation .home');
  homeButton.wrapInner('<span class="ir"/>');
  
  // executes a simtext comparison in fragment modify form
  function compareTexts() {
    if($("#candidateLineFrom").length != 0) {
      $.post("/document_page/compare", {
        candidateLineFrom: $("#candidateLineFrom").val(),
        candidateLineTo: $("#candidateLineTo").val(),
        sourceLineFrom: $("#sourceLineFrom").val(),
        sourceLineTo: $("#sourceLineTo").val(),
        highlight: true
      }, function(response) {
        if($('#candidateText').length == 0) {
          $('#fieldset-candidateGroup').append('<div id="candidateText" class="src-wrapper"/>');
          $('#fieldset-sourceGroup').append('<div id="sourceText" class="src-wrapper"/>');
        }
        $('#candidateText').html(response.data.plag);
        $('#sourceText').html(response.data.source);
      }, "json");
    }
    return false;
  }
  $("#candidateLineFrom, #candidateLineTo, #sourceLineFrom, #sourceLineTo").change(function(){
    compareTexts();
  });
  compareTexts();
  
  // executes a simtext comparison on fragment show page
  function compareFragmentTexts(fragmentId, highlight) {
    $.post("/document_page/compare", {
      fragment: fragmentId,
      highlight: highlight
    }, function(response) {
      $('#candidateText').html(response.data.plag);
      $('#sourceText').html(response.data.source);
    }, "json");
    
    return false;
  }
  $("#compareWithNoColor").change(function(){
    compareFragmentTexts($(this).val(), $(this).attr('checked'));
  });
  
  // creates a new fragment based on selected text
  $('.create-fragment').click(function() {
    var selectedText = window.getSelection().getRangeAt(0).toString();

    $('#fragment-content').val(selectedText);
    $('#fragment-create').submit();

    return false;
  });
  
  // fragment creation form
  $("#candidateDocument").change(function(el){
    updateDocumentPages($(this).val(), ['#candidatePageFrom', '#candidatePageTo']);
  });
  $("#sourceDocument").change(function(){
    updateDocumentPages($(this).val(), ['#sourcePageFrom', '#sourcePageTo']);
  });
  
  function updateDocumentPages(documentId, targetElements) {
    $.post('/document/read', {
      'id': documentId
    }, function(response) {
      if(response.statuscode == 200) {
        // clear the targets
        $.each(targetElements, function(index, targetId) {
          $('' + targetId).html('');
        });
    
        $.each(response.data.pages, function(index, page) {
          $.each(targetElements, function(targetIndex, targetId) {
            $('' + targetId).append($("<option/>", {
              value: page.id, 
              text: page.pageNumber
            }));
          });
        });
        
        $.each(targetElements, function(index, targetId) {
          // select first element
          $('' + targetId + ' option:first-child').attr("selected", "selected");
          $('' + targetId).change();
        });
      } else {
      // @todo: handle error
      }
    }, "json");
  }
  
  $("#candidatePageFrom").change(function(){
    updatePageLines($(this).val(), ['#candidateLineFrom']);
  });
  $("#candidatePageTo").change(function(){
    updatePageLines($(this).val(), ['#candidateLineTo']);
  });
  $("#sourcePageFrom").change(function(){
    updatePageLines($(this).val(), ['#sourceLineFrom']);
  });
  $("#sourcePageTo").change(function(){
    updatePageLines($(this).val(), ['#sourceLineTo']);
  });
  
  function updatePageLines(pageId, targetElements) {
    $.post('/document_page/read', {
      'id': pageId
    }, function(response) {
      if(response.statuscode == 200) {
        // clear the targets
        $.each(targetElements, function(index, targetId) {
          $('' + targetId).html('');
        });
    
        $.each(response.data.lines, function(index, line) {
          $.each(targetElements, function(targetIndex, targetId) {
            $('' + targetId).append($("<option/>", {
              value: line.id, 
              text: line.lineNumber
            }));
          });
        });
      } else {
      // @todo: handle error
      }
    }, "json");
  }
  
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
        $("#main-wrapper").load(url + " .main", function(){
          //this callback is not the nicest way, but currently the only way to make sure, that those things 
          //still work in ajax content
          wrapActions();
          $('a.picture').lightBox();
        });
      }

    });

    $(window).trigger('hashchange');
  });

    $('a.picture').lightBox();
});