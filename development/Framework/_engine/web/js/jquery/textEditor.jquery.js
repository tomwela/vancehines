(function( $ ){
	var boxEditor, 
		btnAlignLeft,
		btnAlignCenter,
		btnAlignRight,
		boxTextarea,
		boxTextSize,
		boxTextFont,
		boxForChange,
		o;
	var methods = {
        init : function( options ) {
  
			var defaults = {
				align:{},
				container:'',
				textarea:'',
				textSize:'',
				textFont:'',
				color:''
			};
			 
			var options = $.extend(defaults, options);
		 
			
			o = options;
			//Assign current element to variable, in this case is UL element
			
			btnAlignLeft = $(o.align.left);
			btnAlignCenter = $(o.align.center);
			btnAlignRight = $(o.align.right);
			boxTextarea = $(o.textarea);
			boxTextSize = $(o.textSize);
			boxTextFont = $(o.textFont);
			boxEditor = $(o.container);
				
			btnAlignLeft.live('click', function() {
				if(boxTextarea.disabled === true) return false;
				btnAlignCenter.removeClass('active');
				btnAlignRight.removeClass('active');
				btnAlignLeft.addClass('active');
				boxTextarea.css({'text-align':'left'})
				boxForChange.css({'text-align':'left'})
				if(typeof cardBuilder !== 'undefined')
  			cardBuilder._setProperty(boxForChange.attr('id'), 'text-align', 'left');
				return false;
			})
			btnAlignCenter.live('click', function() {
				if(boxTextarea.disabled === true) return false;
				btnAlignLeft.removeClass('active');
				btnAlignRight.removeClass('active');
				btnAlignCenter.addClass('active');
				boxTextarea.css({'text-align':'center'})
				boxForChange.css({'text-align':'center'})

				if(typeof cardBuilder !== 'undefined')
  			cardBuilder._setProperty(boxForChange.attr('id'), 'text-align', 'center');
				return false;
			})
			btnAlignRight.live('click', function() {
				if(boxTextarea.disabled === true) return false;
				btnAlignCenter.removeClass('active');
				btnAlignLeft.removeClass('active');
				btnAlignRight.addClass('active');
				boxTextarea.css({'text-align':'right'})
				boxForChange.css({'text-align':'right'})

				if(typeof cardBuilder !== 'undefined')
  			cardBuilder._setProperty(boxForChange.attr('id'), 'text-align', 'right');
				return false;
			})
			
			boxTextSize.live('change', function() {
				if(boxTextarea.disabled === true) return false;
				var size=boxTextSize.val();
				if(typeof cardBuilder !== 'undefined')
				{
					rsize = size * cardBuilder.ratio;
				}
				boxTextarea.css({'font-size':rsize+'pt'})
				boxForChange.css({'font-size':rsize+'pt'})

				if(typeof cardBuilder !== 'undefined')
  				cardBuilder._setProperty(boxForChange.attr('id'), 'font-size', size);
				return false;
			})
			boxTextFont.live('change', function() {
				if(boxTextarea.disabled === true) return false;
				var fontID=boxTextFont.val();
				var font = $('option[value='+fontID+']', boxTextFont).text();
				boxTextarea.css({'font-family':font});
 			  boxForChange.attr('name', fontID);
				boxForChange.css({'font-family':font});

				if(typeof cardBuilder !== 'undefined'){
  			cardBuilder._setProperty(boxForChange.attr('id'), 'fontID', fontID);
  			cardBuilder._setProperty(boxForChange.attr('id'), 'font-family', font);
  			}
				return false;
			})
			
			$('li', o.color).live('click', function() {
				if(boxTextarea.disabled === true) return false;
				$('li', o.color).removeClass('active');
				$(this).addClass('active');				
				var color = $('.color', this).css('background-color');
				boxTextarea.css({'color' : color})
				boxForChange.css({'color' : color})

				if(typeof cardBuilder !== 'undefined')
  			cardBuilder._setProperty(boxForChange.attr('id'), 'color', color);
				return false;
			})
			
			boxTextarea.live('keyup', function() {
				if(boxTextarea.disabled === true) return false;
				var text=boxTextarea.val();
				text = controls.htmlspecialchars(text).replace(/\n\r?/g, '<br />');
				boxForChange.find(".text").html(text);

				if(typeof cardBuilder !== 'undefined')
  			cardBuilder._setProperty(boxForChange.attr('id'), 'text', text);
				return false;
			})
			
			$(".bottom-panel .lock", boxEditor).live('click', function()
			{

				if(typeof cardBuilder !== 'undefined' && CardBuilder.selectedBox)
				{
					$(this).toggleClass('active');
					var b = $(this).hasClass('active')?'1':'0';
					CardBuilder.selectedBox.attr('locked', b);
  				cardBuilder._setProperty(CardBuilder.selectedBox.attr('id'), 'locked', b);
				}
			})
			
		},
        show : function(box) {
			boxForChange = box;
			boxEditor.show();
		},
        hide : function( ) {
			boxEditor.hide();
		},

		disable : function()
		{
			if(!boxTextarea) return false;
			boxTextarea.val('');
			boxTextarea.attr('readonly',1);
			boxTextFont.attr('disabled',1);
			boxTextSize.attr('disabled',1);
			boxTextarea.disabled = true;
		},
		enable: function()
		{
			boxTextarea.disabled = false;
			boxTextarea.removeAttr('readonly');
			boxTextFont.removeAttr('disabled');
			boxTextSize.removeAttr('disabled');
		},

      setText : function( text ) {
			boxTextarea.val(text);
		},
        setAlign : function( align ) {
			var align = align ? align : 'left';
			btnAlignCenter.removeClass('active');
			btnAlignRight.removeClass('active');
			btnAlignLeft.removeClass('active');			
			switch (align) {
				case 'left':
					btnAlignLeft.addClass('active');
				break;
				case 'center':
					btnAlignCenter.addClass('active');
				break;
				case 'right':
					btnAlignRight.addClass('active');
				break;
			}			
			boxTextarea.css({'text-align': align})
		},
    setTextSize : function( size ) {
    	var val = size;
      if(typeof cardBuilder !== 'undefined') 
  		  val /= cardBuilder.ratio;
  		else if(RATIO) val/=RATIO;
  		val = Math.round(val);
  		var sizes = []; $("select.size option").each(function(){ sizes[sizes.length] = $(this).text();});
  		for(var i=sizes.length;i>0;--i)
  			if(sizes[i]<=val){val = sizes[i]; break;}
      boxTextSize.val(val?val:12);
			boxTextarea.css({'font-size':(size?size:12)+'pt'})
		},
        setTextFont : function( fontID, font ) {
			boxTextFont.val(fontID);
			boxTextarea.css({'font-family':font})
		},
        setColor : function( color ) {
			$('li', o.color).removeClass('active');
			$('li', o.color).each(function(i, el){
				if ( $('.color', el).css('background-color') == color ) {
					$(el).addClass('active');	
				}
			})
			boxTextarea.css({'color' : color})
		},
		setLock : function(b)
		{
			window.box = boxEditor;
			if(b!=false) $(".bottom-panel .lock", boxEditor).addClass('active');
			else         $(".bottom-panel .lock", boxEditor).removeClass('active');
		},

		getText : function() {
			return boxTextarea.val();
		},
        getAlign : function() {					
			return boxTextarea.css('text-align')
		},
        getTextSize : function() {
			return boxTextarea.css('font-size')
		},
        getTextFont : function() {
			return boxTextarea.css('font-family')
		},
        getColor : function() {
			return boxTextarea.css('color')
		}
    };
	
	$.textEditor = function(methodOrOptions) {
		if ( methods[methodOrOptions] ) {
			return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
			// Default to "init"
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.tooltip' );
		}    
	};
  
})( jQuery );