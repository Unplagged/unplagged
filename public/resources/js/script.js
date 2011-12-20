$(document).ready(function(){
  
  var dropdownButton = $('<span class="dropdown-button arrow-up"></span>');
  dropdownButton.toggle(function(){
    //$('#main-header').slideUp();
    $('#main-header').css('top', '-32px').css('margin-bottom', '5px');
    dropdownButton.removeClass('arrow-up');
    dropdownButton.addClass('arrow-down');
  }, function(){
    $('#main-header').css('top', '0').css('margin-bottom', '35px');
    dropdownButton.addClass('arrow-up');
    dropdownButton.removeClass('arrow-down');
  });
  $('#main-header').append(dropdownButton);
});


