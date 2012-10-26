$(document).ready(function(){  
    
  // use plugin to validate all inputs like html5 would/should
  $("input,select,textarea").not("[type=submit]").jqBootstrapValidation({
    preventSubmit: true,
    submitError: function ($form, event, errors) {
      event.preventDefault();
    },
    submitSuccess: function ($form, event) {
      var nextTab = $form.find('button[type=submit]').attr('data-step-id');
      switchToTab(nextTab);

      event.preventDefault();
    }
  });
  
  
  $('#navigation a').click(function(){
    if(!$(this).parent().hasClass('disabled')){
      var activeTab = $('#navigation li.active a').attr('data-tab-id');
      var form = $('#step' + activeTab + ' form');
      var submitBtn = form.find('button[type=submit]');

      if(submitBtn.length == 1) {
        var nextStep = submitBtn.attr('data-step-id');
        submitBtn.attr('data-step-id', $(this).attr('data-tab-id'));
        form.submit();
        submitBtn.attr('data-step-id', nextStep);
      } else {
        switchToTab($(this).attr('data-tab-id'));
      }
    }
    return false;
  });
    
  function switchToTab(tabId) {
    $('.progress').removeClass('progress-success, progress-danger');
        
    // before switching, validate the current page first        
    $('.tab-pane').removeClass('active');
    $('#navigation li').removeClass('active');
            
    $('#tab-btn-' + tabId).parent()
      .removeClass('disabled')
      .addClass('active');
    $('#step' + tabId).addClass('active');
        
    if(tabId < $('.nav-pills').length) {
      $('.progress .bar').width((tabId * 20) + '%');       
    } else {        
      startInstallation();
    }
  }
    
  function startInstallation() {
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