/**
 * Plugin that enables Plupload as the file uploader and uses Bootstrap modals to
 * query for additional information.
 * 
 * Dependencies are Twitter Bootstrap and it's js file and Plupload.
 */
(function($){

  $.fn.unplaggedFileUpload = function(){
    var files = new Array();
    var filesRunning = false;
    var fileUploader = null;
    //store the modal dialog that is somewhere in the page
    var fileModal = $('#file-data');
    
    // Convert divs to queue widgets when the DOM is ready
    this.pluploadQueue({
      runtimes : 'html5,flash,silverlight,html4',
      url : '/file/upload',
      max_file_size : '1000mb',
      //chunk_size : '5mb', //disable because html5 + chunking kills the filename currently
      unique_names : true,
      flash_swf_url : '/js/libs/plupload/js/plupload.flash.swf',
      silverlight_xap_url : '/js/libs/plupload/js/plupload.silverlight.xap',
      init: {
        QueueChanged: function(uploader){
          // store the uploader so we have access later on 
          fileUploader = uploader;
          $.each(uploader.files, function(){
            //add the current file to our own queue
            files.push(this);

            //start processing of the queue if it isn't already running
            if(!filesRunning){
              filesRunning = true;
              getDataForNextFile();
            }
          });
        },
        BeforeUpload: function(uploader, file){
          //take the data that was set in the file on QueueChange, so that it gets also uploaded
          uploader.settings['multipart_params'].description = file.description;
          uploader.settings['multipart_params'].newName = file.newName;
        }
      },
      multipart_params: {
        'description': '', 
        'newName': ''
      }
    });
    
    fileModal.find('.modal-save').click(saveChanges);
    fileModal.find('.modal-close').click(closeFileModal);
     
    function getDataForNextFile(){
      var file = files.shift();
    
      if(file){
        fileModal.find('input, textarea').val('');
        //set the filename in the heading
        fileModal.modal('show').find('#newName').val(file.name.replace(/\.[^/.]+$/, ""));
        //store the file on the modal so that we can retrieve on button click
        $.data(fileModal, 'current-file', file);
      } else {
        finishAdditionalData();
      }
    }
    
    function finishAdditionalData(){
      filesRunning = false;
      fileUploader.start();
    }
    
    function saveChanges(){
      var file = $.data(fileModal, 'current-file');
      if(file){
        var descriptionValue = fileModal.find('#description').val(); 
        var newNameValue = fileModal.find('#newName').val(); 
        file.description = descriptionValue;
        file.newName = newNameValue;
      }
      //$.data(fileModal, 'current-file', null);
      fileModal.one('hidden', function(){
        if(files.length > 0){
          getDataForNextFile();
        } else {
          finishAdditionalData();
        }
      }).modal('hide');
      //fix for https://github.com/twitter/bootstrap/issues/2839
      $('.modal-backdrop').hide();
    }
    
    /**
    * Stops the upload of the current file and deletes the data.
    */
    function closeFileModal(){
      var fileAccess = $.data(fileModal, 'current-file');
      if(fileAccess){
        fileUploader.removeFile(fileAccess);

        $.data(fileModal, 'current-file', null);
      
        fileModal.one('hidden', function(){
          if(files.length>0){
            getDataForNextFile();
          } else {
            finishAdditionalData();  
            fileModal.modal('hide');
            $('.modal-backdrop').hide();
          }
        });
      }
    }
  }
})(jQuery);


/**
 * Plugin that enables the contextmenu.
 */
