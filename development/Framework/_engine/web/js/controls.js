var Controls = function()
{
   this.$ = function(el)
   {
      return (typeof(el) == 'string') ? document.getElementById(el) : el;
   };

   this.getStyle = function(el, property)
   {
      el = this.$(el);
      if(!el) { console.log('getStyle: element not found.'); return;}
      if (el.style[property]) return el.style[property.replace(/-\D/g, function(match){return match.charAt(1).toUpperCase();})];
      if (el.currentStyle) style = el.currentStyle[property.replace(/-\D/g, function(match){return match.charAt(1).toUpperCase();})];
      if (document.defaultView && document.defaultView.getComputedStyle)
      {
         if (property.match(/[A-Z]/)) property = property.replace(/([A-Z])/g, '-$1').toLowerCase();
         style = window.getComputedStyle(el, '');
         if(style) style = style.getPropertyValue(property);
      }
      if (!style) style = '';
      if (style == 'auto') style = '0px';
      return style;
   };

   this.hasClass = function(el, className)
   {
      el = this.$(el);
      if(!el) { console.log('hasClass: element not found.'); return false;}
     return (' ' + el.className + ' ').indexOf(' ' + className + ' ') != -1;
   };

   this.addClass = function(el, className)
   {
      el = this.$(el);
      if(!el) { console.log('addClass: element not found.'); return;}
      if (!this.hasClass(el, className)) el.className = (el.className + ' ' + className).replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
   };

   this.removeClass = function(el, className)
   {
      el = this.$(el);
      if(!el) { console.log('removeClass: element not found.'); return;}
      el.className = el.className.replace(new RegExp('(^|\\s)' + className + '(?:\\s|$)'), '$1');
   };

   this.toggleClass = function(el, className)
   {
      if (this.hasClass(el, className)) this.removeClass(el, className);
     else this.addClass(el, className);
   };
   this.isIE = function()
   {
      var ver = parseFloat((navigator.userAgent.match(/ie (\d+)/i) || [0,0]) [1]);
      return ver === -1 ? false : ver;
   }
   this.getCompatElement = function()
   {
      if(this.isIE()) return document.getElementsByTagName('body')[0];
      return ((!document.compatMode || document.compatMode == 'CSS1Compat')) ? document.documentElement : document.body;
   };

   this.isBody = function(el)
   {
      return (/^(?:body|html)$/i).test(el.tagName);
   };

   this.getClientPosition = function(event)
   {
      return {x: (event.pageX) ? event.pageX - window.pageXOffset : event.clientX, y: (event.pageY) ? event.pageY - window.pageYOffset : event.clientY};
   };

   this.getPagePosition = function(event)
   {
      return {x: event.pageX || event.clientX + document.scrollLeft, y: event.pageY || event.clientY + document.scrollTop};
   };

   this.getEventTarget = function(event)
   {
      var target = event.target || event.srcElement;
      while (target && target.nodeType == 3) target = target.parentNode;
      return target;
   };

   this.addEvent = function(el, type, fn)
   {
      el = this.$(el);
      if(!el) { console.log('addEvent: element not found.'); return 0;}
      if (el.addEventListener) el.addEventListener(type.toLowerCase(), fn, false);
      else el.attachEvent('on' + type.toLowerCase(), fn);
   };

   this.removeEvent = function(el, type, fn)
   {
      el = this.$(el);
      if(!el) { console.log('removeEvent: element not found.'); return 0;}
      if (el.removeEventListener) el.removeEventListener(type.toLowerCase(), fn, false);
      else el.detachEvent('on' + type.toLowerCase(), fn);
   };

   this.stopEvent = function(event)
   {
      if (event.stopPropagation) event.stopPropagation();
      else event.cancelBubble = true;
      if (event.preventDefault) event.preventDefault();
      else event.returnValue = false;
   };

   this.getWindowSize = function()
   {
      if (typeof window.innerWidth!== 'undefined')  return {x: window.innerWidth, y: window.innerHeight};
      var doc = this.getCompatElement();
      return {x: doc.clientWidth, y: doc.clientHeight};
   };

   this.getWindowScroll = function()
   {
      var sx = this.f_scrollLeft();
      var sy = this.f_scrollTop();
      return {x: sx, y: sy };
   };

   this.getWindowScrollSize = function()
   {
      var doc = this.getCompatElement(), min = this.getWindowSize();
      return {x: Math.max(doc.scrollWidth, min.x), y: Math.max(doc.scrollHeight, min.y)};
   };

   this.getWindowCoordinates = function()
   {
      var size = this.getWindowSize();
      return {top: 0, left: 0, bottom: size.y, right: size.x, height: size.y, width: size.x};
   };

   this.getSize = function(el)
   {
      el = this.$(el);
      if(!el) { console.log('getSize: element not found.'); return{x:0,y:0};}
      return (this.isBody(el)) ? this.getWindowSize() : {x: el.offsetWidth, y: el.offsetHeight};
   };

   this.getScrollSize = function(el)
   {
      el = this.$(el);
      if(!el) { console.log('getScrollSize: element not found.'); return{x:0,y:0};}
      return (this.isBody(el)) ? this.getWindowScrollSize() : {x: el.scrollWidth, y: el.scrollHeight};
   };

   this.getScroll = function(el)
   {
      el = this.$(el);
      if(!el) { console.log('getScroll: element not found.'); return{x:0,y:0};}
      return (this.isBody(el)) ? this.getWindowScroll() : {x: el.scrollLeft, y: el.scrollTop};
   };

   this.getScrolls = function(el)
   {
      el = this.$(el);
      if(!el) { console.log('getScrolls: element not found.'); return{x:0,y:0};}
      var position = {x: 0, y: 0};
      while (el && !this.isBody(el))
      {
         position.x += el.scrollLeft;
         position.y += el.scrollTop;
         el = el.parentNode;
      }
      return position;
   };

   this.getOffsets = function(el)
   {
      el = this.$(el);
      if(!el) { console.log('getOffsets: element not found.'); return {x:0,y:0};}
      if (el.getBoundingClientRect)
      {
         var bound = el.getBoundingClientRect(), html = this.$(document.documentElement), htmlScroll = this.getScroll(html), elemScrolls = this.getScrolls(el), elemScroll = this.getScroll(el), isFixed = (this.getStyle(el, 'position') == 'fixed');
         return {x: parseInt(bound.left) + elemScrolls.x - elemScroll.x + ((isFixed) ? 0 : htmlScroll.x) - html.clientLeft, y: parseInt(bound.top)  + elemScrolls.y - elemScroll.y + ((isFixed) ? 0 : htmlScroll.y) - html.clientTop};
      }
      var sel = el, position = {x: 0, y: 0};
      if (this.isBody(el)) return position;
      while (el && !this.isBody(el))
      {
         position.x += el.offsetLeft;
         position.y += el.offsetTop;
         if (document.getBoxObjectFor || window.mozInnerScreenX != null)
         {
            if (this.getStyle(el, '-moz-box-sizing') != 'border-box')
            {
               position.x += parseInt(this.getStyle(el, 'border-left-width'));
               position.y += parseInt(this.getStyle(el, 'border-top-width'));
            }
            var parent = el.parentNode;
            if (parent && this.getStyle(parent, 'overflow') != 'visible')
            {
               position.x += parseInt(this.getStyle(parent, 'border-left-width'));
               position.y += parseInt(this.getStyle(parent, 'border-top-width'));
            }
         }
         else if (el != sel && !navigator.taintEnabled)
         {
            position.x += parseInt(this.getStyle(el, 'border-left-width'));
            position.y += parseInt(this.getStyle(el, 'border-top-width'));
         }
         el = el.offsetParent;
      }
      if ((document.getBoxObjectFor || window.mozInnerScreenX != null) && this.getStyle(sel, '-moz-box-sizing') != 'border-box')
      {
         position.x += parseInt(this.getStyle(sel, 'border-left-width'));
         position.y += parseInt(this.getStyle(sel, 'border-top-width'));
      }
      return position;
   };

   this.getPosition = function(el, relative)
   {
      el = this.$(el);
      if(!el) { console.log('getPosition: element not found.'); return {x:0,y:0};}
      if (this.isBody(el)) return {x: 0, y: 0};
      var offset = this.getOffsets(el), scroll = this.getScrolls(el);

      var position = {x: offset.x - scroll.x, y: offset.y - scroll.y};
      var relativePosition = (relative && (relative = this.$(relative))) ? this.getPosition(relative) : {x: 0, y: 0};
      return {x: position.x - relativePosition.x, y: position.y - relativePosition.y};
   };

   this.getCoordinates = function(el, relative)
   {
      el = this.$(el);
      if(!el) { console.log('getCoordinates: element not found.'); return{x:0,y:0};}
      if (this.isBody(el)) return this.getWindowCoordinates();
      var position = this.getPosition(el, relative), size = this.getSize(el);
      var obj = {left: position.x, top: position.y, width: size.x, height: size.y};
      obj.right = obj.left + obj.width;
      obj.bottom = obj.top + obj.height;
      return obj;
   };

   this.setPosition = function(el, pos)
   {
      el = this.$(el);
      if(!el) { console.log('setPosition: element not found.'); return;}
      var position = {left: pos.x - parseInt(this.getStyle(el, 'margin-left')), top: pos.y - parseInt(this.getStyle(el, 'margin-top'))};
      var parent = el.parentNode;
      if (this.getStyle(el, 'position') != 'fixed')
      {
         while (parent && !this.isBody(parent))
         {
            pos = this.getStyle(parent, 'position');
            if (pos == 'absolute' || pos == 'relative')
            {
               var pos = this.getPosition(parent);
               position.left -= pos.x;
               position.top -= pos.y;
               break;
            }
            parent = parent.parentNode;
         }
      }
      else
      {
         var scroll = this.getWindowScroll();
         position.left -= scroll.x;
         position.top -= scroll.y;
      }
      el.style.left = position.left + 'px';
      el.style.top  = position.top + 'px';
   };

   this.centre = function(el, overflow)
   {
      el = this.$(el);
      if(!el) { console.log('centre: element not found.'); return;}
      var size = this.getSize(el), winSize = this.getWindowSize();
      var scroll = this.getWindowScroll();
      xx = (winSize.x - size.x) / 2 + scroll.x;
      yy = (winSize.y - size.y) / 2 + scroll.y;
      if (!overflow)
      {
         if (xx < 0) xx = 0;
         if (yy < 0) yy = 0;
      }
      this.setPosition(el, {x: xx, y: yy});
   };

   this.scrollTo = function(el)
   {
      var pos = this.getPosition(el);
      window.scrollTo(pos.x, pos.y);
   };

   this.focus = function(el, x, y)
   {
      el = this.$(el);
      if(!el) { console.log('focus: element not found.'); return;}
      var parent = el.parentNode, flag = false;
      if (this.getStyle(el, 'position') != 'fixed')
      {
         while (parent && !this.isBody(parent))
         {
            if (this.getStyle(parent, 'position') == 'fixed')
            {
               flag = true;
               break;
            }
            parent = parent.parentNode;
         }
      }
      else flag = true;
      el = this.$(el);
      if (!flag)
      {
         x |=  0;
         y |=  0;
         var pos = this.getPosition(el), winSize = this.getWindowSize(), scroll = this.getWindowScroll();
         if (pos.x > winSize.x + scroll.x || pos.x < scroll.x || pos.y > winSize.y + scroll.y || pos.y < scroll.y) 
            window.scrollTo(pos.x + parseInt(x), pos.y + parseInt(y));
      }
      try {el.focus();} catch (err){}
   };

   this.setOpacity = function(el, opacity)
   {
      el = this.$(el);
      if(!el) { console.log('setOpacity: element not found.'); return;}
      if (opacity == 0 && el.style.visibility != 'hidden') el.style.visibility = 'hidden';
      else if (el.style.visibility != 'visible') el.style.visibility = 'visible';
      if (!el.currentStyle || !el.currentStyle.hasLayout) el.style.zoom = 1;
      if (window.ActiveXObject) el.style.filter = (opacity == 1) ? '' : 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity * 100 + ')';
      el.style.opacity = opacity;
   };

   this.insert = function(el, content)
   {
      this.$(el).innerHTML = content;
   };

   this.display = function(el, display)
   {
      el = this.$(el);
      if(!el) { console.log('display: element not found.'); return;}
      if (display != undefined) el.style.display = display;
      else (el.style.display == 'none') ? el.style.display = '' : el.style.display = 'none';
   };

   this.fade = function(el, show, opacity)
   {
      if (!show) this.display(el, 'none');
      else
      {
         el = this.$(el);
         if (opacity == undefined) opacity = 0.5;
         var size = this.getWindowSize();
         el.style.position = 'fixed';
         el.style.top = '0px';
         el.style.left = '0px';
         el.style.width = size.x + 'px';
         el.style.height = size.y + 'px';
         this.setOpacity(el, opacity);
         this.display(el, '');
      }
   };

   this.makeDraggable = function(el, container, fnStart, fnDrag, fnStop, limit)
   {
      var sx, sy, target; bind = this;
      el = this.$(el);
      if(!el) { console.log('makeDraggable: element not found.'); return;}
      if (container) container = this.$(container);
      var fnMouseMove = function(e)
      {
         var pos = bind.getClientPosition(e || event);
         var x = pos.x - sx, y = pos.y - sy;
         if (container)
         {
            var maxX, maxY, minX, minY, sizeContainer = bind.getSize(container), size = bind.getSize(el), pos = bind.getPosition(container);
            minX = pos.x;
            minY = pos.y;
            maxX = minX + sizeContainer.x - size.x;
            maxY = minY + sizeContainer.y - size.y;
            if (x < minX) x = minX;
            if (x > maxX) x = maxX;
            if (y < minY) y = minY;
            if (y > maxY) y = maxY;
         }
         if (limit)
         {
            if (limit.x)
            {
               if (x < limit.x[0]) x = limit.x[0];
               if (x > limit.x[1]) x = limit.x[1];
            }
            if (limit.y)
            {
               if (y < limit.y[0]) y = limit.y[0];
               if (y > limit.y[1]) y = limit.y[1];
            }
         }
         bind.setPosition(el, {x: x, y: y});
         if (fnDrag) fnDrag(target);
         return false;
      };
      var fnMouseUp = function()
      {
         document.onmousemove = null;
         document.onmouseup = null;
         document.ondragstart = null;
         document.body.onselectstart = null;
         if (fnStop) fnStop(target);
      };
      var fnMouseDown = function(e)
      {
         e = e || event;
         var pos = bind.getClientPosition(e), cpos = bind.getPosition(el);
         sx = pos.x - cpos.x;
         sy = pos.y - cpos.y;
         target = bind.getEventTarget(e);
         if (fnStart) fnStart(target);
         document.onmousemove = fnMouseMove;
         document.onmouseup = fnMouseUp;
         document.ondragstart = function(){return false;};
         document.body.onselectstart = function(){return false;};
         return false;
      };
      this.addEvent(el, 'mousedown', fnMouseDown);
   };
   this.inputFilter = function(input, expression)
   {
      if(!input || !input.value) return false;
      var b = expression.test(input.value);
      if(b) input.value = input.value.replace(expression,'');
      return !b;
   };
   this.inputMask = function(input, expression)
   {
      if(!input || !input.value) return false;
      var b = expression.test(input.value);
      if(!b)
      {
         for(var i=0,s='';i<input.value.length;i++)
         {
            if(!expression.test(s+input.value.charAt(i))){input.value = s; return false;}
            s += input.value.charAt(i);
         }
      }
      return !b;
   };

   this.sleep = function(ms)
   {
      var start = (new Date()).getTime();
      while((new Date()).getTime() < start + ms);
   }
   this.htmlspecialchars = function(string, reverse)
    {
        var specialChars = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;"
            }, x;
        if (typeof(reverse) != "undefined")
        {
            reverse = [];
            for (x in specialChars)
                reverse.push(x);
            reverse.reverse();
            for (x = 0; x < reverse.length; x++)
                string = string.replace(
                    new RegExp(specialChars[reverse[x]], "g"),
                    reverse[x]
                );
            return string;
        }
        for (x in specialChars)
            string = string.replace(new RegExp(x, "g"), specialChars[x]);
        return string;
    };

