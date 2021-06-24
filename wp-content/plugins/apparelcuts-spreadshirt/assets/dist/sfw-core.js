'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/*!
 * JavaScript Cookie v2.2.0
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
  var registeredInModuleLoader = false;
  if (typeof define === 'function' && define.amd) {
    define(factory);
    registeredInModuleLoader = true;
  }
  if ((typeof exports === 'undefined' ? 'undefined' : _typeof(exports)) === 'object') {
    module.exports = factory();
    registeredInModuleLoader = true;
  }
  if (!registeredInModuleLoader) {
    var OldCookies = window.Cookies;
    var api = window.Cookies = factory();
    api.noConflict = function () {
      window.Cookies = OldCookies;
      return api;
    };
  }
})(function () {
  function extend() {
    var i = 0;
    var result = {};
    for (; i < arguments.length; i++) {
      var attributes = arguments[i];
      for (var key in attributes) {
        result[key] = attributes[key];
      }
    }
    return result;
  }

  function init(converter) {
    function api(key, value, attributes) {
      var result;
      if (typeof document === 'undefined') {
        return;
      }

      // Write

      if (arguments.length > 1) {
        attributes = extend({
          path: '/'
        }, api.defaults, attributes);

        if (typeof attributes.expires === 'number') {
          var expires = new Date();
          expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
          attributes.expires = expires;
        }

        // We're using "expires" because "max-age" is not supported by IE
        attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

        try {
          result = JSON.stringify(value);
          if (/^[\{\[]/.test(result)) {
            value = result;
          }
        } catch (e) {}

        if (!converter.write) {
          value = encodeURIComponent(String(value)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
        } else {
          value = converter.write(value, key);
        }

        key = encodeURIComponent(String(key));
        key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
        key = key.replace(/[\(\)]/g, escape);

        var stringifiedAttributes = '';

        for (var attributeName in attributes) {
          if (!attributes[attributeName]) {
            continue;
          }
          stringifiedAttributes += '; ' + attributeName;
          if (attributes[attributeName] === true) {
            continue;
          }
          stringifiedAttributes += '=' + attributes[attributeName];
        }
        return document.cookie = key + '=' + value + stringifiedAttributes;
      }

      // Read

      if (!key) {
        result = {};
      }

      // To prevent the for loop in the first place assign an empty array
      // in case there are no cookies at all. Also prevents odd result when
      // calling "get()"
      var cookies = document.cookie ? document.cookie.split('; ') : [];
      var rdecode = /(%[0-9A-Z]{2})+/g;
      var i = 0;

      for (; i < cookies.length; i++) {
        var parts = cookies[i].split('=');
        var cookie = parts.slice(1).join('=');

        if (!this.json && cookie.charAt(0) === '"') {
          cookie = cookie.slice(1, -1);
        }

        try {
          var name = parts[0].replace(rdecode, decodeURIComponent);
          cookie = converter.read ? converter.read(cookie, name) : converter(cookie, name) || cookie.replace(rdecode, decodeURIComponent);

          if (this.json) {
            try {
              cookie = JSON.parse(cookie);
            } catch (e) {}
          }

          if (key === name) {
            result = cookie;
            break;
          }

          if (!key) {
            result[name] = cookie;
          }
        } catch (e) {}
      }

      return result;
    }

    api.set = api;
    api.get = function (key) {
      return api.call(api, key);
    };
    api.getJSON = function () {
      return api.apply({
        json: true
      }, [].slice.call(arguments));
    };
    api.defaults = {};

    api.remove = function (key, attributes) {
      api(key, '', extend(attributes, {
        expires: -1
      }));
    };

    api.withConverter = init;

    return api;
  }

  return init(function () {});
});

/*!
* Copyright (C) Oh!Fuchs, if not stated otherwise
* Written by Patrick Paul, Oh!Fuchs, Apparelcuts <patrick@ohfuchs.com> 2015-2019
*/

;(function () {
  function Plugin(sfw, $) {

    /**
     * Add leading slash to string
     *
     * @param  {[type]} string [description]
     * @return {[type]}        [description]
     * @since  1.0.0
     */

    sfw.leadingslashit = function (string) {
      return '/' !== string.charAt(0) ? '/' + string : string;
    };

    sfw.maybeAddHost = function () {
      var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';


      var hosturl = 'https://api.spreadshirt.' + sfw.get('host') + '/api/v1';

      if (!url.match(/^(http|https)/ig)) {
        url = hosturl + sfw.leadingslashit(url);
      }

      return url;
    };

    /**
     * Make Spreadshirt API request through proxy
     *
     * @param  {[type]} args [description]
     * @return {[type]}      [description]
     * @since  1.0.0
     */

    sfw.proxy = function (args) {

      // make sure these exist
      args = $.extend({
        url: '',
        data: {}
      }, args);

      // replace url
      args.url = sfw.add_query_args({
        url: args.url
      }, sfw.get('rest').proxy_url);

      return this.api(args);
    };

    /**
     * make request to the Spreadshirt API
     *
     * By default pipes all request through proxy
     *
     * @param  {[type]} args [description]
     * @return {[type]}      [description]
     * @since  1.0.0
     */

    sfw.spreadshirtApi = sfw.spreadshirt = function (args) {

      var promise = $.Deferred();

      args = $.extend({
        data: {},
        url: '',
        proxy: true,
        method: 'get',
        check_result: true
      }, args);

      args.url = sfw.maybeAddHost(args.url);

      // leave post data untouched
      if (_typeof(args.data) == 'object' || args.method.match(/^get$/gim)) {
        args.data = $.extend({
          mediaType: 'json',
          fullData: true
        }, args.data);
      }

      // fix for jQuery <1.9.0
      args.type = args.method;

      var request = args.proxy ? sfw.proxy(args) : $.ajax(args);

      request.fail(function (err) {
        return promise.reject({ error: 'api-request', msg: '', data: err });
      });

      request.done(function ($$response) {

        if (args.check_result && args.method.match(/^get$/gim)) {

          if (!sfw.api.isSpreadshirtItem($$response)) {

            promise.reject({ error: 'api-item-not-valid', msg: '' });
          } else {
            promise.resolve(sfw.api.prepareApiItem($$response));
          }
        }

        promise.resolve($$response);
      });

      return promise;
    };

    /**
     * Make request to the Wordpress Rest API
     *
     * @param  {[type]} args [description]
     * @return {[type]}      [description]
     * @since  1.0.0
     */

    sfw.api = function (args) {

      args = $.extend({
        route: false,
        url: sfw.get('rest').url,
        headers: {},
        method: 'get'
      }, args);

      if (args.route) args.url += sfw.leadingslashit(args.route);

      // nonce the request
      args.headers['X-WP-Nonce'] = sfw.get('rest').nonce;

      // fix for jQuery <1.9.0
      args.type = args.method;

      return $.ajax(args);
    };

    /**
     * extract error message from response
     *
     * @param  {[type]} xhr [description]
     * @return {[type]}     [description]
     * @since  1.0.0
     */

    sfw.extractErrorMsg = function (xhr) {

      var msg = xhr.statusText;

      if (xhr.responseJSON) {
        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
      }

      return msg;
    };

    /**
     * Check if item is most likely an spreadshirt item
     *
     * @param  {[type]} $$item [description]
     * @return {[type]}        [description]
     * @since  1.0.0
     */

    sfw.api.isSpreadshirtItem = function ($$item) {
      var is = (typeof $$item === 'undefined' ? 'undefined' : _typeof($$item)) == 'object' && $$item.hasOwnProperty('href');
      return wp.hooks.applyFilters('sfw.api.isSpreadshirtItem', is);
    };

    /**
     * Retieve an API Item
     *
     * @param  string endpoint A root shop endpoint
     * @param  string spreadshirt_id
     * @return Promise - resolves with the Item
     * @since  1.0.0
     */

    sfw.api.getShopItem = function (endpoint, spreadshirt_id) {

      var promise = $.Deferred(),
          $$item = sfw.session.__.get(spreadshirt_id, endpoint);

      // cached error
      if ($$item && $$item.hasOwnProperty('error')) return promise.reject($$item);

      // from cache
      if ($$item) return promise.resolve(sfw.api.prepareApiItem($$item, endpoint));

      // endpoint exists?
      if (!sfw.get('shop').hasOwnProperty(endpoint)) {

        var error = { error: 'unknown-endpoit' };
        sfw.session.__.set(spreadshirt_id, error, 90, endpoint); // 90 seconds
        return promise.reject(error);
      }

      // filter expiration
      var expires = wp.hooks.applyFilters('sfw.getShopItem.expires', 60 * 60, endpoint, spreadshirt_id);

      // request
      sfw.spreadshirt({
        url: sfw.get('shop')[endpoint].href + '/' + spreadshirt_id,
        dataType: "json",
        data: {
          fullData: 'true'
        }
      })

      // success
      .done(function ($$item) {

        sfw.session.__.set(spreadshirt_id, $$item, expires, endpoint);
        promise.resolve(sfw.api.prepareApiItem($$item, endpoint));
      })

      // error
      .fail(function (err) {
        var error = { error: 'request-item' };

        sfw.session.__.set(spreadshirt_id, error, expires, endpoint);
        promise.reject(error);
      });

      return promise;
    };

    /**
     * Retrieve an ProductType
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    sfw.api.getProductType = function (id) {
      return sfw.api.getShopItem('productTypes', id);
    };

    /**
     * Retrieve an Article
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    sfw.api.getArticle = function (id) {
      return sfw.api.getShopItem('articles', id);
    };

    /**
     * Retrieve a Product
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    sfw.api.getProduct = function (id) {
      return sfw.api.getShopItem('products', id);
    };

    /**
     * Retrieve a currency
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    sfw.api.getCurrency = function (id) {

      if (!id || id == sfw.get('currency').id) {
        return $.Deferred().resolve(sfw.get('currency'));
      }
      return sfw.api.getShopItem('currencies', id);
    };

    /**
     * Retrieve a Country
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    sfw.api.getCountry = function (id) {

      if (!id || id == sfw.get('country').id) {
        return $.Deferred().resolve(sfw.get('country'));
      }
      return sfw.api.getShopItem('countries', id);
    };

    /**
     * Retieve an API List
     *
     * @param  string endpoint A root shop endpoint
     * @param  object args
     * @return Promise - resolves with the Item
     * @since  1.0.0
     */

    sfw.api.getList = function (endpoint) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};


      var promise = $.Deferred(),
          key = endpoint + $.param(args),
          storage = args.locale ? sfw.session.__ : sfw.session,
          $$list = storage.get(key, 'api-lists');

      // cached error
      if ($$list && $$list.hasOwnProperty('error')) return promise.reject($$list);

      // from cache
      if ($$list) return promise.resolve(sfw.api.prepareApiList($$list, endpoint, args));

      // base url
      var url = false;

      // endpoint exists?
      if (sfw.get('shop').hasOwnProperty(endpoint)) {

        url = sfw.get('shop')[endpoint].href;
      } else if (endpoint.match(/https:\/\/api.spreadshirt.(net|com)\//gi)) {
        url = endpoint;
      } else {
        url = 'https://api.spreadshirt.' + sfw.get('host').substr(0, 3) + '/api/v1/' + endpoint;
      }

      // filter expiration
      var expires = wp.hooks.applyFilters('sfw.getList.expires', 60 * 60, endpoint, args);

      // request
      sfw.spreadshirt({
        url: url,
        dataType: "json",
        data: args
      })

      // success
      .done(function ($$list) {

        storage.set(key, $$list, expires, 'api-lists');
        promise.resolve(sfw.api.prepareApiList($$list, endpoint, args));
      })

      // error
      .fail(function (err) {
        var error = { error: 'request-list' };

        storage.set(key, error, expires, 'api-lists');
        promise.reject(error);
      });

      return promise;
    };

    /**
     * Retrieve the shipping types
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    sfw.api.getShippingTypes = function () {

      return sfw.api.getList('shippingTypes', {
        fullData: 'true',
        locale: sfw.get('locale')
      });
    };

    /**
     * Retrieve a currency
     *
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     * @since  1.0.0
     */

    sfw.api.deliveryETA = function () {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};


      args = $.extend({
        quantity: 1,
        shippingTypeId: sfw.get('shop').defaultShippingType.id,
        country: null
      }, args);

      var D = $.Deferred(),
          storage = args.locale ? sfw.session.__ : sfw.session,
          key = $.param(args),
          $$eta = storage.get(key, 'eta');

      if ($$eta) return D.resolve($$eta);

      sfw.api.getCountry(args.country).done(function ($$country) {

        sfw.spreadshirt({
          url: 'deliveryETA',
          check_result: false,
          data: {
            shippingTypeId: args.shippingTypeId,
            countryCode: $$country.isoCode,
            quantity: args.quantity
          }
        }).done(function ($$eta) {
          storage.set(key, $$eta, 60 * 60 * 24, 'eta');
          D.resolve($$eta);
        }).fail(function (e) {
          return D.reject(e);
        });
      }).fail(function (e) {
        return D.reject(e);
      });

      return D;
    };

    // item prototypes
    function Spreadshirt($$item) {

      for (var key in $$item) {
        this[key] = $$item[key];
      }

      this.preview = function () {

        if (this.resources && this.resources.length) return this.resources[0].href;
      };

      this.link = function (type) {
        sfw.getObjectFromList(this.links, 'type', type);
      };

      this.prop = function (key) {
        sfw.getObjectFromList(this.properties, 'key', type);
      };
    }

    sfw.api.prepareApiItem = function ($$item, filter) {

      $$item = $$item instanceof Spreadshirt ? $$item : new Spreadshirt($$item);

      $$item = wp.hooks.applyFilters('sfw.api.item', $$item);
      if (filter) $$item = wp.hooks.applyFilters('sfw.api.item.' + filter, $$item);

      return $$item;
    };

    // SpreadshirtList prototypes
    function SpreadshirtList($$list) {

      for (var key in $$list) {
        this[key] = $$list[key];
      }
    }

    sfw.api.prepareApiList = function ($$list, endpoint, args) {

      $$list = $$list instanceof SpreadshirtList ? $$list : new SpreadshirtList($$list);
      $$list = wp.hooks.applyFilters('sfw.api.list', $$list, endpoint, args);

      return $$list;
    };
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.core.plugins', 'sfw.plugin.api', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    /***********************************************************************/
    // localStorage, sessionStorage

    /**
     * Helper for dealing with localStorage and sessionStorage
     *
     * @param       {[type]} storage [description]
     * @constructor
     * @since       1.0.0
     */

    function StorageProto(storage) {

      if (storage) {
        this.s = storage;
      } else {
        this.s = typeof Storage === 'undefined' ? false : localStorage;
      }

      try {
        // check if storage is really accessable
        var c = 'check';
        this.s.setItem(c, c);
        this.s.removeItem(c);
      } catch (e) {
        //fallback to sfw
        this.s = sfw;
      }

      this.namespace = 'sfw_';

      var self = this;

      // get something from localStorage
      this.get = function (key, prefix) {

        if (!self.s) return;

        self.clean();

        key = self.namespace + (prefix ? prefix + '_' : '') + key;

        var raw = self.s.getItem(key);

        // data does not exist
        if (!raw) return;

        var data = JSON.parse(raw);

        // expired
        if (data.expires && data.expires < new Date().getTime()) {
          self.s.removeItem(key);
          return;
        }

        return data.v;
      };

      // save something in localStorage
      this.set = function (key, value, expires, prefix) {

        if (!self.s) return;

        key = self.namespace + (prefix ? prefix + '_' : '') + key;

        var data = { v: value };

        if (expires > 0) data.expires = new Date().getTime() + expires * 1000;

        self.s.setItem(key, JSON.stringify(data));
      };

      // localized object
      this.__ = {
        get: function get(key) {
          var prefix = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
          var locale = arguments[2];

          prefix += "_" + (locale || sfw.get('locale'));
          return self.get(key, prefix);
        },
        set: function set(key, value, expires) {
          var prefix = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';
          var locale = arguments[4];

          prefix += "_" + (locale || sfw.get('locale'));
          return self.set(key, value, expires, prefix);
        }
      };

      // remove expired items from storage
      this.clean = function () {

        for (var i = 0; i < self.s.length; i++) {

          var key = self.s.key(i);

          // if key matches prefix
          if (key.substring(0, self.namespace.length) == self.namespace) {

            var data = self.s.getItem(key);

            if (data.expires && data.expires < new Date().getTime()) self.s.removeItem(key);
          }
        }
      };
    } // -- end storageProto

    // local storage
    sfw.storage = new StorageProto();
    sfw.session = new StorageProto(typeof Storage === 'undefined' ? false : sessionStorage);

    /***********************************************************************/
    // feedback

    var feedback = sfw.feedback = function (message, $el) {
      var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'notice';


      wp.hooks.doAction('sfw.feedback.' + type, message, $el);
      wp.hooks.doAction('sfw.feedback', type, message, $el);
    };

    feedback.error = function (message, $el) {
      this.call(this, message, $el, 'error');
    };

    feedback.success = function (message, $el) {
      this.call(this, message, $el, 'success');
    };

    /**
     * Adds waiting class to the body tag while waiting for async operations
     *
     * @param  {[type]} promise [description]
     * @param  {[type]} what    [description]
     * @return {[type]}         [description]
     * @since  1.0.0
     */

    feedback.await = function (promise, what, who) {

      var class_ = 'sfw-wait',
          what = what ? class_ + '-for-' + what : what,
          body = document.getElementsByTagName('body')[0],
          count = sfw.get('wait-count') || 0;

      // add general class
      body.classList.add(class_);

      // add what class
      if (what) body.classList.add(what);

      if (who && who.classList) who.classList.add(what);

      // and raise wait counter
      sfw.set('wait-count', (sfw.get('wait-count') || 0) + 1);

      var clear = function clear() {

        // lower wait counter
        sfw.set('wait-count', sfw.get('wait-count') - 1);

        // remove what class
        if (what) body.classList.remove(what);

        if (who && who.classList) who.classList.remove(what);

        // remove general class only when counter is zero
        if (sfw.get('wait-count') <= 0) body.classList.remove(class_);
      };

      if (promise.finally) promise.finally(clear);else if (promise.always) promise.always(clear);
    };

    /***********************************************************************/
    // visibility
    sfw.hidden = function () {
      if (typeof document.hidden !== "undefined") {
        return document.hidden;
      } else if (typeof document.msHidden !== "undefined") {
        return document.msHidden;
      } else if (typeof document.webkitHidden !== "undefined") {
        return document.webkitHidden;
      } else if (typeof document.hasFocus !== "function") {
        return !document.hasFocus();
      }
    };

    /***********************************************************************/
    // json helper

    sfw.filterObjectList = function (list, key, value) {
      var filtered = [];

      if (list) for (var i in list) {

        try {
          if (list[i][key] == value) filtered.push(list[i]);
        } catch (e) {}
      }
      return filtered;
    };

    sfw.getObjectFromList = function (list, key, value) {

      var found = sfw.filterObjectList(list, key, value);

      return found.length ? found.pop() : false;
    };

    sfw.pluck = function (list, key, index) {

      var _list = {};

      for (var i in list) {

        if (list[i][key]) {
          var _i = index ? list[i][index] : i;
          _i && (_list[_i] = list[i][key]);
        }
      }

      return _list;
    };

    sfw.objectToArray = function (list) {
      var arr = [];for (var i in list) {
        arr.push(list[i]);
      }return arr;
    };

    /***********************************************************************/
    // helper

    sfw.sanitize_class = function (str) {

      return str.replace(/[^a-zA-Z0-9-]/ig, '').toLowerCase();
    };

    sfw.add_query_args = function (args, url) {

      if (!url.includes('?')) url += '?';

      for (var param in args) {
        url += '&' + encodeURIComponent(param) + '=' + encodeURIComponent(args[param]);
      }

      return url;
    };
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.core.plugins', 'sfw.plugin.core', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    wp.hooks.addFilter('sfw.api.item.productTypes', 'sfw.producttype', function ($$item, endpoint, spreadshirt_id) {

      /* Testcode StockStates
       if( 812 == $$item.id ) {
        for( var i in $$item.stockStates ) {
          $$item.stockStates[i].available = false;
          if( $$item.stockStates[i].size.id == '2' )
            $$item.stockStates[i].available = false;
          if( $$item.stockStates[i].appearance.id == '411' )
            $$item.stockStates[i].available = false;
          if( $$item.stockStates[i].size.id == '3' && $$item.stockStates[i].appearance.id == '566' )
            $$item.stockStates[i].available = false;
        }
      }*/

      for (var i in $$item.appearances) {
        $$item.appearances[i] = sfw.api.prepareApiItem($$item.appearances[i], 'appearance');
      } /**
         * Check if product is in stock
         *
         * @param  {[type]} appearance [description]
         * @param  {[type]} size       [description]
         * @return {[type]}            [description]
         * @since  1.0.0
         */

      $$item.isAvailable = function (appearance, size) {

        for (var i in this.stockStates) {

          var stockState = this.stockStates[i];

          if (stockState.appearance.id != appearance) continue;

          if (stockState.size.id != size) continue;

          if (stockState.available) {
            return true;
          } else {
            return false;
          }
        }

        return false;
      };

      /**
       * Get available StockStates
       *
       * @param  string property Either appearance or size
       * @param  int value Either appearance id or size id
       * @return array
       * @since  1.0.0
       */

      $$item.getAvailable = function (property, value) {

        var availables = [];

        for (var i in this.stockStates) {

          var stockState = this.stockStates[i];

          // available
          if (stockState.available) {

            if (!(property && value) || stockState[property].id == value) {
              availables.push(stockState);
              continue;
            }
          }
        }

        return availables;
      };

      /**
       * check if size or appearance is sold out
       *
       * @param  string property Either appearance or size
       * @param  int value Either appearance id or size id
       * @return bool
       * @since  1.0.0
       */
      $$item.isSoldOut = function (property, value) {

        return !this.getAvailable(property, value).length;
      };

      /**
       * Get all Sizes that are not available in any appearance
       *
       * @return array
       * @since  1.0.0
       */

      $$item.getSoldOutSizes = function () {

        var soldouts = [];

        for (var i in this.sizes) {
          var size = this.sizes[i];

          if (this.isSoldOut('size', size.id)) soldouts.push(size);
        }

        return soldouts;
      };

      /**
       * Get all appearances that are not available in any size
       *
       * @return array
       * @since  1.0.0
       */

      $$item.getSoldOutAppearances = function () {

        var soldouts = [];

        for (var i in this.appearances) {
          var ap = this.appearances[i];

          if (this.isSoldOut('appearance', ap.id)) soldouts.push(ap);
        }

        return soldouts;
      };

      /**
       * get appearance
       *
       * @param  {[type]} appearance_id [description]
       * @return {[type]}               [description]
       * @since  1.0.0
       */

      $$item.getAppearance = function (id) {

        return sfw.getObjectFromList(this.appearances, 'id', id);
      };

      /**
       * get appearance
       *
       * @param  {[type]} appearance_id [description]
       * @return {[type]}               [description]
       * @since  1.0.0
       */

      $$item.getSize = function (id) {

        return sfw.getObjectFromList(this.sizes, 'id', id);
      };

      //sfw.debug( 'Producttype',  $$item );


      return $$item;
    });
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.core.plugins', 'sfw.plugin.producttype', Plugin, 20);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    wp.hooks.addFilter('sfw.api.list', 'sfw.shippingtypes', function ($$list, endpoint, spreadshirt_id) {

      if (endpoint != 'shippingTypes') return $$list;

      // extend each shippingtype
      $.each($$list.shippingTypes, function (index, $$shippingType) {

        // getCountry
        $$shippingType.getCountry = function (country_id) {
          return sfw.getObjectFromList(this.shippingCountries, 'id', country_id);
        };

        // getState
        $$shippingType.getState = function (state_id) {
          var $$state = false;
          $.each(this.shippingCountries, function (i, $$country) {
            if ($$country.shippingStates && ($$state = sfw.getObjectFromList($$country.shippingStates, 'id', state_id))) return false;
          });
          return $$state;
        };

        // getRegion
        $$shippingType.getRegion = function (region_id) {
          return sfw.getObjectFromList(this.shippingRegions, 'id', region_id);
        };

        // extend countries
        $.each($$shippingType.shippingCountries, function (id, $$country) {

          // get region
          $$country.getRegion = function () {
            return $$shippingType.getRegion($$country.shippingRegion.id);
          };

          // extend states
          if ($$country.shippingStates && $$country.shippingStates.length) $.each($$country.shippingStates, function (id, $$state) {

            // get region
            $$state.getRegion = function () {
              return $$shippingType.getRegion($$state.shippingRegion.id);
            };
          });
        });
      });

      // all countries getter
      $$list._countries = false;

      $$list.getCountries = function (id) {

        if ($$list._countries) return $$list._countries;

        $$list._countries = {};

        $.each($$list.shippingTypes, function (index, $$shippingType) {

          $.each($$shippingType.shippingCountries, function (id, $$country) {

            if (!$$list._countries.hasOwnProperty($$country.id)) {
              $$list._countries[$$country.id] = $$country;
            }
          });
        });

        return $$list._countries;
      };

      $$list.getCountryShippingTypes = function (country_id) {

        var sts = [];

        // loop through shipping types
        $.each($$list.shippingTypes, function (index, $$shippingType) {

          if ($$shippingType.getCountry(country_id)) sts.push($$shippingType);
        });

        return sts;
      };

      $$list.getCountry = function (country_id) {
        return sfw.getObjectFromList(this.getCountries(), 'id', country_id);
      };

      $$list.getRegion = function (region_id) {

        var $$region = false;

        $.each($$list.shippingTypes, function (index, $$shippingType) {
          if ($$region = $$shippingType.getRegion(region_id)) return false;
        });

        return $$region;
      };

      $$list.getState = function (state_id) {

        var $$state = false;

        $.each($$list.shippingTypes, function (index, $$shippingType) {
          if ($$state = $$shippingType.getState(state_id)) return false;
        });

        return $$state;
      };

      return $$list;
    });
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.core.plugins', 'sfw.plugin.shippingtypes', Plugin, 20);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    /**
     * Retrieve a well formatted price
     *
     * @param  {[type]} $$price                  [description]
     * @param  {String} [property='vatIncluded'] [description]
     * @return {[type]}                          [description]
     * @since  1.0.0
     */

    sfw.price = function ($$price) {
      var property = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'vatIncluded';


      var promise = $.Deferred();

      var currency_id = $$price.currency ? $$price.currency.id : sfw.get('currency').id;

      var value = $$price.hasOwnProperty(property) ? $$price[property] : false;

      if (value === false) return promise.reject({ error: 'no-value' });

      sfw.api.getCurrency(currency_id).fail(function (err) {
        return promise.reject(err);
      }).done(function ($$currency) {

        var _val = value.toFixed(parseInt($$currency.decimalCount));
        _val = _val.replace('.', sfw.get('country').decimalPoint);

        var retval = $$currency.pattern;
        retval = retval.replace('%', '<span class="--value">' + _val + '</span>');
        var currency_display = wp.hooks.applyFilters('sfw.price.display', 'symbol');
        retval = retval.replace('$', '<span class="--currency --' + currency_display + '">' + $$currency[currency_display] + '</span>');
        retval = $('<span class="sfw-price">' + retval + '</span>');
        retval = wp.hooks.applyFilters('sfw.price', retval, $$price, property, $$currency);

        promise.resolve(retval);
      });

      return promise;
    };

    /**
     * Retrieve a price markup that can later be converted to a well formatted price
     *
     * @param  {[type]} $$price                  [description]
     * @param  {String} [property='vatIncluded'] [description]
     * @return {[type]}                          [description]
     * @since  1.0.0
     */

    sfw.price.stringify = function ($$price) {
      var property = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'vatIncluded';


      var str = '<span data-price-placeholder=\'' + JSON.stringify($$price) + '\' data-price-property="' + property + '">--,-- $</span>';

      return str;
    };

    /**
     * Search for inline prices and formats them
     *
     * @return {[type]} [description]
     * @since  1.0.0
     */

    sfw.price.parseAll = function () {

      $('[data-price-placeholder]').each(function () {
        var _this = this;

        sfw.price($(this).data('price-placeholder'), $(this).data('price-property')).done(function (pricestr) {
          $(_this).replaceWith(pricestr);
        });
      });
    };

    wp.hooks.addAction('sfw.price.formatAll', 'sfw', sfw.price.parseAll);
    wp.hooks.addAction('sfw.refresh.prices', 'sfw', sfw.price.parseAll);
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.core.plugins', 'sfw.plugin.price', Plugin, 12);
})(); // -- end anon

