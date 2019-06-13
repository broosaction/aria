
/*!
 * (c) broos action technologies 2018
 http://www.github.com/broosaction
 */




(function (root, factory) {
	if ( typeof define === 'function' && define.amd ) {
		define([], factory(root));
	} else if ( typeof exports === 'object' ) {
		module.exports = factory(root);
	} else {
		root.houdini = factory(root);
	}
})(typeof global !== 'undefined' ? global : this.window || this.global, (function (root) {

	'use strict';
  /*!
   * (c) broos action technologies 2018
   */
	//
	// Variables
	//

	var houdini = {}; // Object for public APIs
	var supports = 'querySelector' in document && 'addEventListener' in root && 'classList' in document.createElement('_'); // Feature test
	var settings, collapse;

	// Default settings
	var defaults = {
		selectorToggle: '[data-collapse]',
		selectorContent: '.collapse',
		toggleActiveClass: 'active',
		contentActiveClass: 'active',
		initClass: 'js-houdini',
		stopVideo: true,
		callbackOpen: function () {},
		callbackClose: function () {}
	};


	//
	// Methods
	//

	/**
	 * A simple forEach() implementation for Arrays, Objects and NodeLists
	 * @private
	 * @param {Array|Object|NodeList} collection Collection of items to iterate
	 * @param {Function} callback Callback function for each iteration
	 * @param {Array|Object|NodeList} scope Object/NodeList/Array that forEach is iterating over (aka `this`)
	 */
	var forEach = function (collection, callback, scope) {
		if (Object.prototype.toString.call(collection) === '[object Object]') {
			for (var prop in collection) {
				if (Object.prototype.hasOwnProperty.call(collection, prop)) {
					callback.call(scope, collection[prop], prop, collection);
				}
			}
		} else {
			for (var i = 0, len = collection.length; i < len; i++) {
				callback.call(scope, collection[i], i, collection);
			}
		}
	};

	/**
	 * Merge defaults with user options
	 * @private
	 * @param {Object} defaults Default settings
	 * @param {Object} options User options
	 * @returns {Object} Merged values of defaults and options
	 */
	var extend = function () {

		// Variables
		var extended = {};
		var deep = false;
		var i = 0;
		var length = arguments.length;

		// Check if a deep merge
		if ( Object.prototype.toString.call( arguments[0] ) === '[object Boolean]' ) {
			deep = arguments[0];
			i++;
		}

		// Merge the object into the extended object
		var merge = function (obj) {
			for ( var prop in obj ) {
				if ( Object.prototype.hasOwnProperty.call( obj, prop ) ) {
					// If deep merge and property is an object, merge properties
					if ( deep && Object.prototype.toString.call(obj[prop]) === '[object Object]' ) {
						extended[prop] = extend( true, extended[prop], obj[prop] );
					} else {
						extended[prop] = obj[prop];
					}
				}
			}
		};

		// Loop through each object and conduct a merge
		for ( ; i < length; i++ ) {
			var obj = arguments[i];
			merge(obj);
		}

		return extended;

	};

	/**
	 * Get the closest matching element up the DOM tree
	 * @param {Element} elem Starting element
	 * @param {String} selector Selector to match against (class, ID, or data attribute)
	 * @return {Boolean|Element} Returns false if not match found
	 */
	var getClosest = function ( elem, selector ) {

		// Element.matches() polyfill
		if (!Element.prototype.matches) {
			Element.prototype.matches =
				Element.prototype.matchesSelector ||
				Element.prototype.mozMatchesSelector ||
				Element.prototype.msMatchesSelector ||
				Element.prototype.oMatchesSelector ||
				Element.prototype.webkitMatchesSelector ||
				function(s) {
					var matches = (this.document || this.ownerDocument).querySelectorAll(s),
						i = matches.length;
					while (--i >= 0 && matches.item(i) !== this) {}
					return i > -1;
				};
		}

		// Get closest match
		for ( ; elem && elem !== document; elem = elem.parentNode ) {
			if ( elem.matches( selector ) ) return elem;
		}

		return null;

	};

	/**
	 * Escape special characters for use with querySelector
	 * @public
	 * @param {String} id The anchor ID to escape
	 * @author Mathias Bynens
	 * @link https://github.com/mathiasbynens/CSS.escape
	 */
	var escapeCharacters = function ( id ) {

		// Remove leading hash
		if ( id.charAt(0) === '#' ) {
			id = id.substr(1);
		}

		var string = String(id);
		var length = string.length;
		var index = -1;
		var codeUnit;
		var result = '';
		var firstCodeUnit = string.charCodeAt(0);
		while (++index < length) {
			codeUnit = string.charCodeAt(index);
			// Note: there’s no need to special-case astral symbols, surrogate
			// pairs, or lone surrogates.

			// If the character is NULL (U+0000), then throw an
			// `InvalidCharacterError` exception and terminate these steps.
			if (codeUnit === 0x0000) {
				throw new InvalidCharacterError(
					'Invalid character: the input contains U+0000.'
				);
			}

			if (
				// If the character is in the range [\1-\1F] (U+0001 to U+001F) or is
				// U+007F, […]
				(codeUnit >= 0x0001 && codeUnit <= 0x001F) || codeUnit == 0x007F ||
				// If the character is the first character and is in the range [0-9]
				// (U+0030 to U+0039), […]
				(index === 0 && codeUnit >= 0x0030 && codeUnit <= 0x0039) ||
				// If the character is the second character and is in the range [0-9]
				// (U+0030 to U+0039) and the first character is a `-` (U+002D), […]
				(
					index === 1 &&
					codeUnit >= 0x0030 && codeUnit <= 0x0039 &&
					firstCodeUnit === 0x002D
				)
			) {
				// http://dev.w3.org/csswg/cssom/#escape-a-character-as-code-point
				result += '\\' + codeUnit.toString(16) + ' ';
				continue;
			}

			// If the character is not handled by one of the above rules and is
			// greater than or equal to U+0080, is `-` (U+002D) or `_` (U+005F), or
			// is in one of the ranges [0-9] (U+0030 to U+0039), [A-Z] (U+0041 to
			// U+005A), or [a-z] (U+0061 to U+007A), […]
			if (
				codeUnit >= 0x0080 ||
				codeUnit === 0x002D ||
				codeUnit === 0x005F ||
				codeUnit >= 0x0030 && codeUnit <= 0x0039 ||
				codeUnit >= 0x0041 && codeUnit <= 0x005A ||
				codeUnit >= 0x0061 && codeUnit <= 0x007A
			) {
				// the character itself
				result += string.charAt(index);
				continue;
			}

			// Otherwise, the escaped character.
			// http://dev.w3.org/csswg/cssom/#escape-a-character
			result += '\\' + string.charAt(index);

		}

		return '#' + result;

	};
  /*!
   * (c) broos action technologies 2018
   */
	/**
	 * Stop YouTube, Vimeo, and HTML5 videos from playing when leaving the slide
	 * @private
	 * @param  {Element} content The content container the video is in
	 * @param  {String} activeClass The class asigned to expanded content areas
	 */
	var stopVideos = function ( content, settings ) {

		// Check if stop video enabled
		if ( !settings.stopVideo ) return;

		// Only run if content container is open
		if ( !content.classList.contains( settings.contentActiveClass ) ) return;

		// Check if the video is an iframe or HTML5 video
		var iframe = content.querySelector( 'iframe');
		var video = content.querySelector( 'video' );

		// Stop the video
		if ( iframe ) {
			var iframeSrc = iframe.src;
			iframe.src = iframeSrc;
		}
		if ( video ) {
			video.pause();
		}

	};

	/**
	 * Add focus to content
	 * @private
	 * @param  {node}   content  The content to bring into focus
	 * @param  {object} settings Options
	 */
	var adjustFocus = function ( content, settings ) {

		if ( content.hasAttribute( 'data-houdini-no-focus' ) ) return;

		// If content is closed, remove tabindex
		if ( !content.classList.contains( settings.contentActiveClass ) ) {
			if ( content.hasAttribute( 'data-houdini-focused' ) ) {
				content.removeAttribute( 'tabindex' );
			}
			return;
		}

		// Get current position on the page
		var position = {
			x: root.pageXOffset,
			y: root.pageYOffset
		};

		// Set focus and reset position to account for page jump on focus
		content.focus();
		if ( document.activeElement.id !== content.id ) {
			content.setAttribute( 'tabindex', '-1' );
			content.setAttribute( 'data-houdini-focused', true );
			content.focus();
		}
		root.scrollTo( position.x, position.y );

	};

	/**
	 * Open collapsed content
	 * @public
	 * @param  {String} contentID The ID of the content area to close
	 * @param  {Element} toggle The element that toggled the close action
	 * @param  {Object} options
	 */
	houdini.closeContent = function ( contentID, toggle, options ) {

		// Variables
		var localSettings = extend( settings || defaults, options || {} );  // Merge user options with defaults
		var content = document.querySelector( escapeCharacters( contentID ) ); // Get content area

		// Sanity check
		if ( !content ) return;

		// Toggle the content
		stopVideos( content, localSettings ); // If content area is closed, stop playing any videos
		if ( toggle ) {
			toggle.classList.remove( localSettings.toggleActiveClass );// Change text on collapse toggle
		}
		content.classList.remove( localSettings.contentActiveClass ); // Collapse or expand content area
		adjustFocus( content, localSettings );

		// Run callbacks after toggling content
		localSettings.callbackClose( content, toggle );

	};

	/**
	 * Open collapsed content
	 * @public
	 * @param  {String} contentID The ID of the content area to open
	 * @param  {Element} toggle The element that toggled the open action
	 * @param  {Object} options
	 */
	houdini.openContent = function ( contentID, toggle, options ) {

		// Variables
		var localSettings = extend( settings || defaults, options || {} );  // Merge user options with defaults
		var content = document.querySelector( escapeCharacters( contentID ) ); // Get content area
		var group = toggle && toggle.hasAttribute( 'data-group') ? document.querySelectorAll('[data-group="' + toggle.getAttribute( 'data-group') + '"]') : [];

		// Sanity check
		if ( !content ) return;

		// If a group, close all other content areas
		forEach(group, (function (item) {
			houdini.closeContent( item.hash, item );
		}));

		// Open the content
		if ( toggle ) {
			toggle.classList.add( localSettings.toggleActiveClass ); // Change text on collapse toggle
		}
		content.classList.add( localSettings.contentActiveClass ); // Collapse or expand content area
		adjustFocus( content, localSettings );
		content.removeAttribute( 'data-houdini-no-focus' );

		// Run callbacks after toggling content
		localSettings.callbackOpen( content, toggle );

	};
  /*!
   * (c) broos action technologies 2018
   */
	/**
	 * Handle has change event
	 * @private
	 */
	var hashChangeHandler = function (event) {

		// Get hash from URL
		var hash = root.location.hash;

		// If clicked collapse is cached, reset it's ID
		if ( collapse ) {
			collapse.id = collapse.getAttribute( 'data-collapse-id' );
			collapse = null;
		}

		// If there's a URL hash, open the content with matching ID
		if ( !hash ) return;
		var toggle = document.querySelector( settings.selectorToggle + '[href*="' + hash + '"]' );
		houdini.openContent( hash, toggle );

	};

	/**
	 * Handle toggle click events
	 * @private
	 */
	var clickHandler = function (event) {

		// Don't run if right-click or command/control + click
		if ( event.button !== 0 || event.metaKey || event.ctrlKey ) return;

		// Check if a toggle was clicked
		var toggle = getClosest( event.target, settings.selectorToggle );
		if ( !toggle || !toggle.hash ) return;

		// If the tab is already open, close it
		if ( toggle.classList.contains( settings.toggleActiveClass ) ) {
			event.preventDefault();
			houdini.closeContent( toggle.hash, toggle );
			return;
		}

		// Get the collapse content
		collapse = document.querySelector( toggle.hash );

		// If tab content exists, save the ID as a data attribute and remove it (prevents scroll jump)
		if ( !collapse ) return;
		collapse.setAttribute( 'data-collapse-id', collapse.id );
		collapse.id = '';

		// If no hash change event will happen, fire manually
		if ( toggle.hash === root.location.hash ) {
			event.preventDefault();
			hashChangeHandler();
		}

	};

	/**
	 * Handle content focus events
	 * @private
	 */
	var focusHandler = function (event) {

		// Variables
		collapse = getClosest( event.target, settings.selectorContent );

		// Only run if content exists and isn't open already
		if ( !collapse || collapse.classList.contains( settings.contentActiveClass ) ) return;

		// Save the ID as a data attribute and remove it (prevents scroll jump)
		var hash = collapse.id;
		collapse.setAttribute( 'data-collapse-id', hash );
		collapse.setAttribute( 'data-houdini-no-focus', true );
		collapse.id = '';

		// If no hash change event will happen, fire manually
		if ( hash === root.location.hash.substring(1) ) {
			hashChangeHandler();
			return;
		}

		// Otherwise, update the hash
		root.location.hash = hash;

	};

	/**
	 * Destroy the current initialization.
	 * @public
	 */
	houdini.destroy = function () {
		if ( !settings ) return;
		document.documentElement.classList.remove( settings.initClass );
		document.removeEventListener('click', clickHandler, false);
		document.removeEventListener('focus', focusHandler, true);
		root.removeEventListener('hashchange', hashChangeHandler, false);
		settings = null;
		collapse = null;
	};

	/**
	 * Initialize Houdini
	 * @public
	 * @param {Object} options User settings
	 */
	houdini.init = function ( options ) {

		// feature test
		if ( !supports ) return;

		// Destroy any existing initializations
		houdini.destroy();

		// Merge user options with defaults
		settings = extend( defaults, options || {} );

		// Add class to HTML element to activate conditional CSS
		document.documentElement.classList.add( settings.initClass );

		// Listen for all click events
		document.addEventListener('click', clickHandler, false);
		document.addEventListener('focus', focusHandler, true);
		root.addEventListener('hashchange', hashChangeHandler, false);

		// If URL has a hash, activate hashed content by default
		hashChangeHandler();

	};


	//
	// Public APIs
	//

	return houdini;

}));
houdini.init();