this.f_clientWidth = function() {
   return this.f_filterResults (
      window.innerWidth          ? window.innerWidth                       : 0,
      document.documentElement   ? document.documentElement.clientWidth    : 0,
      document.body              ? document.body.clientWidth               : 0
   );
}
this.f_clientHeight = function() {
   return this.f_filterResults (
      window.innerHeight         ? window.innerHeight                      : 0,
      document.documentElement   ? document.documentElement.clientHeight   : 0,
      document.body              ? document.body.clientHeight              : 0
   );
}
this.f_scrollLeft = function() {
   return this.f_filterResults (
      window.pageXOffset         ? window.pageXOffset                      : 0,
      document.documentElement   ? document.documentElement.scrollLeft     : 0,
      document.body              ? document.body.scrollLeft                : 0
   );
}
this.f_scrollTop = function() {
   return this.f_filterResults (
      window.pageYOffset         ? window.pageYOffset                      : 0,
      document.documentElement   ? document.documentElement.scrollTop      : 0,
      document.body              ? document.body.scrollTop                 : 0
   );
}
this.f_filterResults = function(n_win, n_docel, n_body) {
   var n_result = n_win ? n_win : 0;
   if (n_docel && (!n_result || (n_result > n_docel))) n_result = n_docel;
   return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}

};



if(!window.console) 
   window.console = 
   {
      log : function(a)
      { 
         // alert(a);
      }
   };//for IE fix
var controls = new Controls();