//@prepros-prepend ../../node_modules/js-cookie/src/js.cookie.js
//@prepros-prepend _copyright.js
//@prepros-prepend core/api.js
//@prepros-prepend core/core.js
//@prepros-prepend core/producttype.js
//@prepros-prepend core/shippingtypes.js
//@prepros-prepend core/price.js

(function (global) {
  'use strict';

  if (!global.console) {
    global.console = {};
  }
  var con = global.console;
  var prop, method;
  var dummy = function dummy() {};
  var methods = 'assert,clear,count,debug,dir,dirxml,error,info,log,markTimeline,table,time,timeEnd,timeStamp,trace,warn'.split(',');
  while (method = methods.pop()) {
    if (!con[method]) con[method] = dummy;
  }
})(typeof window === 'undefined' ? undefined : window);

;(function ($, win, undefined) {

  var sfw = win.sfw = {};

  ////////////////// Console polyfill

  sfw.debug = function () {
    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    if (parseInt(sfw.get('debug'))) console.log.apply(this, args);
  };

  ////////////////// Cookies Plugin

  if (Cookies) sfw.Cookies = Cookies.noConflict();

  ////////////////// Config

  sfw.get = sfw.getItem = function (key) {
    return this.config[key] || undefined;
  };

  sfw.set = sfw.setItem = function (key, value) {
    this.config[key] = value;
  };

  sfw.remove = sfw.removeItem = function (key) {
    delete this.config[key];
  };

  sfw.config = win.sfw_config || {};
  sfw.config = wp.hooks.applyFilters('sfw.config', sfw.config);

  sfw.debug('Shop Config', sfw.config);

  ////////////////// Translations

  sfw.__ = wp.i18n.__;
  sfw._n = wp.i18n._n;
  sfw._x = wp.i18n._x;
  sfw.sprintf = wp.i18n.sprintf;
  sfw.i18n = wp.i18n;

  ////////////////// Check State and init

  console.log('%cThis site uses Spreadshirt for Wordpress by Apparelcuts.com v.' + sfw.get('version'), 'background-color:#70D9CC;color:black;');
  //console.log( "  __   __\r\n /  `-'  \\\r\n/_|     |_\\\r\n  |     |\r\n  |     |\r\n  |_____|" );

  wp.hooks.doAction('sfw.register.core.plugins', sfw, $);

  wp.hooks.doAction('sfw.register.plugins', sfw, $);

  // if the sfw/init action did run on the server
  if (sfw.get('init')) {

    $(document).ready(function () {

      /*
       This hook is only called once
       */
      wp.hooks.doAction('sfw.ready', sfw, $);

      /*
       This hook may be called multiple times
       */
      wp.hooks.doAction('sfw.refresh');
    });
  }
})(jQuery, window || {}, undefined);