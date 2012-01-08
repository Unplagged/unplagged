$(document).ready(function(){
  
  var dropdownButton = $('<span class="dropdown-button arrow-up"></span>');
  dropdownButton.toggle(function(){
    //$('#main-header').slideUp();
    $('#main-header').css('top', '-32px').css('margin-bottom', '80px');
    dropdownButton.removeClass('arrow-up');
    dropdownButton.addClass('arrow-down');
  }, function(){
    $('#main-header').css('top', '0').css('margin-bottom', '115px');
    dropdownButton.addClass('arrow-up');
    dropdownButton.removeClass('arrow-down');
  });
  $('#settings-panel').append(dropdownButton);
});


