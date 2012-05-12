/**
 * This file is the place for all self-written scripts, as long as they are short enough
 * to be maintained here.
 * 
 * Please put everything at least into a self-executing function block to don't pollute
 * the global namespace.
 */

$(document).ready(function(){
  // lined textareas
  $("textarea.line-numbers").numberfy();
  $('.tooltip-toggle').tooltip();

  $('.dropdown-toggle').dropdown();
  $('.alert').prepend('<a class="close" data-dismiss="alert" href="#">&times;</a>');
  $().alert()
 
  // submit the case selection on change of the dropdown
  $('.case-settings-box select').chosen({
    allow_single_deselect: true
  }).change(function(){
    $(this).closest('form').submit();  
  });
  
  //select all for de-hyphen area
  $('.de-hyphen form').css('position', 'relative').append('<a class="btn select-all" href=""><i class="icon-ok-circle"></i>Deselect all</a>');
  var selectAllLink = $('.select-all');
  selectAllLink.css({
    position: 'absolute', 
    right: 0, 
    bottom: 0
  }).toggle(function(){
    $(this).html('<i class="icon-ok-circle"></i>Select all').parents('form').find('input[type=checkbox]').attr('checked', false);
  }, function(){
    $(this).html('<i class="icon-remove-circle"></i>Deselect all').parents('form').find('input[type=checkbox]').attr('checked', true);
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
  
  
  $(".toggle-comments").live('click', function() {
    var targetId = $(this).attr("for");
    var target = $("#" + targetId);
    var comments = target.children(".comments");
    var loading = target.children(".comments-loading");
    var sourceId = target.children(".write-comment-box").children("input[name='sourceId']").val();

    if(target.is(':visible')) {
      $(this).html("<i class=\"icon-comments icon-fam\"></i>Show conversation");
      $(this).removeClass("expanded");
      
      target.slideUp(800, function() {
        comments.html("");
      });
    } else {
      $(this).html("<i class=\"icon-comments icon-fam\"></i>Hide conversation");
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
  
  // sends comment when return key is pressed in input field
  $('.comment-field').live('keyup', function(e) {
    if(e.keyCode == 13) {
      $(this).parent().children('.write-comment').click();
    }
  });
  
  $(".write-comment").live('click', function(){
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
  
  $(".compare-with-color").click(function(){
    var btn = $(this);
    
    if(btn.attr('data-colors') == 'true') {
      btn.attr('data-colors', 'false');
      compareFragmentTexts(btn.attr('data-value'), 'false');
      btn.html('<i class="icon-ok-circle"></i>Show colors');
    } else {
      btn.attr('data-colors', 'true');
      compareFragmentTexts(btn.attr('data-value'), 'true');
      btn.html('<i class="icon-remove-circle"></i>Hide colors');
    }
    return false;
  });
  
  // creates a new fragment based on selected text
  $('.create-fragment').live('click', function() {
    var selectedText = window.getSelection().getRangeAt(0).toString();

    $('#fragment-content').val(selectedText);
    $('#fragment-create').submit();

    return false;
  });
  
  // fragment creation form
  $("#candidateDocument").change(function(el){
    $('#candidateText').html('');
    updateDocumentPages($(this).val(), ['#candidatePageFrom', '#candidatePageTo']);
  });
  $("#sourceDocument").change(function(){
    $('#sourceText').html('');
    updateDocumentPages($(this).val(), ['#sourcePageFrom', '#sourcePageTo']);
  });
  
  function updateDocumentPages(documentId, targetElements) {
    $.post('/document/read', {
      'id': documentId
    }, function(response) {
      if(response.statuscode == 200) {
        // clear the targets
        $.each(targetElements, function(index, targetId) {
          // clear the targets
          $('' + targetId).html('');
          $('' + targetId).removeAttr('disabled');
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
        $.each(targetElements, function(index, targetId) {
          $('' + targetId).html('');
          $('' + targetId).attr('disabled', 'disabled');
          $('' + targetId).change();
        });
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
        $.each(targetElements, function(index, targetId) {
          // clear the targets
          $('' + targetId).html('');
          $('' + targetId).removeAttr('disabled');
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
        $.each(targetElements, function(index, targetId) {
          $('' + targetId).html('');
          $('' + targetId).attr('disabled', 'disabled');
        });
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
  
  // turns checkboxes in forms into single toggle elements
  $('input[type="checkbox"].btn').each(function(index) {
    var element = $(this);
    var classes = element.attr('class');

    // get label value and hide the element afterwards
    var label = $('#' + element.attr('id') + '-label label').text();
    $('#' + element.attr('id') + '-label').hide();
    $('#' + element.attr('id') + '-element').hide();
    
    
    // insert the new bootstrap-based element
    element.parent().parent().append('<a class="' + classes + '" data-toggle="button" data-checkbox="' + element.attr('id') + '">' + label + '</a>');
    if(element.is(':checked')){
      $('a[data-checkbox="' + element.attr('id') + '"]').trigger('click').addClass('active');
    }
    
  });
  
  $('.btn[data-toggle="button"]').click(function(){
    var id = $(this).attr('data-checkbox');
    var cb = $('#' + id);
    
    cb.attr('checked', !cb.is(':checked'));
    
    if(cb.hasClass('inherited')){
      if($(this).hasClass('btn-primary')) {
        $(this).removeClass('btn-primary');
      } else {
        $(this).addClass('btn-primary');
      }
    }
  });
});
