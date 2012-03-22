/**
 * Sudoku namespace
 */
WCF.Sudoku = {};

WCF.Sudoku.Table = Class.extend({
	/**
	 * proxy object
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Initializes a new sudoku table
	 * 
	 * @param	integer		containerID
	 * @param	jQuery		container
	 */
	init: function(tableSelector) {
		this._tableSelector = tableSelector;
		this._table = $(tableSelector);
		
		// init value selector
		WCF.Sudoku.ValueSelector.init();
		
		// attach mouse events to all non-given cells
		this._table.find('td:not(.wcf-sudokuGivenValue)').each($.proxy(function(index, element) {
			var $element = $(element);			
			$element.click(($.proxy(function(event) {
				var $target = $(event.target);					
				if (typeof this._selected != 'undefined' && this._selected.wcfIdentify() != $target.wcfIdentify()) {
					this._selected.removeClass('wcf-sudokuSelectedCell');
				}
				else if (typeof this._selected != 'undefined' && this._selected.wcfIdentify() == $target.wcfIdentify()) {
					var $targetOffsets = $target.getOffsets('offset');
					var $targetDimensions = $target.getDimensions('outer');
					WCF.Sudoku.ValueSelector.getSelector().show();
					var $selectorDimensions = WCF.Sudoku.ValueSelector.getSelector().find('.wcf-dropdown').getDimensions('outer');
					var $selectorDimensionsInner = WCF.Sudoku.ValueSelector.getSelector().find('.wcf-dropdown').getDimensions('inner');
					WCF.Sudoku.ValueSelector.getSelector().hide();

					var $targetCenter = $targetOffsets.left + Math.ceil($targetDimensions.width / 2);
					var $selectorHalfWidth = Math.ceil($selectorDimensions.width / 2);
					
					var $top = $targetOffsets.top - $targetDimensions.width;
					var $left = Math.round($targetOffsets.left - $selectorHalfWidth + ($targetDimensions.width / 2));
					
					WCF.Sudoku.ValueSelector.getSelector().css({
						top: $top + "px",
						left: $left + "px"
					});
					
					// show tooltip
					WCF.Sudoku.ValueSelector.getSelector().fadeIn('fast', $.proxy(function() {
						WCF.CloseOverlayHandler.addCallback('WCF.Sudoku.Table', $.proxy(this._closeAll, this));
					}, this));										
					// new WCF.Sudoku.ValueSelector.test();
				}					
				this._selected = $(event.target);
				this._selected.addClass('wcf-sudokuSelectedCell');					
			}, this)));					
		}, this));		
		
		// init proxy
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
	},
	
	/**
	 * Closes all inline editors.
	 */
	_closeAll: function() {
		WCF.Sudoku.ValueSelector.getSelector().hide();
		WCF.CloseOverlayHandler.removeCallback('WCF.Sudoku.Table');
	}
});

WCF.Sudoku.ValueSelector = {
		GRID_SIZE: 9,
		
		/**
		 * initialization state
		 * @var	boolean
		 */
		_didInit: false,

		init: function() {
			if (!this._didInit) {
				// create selector element
				this._selector = $('<nav class="wcf-pageNavigation"><div class="wcf-dropdown"><ul></ul></div></nav>').appendTo($('body')).hide();				
				var $i = 1;
				for ($i = 1; $i <= this.GRID_SIZE; $i++) {					
					var $liElement = $('<li></li>');
					if ($i % 3 == 0) $liElement.addClass('break');
					var $aElement = $('<a>' + $i + '</a>');
					$liElement.append($aElement);
					this._selector.find('ul').append($liElement);
				
				}
				this._selector.css({position: 'absolute'});
				this._selector.find('.wcf-dropdown').css({
						'min-width': '70px',
						'max-width': '70px'
				});
				this._selector.find('.wcf-dropdown').show();

				this._didInit = true;
			}
		},
		
		getSelector: function() {
			return this._selector;
		},
		
		test: function() {
			alert("I'm alive!");
		}
};