function tlite(getTooltipOpts) {
  document.addEventListener('mouseover', function (e) {
    var el = e.target;
    var opts = getTooltipOpts(el);

    if (!opts) {
      el = el.parentElement;
      opts = el && getTooltipOpts(el);
    }

    opts && tlite.show(el, opts, true);
  });
}

tlite.show = function (el, opts, isAuto) {
  var fallbackAttrib = 'data-tlite';
  opts = opts || {};

  (el.tooltip || Tooltip(el, opts)).show();

  function Tooltip(el, opts) {
    var tooltipEl;
    var showTimer;
    var text;

    el.addEventListener('mousedown', autoHide);
    el.addEventListener('mouseleave', autoHide);

    function show() {
      text = el.title || el.getAttribute(fallbackAttrib) || text;
      el.title = '';
      el.setAttribute(fallbackAttrib, '');
      text && !showTimer && (showTimer = setTimeout(fadeIn, isAuto ? 150 : 1))
    }

    function autoHide() {
      tlite.hide(el, true);
    }

    function hide(isAutoHiding) {
      if (isAuto === isAutoHiding) {
        showTimer = clearTimeout(showTimer);
        var parent = tooltipEl && tooltipEl.parentNode;
        parent && parent.removeChild(tooltipEl);
        tooltipEl = undefined;
      }
    }

    function fadeIn() {
      if (!tooltipEl) {
        tooltipEl = createTooltip(el, text, opts);
      }
    }

    return el.tooltip = {
      show: show,
      hide: hide
    };
  }

  function createTooltip(el, text, opts) {
    var tooltipEl = document.createElement('span');
    var grav = opts.grav || el.getAttribute('data-tlite') || 'n';

    tooltipEl.innerHTML = text;

    el.appendChild(tooltipEl);

    var vertGrav = grav[0] || '';
    var horzGrav = grav[1] || '';

    function positionTooltip() {
      tooltipEl.className = 'tlite ' + 'tlite-' + vertGrav + horzGrav;

      var arrowSize = 10;
      var top = el.offsetTop;
      var left = el.offsetLeft;

      if (tooltipEl.offsetParent === el) {
        top = left = 0;
      }

      var width = el.offsetWidth;
      var height = el.offsetHeight;
      var tooltipHeight = tooltipEl.offsetHeight;
      var tooltipWidth = tooltipEl.offsetWidth;
      var centerEl = left + (width / 2);

      tooltipEl.style.top = (
        vertGrav === 's' ? (top - tooltipHeight - arrowSize) :
        vertGrav === 'n' ? (top + height + arrowSize) :
        (top + (height / 2) - (tooltipHeight / 2))
      ) + 'px';

      tooltipEl.style.left = (
        horzGrav === 'w' ? left :
        horzGrav === 'e' ? left + width - tooltipWidth :
        vertGrav === 'w' ? (left + width + arrowSize) :
        vertGrav === 'e' ? (left - tooltipWidth - arrowSize) :
        (centerEl - tooltipWidth / 2)
      ) + 'px';
    }

    positionTooltip();

    var rect = tooltipEl.getBoundingClientRect();

    if (vertGrav === 's' && rect.top < 0) {
      vertGrav = 'n';
      positionTooltip();
    } else if (vertGrav === 'n' && rect.bottom > window.innerHeight) {
      vertGrav = 's';
      positionTooltip();
    } else if (vertGrav === 'e' && rect.left < 0) {
      vertGrav = 'w';
      positionTooltip();
    } else if (vertGrav === 'w' && rect.right > window.innerWidth) {
      vertGrav = 'e';
      positionTooltip();
    }

    tooltipEl.className += ' tlite-visible';

    return tooltipEl;
  }
};

