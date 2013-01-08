$(document).ready(function(){  
    
  var timerHandle = null;
  var pendingTimer = null;  
  var messages = new Array();
  var lastStep = 0;
  var nextStep = parseInt($('.wizard-steps').attr('data-current-step'));
  
  init();
  function init(){
    $('.alert .close').click(function(){
      $(this).parent().slideUp();
    }); 
    $('#start-button').click(startInstallation);
    
    $('#enableImprint').click(function(){
      if($(this).is(':checked')){
        $('.imprint-data').slideDown();
        var body = $(this).parents('.modal-body');
        body.animate({
          scrollTop: body.prop("scrollHeight")
        }, 500);
      }else{
        $('.imprint-data').slideUp();
      }
    });
   
/*/ use plugin to validate all inputs like html5 would/should
    $("input,select,textarea").not("[type=submit]").jqBootstrapValidation({
      preventSubmit: true,
      submitError: function ($form, event, errors) {
        $form.find('.modal-alert').slideDown();
        event.preventDefault();
      },
      submitSuccess: function ($form, event) {
        $form.find('.modal-alert').slideUp();
        event.preventDefault();
      }
    });  */
}
  
/**
   * Checks whether we are ready to start and then starts the necessary steps.
   */
function startInstallation() {
  if(!$(this).hasClass('disabled')){
    lastStep = 0;
    $(".alert").hide();
    $('.welcome-message').slideUp('slow');
    addConsoleLine('Starting installation', 'text-info'); 
    
    startCurrentStep();
  }
}
  
function startCurrentStep(){
  if(nextStep!=lastStep){
    lastStep = nextStep;
      
    switch(nextStep){
      case 1:
        startDirectoryCreation();
        break;
      case 2:
        saveDatabaseCredentials();
        break;
      case 3:
        saveContactData();
        break;
      case 4:
        createAdmin();
        break;
      case 5:
        checkSoftware();
        break;
      case 6:
        $('#finish-modal').modal('show');
        break;
    }
  }
}
  
function checkSoftware(){
  addConsoleLine('Querying for software paths', 'text-info');
  $('#software-modal').modal({
    show: true, 
    backdrop:'static'
  });
}
  
function saveContactData(){
  addConsoleLine('Querying for contact data<span class="pending"></span>', 'text-info');
  $('#contact-modal').modal({
    show: true, 
    backdrop:'static'
  });
  startPendingPoints();
}
  
function createAdmin(){
  addConsoleLine('Querying for admin account', 'text-info');
  $('#admin-modal').modal({
    show: true, 
    backdrop:'static'
  });
}
  
function saveDatabaseCredentials(){
  addConsoleLine('Querying for database credentials<span class="pending"></span>', 'text-info');
  $('#database-modal').modal({
    show: true, 
    backdrop:'static'
  });
  startPendingPoints();
}
  
$('.close-button').click(function(){
  $(this).parents('.modal').modal('hide');
  enableRestart();
  clearPendingPoints();
  addConsoleLine('User cancelled');
    
  return false;
});
  
$('.modal-footer button').click(function(){
  $form = $(this).parents('form');
  submitForm($form, formSuccess);
  $(this).parents('.modal').modal('hide');
  return false;
});
  
function setNextStep(stepNumber){
  nextStep = stepNumber;
}
    
function formSuccess(response, status) {
  clearPendingPoints();
  if(!response.success){
    printAllMessages(response.messages, enableRestart, '.wrong-input');
  }else{
    setNextStep(nextStep+1);
    printAllMessages(response.messages, startCurrentStep);
  }
}
  
/**
   * Starts the creation of the directories on the server-side.
   */
function startDirectoryCreation(){
  addConsoleLine('Creating folder structure<span class="pending"></span>', 'text-info');
  $.ajax({
    url: window.location.pathname + '/installDirectories', 
    type: 'POST',
    dataType: 'json',
    success: function(response, status) {
      clearPendingPoints();
      if(!response.success){
        printAllMessages(response.messages, enableRestart, '.fix-console');
      }else{
        printAllMessages(response.messages, startCurrentStep);
        setNextStep(2);
      }
    },
    error: function(jqXHR, textStatus){
      clearPendingPoints();
      if(textStatus=='timeout'){
        addConsoleLine('A timeout occured during the download. Please try again later.', 'text-error');
      } 
    }
  });
  startPendingPoints();
}
  
/**
   * Uses AJAX to send the given form and starts the callback afterwards.
   */
function submitForm($form, successCallback){
  //simply serialize everything, send it and let the other side figure out whether the data is valid
  var formData = $form.find('input,select,textarea').serializeArray();
  $.ajax({
    url: $form.attr('action'),
    type: 'POST', 
    data: formData,
    dataType: 'json',
    success: successCallback
  });
}  
  
/**
 * Shows the button for restarting the installation and the error with the given class name if given.
 */
function enableRestart(error){
  if(error){
    $(error).show();
  }
  $('#start-button').text('Restart Installation');
  $('.welcome-message').slideDown('slow');
}
  
/**
 * Starts a loading animation(adding points one by one...) to all elements with the class "pending".
 */
function startPendingPoints(){
  if(!pendingTimer){
    pendingTimer = setInterval(function(){
      var $pending = $('.pending');
      $pending.text();
      if($pending.text().length < 8){
        $pending.append('.');
      }else{
        $pending.text('.');
      }
    }, 1500)
  }
}
  
/**
 * Stops the loading animation.
 */
function clearPendingPoints(){
  clearInterval(pendingTimer);
  $('.pending').remove();
}
  
/**
 * Prints all the given messages in the console.
 */
function printAllMessages(messages, callback, callbackParams){
  for(var i=0; i< messages.length; i++) {
    addConsoleLine(messages[i].message, 'text-' + messages[i].namespace, callback, callbackParams);
  }
}
  
/**
 * Adds the given message to the array and starts the timed showing of lines, to simulate a
 * work in progres effect.
 */
function addConsoleLine(line, classes, callback, callbackParams){
  messages.push('<li>&gt;&gt;&gt;&nbsp;<span class="' + classes + '">' + line + '</span></li>');
  clearInterval(timerHandle);
    
  timerHandle = setInterval(function(callback, callbackParams){
    if(messages.length>0){
      var console = $("#console");
      $('#console li:last-child').before(messages.shift());
      console.animate({
        scrollTop: console.prop("scrollHeight") - console.height()
      }, 500);
    }else{
      if(callback){
        callback(callbackParams);
      }
    }
  }, 700, callback, callbackParams);
}
  
});