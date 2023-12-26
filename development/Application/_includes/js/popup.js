var PopUp = function()
{
   this.pp = new Array();
   this.ff = {};
   var bind = this;

   this.onKeyUp = function(e)
   {
      var e = e || window.event;
      if (e.keyCode == 27)
      {
         var id = bind.pp.pop();
         if (id == undefined) return;
		 bind.pp.push(id);
         bind.hide(id);
         controls.stopEvent(e);
         if (!e.preventDefault) e.keyCode = 0;
      }
   };
   
   this.onClickBody = function(e)
   {
      var id = bind.pp.pop();
      if (id == undefined) return;
	  bind.pp.push(id);
	  e = e || window.event;
	  var pos = controls.getClientPosition(e);
      var coor = controls.getCoordinates(id);
      var scroll = controls.getWindowScroll();
	  if (pos.x < coor.left - scroll.x || pos.x > coor.right - scroll.x || pos.y < coor.top - scroll.y || pos.y > coor.bottom - scroll.y)
	  {
	     if (!bind.isClicked) bind.hide(id);
		 bind.isClicked = false;
      }
      controls.stopEvent(e);
   };

   controls.addEvent(document, 'keyup', this.onKeyUp);
   controls.addEvent(document, 'click', this.onClickBody);

   this.initialize = function(id, shadow)
   {
      if (!controls.$(id)) return;
      if (shadow)
      {
         var fade = controls.$('fade_' + id);
         if (!fade)
         {
            var zindex = parseInt(controls.getStyle(id, 'zIndex'));
            fade = document.createElement('div');
            fade.id = 'fade_' + id;
            fade.className = 'popup-shadow';
            fade.style.display = 'none';
            if (zindex > 0) fade.style.zIndex = zindex - 1;
            document.body.appendChild(fade);
         }
      }
   };

   this.show = function(id, opacity, centre, fadeTime, closeTime)
   {
      var bind = this;
      if (centre == undefined) centre = 1;
      if (opacity == undefined) opacity = 0.5;
	  if (this.pp.length > 0 && this.pp[this.pp.length - 1] == id) 
	  {
	     controls.display(id, '');
		 if (controls.$('fade_' + id) && opacity > 0) controls.fade('fade_' + id, true, opacity);
	     return;
	  }
      if (typeof(hideBadElements) == 'function') hideBadElements();
      var zindex = parseInt(controls.getStyle((this.pp.length > 0) ? this.pp[this.pp.length - 1] : id, 'zIndex'));
      if (zindex > 0)
      {
	     if (this.pp.length > 0)
		 {
            controls.$(id).style.zIndex = zindex + 10;
            if (controls.$('fade_' + id)) controls.$('fade_' + id).style.zIndex = zindex + 9;
	     }
		 else if (controls.$('fade_' + id)) controls.$('fade_' + id).style.zIndex = zindex - 1;
      }
      if (controls.$('fade_' + id) && opacity > 0) controls.fade('fade_' + id, true, opacity);
      if (fadeTime > 0)
      {
         this.ff[id] = {'time': fadeTime, 'opacity': opacity};
         controls.setOpacity(id, 0);
         var op = 0;
         var h = setInterval(function()
         {
            op += 0.1;
            if (op >= 1) clearInterval(h);
            else controls.setOpacity(id, op);
         }, fadeTime / 10);
      }
      if (closeTime > 0)
      {
         this.ff[id]['onclose'] = setTimeout(function()
         {
            bind.hide(id);
            for (var i = 0; i < bind.pp.length; i++)
            {
               if (bind.pp[i] == id)
               {
                  bind.pp.splice(i, 1);
                  return;
               }
            }
         }, closeTime);
      }
      controls.display(id, '');
      if (centre > 0) controls.centre(id);
      this.pp.push(id);
   };

   this.hide = function(id)
   {
      if (controls.$(id).style.display == 'none') return;
      if (typeof(showBadElements) == 'function') showBadElements();
      if (controls.$('fade_' + id)) controls.fade('fade_' + id, false);
      if (this.ff[id])
      {
         if (this.ff[id]['onclose']) clearTimeout(this.ff[id]['onclose']);
         var op = 1;
         var h = setInterval(function()
         {
            op -= 0.1;
            if (op <= 0)
            {
               clearInterval(h);
               controls.display(id, 'none');
            }
            else controls.setOpacity(id, op);
         }, this.ff[id]['time'] / 10);
      }
      else controls.display(id, 'none');
      bind.pp.pop();
   };
};

var popup = new PopUp();