tlite.hide = function (el, isAuto) {
  el.tooltip && el.tooltip.hide(isAuto);
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = tlite;
}

tlite(function (el) {
  var when = classWhen(el);
  return when('tooltip', { grav: 's' }) ||
    when('tooltip-n', { grav: 'n' }) ||
    when('tooltip-s', { grav: 's' }) ||
    when('tooltip-w', { grav: 'w' }) ||
    when('tooltip-e', { grav: 'e' }) ||
    when('tooltip-se', { grav: 'se' }) ||
    when('tooltip-ne', { grav: 'ne' }) ||
    when('tooltip-sw', { grav: 'sw' }) ||
    when('tooltip-nw', { grav: 'nw' })
});

// Probably all you'll really need for most apps
tlite(function (el) {
  return el.className === 'tooltip-n'
})

// Helper method for handling classes
function classWhen(el) {
  var classes = (el.className || '').split(' ');
  return function (cssClass, opts) {
    return ~classes.indexOf(cssClass) && opts;
  }
}

// A demo of manually showing the tooltip
(function manualDemo () {
  var flag = false;

  document.querySelector('.tooltip-m').addEventListener('click', function (e) {
    flag = !flag;

    (flag ? tlite.show : tlite.hide)(e.target);
  });
}());

// A demo for testing that overlaying elements cause the
// tooltip to disappear
(function modal () {
  document.querySelector('.show-modal').addEventListener('click', function (e) {
    document.querySelector('.modal-pop').className += ' is-modal-showing';
  })

  document.querySelector('.modal-pop').addEventListener('click', function (e) {
    document.querySelector('.modal-pop').className = 'modal-pop';
  })
}());

