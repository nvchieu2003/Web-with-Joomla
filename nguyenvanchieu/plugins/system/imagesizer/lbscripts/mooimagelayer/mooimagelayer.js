/*
---
description: A small and fast inline image loader.
license: GPL
authors: Alexander Hofbauer
provides: [mooImageLayer]
requires:
  core:1.4.4: [Class, Class.Extras, Event]
...
*/

var mooImageLayer = new Class
({
	Implements: Options,

	layer: null,
	image: null,
	loading: null,
	displaying: false,
	dimension: false,

	options: {
		linkselector: '.mil-imagelink',
		bgclass: 'mil-bg',
		loadingclass: 'mil-bg-loading',
		imgclass: 'mil-img',
		sizefactor: 0.95,
		resize: false,
		autoheight: true,
		findParent: false
	},

	initialize: function(options)
	{
		this.setOptions(options);
		window.addEvent('domready', function() { this.startup(); }.bind(this));
	},

	startup: function()
	{
		this.layer = new Element('div', {
			'class': this.options.bgclass,
			styles: {
				display: 'none'
			}
		});

		this.layer.addEvent('click', function() {
			this.hideLayer();
		}.bind(this));

		this.loading = new Element('div', {
			'class': this.options.loadingclass,
			html: 'Loading image...'
		});

		this.layer.grab(this.loading);

		this.image = new Element('img', {
			'class': this.options.imgclass,
			title: 'click to close',
			styles: {
				opacity: 0
			}
		});

		this.image.addEvent('click', function() {
			this.hideLayer();
		}.bind(this));

		this.image.addEvent('load', function() {
			this.scaleImage();
			this.loading.fade('out');
			this.image.setStyle('display', '');
			this.image.fade('in');
		}.bind(this));

		document.id(document.body).adopt(this.layer, this.image);

		var that = this;
		$$(this.options.linkselector).addEvent('click', function(e) {
			var link = null;
			if (that.options.findParent) {
				link = this.getParent('a.' + that.options.linkselector).get('href');
			} else {
				link = this.get('href');
			}
			if (link != null) {
				that.showLayer(link);
				return false;
			}
			return true;
		});

		if (this.options.resize) {
			window.addEvent('resize', function() {
				if (this.displaying) {
					if (this.options.autoheight) {
						this.layer.setStyle('height', document.id(document.body).getScrollSize().y);
					}
					this.scaleImage();
				}
			}.bind(this));
		}
	},


	showLayer: function(url)
	{
		this.displaying = true;
		this.loading.setStyles({
			opacity: '1',
			visibility: ''
		});
		this.layer.setStyle('display', 'block');
		if (this.options.autoheight) {
			this.layer.setStyle('height', document.id(document.body).getScrollSize().y);
			this.loading.setStyle('top', document.id(document.body).getScroll().y);
		}
		this.image.set('src', url);
	},

	hideLayer: function()
	{
		this.displaying = false;
		this.dimension = false;
		this.image.setStyles({
			opacity: '0', // could be faded as well
			display: 'none'
		});
		this.layer.setStyle('display', 'none');
		/* for webkit browsers src needed to be set to something
		 * different or else it won't show the same image twice */
		this.image.set('src', '');
		this.image.removeProperties('height', 'width');
		this.dimension = false;
	},


	scaleImage: function()
	{
		var doc = document.id(document.body).getSize();

		var max = {
			x: doc.x * this.options.sizefactor,
			y: doc.y * this.options.sizefactor
		};

		var dim = this.dimension;
		if (!dim) {
			var width = this.image.width;
			var height = this.image.height;

			if (this.image.naturalWidth) {
				width = this.image.naturalWidth;
			}

			if (this.image.naturalHeight) {
				height = this.image.naturalHeight;
			}

			dim = {
				x: width,
				y: height
			};
			this.dimension = dim;
		}

		var newX = dim.x;
		var newY = dim.y;

		if (dim.x > max.x || dim.y > max.y) {
			if (dim.x > max.x) {
				newX = max.x;
				newY = dim.y * newX / dim.x;
			}
			/* height could still be too big after resizing width and
			 * then height proportionally, so no "else if" here */
			if (newY > max.y) {
				newY = max.y;
				newX = dim.x * newY / dim.y;
			}

			newX = Math.floor(newX);
			newY = Math.floor(newY);
		}

		this.image.set({
			width: newX,
			height: newY
		});

		var bodyScroll = document.id(document.body).getScroll();
		this.image.setPosition({
			x: bodyScroll.x + (doc.x - newX) / 2,
			y: bodyScroll.y + (doc.y - newY) / 2,
		});
	}

});
