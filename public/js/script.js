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
    var startLine = '';
    var endLine = '';
    
    if (document.selection) {
      startLine = document.selection.createRange().parentElement();
    } else {
      var selection = window.getSelection();
      if (selection.rangeCount > 0) {
        startLine = selection.getRangeAt(0).startContainer.parentNode;
        endLine = selection.getRangeAt(0).endContainer.parentNode;
      }
    }
    
    $('#fragment-start-line').val($(startLine).attr('value'));
    $('#fragment-end-line').val($(endLine).attr('value'));
    $('#fragment-create').submit();

    return false;
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
      console.log(element);
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
  
  // conversation
  $(".toggle-conversation").live('click', function() {
    var targetId = $(this).attr("for");
    var target = $("#" + targetId);
    var conversation = target.children(".conversation");
    var loading = target.children(".conversation-loading");
    var sourceId = target.children(".write-comment-box").children("input[name='sourceId']").val();

    if(target.is(':visible')) {
      $(this).html("<i class=\"icon-conversation icon-fam\"></i>Show conversation");
      $(this).removeClass("expanded");
      
      target.slideUp(800, function() {
        conversation.html("");
      });
    } else {
      $(this).html("<i class=\"icon-conversation icon-fam\"></i>Hide conversation");
      $(this).addClass("expanded");
      target.show();
      conversation.hide();
      loading.slideDown(800, function() {
        // get the whole conversation
        $.post('/notification/conversation', {
          'source': sourceId
        }, function(data) {
          if(!data.errorcode) {
            conversation.html("");
            $.each(data, function(index, value) {
              conversation.append(renderConversation(value));
            });
            loading.slideUp(800, function() {
              conversation.slideDown(300);
            });
          } else {
            conversation.html('<div class="comment">' + data.message + '</div>');
            loading.slideUp(800, function() {
              conversation.slideDown(300);
            });
          }
        }, "json");
      });

    }
    return false;
  });
  
  function renderConversation(data, target) {
    var tpl;
    
    switch(data.type) {
      case 'comment':
        tpl = '<div class="comment">' +
        '<div class="image"><img class="avatar-small" src="' + data.author.avatar + '" /></div>' +
        '<div class="details">' +
        '<div class="title"><b>' + data.author.username + '</b> ' + data.text + 
        ' <span class="date">' + data.created.humanTiming + '</span>' +
        '</div>' +
        '</div>' +
        '</div>';
        break;
      case 'rating':
        var icon = data.rating ? 'icon-thumbs-up' : 'icon-thumbs-down';
        tpl =  '<div class="rating">' +
        '<div class="details">' +
        '<div class="title">' + '<i class="' + icon + '"></i> <b>' + data.user.username + '</b> rated the fragment.' + 
        ' <span class="date">' + data.created.humanTiming + '</span>' +
        '</div>' +
        '</div>' +
        '</div>';
        break;
    }
    
    if(!target) {
      return tpl;
    } else {
      //              console.log(target);

      target.append(tpl);
    }
  }
  
  // sends comment when return key is pressed in input field
  $('.comment-field').live('keyup', function(e) {
    if(e.keyCode == 13) {
      $(this).parent().children('.write-comment').click();
    }
  });
  
  $(".write-comment").live('click', function(){
    var source = $(this).closest(".write-comment-box").children("input[name='sourceId']");
    var text = $(this).closest(".write-comment-box").children("input[name='text']");

    var target = $(this).closest('.conversation-wrapper').children(".conversation");
    if(text.val()) {
      $.post('/comment/create', {
        'source': source.val(),
        'text': text.val()
      }, function(data) {
        text.val("");
        renderConversation(data, target)
      }, "json");
    }
    return false;
  });
  
  
  // autocompletion form element stuff (used for tags and collaborators)
  $('input[data-callback]').autocomplete({
    create: function(event, ui){ 
      updateAutocompleteSource($(this).attr('id'));
    },
    focus: function(event, ui) {
      return false;
    },
    select: function(event, ui) {      
      var sourceId = $(this).attr('id');
      var viewScript = $(this).attr('data-view-script');
      
      createSelectedElement(sourceId, ui.item.value, ui.item.label, viewScript);
      updateAutocompleteSource(sourceId);

      $(this).val('');
      return false;
    }
  }).live('keypress', function (e) {
    if(e.keyCode == 13){
      var viewScript = $(this).attr('data-view-script');
      if(viewScript == 'tag') { 
        var sourceId = $(this).attr('id');

        createSelectedElement(sourceId, $(this).val(), $(this).val(), viewScript);
        $(this).val('');
      }
      return false;
    }
  });
      
  function updateAutocompleteSource(sourceId){
    console.log('update it');
    var source = $('#' + sourceId);
    source.autocomplete('option', 'source', source.attr('data-callback') + '/skip/' + getIdsToSkip(sourceId, true));
  }
  
  function createSelectedElement(sourceId, value, label, viewScript){
    var element = '';
    
    if(viewScript == 'tag') {
      element = '<a data-source="' + sourceId + '" data-remove="true" data-id="' + value + '" href="#" data-for="' + value + '" class="btn">';
      element += '<i class="icon-tag icon-fam"></i>' + label + '<i class="icon-remove icon-right"></i>';
      element += '<input type="hidden" name="' + sourceId + '[]" value="' +  value + '" /></a> ';
      
    } else if(viewScript == 'user') {
      element = '<div class="well" data-source="' + sourceId + '" data-id="' + value + '">';
      element += '<img class="avatar-small no-shadow" src="/images/default-avatar.png">';
      element += '<div class="names">';
      element += '<span class="username">' + label + '</span>';
      element += '<span class="realname">Benjamin Oertel</span>';
      element += '</div>';
      element += '<div class="options"><a href="#" class="btn">edit rights</a> ';
      element += '<a href="#" class="btn" data-remove="true" data-for="' + value + '"><i class="icon-remove"></i></a></div>';
      element += '<input type="hidden" name="' + sourceId + '[]" value="' +  value + '" />';
      element += '</div>';      
    }

    $('div[data-wrapper-for=' + sourceId + ']').append(element);
  }
  
  // Gets the ids of the elements which should not be returned through autocompletion anymore.
  function getIdsToSkip(sourceId, stringify){
    var skipIds = [];
    
    $.each($('*[data-source=' + sourceId + ']'), function() {
      var dataId = $(this).attr('data-id');
      // is an integer
      if(!isNaN(parseInt(dataId * 1)) && dataId.length > 0){
        skipIds.push(dataId);
      }
    });
    
    return stringify ? skipIds.join(',') : skipIds;
  }

  $('div[data-wrapper-for] .btn[data-remove]').live('click', function(){
    var elementId = $(this).attr('data-for');
    var element = $('*[data-id=' + elementId + ']');
    var sourceId = element.attr('data-source');
   // element.outerHeight(true);
   // element.hide(500, function() {
      element.remove();
   // });
    updateAutocompleteSource(sourceId);

    return false;
  });
  
  $("#type").change(function(){
    updateBibTexForm();
  });
  updateBibTexForm();
  
  function updateBibTexForm(){
    var type = $('#type').val();
    
    if(type == 'full' || type == 'periodikum' ) 
    { 
      $('#zeitschrift-element').show();
      $('#zeitschrift-label').show();	
      $('#monat-element').show();
      $('#monat-label').show();
      $('#tag-element').show();
      $('#tag-label').show();
      $('#nummer-element').show();
      $('#nummer-label').show();
    } else {
      $('#zeitschrift-element').hide();
      $('#zeitschrift-label').hide();
      $('#monat-element').hide();
      $('#monat-label').hide();									
      $('#tag-element').hide();
      $('#tag-label').hide();
      $('#nummer-element').hide();
      $('#nummer-label').hide();									
    }
   
    if(type == 'full' || type == 'aufsatz' ) 
    { 
      $('#sammlung-element').show();
      $('#sammlung-label').show();
      $('#hrsg-element').show();
      $('#hrsg-label').show();
      $('#issn-element').show();
      $('#issn-label').show();						
    } else {
      $('#sammlung-element').hide();
      $('#sammlung-label').hide();
      $('#hrsg-element').hide();
      $('#hrsg-label').hide();
      $('#issn-element').hide();
      $('#issn-label').hide();
    }
   
    if(type == 'full' || type == 'aufsatz' || type == 'periodikum') 
    { 
      $('#seiten-element').show();
      $('#seiten-label').show();	
   
    } else {
      $('#seiten-element').hide();
      $('#seiten-label').hide();
   
    }
   
    if(type == 'full' || type == 'buch' || type == 'aufsatz') 
    { 
      $('#isbn-element').show();
      $('#isbn-label').show();
   
    } else {
      $('#isbn-element').hide();
      $('#isbn-label').hide();
   
    }
    if(type == 'full'){
      $('#kuerzel-element').show();
      $('#kuerzel-label').show();
      $('#beteiligte-element').show();
      $('#beteiligte-label').show();
      $('#ausgabe-element').show();
      $('#ausgabe-label').show();
      $('#umfang-element').show();
      $('#umfang-label').show();
      $('#reihe-element').show();
      $('#reihe-label').show();
      $('#doi-element').show();
      $('#doi-label').show();
      $('#urn-element').show();
      $('#urn-label').show();
      $('#wp-element').show();
      $('#wp-label').show();
      $('#schluessel-element').show();
      $('#schluessel-label').show();
    } else{
      $('#kuerzel-element').hide();
      $('#kuerzel-label').hide();
      $('#beteiligte-element').hide();
      $('#beteiligte-label').hide();
      $('#ausgabe-element').hide();
      $('#ausgabe-label').hide();
      $('#umfang-element').hide();
      $('#umfang-label').hide();
      $('#reihe-element').hide();
      $('#reihe-label').hide();
      $('#doi-element').hide();
      $('#doi-label').hide();
      $('#urn-element').hide();
      $('#urn-label').hide();
      $('#wp-element').hide();
      $('#wp-label').hide();
      $('#schluessel-element').hide();
      $('#schluessel-label').hide();
    }
  }
  
});
