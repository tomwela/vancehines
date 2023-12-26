(function( $ ){
  $.fn.resizableBox = function( options ) {
    
    var defaults = {
		containment: 'body'
	};
	 
	var options = $.extend(defaults, options);
 
	return this.each(function() {
		var o =options;
		//Assign current element to variable, in this case is UL element
		var obj = $(this);             
		 
		//Get all LI in the UL
		var handle_n = $(".handler-n", obj),
			handle_ne = $(".handler-ne", obj),
			handle_e = $(".handler-e", obj),
			handle_se = $(".handler-se", obj),
			handle_s = $(".handler-s", obj),
			handle_sw = $(".handler-sw", obj),
			handle_w = $(".handler-w", obj),
			handle_nw = $(".handler-nw", obj);
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
		handle_n.live('mousedown', function(e) {
			var start=true
				var container = obj.closest(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var maxY=obj[0].t+obj[0].h;
			$('body').css('cursor','n-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					var y;
					if (e.pageY > maxY) {
						y = maxY;
					} else if (e.pageY < minY) {
						y = minY;
					} else {
						y = e.pageY;
					}
					obj.height(obj[0].t+obj[0].h-y).css('top', y-minY)
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
		
		handle_ne.live('mousedown', function(e) {
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			var start=true
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var maxY=obj[0].t+obj[0].h;
			var minX=obj[0].l;
			$('body').css('cursor','ne-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					var y,x;
					if (e.pageY > maxY) {
						y = maxY;
					} else if (e.pageY < minY) {
						y = minY;
					} else {
						y = e.pageY;
					}
					if (e.pageX > maxX) {
						x = maxX;
					} else if (e.pageX < minX) {
						x = minX;
					} else {
						x = e.pageX;
					}
					obj.height(obj[0].t+obj[0].h-y).width(x-obj[0].l).css('top', y-minY)
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
		
		handle_e.live('mousedown', function(e) {
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			var start=true
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var minX=obj[0].l;
			$('body').css('cursor','e-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					var x;
					if (e.pageX > maxX) {
						x = maxX;
					} else if (e.pageX < minX) {
						x = minX;
					} else {
						x = e.pageX;
					}
					obj.width(x-obj[0].l)
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
		
		handle_se.live('mousedown', function(e) {
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			var start=true
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var minY=obj[0].t;
			var minX=obj[0].l;
			$('body').css('cursor','se-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					var y,x;
					if (e.pageY > maxY) {
						y = maxY;
					} else if (e.pageY < minY) {
						y = minY;
					} else {
						y = e.pageY;
					}
					if (e.pageX > maxX) {
						x = maxX;
					} else if (e.pageX < minX) {
						x = minX;
					} else {
						x = e.pageX;
					}
					obj.height(y-obj[0].t).width(x-obj[0].l)
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
		
		handle_s.live('mousedown', function(e) {
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			var start=true
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var minY=obj[0].t;
			$('body').css('cursor','s-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					var y;
					if (e.pageY > maxY) {
						y = maxY;
					} else if (e.pageY < minY) {
						y = minY;
					} else {
						y = e.pageY;
					}
					obj.height(y-obj[0].t)
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
		
		handle_sw.live('mousedown', function(e) {
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			var start=true
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var minY=obj[0].t;
			var maxX=obj[0].l+obj[0].w;
			$('body').css('cursor','sw-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					var y,x;
					if (e.pageY > maxY) {
						y = maxY;
					} else if (e.pageY < minY) {
						y = minY;
					} else {
						y = e.pageY;
					}
					if (e.pageX > maxX) {
						x = maxX;
					} else if (e.pageX < minX) {
						x = minX;
					} else {
						x = e.pageX;
					}
					obj.height(y-obj[0].t).width(obj[0].l+obj[0].w-x).css('left',(x-minX))
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
		
		handle_w.live('mousedown', function(e) {
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			var start=true;
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var maxX=obj[0].l+obj[0].w;
			$('body').css('cursor','w-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					if (e.pageX > maxX) {
						x = maxX;
					} else if (e.pageX < minX) {
						x = minX;
					} else {
						x = e.pageX;
					}
					obj.width(obj[0].l+obj[0].w-x).css('left',(x-minX))
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
		
		handle_nw.live('mousedown', function(e) {
			var container = $(o.containment),
			minX=container.offset().left,
			maxX=minX+container.width(),
			minY=container.offset().top,
			maxY=minY+container.height();
			var start=true
			obj[0].l = obj.offset().left;
			obj[0].t = obj.offset().top;
			obj[0].w = obj.width();
			obj[0].h = obj.height();
			var maxY=obj[0].t+obj[0].h;
			var maxX=obj[0].l+obj[0].w;
			$('body').css('cursor','nw-resize')
			$('body').live('mousemove',function(e) {
				if(start) {
					var y,x;
					if (e.pageY > maxY) {
						y = maxY;
					} else if (e.pageY < minY) {
						y = minY;
					} else {
						y = e.pageY;
					}
					if (e.pageX > maxX) {
						x = maxX;
					} else if (e.pageX < minX) {
						x = minX;
					} else {
						x = e.pageX;
					}
					obj.height(obj[0].t+obj[0].h-y).width(obj[0].l+obj[0].w-x).css({'left':(x-minX), 'top':((y-minY))})
				}
			}).live('mouseup',function() {
				if(start) {
					start=false;
					$('body').die('mousemove').die('mouseup').css('cursor','')
				}
			});
		});
	});  
  };
})( jQuery );