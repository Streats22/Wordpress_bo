'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/*!
* Copyright (C) Oh!Fuchs, if not stated otherwise
* Written by Patrick Paul, Oh!Fuchs, Apparelcuts <patrick@ohfuchs.com> 2015-2019
*/

//@prepros-prepend _copyright.js

//@prepros-append public/basket.js

//@prepros-append public/confomat.js

//@prepros-append public/shipping.js

//@prepros-append public/forms.js

;(function () {
  function Plugin(sfw, $) {

    var basket = sfw.basket = { $$basket: false };

    /**
     * cache the basket on the client
     *
     * @param  rawBasket
     * @return basketProto $$basket
     * @since  1.0.0
     */

    function update($$raw_basket) {

      $$raw_basket.received = $$raw_basket.received || new Date().getTime();
      var $$basket = basket.equip($$raw_basket);

      var $$oldbasket = basket.$$basket;

      // bail if the new basket is older than existing basket
      if ($$oldbasket && $$basket.received <= $$oldbasket.received) {

        //sfw.debug( 'No basket update required' );
        return $$oldbasket;
      }

      !$$oldbasket && sfw.debug('Basket loaded for the first time on this page');
      $$oldbasket && sfw.debug('Fresh Basket received');

      // save new basket
      $$basket = wp.hooks.applyFilters('sfw.basket', $$basket);
      // delete raw property on basketProto to prevent infinite loops
      delete $$basket.raw;
      sfw.storage.__.set('basket', $$basket);
      basket.$$basket = $$basket;

      // fallback cookie
      sfw.Cookies.set('sfw-basket-id', $$basket.id, { expires: 30 }); // 30 days


      // object stores modifications
      var items = { new: [], modified: [], removed: [] },
          modified = false;

      // loop through current items
      for (var i in $$basket.basketItems) {

        var $$basketItem = $$basket.basketItems[i];
        var $$oldBasketItem = $$oldbasket ? $$oldbasket.getItem($$basketItem.id) : false;

        // item is new
        if (!$$oldBasketItem) {
          modified = true;
          items.new.push($$basketItem);
          wp.hooks.doAction('sfw.basket.item.added', $$basketItem, $$basket);
        }
        // item exists
        else if (JSON.stringify($$oldBasketItem) != JSON.stringify($$basketItem)) {
            modified = true;
            items.modified.push($$basketItem);
            wp.hooks.doAction('sfw.basket.item.changed', $$basketItem, $$oldBasketItem, $$basket);
          }
      }

      // loop through old items
      if ($$oldbasket) for (var i in $$oldbasket.basketItems) {
        var $$oldBasketItem = $$oldbasket.basketItems[i];
        var $$maybeBasketItem = $$basket.getItem($$oldBasketItem.id);

        if (!$$maybeBasketItem) {
          modified = true;
          items.removed.push($$oldBasketItem);
          wp.hooks.doAction('sfw.basket.item.removed', $$oldBasketItem, $$basket);
        }
      }

      if (modified) wp.hooks.doAction('sfw.basket.change', $$basket, items, $$oldbasket);

      if (!$$oldbasket) wp.hooks.doAction('basket.initiated', $$basket);

      return $$basket;
    }

    /**
     * helper to add extended functionality to basket objects
     *
     * @param  {[type]} $$raw_basket [description]
     * @return {[type]}              [description]
     * @since  1.0.0
     */

    function basketProto($$raw_basket) {

      var self = this;

      $.extend(this, $$raw_basket);

      this.raw = $$raw_basket;

      for (var i in this.basketItems) {
        if (!(this.basketItems[i] instanceof basketItemProto)) this.basketItems[i] = new basketItemProto(this.basketItems[i]);
      }

      this.getItem = function (id) {

        for (var i in self.basketItems) {
          if (self.basketItems[i].id == id) {
            return self.basketItems[i];
          }
        }

        return false;
      };

      this.totalQuantity = function () {

        if (!_typeof(this._tq) == 'undefined') return this._tq;

        this._tq = 0;

        if (self.basketItems.length) self.basketItems.forEach(function ($$item) {
          self._tq += $$item.quantity;
        });

        return this._tq;
      };

      /**
       * Retrieve link by type
       *
       * @param  {[type]} type [description]
       * @return {[type]}      [description]
       * @since  1.0.0
       */

      this.link = function (type) {
        for (var i in this.links) {
          if (this.links[i].type == type) return this.links[i].href;
        }
        return;
      };

      this.isEmpty = function () {
        return this.totalQuantity() <= 0;
      };

      wp.hooks.doAction('sfw.basket.proto', this);
    }

    /**
     * helper to add extended functionality to basket item objects
     *
     * @param  {[type]} $$raw_basket [description]
     * @return {[type]}              [description]
     * @since  1.0.0
     */

    function basketItemProto($$raw_basketitem) {

      var self = this;

      // merge with original object
      $.extend(this, $$raw_basketitem);

      /*
      copy of original object
      */
      this.raw = $$raw_basketitem;

      /**
       * Retrieve elemnent.property by key value
       *
       * @param  {[type]} key [description]
       * @return {[type]}     [description]
       * @since  1.0.0
       */

      this.prop = function (key) {
        for (var i in this.element.properties) {
          if (this.element.properties[i].key == key) return this.element.properties[i].value;
        }
        return;
      };

      /**
       * Retrieve link by type
       *
       * @param  {[type]} type [description]
       * @return {[type]}      [description]
       * @since  1.0.0
       */

      this.link = function (type) {
        for (var i in this.links) {
          if (this.links[i].type == type) return this.links[i].href;
        }
        return;
      };

      /**
       * Retrieve preview image Url
       *
       * @param  {Array}  [dimensions=[200, 200]]         [description]
       * @return {[type]}                   [description]
       * @since  1.0.0
       */

      this.imageUrl = function () {
        var dimensions = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : { width: 200, height: 200 };


        return 'https://image.spreadshirtmedia.' + sfw.get('host') + '/image-server/v1/products/' + self.prop('product') + ',width=' + dimensions.width + ',height=' + dimensions.height + ',appearanceId=' + self.prop('appearance') + '.png';
      };

      /**
       * Retrieve preview image HTML
       *
       * @param  {[type]} dimensions [description]
       * @return {[type]}            [description]
       * @since  1.0.0
       */

      this.image = function (dimensions) {

        return '<img src="' + self.imageUrl(dimensions) + '" />';
      };

      /**
       * get a virtual price node with total price e.g. price multiplied with quantity
       *
       * @return {[type]} [description]
       * @since  1.0.0
       */

      this.priceTotal = function () {

        var $$price = $.extend({}, self.priceItem);

        $$price.vatIncluded = self.quantity * $$price.vatIncluded;
        $$price.vatExcluded = self.quantity * $$price.vatExcluded;
        $$price.display = self.quantity * $$price.display;

        return $$price;
      };

      /**
       * Remove this item from the basket
       *
       * @return {[type]} [description]
       * @since  1.0.0
       */

      this.remove = function () {

        var promise = $.Deferred();

        sfw.feedback.await(promise, 'update-basket');

        sfw.spreadshirt({
          url: self.href,
          method: 'DELETE'
        }).fail(function () {
          return promise.reject({ error: 'delete-basketitem' });
        }).done(function () {
          basket.get(true).fail(function () {
            return promise.reject({ error: 'delete-basketitem' });
          }).done(function ($$basket) {
            return promise.resolve($$basket);
          }).done(function ($$basket) {
            return wp.hooks.doAction('sfw.basket.item.removed.now');
          });
        });

        return promise;
      };

      /**
       * Decrease the quantity of this item
       *
       * @return {[type]} [description]
       * @since  1.0.0
       */

      this.decrease = function () {
        return self.updateQuantity(self.quantity - 1);
      };

      /**
       * Increase the quantity of this item
       *
       * @return {[type]} [description]
       * @since  1.0.0
       */

      this.increase = function () {
        return self.updateQuantity(self.quantity + 1);
      };

      /**
       * Set the quantity of this item
       *
       * @param  {[type]} new_quantity [description]
       * @return {[type]}              [description]
       * @since  1.0.0
       */

      this.updateQuantity = function (new_quantity) {

        if (new_quantity <= 0) return self.remove();

        var promise = $.Deferred();

        sfw.feedback.await(promise, 'update-basket');

        var payload = JSON.stringify($.extend({}, self.raw, { quantity: new_quantity }));

        sfw.spreadshirt({
          url: self.href,
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          data: payload,
          method: 'PUT'
        }).fail(function () {
          return promise.reject({ error: 'update-basketitem' });
        }).done(function () {
          basket.get(true).fail(function () {
            return promise.reject({ error: 'update-basketitem' });
          }).done(function ($$basket) {
            return promise.resolve($$basket);
          }).done(function ($$basket) {
            return wp.hooks.doAction('sfw.basket.item.updated.now');
          });
        });

        return promise;
      };

      // allow modification
      wp.hooks.doAction('sfw.basket.item.proto', this);
    }

    /**
     * add functionality to basket
     *
     * @param  {[type]} $$basket [description]
     * @return {[type]}          [description]
     * @since  1.0.0
     */

    basket.equip = function ($$maybe_basket) {

      if ($$maybe_basket && !($$maybe_basket instanceof basketProto)) {
        $$maybe_basket = new basketProto($$maybe_basket);
      }

      return $$maybe_basket;
    };

    /**
     * Retrieve a basket
     *
     * @param  string id A basket id
     * @param  bool strictmode Create new basket when basket with id isn't found
     * @return Promise
     * @since  1.0.0
     */

    basket.request_promise = false;

    basket.request = function (id, strictmode) {

      // return existing promise while pending
      if (typeof basket.request_promise.state == 'function') {
        if ('pending' == basket.request_promise.state()) {
          return basket.request_promise;
        }
      }

      var promise = basket.request_promise = $.Deferred();

      sfw.debug('Requesting Basket');

      sfw.spreadshirt({
        url: sfw.get('shop').baskets.href + '/' + id,
        dataType: "json",
        data: {
          locale: sfw.get('locale')
        }
      }).done(function ($$raw_basket) {
        var $$basket = update($$raw_basket);
        promise.resolve($$basket);
      }).fail(function () {
        // create new basket
        if (!strictmode) {

          basket.create().done(function ($$basket) {
            return promise.resolve($$basket);
          }).fail(function (err) {
            return promise.reject(err);
          });
        } else {
          promise.reject({ error: 'request-basket' });
        }
      });

      return promise;
    };

    /**
     * Create a new empty basket
     *
     * @return Promise
     * @since  1.0.0
     */
    basket.create_promise = false;

    basket.create = function () {

      // return existing promise while pending
      if (typeof basket.create_promise.state == 'function') {
        if ('pending' == basket.create_promise.state()) {
          return basket.create_promise;
        }
      }

      var promise = basket.create_promise = $.Deferred();

      var payload = JSON.stringify({
        basket: {
          shop: {
            id: sfw.get('shop').id
          }
        }
      });

      sfw.spreadshirt({
        url: sfw.get('shop').baskets.href + '?locale=' + sfw.get('locale'),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: payload,
        method: 'POST'
      }).fail(function () {
        return promise.reject({ error: 'create-basket' });
      }).done(function ($$minimal_basket) {
        return wp.hooks.doAction('sfw.basket.created', $$minimal_basket);
      }).done(function ($$minimal_basket) {
        basket.request($$minimal_basket.id).fail(function () {
          return promise.reject({ error: 'create-basket' });
        }).done(function ($$basket) {
          return promise.resolve($$basket);
        });
      });

      return promise;
    };

    /**
     * Get the current (if exists) or a new basket
     *
     * @return Promise
     * @since  1.0.0
     */

    basket.get = function (force_update) {

      var $$raw_basket = sfw.storage.__.get('basket');

      // the storage contained a basket
      if ($$raw_basket) {

        // check if the basket expired
        var max_age = 5 * 60 * 1000,
            now = new Date().getTime();

        if (now - $$raw_basket.received > max_age) {
          sfw.debug('Basket reached max age');
          // the basket is expired
          // use basket id to request an update
          return basket.request($$raw_basket.id);
        } else if (force_update) {
          sfw.debug('Basket update forced');
          // the basket has probably changed and must be requested
          return basket.request($$raw_basket.id);
        } else {
          // the basket is not expired
          // trigger basket update
          var $$basket = update($$raw_basket);

          return $.Deferred().resolve($$basket);
        }
      }
      // no basket in the storage
      else {

          // fallback basket id via cookie , in case localstorage is not available
          var cookie_basket_id = sfw.Cookies.get('sfw-basket-id');

          if (cookie_basket_id) {
            sfw.debug('Use Cookie to restore Basket');
            return basket.request(cookie_basket_id);
          }

          //create new basket
          return basket.create();
        }
    };

    /**
     * Adds a new item to the basket
     *
     * @return Promise
     * @since  1.0.0
     */

    basket.add = function (item) {

      wp.hooks.doAction('sfw.basket.item.added.before');

      item = $.extend({
        quantity: 1,
        continueShopping: sfw.get('continueShoppingLink'),
        edit: '',
        editable: true,
        type: 'sprd:article',
        'article-id': false,
        'product-id': false,
        'producttype-id': false,
        href: false,
        size: false,
        appearance: false,
        origin: 'orderform'
      }, item);

      // auto set href
      if (item['article-id'] && !item.href) {
        item.href = sfw.get('shop').articles.href + '/' + item['article-id'];
        item.type = 'sprd:article';
      } else if (item['product-id'] && !item.href) {
        item.href = sfw.get('shop').products.href + '/' + item['product-id'];
        item.type = 'sprd:product';
      }

      // auto set edit link
      try {

        if (!item.edit && item.editable) {
          if (item['product-id']) {
            item.edit = sfw.get('pages').confomat + '#!P' + item['product-id'];
          }
        }
      } catch (e) {}

      // filter
      item = wp.hooks.applyFilters('sfw.basket.item.new', item);

      var promise = $.Deferred();

      sfw.feedback.await(promise, 'update-basket');

      //hooks
      promise.always(function () {
        return wp.hooks.doAction('sfw.basket.added.after');
      });
      promise.done(function () {
        return wp.hooks.doAction('sfw.basket.item.added.now');
      });
      promise.fail(function (err) {
        return wp.hooks.doAction('sfw.basket.added.fail', err);
      });

      // bail
      if (!item['producttype-id'] || !item.appearance || !item.size || !item.href) return promise.reject({ error: 'bad-request' }, item);

      // create payload
      var payload = {
        quantity: item.quantity,
        element: {
          type: item.type,
          href: item.href,
          properties: [{ key: 'appearance', value: item.appearance }, { key: 'size', value: item.size }]
        },
        links: [{ type: 'edit', href: item.edit }, { type: 'continueShopping', href: item.continueShopping }]
      };

      payload = wp.hooks.applyFilters('sfw.basket.item.payload', payload);
      payload = JSON.stringify(payload);

      sfw.api.getProductType(item['producttype-id']).fail(function (err) {
        return promise.reject(err);
      }).done(function ($$producttype) {

        // not in stock
        if (!$$producttype.isAvailable(item.appearance, item.size)) {
          return promise.reject({ error: 'out-of-stock' });
        }

        sfw.basket.get().done(function ($$basket_old) {

          // try to add item
          sfw.spreadshirt({
            url: $$basket_old.href + '/items',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: payload,
            method: 'POST'
          }).fail(function (err) {
            return promise.reject({ error: 'update-basketitem' }, err);
          }).done(function (success) {
            // retrieve new basket
            sfw.basket.get(true).fail(function (err) {
              return promise.reject(err);
            }).done(function ($$basket) {

              promise.resolve($$basket);
            });
          });
        }).fail(function (err) {
          return promise.reject(err);
        });
      });

      return promise;
    };

    /**
     * return an item from the current basket
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    basket.item = function (id) {

      var promise = $.Deferred();

      basket.get().fail(function (err) {
        return promise.reject(err);
      }).done(function ($$basket) {

        var $$basketItem = $$basket.getItem(id);

        if ($$basketItem) promise.resolve($$basketItem);

        promise.reject({ error: 'no-item-found' });
      });

      return promise;
    };

    wp.hooks.addAction('sfw.ready', 'sfw.basket.interval.init', function () {

      // retrieve basket / check if basket is expired
      basket.get();

      // when window gets focus
      $(window).on('focus', function () {
        return basket.get();
      });

      // check every 5 Seconds in window is visible
      setInterval(function () {
        if (!sfw.hidden()) {
          basket.get();
        }
      }, 5000);
    });
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.plugins', 'sfw.plugin.basket', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    var confomat = sfw.confomat = { init: 0, instance: 0 };

    /**
     * Create Confomat Instance
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    function create() {

      confomat.$el = $('[data-confomat]').first();

      if (!confomat.$el.length) return;

      confomat.config = confomat.$el.data('confomat');
      var settings = {
        type: 'auto',
        shopId: sfw.get('shop').id,
        platform: sfw.get('host') == 'net' ? 'EU' : 'NA',
        target: confomat.$el[0],
        mode: 'external',
        continueShoppingLink: sfw.get('continueShoppingLink'),
        //defaults 				      : { showSaveAndShare : false },
        shopUrl: sfw.get('home'),
        shareUrlTemplate: sfw.get('home'),
        locale: sfw.get('locale'),
        paramlist: [],
        parse_from_url: false,
        sync_hash: false
      };

      $.extend(settings, confomat.config);

      if (settings.parse_from_url) $.extend(settings, confomat.hash.parse(settings.paramlist));

      confomat.settings = wp.hooks.applyFilters('sfw.confomat.settings', settings);

      //console.log( 'Tablomat Settings',  settings );

      if ('auto' == settings.type) {
        if (confomat.$el.width() < 600) {
          settings.type = 'smartomat';
        } else {
          settings.type = 'sketchomat';
        }
      }

      if (settings.type == 'smartomat' || settings.type == 'sketchomat') {
        if (!settings.latest) settings.version = '3.7.5';
      }

      var ready_callback = function ready_callback(err, app) {

        if (err) {
          wp.hooks.doAction('sfw.confomat.failed', err);
        } else {
          confomat.instance = app;
          wp.hooks.doAction('sfw.confomat.ready', app);
        }
      };

      sfw.basket.get().done(function ($$basket) {
        // init with basket settings
        settings.apiBasketId = $$basket.id;

        settings.addToBasket = function (item, callback) {

          //console.log( item );

          var data = {
            quantity: item.quantity,
            appearance: item.appearance.id,
            size: item.size.id,
            'product-id': item.product.id,
            'producttype-id': item.productType.id

          };

          sfw.basket.add(data).always(function () {
            callback();
          });
        };

        sfw.debug(settings);

        spreadshirt.create(settings.type, settings, ready_callback);
      }).fail(function (err) {
        // create without basket
        if (wp.hooks.applyFilters('sfw.confomat.allow_no_basket', true)) {

          sfw.debug(settings);

          spreadshirt.create(settings.type, settings, ready_callback);
        } else {
          ready_callback(err);
        }
      });
    };

    /**
     * Create Confomat Instance if wrapper is found on the current page
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    confomat.load = function () {

      // search for existing confomat
      if (0 == $('[data-confomat]').length || confomat.init) return;

      // spreadshirtLoaded is the callback name
      var spreadshirtLoaded = window.spreadshirtLoaded = create;
      confomat.init = true;

      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = sfw.get('url') + 'resources/spreadshirt.min.js';
      $("body").append(script);
    };

    confomat.hash = {};

    /**
     * Triggers save of the current product and saves it's id to the location hash
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    confomat.hash.saveToLocation = function () {

      // bail if confomat is not ready
      if (!confomat.instance) return;

      // bail if window is hidden
      if (sfw.hidden()) return;

      if (typeof confomat.instance.saveProduct != 'function') return;

      confomat.instance.saveProduct(function (err, productId) {

        // refresh hash
        if (!err) window.location.hash = '!P' + productId;
      });
    };

    /**
     * Create Hash syncronization Interval
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    confomat.hash.keepSynced = function () {

      if (confomat.settings.sync_hash) setInterval(confomat.hash.saveToLocation, 15 * 1000);
    };

    wp.hooks.addAction('sfw.confomat.ready', 'sfw', confomat.hash.keepSynced);

    confomat.hash.parse = function () {
      var allowed_params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};


      var hash = window.top.location.hash || '',
          params = {};

      if (typeof hash != 'string' || !hash.indexOf('!')) return params;

      // remove #
      hash = hash.substring(1);

      if (matches = hash.match(/^!\/customize\/product\/([0-9]+)\/view\/([0-9]+)/i)) {
        // matches tablomat generic sharing link
        params.productId = matches[1];
        params.view = matches[2];
      } else {
        // probably supported spreadpress params
        var parts = hash.split('!');
        // remove empty first element
        parts.shift();

        for (var i in parts) {
          var part = parts[i];

          for (var param in allowed_params) {

            if (part.indexOf(param) === 0) {
              var value = part.substr(param.length);
              if (value) {
                var long_param = allowed_params[param];
                params[long_param] = value;
              }
            }
          }
        }
      }
      return params;
    };

    wp.hooks.addAction('sfw.refresh', 'sfw.confomat.load', confomat.load, 20);
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.plugins', 'sfw.plugin.confomat', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    var shipping = sfw.shipping = { init: 0 };

    /**
     * Generate shipping cost details
     *
     * @param  {[type]} $$_region
     * @param  {[type]} $$shippingTypes
     * @return {[type]}
     * @since  1.0.0
     */

    function get_details($$_region, $$shippingTypes) {

      var $details = $('<div class="--details"></div>');

      $.each($$shippingTypes.shippingTypes, function (_g, $$shippingType) {

        // skip shippingtypes without this region
        var $$region = $$shippingType.getRegion($$_region.id);
        if (!$$region) return;

        var $detail = $('<div class="--detail"></div>').addClass('--' + sfw.sanitize_class($$shippingType.name)).addClass('--shipping-type-' + sfw.sanitize_class($$shippingType.id));

        $('<h3></h3>').text($$shippingType.name).appendTo($detail);

        $('<p></p>').addClass('--shipping-type-description').text($$shippingType.description).appendTo($detail);

        var $table = $('<table class="sfw-table"></table>');

        var $th = $('<tr></tr>');
        var $tr = $('<tr></tr>');

        $.each($$region.shippingCosts, function (j, costs) {

          if (costs.orderValueRange.to == 0.01) return;

          // label
          $('<th></th>').html(sfw.sprintf(sfw._x('%s and above', '%s price min in shipping costs table', 'apparelcuts-spreadshirt'), sfw.price.stringify(costs.orderValueRange, 'from'))).appendTo($th);

          // value
          $('<td></td>').html(sfw.price.stringify(costs.cost)).appendTo($tr);
        });

        $('<div class="sfw-table-container"></div>').append($table.append($th).append($tr)).appendTo($detail);

        $details.append($detail);
      });

      return $details;
    }

    /**
     * refresh shipping costs details based on country
     *
     * @param  {[type]} $shipping_el
     * @param  {[type]} $$shippingTypes
     * @return {[type]}
     * @since  1.0.0
     */

    function refresh_shipping_info($shipping_el, $$shippingTypes) {

      // remove old state select
      $shipping_el.find('select.--state').prev('label').remove();
      $shipping_el.find('select.--state').remove();

      var $country_select = $shipping_el.find('select.--country'),
          $$country = $$shippingTypes.getCountry($country_select.val());

      // replace details helper
      var replace_details = function replace_details($$region) {

        $shipping_el.find('.--details').replaceWith(get_details($$region, $$shippingTypes));

        // fill in async prices
        sfw.price.parseAll();
      };

      // maybe add new state select
      if ($$country.shippingStates && $$country.shippingStates.length) {

        // create state select

        var id = 'sfw-shippingcalculator-state';

        var $label = $('<label></label>').attr('for', id).text(sfw.__('State', 'apparelcuts-spreadshirt'));

        var $select = $('<select class="--state" id="' + id + '"></select>');

        var states = $$country.shippingStates;

        states.sort(function (a, b) {
          return a.name.localeCompare(b.name);
        });

        $.each(states, function (index, $$state) {
          $select.append($('<option></option>').attr('value', $$state.id).text($$state.name));
        });

        // select first state by default
        $select.find('option').first().attr('selected', true);

        // insert after country select
        $label.add($select).insertAfter($country_select);

        // refresh helper
        var refresh = function refresh() {

          var $$state = $$shippingTypes.getState($select.val());

          var $$region = $$state.getRegion();
          replace_details($$region);
        };

        //refresh details on state change
        $select.on('change', refresh);
        //reresh now
        refresh();
      } else {

        // refresh details without state
        var $$region = $$country.getRegion();
        replace_details($$region);
      }
    }

    /**
     * Initially render shipping cost details
     *
     * @param  {[type]} $$shippingTypes
     * @return {[type]}
     * @since  1.0.0
     */

    function render($$shippingTypes) {

      var deferred = $.Deferred(),
          $shipping_el = $(this);

      // create country select

      var id = 'sfw-shippingcalculator-country';

      var $label = $('<label></label>').attr('for', id).text(sfw.__('Delivery country', 'apparelcuts-spreadshirt'));

      var $country_select = $('<select class="--country" id="' + id + '"></select>');

      var countries = $$shippingTypes.getCountries();

      var countries_arr = sfw.objectToArray(countries);
      countries_arr.sort(function (a, b) {
        return a.name.localeCompare(b.name);
      });

      $.each(countries_arr, function (index, $$country) {
        $country_select.append($('<option></option>').attr('value', $$country.id).text($$country.name));
      });

      // try to select shop country

      var _default = sfw.getObjectFromList(countries, 'isoCode', sfw.get('country').isoCode);
      _default = _default ? _default.id : false;
      _default = wp.hooks.applyFilters('sfw.default_shipping_country', _default);

      if (_default) $country_select.find('option[value="' + _default + '"]').attr('selected', true);

      // empty details container
      var $details = $('<div class="--details"></div>');

      // append everything
      $shipping_el.append($label).append($country_select).append($details);

      // refresh shipping info on country change
      $country_select.on('change', function () {
        refresh_shipping_info($shipping_el, $$shippingTypes);
      });
      refresh_shipping_info($shipping_el, $$shippingTypes);

      deferred.done(function () {
        wp.hooks.doAction('sfw.shipping.render', $shipping_el);
      });

      return deferred;
    }

    /**
     * Renders all unrendered shipping costs
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    function renderAll() {

      wp.hooks.doAction('sfw.shipping.renderedAll.before');

      //console.log('getting shipping types ');
      var shipping = sfw.api.getShippingTypes();

      $.when(shipping).done(function () {
        for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
          args[_key] = arguments[_key];
        }

        var renders = [];

        // orderforms
        $('[data-shipping]').not('[data-sfw-rendered]').each(function () {
          renders.push(render.apply(this, args));
          $(this).attr('data-sfw-rendered', true);
        });

        // when all forms have been rendered
        $.when(renders).always(function () {
          return wp.hooks.doAction('sfw.shipping.renderedAll');
        });
      });
    }
    wp.hooks.addAction('sfw.shipping.render.all', 'sfw.shipping.renderAll', renderAll);
    wp.hooks.addAction('sfw.refresh', 'sfw.shipping.renderAll', renderAll);
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.plugins', 'sfw.plugin.shipping', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    var forms = sfw.forms = { utils: {} };

    /**
     * serializes form into object
     *
     * @param  {[type]} $el [description]
     * @return {[type]}     [description]
     * @since  1.0.0
     */

    forms.utils.serializeObject = function ($el) {

      var data = $el.serializeArray();
      var retval = {};
      for (var i = 0; i < data.length; i++) {
        retval[data[i]['name']] = data[i]['value'];
      }
      return retval;
    };

    forms.utils.getLoader = function (context) {

      var $loader = $('<div class="sfw-loader"></div>').append('<i class="ac ac-shirt-iheart"></i>').append('<i class="ac ac-shirt"></i>');

      return wp.hooks.applyFilters('sfw.loader', $loader, context);
    };

    sfw.forms.order = {};

    /**
     * disables orderform
     *
     * @param  {[type]} $form        [description]
     * @param  {[type]} reason_class [description]
     * @return {[type]}              [description]
     * @since  1.0.0
     */

    function disableForm($form) {

      $form.addClass('--disabled').removeClass('--enabled');
      $form.find('select,input,button,[data-add-to-basket]').attr('disabled', true);
      wp.hooks.doAction('sfw.forms.order.disabled', $form);
    }

    /**
     * enables orderform
     *
     * @param  {[type]} $form [description]
     * @return {[type]}       [description]
     * @since  1.0.0
     */

    function enableForm($form) {

      $form.removeClass('--disabled').addClass('--enabled');
      $form.find('select,input,button,[data-add-to-basket]').removeAttr('disabled');
      wp.hooks.doAction('sfw.forms.order.enabled', $form);
    }

    function create_ui($form, $$producttype) {

      // appearance

      var $appearance_select = $('<div class="sfw-select --appearance"></div>');

      $.each($$producttype.appearances, function (index, $$appearance) {

        var $option = $('<span class="--option --appearance --color" data-value="' + $$appearance.id + '" title="' + $$appearance.name + '"></span>').append('<img src="' + $$appearance.preview() + '"/>').appendTo($appearance_select);
      });

      $form.find('[name="appearance"]').after($appearance_select);

      // size

      var $size_select = $('<div class="sfw-select --size"></div>');

      $.each($$producttype.sizes, function (index, $$size) {

        var $option = $('<span class="--option --size" data-value="' + $$size.id + '"></span>').append($$size.name).appendTo($size_select);
      });

      $form.find('[name="size"]').after($size_select);

      // add loader
      var $loader = forms.utils.getLoader();
      //$loader.find('.ac-shirt-iheart').removeClass('ac-shirt-iheart').addClass( 'ac-shirt-plus');
      $form.append($loader);
    }

    function bind_ui($form, $$producttype) {

      var $a = $form.find('[name="appearance"]'),
          $s = $form.find('[name="size"]'),
          a_val = $a.val(),
          s_val = $s.val();

      $a.on('change', function () {
        wp.hooks.doAction('sfw.forms.order.appearance.change', $form, $(this).val(), $$producttype);
      });

      var $a_ops = $form.find('.--option.--appearance').on('click', function () {
        a_val = $(this).data('value');
        $a.val(a_val).trigger('change');
      });

      var $s_ops = $form.find('.--option.--size').on('click', function () {
        s_val = $(this).data('value');
        $s.val(s_val).trigger('change');
      });

      // hover effect
      if (wp.hooks.applyFilters('sfw.forms.order.dohover', true)) {

        var a_to;

        $a_ops.on('mouseenter', function () {
          var _this = this;

          if ($a.val() != $(this).data('value')) a_to = setTimeout(function () {
            $a.val($(_this).data('value')).trigger('change');
          }, 250);
        }).on('mouseleave', function () {
          clearTimeout(a_to);
          if (a_val && a_val != $a.val()) $a.val(a_val).trigger('change');
        });

        var s_to;

        $s_ops.on('mouseenter', function () {
          var _this2 = this;

          if ($s.val() != $(this).data('value')) s_to = setTimeout(function () {
            $s.val($(_this2).data('value')).trigger('change');
          }, 250);
        }).on('mouseleave', function () {
          clearTimeout(s_to);
          if (s_val && s_val != $s.val()) $s.val(s_val).trigger('change');
        });
      }

      // add to basket button

      var $button = $form.find('[data-add-to-basket],.sfw-add-to-basket');

      if ($button.length) $button.on('click', function () {
        if (!$(this).attr('disabled')) wp.hooks.doAction('sfw.forms.order.addToBasket', $form);
      });

      $a.add($s).on('change', function () {
        return refresh_ui($form, $$producttype);
      });
      refresh_ui($form, $$producttype);
    }

    function refresh_ui($form, $$producttype) {

      var $a = $form.find('[name="appearance"]'),
          $s = $form.find('[name="size"]'),
          appearance = $a.val(),
          size = $s.val(),
          is_available = $$producttype.isAvailable(appearance, size);

      // set appearance state class
      $form.find('.--option.--appearance').each(function () {
        var $e = $(this),
            value = $e.data('value');
        $e
        // if the color is sold out for the current size
        .toggleClass('--na-config', !$$producttype.isAvailable(value, size))
        // if the color is completely sold out
        .toggleClass('--na', $$producttype.isSoldOut('appearance', value))
        // if the color is selected
        .toggleClass('--selected', appearance == value);
      });

      // set size state class
      $form.find('.--option.--size').each(function () {
        var $e = $(this),
            value = $e.data('value');
        $e
        // if the size is sold out for the current appearance
        .toggleClass('--na-config', !$$producttype.isAvailable(appearance, value))
        // if the size is completely sold out
        .toggleClass('--na', $$producttype.isSoldOut('size', value))
        // if the size is selected
        .toggleClass('--selected', size == value);
      });

      var stockstate = is_available ? sfw.__('available', 'apparelcuts-spreadshirt') : sfw.__('currently unavailable', 'apparelcuts-spreadshirt');

      if (!is_available) {

        if ($$producttype.isSoldOut('appearance', appearance)) {
          stockstate = sfw.__('Only available in other colors', 'apparelcuts-spreadshirt');
        } else if ($$producttype.isSoldOut('size', size)) {
          stockstate = sfw.__('Only available in other sizes', 'apparelcuts-spreadshirt');
        }
      }

      stockstate = wp.hooks.applyFilters('sfw.stockstate.display', '&#9679; ' + stockstate, is_available, appearance, size, $$producttype, $form);
      $form.find('.--stockstate').toggleClass('--na', !is_available).html(stockstate);

      var $b = $form.find('[data-add-to-basket]').attr('disabled', !is_available);
    }

    /**
     * Renders a order form
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    function renderOrderForm() {

      var $form = $(this),
          promise = $.Deferred(),
          $producttype_id = $form.find('input[name="producttype-id"]'),
          producttype_id = $producttype_id.val();

      wp.hooks.doAction('sfw.forms.order.render.before', $form);

      // disable before validating
      disableForm($form);

      // bail
      if (!producttype_id) {
        return promise.reject($form, 'missing-fields');
      }

      sfw.api.getProductType(producttype_id).fail(function (err) {
        return promise.reject($form, 'product-type-unavailable');
      }).done(function ($$producttype) {

        // $form should still be disabled

        if (!$$producttype.isSoldOut()) {

          create_ui($form, $$producttype);
          bind_ui($form, $$producttype);

          enableForm($form);

          $form.addClass('--available');

          return promise.resolve($form, $$producttype);
        } else {
          $form.addClass('--out-of-stock');
          return promise.reject($form, 'out-of-stock');
        }
      });

      promise.fail(function ($form, reason) {

        disableForm($form);

        $form.addClass('--unavailable');

        if (reason) $form.addClass('--' + reason);
      });

      promise.done(function ($form, $$producttype) {
        return wp.hooks.doAction('sfw.forms.order.render.done', $form, $$producttype);
      });
      promise.fail(function ($form, $$producttype) {
        return wp.hooks.doAction('sfw.forms.order.render.fail', $form, $$producttype);
      });
      promise.always(function ($form, $$producttype) {
        return wp.hooks.doAction('sfw.forms.order.render', $form, $$producttype);
      });

      return promise;
    }

    /**
     * Renders all unrendered order forms on the current page
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    function renderOrderFormAll() {

      wp.hooks.doAction('sfw.forms.order.renderedAll.before');

      var renders = [];

      // orderforms
      $('form[data-article]').not('[data-sfw-rendered]').each(function () {
        renders.push(renderOrderForm.call(this));
        $(this).attr('data-sfw-rendered', true);
      });

      // when all forms have been rendered
      $.when(renders).always(function () {
        return wp.hooks.doAction('sfw.forms.order.renderedAll');
      });
    }

    wp.hooks.addAction('sfw.refresh', 'sfw.renderOrderFormAll', renderOrderFormAll);

    /**
     * adds item from given orderform to basket
     *
     * @param {[type]} $form [description]
     * @since 1.0.0
     */

    function addToBasket($form) {

      var item = forms.utils.serializeObject($form);
      $form.find('.--msg-basket-error').hide();

      var promise = sfw.basket.add(item);

      sfw.feedback.await(promise, 'add-to-basket', $form[0]);

      promise.fail(function () {
        $form.find('.--msg-basket-error').show();
      });
    }

    wp.hooks.addAction('sfw.forms.order.addToBasket', 'sfw', addToBasket, 5);

    function initShippingStockState() {

      sfw.api.deliveryETA().done(function ($$eta) {

        console.log($$eta);

        function loadShippingStockState(stockstate, is_available, appearance, size, $$producttype, $form) {

          console.log(stockstate);
          if (is_available) {
            stockstate = '&#9679; ' + sfw.sprintf(sfw._x('Delivery time: %1$s - %2$s working days', 'estimated shipping, %s$1 min days, %s$2 max days', 'apparelcuts-spreadshirt'), $$eta.minDays, $$eta.maxDays);
          }

          return stockstate;
        }

        wp.hooks.addFilter('sfw.stockstate.display', 'sfw.loadShippingStockState', loadShippingStockState);
      });
    }

    wp.hooks.addAction('sfw.forms.order.renderedAll.before', 'sfw.initShippingStockState', initShippingStockState);

    /***************************************************************/
    /** Basket */

    /**
     * create default basket form
     *
     * @param  {[type]} el [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    function renderBasketForm() {

      var $basket = $(this),
          $basket_empty = $('<div class="show-for-empty-basket sfw-basket-empty-message">' + sfw.__('Your Basket is currently empty', 'apparelcuts-spreadshirt') + '</div>');

      var $basketItems = $('<ul data-basket-items class="show-for-basket"></ul>');

      var $summary = $('<div class="sfw-basket-summary grid-x show-for-basket"></div>').append('<div class="cell small-8"><span class="sfw-vat-hint">' + sfw.__('Total Items', 'apparelcuts-spreadshirt') + ':</span></div>').append('<div class="cell small-4"><span data-basket-price="items"></span></div>').append('<div class="cell small-8"><span class="sfw-sum-shipping">' + sfw.__('Shipping', 'apparelcuts-spreadshirt') + ':</span></div>').append('<div class="cell small-4"><span data-basket-price="shipping"></span></div>').append('<div class="cell small-8"><span class="sfw-sum-total">' + sfw.__('Total', 'apparelcuts-spreadshirt') + ':</span><p class="basket-price-hint">' + sfw.__('Incl. Vat, incl. Shipping', 'apparelcuts-spreadshirt') + '</p></div>').append('<div class="cell small-4"><span data-basket-price="total"></span></div>');

      $basket.append(wp.hooks.applyFilters('sfw.forms.basket.empty', $basket_empty)).append(wp.hooks.applyFilters('sfw.forms.basket.basketItems', $basketItems)).append(wp.hooks.applyFilters('sfw.forms.basket.summary', $summary)).append(forms.utils.getLoader('basket'));

      sfw.basket.get().done(function ($$basket) {

        for (var i in $$basket.basketItems) {
          $basketItems.append(createBasketItem($$basket.basketItems[i]));
        }
        wp.hooks.doAction('sfw.price.formatAll');
      });

      wp.hooks.doAction('sfw.forms.basket.rendered', $basket);
    }

    /**
     * Create BasketItem Markup
     *
     * @param  {[type]} $$item [description]
     * @return {[type]}        [description]
     * @since  1.0.0
     */

    function createBasketItem($$item) {

      var $basketItem = $('<li></li>').attr('data-basket-item', $$item.id);
      $basketItem.append('<div class="grid-x">' + '<div class="cell small-3 --sfw-article-image">' + $$item.image() + '</div>' + '<div class="cell small-9 --sfw-article-meta">' + '<div class="sfw-item-title">' + $$item.description + '</div>' + '<div class="sfw-item-appearance"><span class="sfw-label">Color:</span> ' + $$item.prop('appearanceLabel') + '</div>' + '<div class="sfw-item-size"><span class="sfw-label">Size:</span> ' + $$item.prop('sizeLabel') + '</div>' + '</div>' + '</div>');

      var $price = $('<div class="--sfw-item-price"></div>').html(sfw.price.stringify($$item.priceTotal()));

      var $controls = $('<div class="sfw-quantity-controls"></div>').append('<span class="sfw-button" data-quantity-control="remove">' + sfw.__('Remove', 'apparelcuts-spreadshirt') + '</span>').append('<span class="sfw-button" data-quantity-control="decrease">-</span>').append('<span data-basket-item-quantity>' + $$item.quantity + '</span>').append('<span class="sfw-button" data-quantity-control="increase">+</span>');

      var editLink = $$item.link('edit');

      if (editLink) $controls.append($('<a class="sfw-button">' + sfw.__('Edit', 'apparelcuts-spreadshirt') + '</a>').attr('href', editLink));

      $basketItem.find('.--sfw-article-meta').append($price).append($controls);

      $basketItem.toggleClass('--decreaseable', $$item.quantity > '1');

      return wp.hooks.applyFilters('sfw.forms.basket.item', $basketItem, $$item);
    }

    /**
     * Add BasketItem to Userinterface
     *
     * @param  {[type]} $$basketItem [description]
     * @param  {[type]} $$basket     [description]
     * @return {[type]}              [description]
     * @since  1.0.0
     */

    function uiAddBasketItem($$basketItem, $$basket) {
      $('[data-basket-items]').append(createBasketItem($$basketItem));
      wp.hooks.doAction('sfw.price.formatAll');
    }

    wp.hooks.addAction('sfw.basket.item.added', 'sfw', uiAddBasketItem);

    /**
     * Update BasketItem Userinterface
     *
     * @param  {[type]} $$basketItem [description]
     * @param  {[type]} $$basket     [description]
     * @return {[type]}              [description]
     * @since  1.0.0
     */

    function uiUpdateBasketItem($$basketItem, $$basket) {
      $('[data-basket-item="' + $$basketItem.id + '"]').each(function () {
        $(this).find('[data-basket-item-quantity]').text($$basketItem.quantity);
        $(this).find('.--sfw-item-price').html(sfw.price.stringify($$basketItem.priceTotal()));
        $(this).toggleClass('--decreaseable', $$basketItem.quantity > '1');
      });
      wp.hooks.doAction('sfw.price.formatAll');
    }

    wp.hooks.addAction('sfw.basket.item.changed', 'sfw', uiUpdateBasketItem);

    /**
     * Remove BasketItem from Userinterface
     *
     * @param  {[type]} $$basketItem [description]
     * @param  {[type]} $$basket     [description]
     * @return {[type]}              [description]
     * @since  1.0.0
     */

    function uiRemoveBasketItem($$basketItem, $$basket) {
      $('[data-basket-item="' + $$basketItem.id + '"]').remove();
    }

    wp.hooks.addAction('sfw.basket.item.removed', 'sfw', uiRemoveBasketItem);

    /**
     * Refreshs Basket Userinterface
     *
     * @param  {[type]} $$basket [description]
     * @return {[type]}          [description]
     * @since  1.0.0
     */

    function refreshBasketUI($$basket) {

      var $body = $('body');

      // body class depending on number of basket items
      $body.toggleClass('sfw-basket-empty', $$basket.isEmpty());
      $body.toggleClass('sfw-basket-not-empty', !$$basket.isEmpty());
      $body.toggleClass('sfw-basket-single-item', $$basket.totalQuantity() == 1);

      // update quantity labels
      $body.find('[data-basket-quantity]').text($$basket.totalQuantity());
      var num = $$basket.totalQuantity();
      $body.find('[data-basket-quantity-label]').text(sfw.sprintf(sfw._n('1 Item', '%1$s Items', num, 'apparelcuts-spreadshirt'), num));

      // update prices
      $body.find('[data-basket-price]').each(function () {
        var _this3 = this;

        var $$price;
        switch ($(this).data('basket-price')) {
          case 'shipping':
            $$price = $$basket.shipping.price;break;
          case 'items':
            $$price = $$basket.priceItems;break;
          case 'total':
          default:
            $$price = $$basket.priceTotal;break;
        }

        sfw.price($$price, $(this).data('price-property')).done(function (pricestr) {
          return $(_this3).html(pricestr);
        }).fail(function () {
          return $(_this3).html('');
        });
      });

      $body.find('[data-checkout],.sfw-checkout').each(function () {

        $(this).data('href', $$basket.link("defaultCheckout"));

        if ($(this).is('a')) $(this).attr('href', $$basket.link("defaultCheckout"));
      }).toggle(wp.hooks.applyFilters('sfw.hide.checkout', !$$basket.isEmpty()));
    }

    wp.hooks.addAction('sfw.basket.change', 'sfw', refreshBasketUI);

    /**
     * Renders all unrendered order forms on the current page
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    function renderBasketFormAll() {

      wp.hooks.doAction('sfw.forms.basket.renderedAll.before');

      var renders = [];

      // orderforms
      $('[data-basket]').not('[data-sfw-rendered]').each(function () {
        renders.push(renderBasketForm.call(this));
        $(this).attr('data-sfw-rendered', true);
      });

      // when all forms have been rendered
      $.when(renders).always(function () {
        sfw.basket.get().done(refreshBasketUI);
        wp.hooks.doAction('sfw.forms.basket.renderedAll');
      });
    }

    wp.hooks.addAction('sfw.refresh', 'sfw.renderBasketFormAll', renderBasketFormAll, 20);

    // bind quantity control buttons
    // Buttons must be placed inside a BasketItem with attr data-basket-item set to basketItem.id
    $('body').on('click', '[data-quantity-control]', function () {

      // bail while other basket actions are performed to prevent race conditions
      if ($('body').hasClass('sfw-wait-update-basket')) return;

      var $basketItem = $(this).parents('[data-basket-item]'),
          control = $(this).data('quantity-control');

      if (!$basketItem.length) return;

      sfw.basket.item($basketItem.data('basket-item')).done(function ($$basketItem) {

        if (typeof $$basketItem[control] !== 'function') return;

        $$basketItem[control].call();
      });
    });

    // bind checkout button
    $('body').on('click', '[data-checkout],.sfw-checkout', function (e) {

      if (!$(this).is('a')) {

        if (wp.hooks.applyFilters('sfw.checkout.same.window', true)) {
          document.location = $(this).data('href');
          e.preventDefault();
        } else {
          window.open($(this).data('href'));
        }
      }
    });
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.plugins', 'sfw.plugin.confomat', Plugin);
})(); // -- end anon