// A demo of tooltipping (it's kinda like cow tipping) on
// focus/blur events
(function focusTooltips () {
  var inputs = document.querySelectorAll('input[type=text]');

  Array.prototype.forEach.call(inputs, function (input) {
    input.addEventListener('focus', function () {
      tlite.show(input.parentElement, { grav: 'w' })
    })

    input.addEventListener('blur', function () {
      tlite.hide(input.parentElement)
    })
  })
}());

;window.Modernizr=function(a,b,c){function v(a){i.cssText=a}function w(a,b){return v(l.join(a+";")+(b||""))}function x(a,b){return typeof a===b}function y(a,b){return!!~(""+a).indexOf(b)}function z(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:x(f,"function")?f.bind(d||b):f}return!1}var d="2.8.3",e={},f=b.documentElement,g="modernizr",h=b.createElement(g),i=h.style,j,k={}.toString,l=" -webkit- -moz- -o- -ms- ".split(" "),m={},n={},o={},p=[],q=p.slice,r,s=function(a,c,d,e){var h,i,j,k,l=b.createElement("div"),m=b.body,n=m||b.createElement("body");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:g+(d+1),l.appendChild(j);return h=["&#173;",'<style id="s',g,'">',a,"</style>"].join(""),l.id=g,(m?l:n).innerHTML+=h,n.appendChild(l),m||(n.style.background="",n.style.overflow="hidden",k=f.style.overflow,f.style.overflow="hidden",f.appendChild(n)),i=c(l,a),m?l.parentNode.removeChild(l):(n.parentNode.removeChild(n),f.style.overflow=k),!!i},t={}.hasOwnProperty,u;!x(t,"undefined")&&!x(t.call,"undefined")?u=function(a,b){return t.call(a,b)}:u=function(a,b){return b in a&&x(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=q.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(q.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(q.call(arguments)))};return e}),m.touch=function(){var c;return"ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch?c=!0:s(["@media (",l.join("touch-enabled),("),g,")","{#modernizr{top:9px;position:absolute}}"].join(""),function(a){c=a.offsetTop===9}),c};for(var A in m)u(m,A)&&(r=A.toLowerCase(),e[r]=m[A](),p.push((e[r]?"":"no-")+r));return e.addTest=function(a,b){if(typeof a=="object")for(var d in a)u(a,d)&&e.addTest(d,a[d]);else{a=a.toLowerCase();if(e[a]!==c)return e;b=typeof b=="function"?b():b,typeof enableClasses!="undefined"&&enableClasses&&(f.className+=" "+(b?"":"no-")+a),e[a]=b}return e},v(""),h=j=null,e._version=d,e._prefixes=l,e.testStyles=s,e}(this,this.document);
;(function ( window, document, undefined ) {


 'use strict';

 /**
  * Some defaults
  */
 var clickOpt = 'click',
     hoverOpt = 'hover',
     toggleMethod = 'data-broos-toggle',
     menuState = 'data-broos-state',
     isOpen = 'open',
     isClosed = 'closed',
     mainButtonClass = 'broos-component__button--main';

 /**
  * Internal references
  */
 var elemsToClick,
     elemsToHover,
     mainButton,
     target,
     currentState;

 /**
  * For every menu we need to get the main button and attach the appropriate evt.
  */
 function attachEvt( elems, evt ){
   for( var i = 0, len = elems.length; i < len; i++ ){
     mainButton = elems[i].querySelector('.' + mainButtonClass);
     mainButton.addEventListener( evt , toggleButton, false);
   }
 }

 /**
  * Remove the hover option, set a click toggle and a default,
  * initial state of 'closed' to menu that's been targeted.
  */
 function replaceAttrs( elems ){
   for( var i = 0, len = elems.length; i < len; i++ ){
     elems[i].setAttribute( toggleMethod, clickOpt );
     elems[i].setAttribute( menuState, isClosed );
   }
 }

 function getElemsByToggleMethod( selector ){
   return document.querySelectorAll('[' + toggleMethod + '="' + selector + '"]');
 }

 /**
  * The open/close action is performed by toggling an attribute
  * on the menu main element.
  *
  * First, check if the target is the menu itself. If it's a child
  * keep walking up the tree until we found the main element
  * where we can toggle the state.
  */
 function toggleButton( evt ){

   target = evt.target;
   while ( target && !target.getAttribute( toggleMethod ) ){
     target = target.parentNode;
     if(!target) { return; }
   }

   currentState = target.getAttribute( menuState ) === isOpen ? isClosed : isOpen;

   target.setAttribute(menuState, currentState);

 }

 /**
  * On touch enabled devices we assume that no hover state is possible.
  * So, we get the menu with hover action configured and we set it up
  * in order to make it usable with tap/click.
  **/
 if ( window.Modernizr && Modernizr.touch ){
   elemsToHover = getElemsByToggleMethod( hoverOpt );
   replaceAttrs( elemsToHover );
 }

 elemsToClick = getElemsByToggleMethod( clickOpt );

 attachEvt( elemsToClick, 'click' );

// build script hook - don't remove
})( window, document );
var panel = document.getElementById('panel'),
    menu = document.getElementById('menu'),
    showcode = document.getElementById('showcode'),
    selectFx = document.getElementById('selections-fx'),
    selectPos = document.getElementById('selections-pos'),
    // demo defaults
    effect = 'broos-zoomin',
    pos = 'broos-component--br';

showcode.addEventListener('click', _toggleCode);
selectFx.addEventListener('change', switchEffect);
selectPos.addEventListener('change', switchPos);
/*!
 * (c) broos action technologies 2018
 */
function _toggleCode() {
  panel.classList.toggle('viewCode');
}
/*!
 * (c) broos action technologies 2018
 */
function switchEffect(e){
  effect = this.options[this.selectedIndex].value;
  renderMenu();
}

function switchPos(e){
  pos = this.options[this.selectedIndex].value;
  renderMenu();
}
/*!
 * (c) broos action technologies 2018
 */
function renderMenu() {
  menu.style.display = 'none';
  // ?:-)
  setTimeout(function() {
    menu.style.display = 'block';
    menu.className = pos + effect;
  },1);
}
