/**
 * This file is the place for all self-written scripts, as long as they are short enough
 * to be maintained here.
 * 
 * Please put everything at least into a self-executing function block to don't pollute
 * the global namespace.
 */
$(document).ready(function(){  
  
  // enable line numbers on textareas with a certain class name
  $('textarea.line-numbers').numberfy();
  // enable the twitter bootstrap tooltip and dropdowns for certain classnames
  $('.tooltip-toggle').tooltip();
  $('.dropdown-toggle').not('#login-button').dropdown();

  $('.alert:not(.unclosable)').prepend('<a class="close" data-dismiss="alert" href="#">&times;</a>');
  $().alert()
 
  // enable chosen for the case selection in the upper right and submit it on change
  $('.case-settings-box select').chosen({
    allow_single_deselect: true
  }).change(function(){
    $(this).closest('form').submit();  
  });
  
  // select all button for de-hyphen area
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
  homeButton.wrapInner('<span class="ir home-icon"/>');
  
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
      btn.html('<i class="icon-ok-circle"></i>' + $.t('showcolors'));
    } else {
      btn.attr('data-colors', 'true');
      compareFragmentTexts(btn.attr('data-value'), 'true');
      btn.html('<i class="icon-remove-circle"></i>' + $.t('hidecolors'));
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
  
  // fragment update for two column view
  $(".set-candidate-fragment, .set-source-fragment").live('click', function() {
    var target = '#candidate-text';
    if($(this).hasClass('set-source-fragment')) {
      target = '#source-text';
    }
    
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
    var str = '';
    // if the element is not a list element, get the parent li element
    if($(startLine)[0].tagName != 'LI') {
      startLine = $(startLine).parent('li');
      str = $(startLine).find("span").text();
    } else {
      str = $(startLine).text();
    }
    if($(endLine)[0].tagName != 'LI') {
      endLine = $(endLine).parent('li');
      str = $(endLine).find("span").text();
    } else {
      str = $(endLine).text();
    }
     
    if(str.length > 30) {
      str = str.substr(0, 30) + '...';
    }
    
    var textElement = $(target);
    textElement.html("'" + str + "'");
    textElement.closest('li').removeClass('hidden');
      
    $(this).parent().addClass('hidden');
      
    if($(this).hasClass('set-candidate-fragment')) {
      $('#fragment-candidate-start-line').val($(startLine).attr('value'));
      $('#fragment-candidate-end-line').val($(endLine).attr('value'));
    } else {
      $('#fragment-source-start-line').val($(startLine).attr('value'));
      $('#fragment-source-end-line').val($(endLine).attr('value'));
    }
    
    return false;
  });
  
  $(".reset-candidate-fragment, .reset-source-fragment").live('click', function() {
    var target = '.set-candidate-fragment';
    if($(this).hasClass('reset-source-fragment')) {
      target = '.set-source-fragment';
    }
    $(target).parent().removeClass('hidden');
    $(this).parent().addClass('hidden');
    
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
          $('a.picture').lightBox();
        });
      }
    });

    $(window).trigger('hashchange');
  });

  $('a.picture').lightBox();
  
  // turns checkboxes in forms into single toggle elements
  $('input[type="checkbox"].btn').each(function(index) {
    updateCheckBox($(this));    
  });
  
  function updateCheckBox(element) {
    var classes = element.attr('class');

    // get label value and hide the element afterwards
    var label = $('#' + element.attr('id') + '-label label').text();

    $('#' + element.attr('id') + '-label').hide();
    $('#' + element.attr('id') + '-element').hide();
    
    // insert the new bootstrap-based element
    element.parent().parent().prepend('<a class="' + classes + '" data-toggle="button" data-checkbox="' + element.attr('id') + '">' + label + '</a>');
    if(element.is(':checked')){
      $('a[data-checkbox="' + element.attr('id') + '"]').trigger('click').addClass('active');
    }
  }
  
  $('.btn[data-toggle="button"]').live('click', function(){
    var id = $(this).attr('data-checkbox');
    var cb = $('#' + id);
    
    cb.attr('checked', !cb.is(':checked'));
    
    if(cb.hasClass('inherited') && !cb.hasClass('disabled')){
      if($(this).hasClass('btn-primary')) {
        $(this).removeClass('btn-primary');
      } else {
        $(this).addClass('btn-primary');
      }
    }
  });
  
  // conversation
  $('.comment-toggle').live('click', function() {
    var $activity = $(this).parents('.activity');
    var $comments = $activity.find('.comments-box');

    if($comments.is(':visible')) {
      $comments.slideUp('slow');
    } else {
      $comments.slideDown('slow');
    }
    return false;
  });
  
  function renderConversation(data, target) {
    var tpl;
    
    switch(data.type) {
      case 'comment':
        tpl = '<li class="comment">' +
        '<div class="image"><img class="avatar-small" src="' + data.author.avatar + '" /></div>' +
        '<div class="details">' +
        '<div class="title"><b>' + data.author.username + '</b> ' + data.text + 
        ' <span class="date">' + data.created.humanTiming + '</span>' +
        '</div>' +
        '</div>' +
        '</li>';
        break;
      case 'rating':
        var icon = data.rating ? 'icon-thumbs-up' : 'icon-thumbs-down';
        tpl =  '<div class="rating">' +
        '<div class="details">' +
        '<div class="title">' + '<i class="' + icon + '"></i> <b>' + data.user.username + '</b> ' + $.t('conversation.rated') + '.' +
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
  
  $('.comment-submit').live('click', function(){
    var $commentForm = $(this).closest('.comment-form');
    console.log($commentForm.find('.comment-text'));
    $.post($commentForm.attr('action'), $commentForm.serialize(), function(data) {

      //text.val('');
      //renderConversation(data, target)
    }, 'json');
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
      
      var options = null;
      if(viewScript == 'collaborator') {
        options = {
          'caseId': $(this).attr('data-case'),
          'roleId': ui.item.role
        }
      }
      createSelectedElement(sourceId, ui.item.value, ui.item.label, viewScript, options);
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
    var source = $('#' + sourceId);
    source.autocomplete('option', 'source', source.attr('data-callback') + '/skip/' + getIdsToSkip(sourceId, true));
  }
  
  function createSelectedElement(sourceId, value, label, viewScript, options){
    var element = '';
    
    if(viewScript == 'tag') {
      element = '<a data-source="' + sourceId + '" data-remove="true" data-id="' + value + '" href="#" data-for="' + value + '" class="btn">';
      element += '<i class="icon-tag icon-fam"></i>' + label + '<i class="icon-remove icon-right"></i>';
      element += '<input type="hidden" name="' + sourceId + '[]" value="' +  value + '" /></a> ';
      $('div[data-wrapper-for=' + sourceId + ']').append(element);
      
    } else if(viewScript == 'collaborator') {
      var caseId = (parseInt(options.caseId) == options.caseId) ? '/id/' + options.caseId : '';

      $.post('/case/get-roles' + caseId, {}, function(response) {  
        element = '<div class="well" data-source="' + sourceId + '" data-id="' + value + '">';
        element += '<img class="avatar no-shadow" src="/images/default-avatar.png">';
        element += '<div class="names">';
        element += '<span class="username">' + label + '</span>';
        element += '</div>';
        element += '<div class="options">';
        element += '<select class="span2" name="' + sourceId + '[' + options.roleId + ']" style="width: 150px;">';
        
        $.each(response.roles, function(roleId, roleName) { 
          element += '<option value=' + roleId + '>' + roleName + '</option>';
        });
        element += '</select>';
        element += ' <a href="#" class="btn btn-danger" data-remove="true" data-for="' + value + '"><i class="icon-remove"></i></a></div>';
        element += '<input type="hidden" name="' + sourceId + '-users[]" value="' +  value + '" />';
        element += '</div>';
        $('div[data-wrapper-for=' + sourceId + ']').append(element);
        updateAutocompleteSource(sourceId);
      }, "json");
      
    } else if(viewScript == 'permission') {
      element = '<div class="well" data-source="' + sourceId + '" data-id="' + value + '">';
      element += '<img class="avatar no-shadow" src="/images/default-avatar.png">';
      element += '<div class="names">';
      element += '<span class="username">' + label + '</span>';
      element += '</div>';

      var permissions = ['authorize', 'delete', 'update', 'read'];
      element += '<div class="options">';
      $.each(permissions, function(index, permission) { 
        element += '<span id="' + permission + '-' + value + '-label">';
        element += '<label for="' + permission + '-' + value + '">' + $.t('permission.' + permission) + '</label>';
        element += '<input type="checkbox" name="' + permission + '[]" id="' + permission + '-' + value + '" value="' + value + '" class="btn btn-checkbox btn-small">';
        element += '</span>';
      });

      element += '<a href="#" class="btn btn-danger" data-remove="true" data-for="' + value + '"><i class="icon-remove"></i></a></div>';
      element += '<input type="hidden" name="' + sourceId + '[]" value="' +  value + '" />';
      element += '</div>';
      
      $('div[data-wrapper-for=' + sourceId + ']').append(element); 
      $('div[data-id=' + value + '] input[type="checkbox"].btn').each(function(index) {
        updateCheckBox($(this));
      });
    }

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

    element.remove();
    updateAutocompleteSource(sourceId);

    return false;
  });
  
  /**
   * Updates the fields shown based on the current bibtex type.
   */
  function updateBibTexForm(){
    var type = $('#bibSourceType').val();

    $.each($('.bibtex'), function() {
      var id = $(this).attr('id');
      if($(this).hasClass(type)){
        $('#' + id + '-label').show();
        $('#' + id + '-element').show();
      } else {
        $('#' + id + '-label').hide();
        $('#' + id + '-element').hide();
        $(this).val('');
      }
    });
  }
  $("#bibSourceType").change(function(){
    updateBibTexForm();
  });
  updateBibTexForm();
  
  
  $('#upload-queue').unplaggedFileUpload();
  $().unplaggedContextMenu();

  $('#actions-menu').css('margin-top', '-' + $('#actions-menu').height()/2 + 'px');

  $('#actions-menu.poped-in').live('click', function() {
    $(this).children('.arrow').html('&raquo;');
    $(this).css('margin-right', '0px');
    $(this).removeClass('poped-in').addClass('poped-out');
  });
  $('#actions-menu.poped-out').live('click', function() {
    $(this).children('.arrow').html('&laquo;');
    $(this).css('margin-right', '-250px');
    $(this).removeClass('poped-out').addClass('poped-in');
  });
  
  $('#source-document-select #source-document').change(function(){
    $('#source-document-select').submit();
  });
  
  $('.pager .disabled a').live('click', function() {
    return false;
  });
  
  $('label.required').append(' <span class="red">*</span>');
  
  // update the file id in the parse file modal
  $('a[data-toggle=modal][href=#parseFile]').live('click', function(){
    $('#parseFile #file-id').val($(this).attr('data-id'));
  });
  
  $('a[rel=tooltip]').tooltip();
  
  //login dropdown
  $('#login-button').click(function(){
    var loginDropdown = $(this).parents('.login-group').find('.login-dropdown');
    if(loginDropdown.is(':hidden')){
      $(this).parents('.login-group').find('.login-dropdown').show();
    }else{
      $(this).parents('.login-group').find('.login-dropdown').hide();
    }
    
    return false;
  });
  
  $('.login-group .close').click(function(){
    $(this).parents('.login-group').find('.login-dropdown').hide();
  });
  
});