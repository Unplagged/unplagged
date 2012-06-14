/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,e,b){
  var c="hashchange",h=document,f,g=$.event.special,i=h.documentMode,d="on"+c in e&&(i===b||i>7);
  function a(j){
    j=j||location.href;
    return"#"+j.replace(/^[^#]*#?(.*)$/,"$1")
    }
    $.fn[c]=function(j){
    return j?this.bind(c,j):this.trigger(c)
    };
    
  $.fn[c].delay=50;
  g[c]=$.extend(g[c],{
    setup:function(){
      if(d){
        return false
        }
        $(f.start)
      },
    teardown:function(){
      if(d){
        return false
        }
        $(f.stop)
      }
    });
f=(function(){
  var j={},p,m=a(),k=function(q){
    return q
    },l=k,o=k;
  j.start=function(){
    p||n()
    };
    
  j.stop=function(){
    p&&clearTimeout(p);
    p=b
    };
    
  function n(){
    var r=a(),q=o(m);
    if(r!==m){
      l(m=r,q);
      $(e).trigger(c)
      }else{
      if(q!==m){
        location.href=location.href.replace(/#.*/,"")+q
        }
      }
    p=setTimeout(n,$.fn[c].delay)
  }
  $.browser.msie&&!d&&(function(){
  var q,r;
  j.start=function(){
    if(!q){
      r=$.fn[c].src;
      r=r&&r+a();
      q=$('<iframe tabindex="-1" title="empty"/>').hide().one("load",function(){
        r||l(a());
        n()
        }).attr("src",r||"javascript:0").insertAfter("body")[0].contentWindow;
      h.onpropertychange=function(){
        try{
          if(event.propertyName==="title"){
            q.document.title=h.title
            }
          }catch(s){}
    }
  }
};

j.stop=k;
o=function(){
  return a(q.location.href)
  };
  
l=function(v,s){
  var u=q.document,t=$.fn[c].domain;
  if(v!==s){
    u.title=h.title;
    u.open();
    t&&u.write('<script>document.domain="'+t+'"<\/script>');
    u.close();
    q.location.hash=v
    }
  }
})();
return j
})()
})(jQuery,this);
       

/**
 * Plugin that enables Plupload as the file uploader and uses Bootstrap modals to
 * query for additional information.
 * 
 * Dependencies
 */
(function($){

  $.fn.unplaggedFileUpload = function(){
    var files = new Array();
    var filesRunning = false;
    var fileUploader = null;  
    
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
        QueueChanged: queueFileForModalForm,
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
  
    function queueFileForModalForm(uploader){
      fileUploader = uploader;
      $.each(uploader.files, function(){
        //add the current file to our own queue
        files.push(this);

        if(!filesRunning){
          filesRunning = true;
          getDataForNextFile();
        }
      });
      
    }  
    
    var fileModal = $('#file-data');
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