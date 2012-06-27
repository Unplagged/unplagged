/**
 * Plugin that enables Plupload as the file uploader and uses Bootstrap modals to
 * query for additional information.
 * 
 * Dependencies are Twitter Bootstrap, it's js file and Plupload.
 */
(function($){

  $.fn.unplaggedFileUpload = function(){
    var fileUploader = null;
    var modalCounter = 0;
    var $this = this;
    var modalVisible = false;
    //just a helper to fix the weird behaviour of QueueChanged, which fires just once
    //when files are added, but does when files are removed during the dialog phase
    var fired = false;
    
    
    function createNextFileModal(dataBackdrop, fileCount){
      var addToCase = '';
      if($this.attr('data-showAddToCase')){
        addToCase =        
          '      <dt id="addToCase-label">' + 
          '        <label for="addToCase" class="optional">' + $this.attr('data-addToCase') + ':</label>' + 
          '      </dt>' +
          '      <dd id="addToCase-element">' +
          '       <input type="checkbox" id="addToCase" class="addToCase" />' +
          '      </dd>'; 
      }
      
      var defaultFileModal = 
      '<div data-modal-number="' + (modalCounter + 1) + '" class="modal hide" data-backdrop="' + dataBackdrop + '">' +
      '  <div class="modal-header">' +
      '    <button class="close modal-close">x</button>' +
      '    <h3>' + $this.attr('data-file-information') + ' <span class="count">' + (modalCounter + 1) + '/' + fileCount + '</span></h3>' +
      '  </div>' +
      '  <div class="modal-body">' +
      '    <dl>' +
      '      <dt id="newName-label"><label for="newName-' + modalCounter + '">' + $this.attr('data-filename') + ':*</label></dt>' +
      '      <dd id="newName-element">' +
      '      <input type="text" id="newName-' + modalCounter + ' name="newName" value="" class="tooltip-toggle newName"></dd>' +
      '      <dt id="description-label">' + 
      '        <label for="description" class="optional">' + $this.attr('data-description') + ':</label>' + 
      '      </dt>' +
      '      <dd id="description-element">' +
      '        <textarea id="description-' + modalCounter + '" class="description" rows="10" cols="160"></textarea>' + 
      '      </dd>' + 
      '      <dt id="makePublic-label">' + 
      '        <label for="makePublic" class="optional">' + $this.attr('data-makePublic') + ':</label>' + 
      '      </dt>' +
      '      <dd id="makePublic-element">' +
      '       <input type="checkbox" id="makePublic" class="makePublic" />' +
      '      </dd>' +
      addToCase + 
      '    </dl>' +
      '  </div>' +
      '  <div class="modal-footer">' +
      '    <button class="btn modal-close">' + $this.attr('data-close') + '</button>' +
      '    <button class="btn btn-primary modal-save">' + $this.attr('data-save') + '</button>' +
      '  </div>' +
      '</div>';
      
      modalCounter++;
      return $(defaultFileModal);
    }
    
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
          if(!fired){
            fired = true;
            // store the uploader so we have access later on 
            fileUploader = uploader;

            //count the files
            var fileCount = 0;
            $.each(uploader.files, function(){
              fileCount++;  
            });
          
            //create a modal for each file
            $.each(uploader.files, function(){
              var fileModal = createNextFileModal('static', fileCount);
              $('body').append(fileModal);
              fileModal.find('input, textarea').val('');
              //store the file on the modal so that we can retrieve on button click
              fileModal.data('current-file', this);
              //set the filename in the input field
              fileModal.find('.newName').val(this.name.replace(/\.[^/.]+$/, ""));
            
              if(!modalVisible){
                modalVisible = true;
                fileModal.modal('show');  
              }
            }); 
          }
        },
        BeforeUpload: function(uploader, file){
          //take the data that was set in the file on QueueChange, so that it gets also uploaded
          uploader.settings['multipart_params'].description = file.description;
          uploader.settings['multipart_params'].newName = file.newName;
          uploader.settings['multipart_params'].makePublic = file.makePublic;
          if($this.attr('data-showAddToCase')){
            uploader.settings['multipart_params'].addToCase = file.addToCase;
          }
        },
        UploadComplete: function(){
          window.location = $this.attr('data-redirect');
        }
      },
      multipart_params: {
        'description': '', 
        'newName': '',
        'makePublic': '',
        'addToCase': ''
      }
    });
    
    //attach button listeners to dynamically added modals
    $('.modal-save').live('click', saveChanges);
    $('.modal-close').live('click', closeFileModal);
    
    function saveChanges(){
      var fileModal = $(this).closest('.modal');
      var file = fileModal.data('current-file');
      if(file){
        file.description = fileModal.find('.description').val();
        file.newName = fileModal.find('.newName').val();
        if(fileModal.find('.makePublic:checked').length>0){
          file.makePublic = true;
        }
        if(fileModal.find('.addToCase:checked').length>0){
          file.addToCase = true;
        }
      }
      $.data(fileModal, 'current-file', null);
      
      removeCurrentModal(fileModal);      
    }
    
    /**
     * Hides the given modal and removes it from the DOM afterwards. It also starts
     * the display of the next dialog if necessary.
     */
    function removeCurrentModal(fileModal){
      fileModal.one('hidden', function(){
        var currentNumber = parseInt(fileModal.attr('data-modal-number'));
        fileModal.remove();
        //fix for https://github.com/twitter/bootstrap/issues/2839
        $('.modal-backdrop').hide().remove();
        showNext(currentNumber + 1);
      });
      fileModal.modal('hide');  
    }
    
    /**
     * Stops the upload of the current file and deletes the data.
     */
    function closeFileModal(){
      var fileModal = $(this).closest('.modal');
      
      //user cancelled, so remove the file of this modal from the uploader queue
      var file = fileModal.data('current-file');
      if(file){
        fileUploader.removeFile(file);
      }
      
      removeCurrentModal(fileModal);
    }
    
    function showNext(nextModal){
      var fileModal = $('.modal[data-modal-number=' + nextModal + ']')
      if(fileModal.length > 0){
        fileModal.modal('show');
      } else {
        //we need to start here if nothing is left, because the queueChanged event
        //weirdly just fires the first time, so if the user would add more files
        //after we finished the dialogs once, nothing would happen
        //if this is fixed sometime in bootstrap, we could let the user start 
        //the upload for theirselves and add files multiple times
        fileUploader.start();  
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
      
      '<li><a href="#" class="set-candidate-fragment"><i class="icon-bookmark"></i> Set as candidate of fragment</a></li>' +
      '<li class="hidden"><a class="reset-candidate-fragment"href="#"><i class="icon-remove"></i> <span id="candidate-text">test</span></a></li>' +
      '<li><a href="#" class="set-source-fragment"><i class="icon-bookmark"></i> Set as source of fragment</a></li>' +
      '<li class="hidden"><a class="reset-source-fragment" href="#"><i class="icon-remove"></i> <span id="source-text">test</span></a></li>' +
      '<li class="disabled"><a href="#" class="create-fragment"><i class="icon-tasks"></i> Create fragment</a></li>' +

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