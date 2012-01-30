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
  
  //wrap home menu button, so that icon gets shown
  var homeButton = $('#header .navigation .home');
  homeButton.wrapInner('<span class="ir"/>');
});


