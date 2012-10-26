$(document).ready(function(){  
    
  $('.alert .close').click(function(){
    $(this).parent().slideUp();
  }) 
    
  // use plugin to validate all inputs like html5 would/should
  $("input,select,textarea").not("[type=submit]").jqBootstrapValidation({
    preventSubmit: true,
    submitError: function ($form, event, errors) {
      $('.alert').slideDown();
      event.preventDefault();
    },
    submitSuccess: function ($form, event) {
      $('.alert').slideUp();
      var nextTab = $form.find('button[type=submit]').attr('data-step-id');
      switchToStep(nextTab);

      event.preventDefault();
    }
  });
  
  
  $('#navigation a').click(function(){
    if(!$(this).hasClass('disabled')){
      var form = $('.tab-pane.active form');
      var submitBtn = form.find('button[type=submit]');

      if(submitBtn.length == 1) {
        var nextStep = submitBtn.attr('data-step-id');
        console.log(form.index());
        console.log(nextStep);
        if(nextStep-1 < form.index()){
          switchToStep($(this).attr('data-tab-id'));
        } else {
          submitBtn.attr('data-step-id', $(this).attr('data-tab-id'));
          form.submit();
          submitBtn.attr('data-step-id', nextStep);
        }
      } else {
        switchToStep($(this).attr('data-tab-id'));
      }
    }
    return false;
  });
    
  function switchToStep(stepNumber) {         
    $('.tab-pane').removeClass('active');
            
    // change color of all step navigation elements before the current
    var navigationElements = $('#navigation a');
    navigationElements.removeClass('current').addClass('disabled').slice(0, stepNumber).addClass('current');
    navigationElements.slice(0, stepNumber + 1).removeClass('disabled');
    $('.tab-content .tab-pane').eq(stepNumber-1).addClass('active');
    
    if(stepNumber == $('#navigation a').length){
      startInstallation();
    }
  }
    
  function startInstallation() {
      console.log('hier');
    var checksConsole = $('#checks-console');
    var installationConsole = $('#installation-console');
    installationConsole.html('');
    $('#installation-loading').show();
            
    var data = $('input,select,textarea').serializeArray();
    $.post('/index.php', data, function(response) {
      response = $.parseJSON(response);
            
      var count = response.steps.length;
      var pos = 0;
            
      // on each installation step, the progress bar has to increase            
      var intVar = setInterval(function(){
        var message = response.steps[pos].message;
        var type = response.steps[pos].type;
                
        if(type == 'error') {
          installationConsole.append('<p class="text-error">' + message + ' [error]</p>');
        } else if(type == 'success') {
          installationConsole.append('<p class="text-success">' + message + ' [ok]</p>');
        } else {
          installationConsole.append('<p class="text-status">' + message + '</p>');
        }
                
        pos++;
                
        // update the progress bar on each step
        $('.progress .bar').width(( 60 + (40 * pos / count)) + '%'); 
        if(pos == count) {
          clearInterval(intVar);
                    
          var summaryClass = response.summary.type == 'success' ? 'text-success' : 'text-error';
          installationConsole.append('<p class="' + summaryClass + '"><strong>' + response.summary.message + '</strong></p>');
          $('#installation-loading').hide();
                    
          var barClass = response.summary.type == 'success' ? 'progress-success' : 'progress-danger';
          $('.progress').addClass(barClass);
          $('.progress .bar').width('100%');     
        }
            
      }, 100);
            
            
    });
  }
    
});