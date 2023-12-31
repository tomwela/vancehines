var CustomDDB = function()
{
   this.ids = {};
   
   this.show = function(id)
   {
      if (!controls.$(id)) return;
      var display = controls.getStyle('list_' + id, 'display');
      if(display != 'none') return;
      var ddbID  = 'container_'+id;
      var listID = 'list_'+id;
      controls.display(listID, 'block');
      controls.$(ddbID).style.zIndex++;
      controls.addClass('select_' + id, 'open');
      controls.addClass('container_' + id, 'open');
      // console.log(id, controls.$(ddbID).style.zIndex);
   }

   this.hide = function(id)
   {
      if (!controls.$(id)) return;
      var display = controls.getStyle('list_' + id, 'display');
      if(display == 'none') return;
      var ddbID  = 'container_'+id;
      var listID = 'list_'+id;
      controls.display(listID, 'none');
      controls.$(ddbID).style.zIndex--;
      controls.removeClass('select_' + id, 'open');
      controls.removeClass('container_' + id, 'open')
      // console.log(id, controls.$(ddbID).style.zIndex);
   }


   this.initialize = function(id)
   {
      if (!controls.$(id)) return;
      this.ids[id] = id;
      var bind = this;
      controls.addEvent(document.body, 'click', function()
      {
        if (!controls.$(id)) return;
        bind.hide(id);
      });
    var click = function(e)
    {
      if(controls.hasClass('container_' + id, 'disabled')){controls.stopEvent(e||event); return false;}
      var listid = 'list_'+id;
      var display = controls.getStyle(listid, 'display');
      for (var uid in bind.ids) if (controls.$('list_' + uid) && uid!=id) bind.hide(uid);
      if(display == 'none')
      {
        bind.show(id);
      }
      else
      {
        bind.hide(id);
      }
     controls.stopEvent(e || event);
    }; 
    var select = function(e)
    {
      click(e);
      if(controls.hasClass('container_' + id, 'disabled')){controls.stopEvent(e||event); return false;}
      controls.$(id).value = this.getAttribute('data-id');
      controls.$('val_'+id).innerHTML = this.getAttribute('data-text');
      bind.hide(id);
      eval(controls.$('container_'+id).getAttribute('onchange'));
    };
    controls.addEvent('val_' + id, 'click',    click);
    controls.addEvent('select_' + id, 'click', click);
    for(var i=0; i<controls.$('list_'+id).childNodes.length; i++)
      controls.addEvent('li_' + i + '_' + id, 'click', select);


    setInterval(function()
    {
      if (!controls.$(id)) return;
      var cls = controls.$(id).className; 
      if (cls == 'error') 
      {
        controls.addClass('container_' + id, cls);
        controls.addClass('list_' + id, cls);
      } 
      else 
      {
        controls.removeClass('container_' + id, 'error');
        controls.removeClass('list_' + id, 'error');
      }
    }, 200);
   }
};

var cddb = new CustomDDB();