(function($){
  $.fn.unplaggedContextMenu = function(){
    //unobtrusively add the context menu, so that users without js don't see it
    addContextMenu();
  
    var searchBuffer='';
    // tells whether the click was on the contextmenu or not
    var contextMenu = false;
    var contextMenuElement = $('#contextmenu');
  
    /**
   * Adds the html for the context menu to the body.
   */
    function addContextMenu(){
      var contextMenuElement = '<ul id="contextmenu" class="contextmenu dropdown-menu">' + 
      '<li class="google-search-for start-search"><a href="#"><i class="icon-search"></i> Google Suche nach <span id="google-search-words"></span></a></li>' +
      '<li class="google-search-for delete-search-words"><a href="#"><i class="icon-remove"></i> Google-Suchwörter löschen</a></li>' +
      '<li class="divider"></li>' +
      '<li><a href="#" class="create-fragment"><i class="icon-tasks"></i> Create fragment</a></li>' +
      '<li class="divider"></li>' +
      '<li><a href="http://www.google.de"><i class="icon-globe"></i> Open Google</a></li>' +
      '<li><a href="#" onclick="window.print();"><i class="icon-print"></i> Print page</a></li>' +
      '</ul>';
  
      $('body').append(contextMenuElement);
    }
  
    $('#contextmenu .delete-search-words a').click(deleteSearchWords);
    $('#contextmenu .start-search a').click(googleSearch);
  
  
    //to make it possible to show the contextmenu only on certain elements, 
    //we only use it when the class show-contextmenu is present
    $('.show-contextmenu')
    .attr('title', 'Tip: Use Contextmenu')
    .attr('data-content', 'You can mark words with a leftclick and then open a contexmenu on right click.')
    .popover({
      placement: 'top'
    }).bind('contextmenu', showCustomContextmenu);

    //we probably only need mouseup, because then we know that the selection is finished
    $('.show-contextmenu').bind('mouseup', clickHandler);
    $('.show-contextmenu').click(function(){
      contextMenuElement.hide();
    });

    //mouse enter should have better performance then mousemove, because it should only get called once
    contextMenuElement.mouseenter(function(){
      contextMenu = true;
    });
  
    contextMenuElement.mouseout(function(){
      contextMenu = false;
    });

    function showCustomContextmenu(ev)
    {  
      // if searchBuffer is not empty, it means that the user has selected a word
      // => show our context menu.
      x = (document.all) ? window.event.x + document.body.scrollLeft : ev.pageX;
      y = (document.all) ? window.event.y + document.body.scrollTop : ev.pageY;
      var scrollTop = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop;
      var scrollLeft = document.body.scrollLeft ? document.body.scrollLeft : document.documentElement.scrollLeft;

      contextMenuElement.css({
        'left': ev.clientX + scrollLeft + 'px',
        'top': ev.clientY + scrollTop + 'px',
        'display': 'block'
      });

      // avoid showing default contextMenu
      return false;
    }

    function clickHandler(event)
    {
      // if click on contextmenu or 'right' click, we do not need to save the selection
      // in the searchBuffer
      if (!contextMenu && event.which === 1) {
        
        var selectedText = getSelectedText();
        //shouldn't be possible to get undefined or ' ' now, because the 
        //function always returns '' and is trimmed now
        if (selectedText != ''){
          searchBuffer += ' ' + selectedText;
          updateGoogleSearchText();
        }
      }
    }

    function getSelectedText()
    {
      var text = '';

      if (window.getSelection)
      {
        text = window.getSelection().toString();
      }
      else if (document.getSelection)
      {
        text = document.getSelection();
      }
      else if (document.selection)
      {
        text = document.selection.createRange().text;
      }

      return $.trim(text);
    }

    function copyToClipboard(s) {
      if (window.clipboardData && clipboardData.setData) {
        clipboardData.setData('text', s);
      }
    }

    function deleteSearchWords(){
      searchBuffer = "";
      updateGoogleSearchText();
    
      return false;
    }

    function googleSearch(){
      window.open('http://www.google.de/search?q='+searchBuffer, '_newTab');
      searchBuffer = '';
      updateGoogleSearchText();
    }

    function updateGoogleSearchText(){
      if(searchBuffer.length == 0) {
        $('.google-search-for').hide();
      } else {
        $('.google-search-for').show();
      
        var str = searchBuffer;
        if(str.length > 30) {
          str = str.substr(0, 30) + '...';
        }
        $('#google-search-words').html("'" + str + "'");
      }
    }
  }
})(jQuery);