'use strict';

/*!
* Copyright (C) Oh!Fuchs, if not stated otherwise
* Written by Patrick Paul, Oh!Fuchs, Apparelcuts <patrick@ohfuchs.com> 2015-2019
*/

//@prepros-prepend _copyright.js

//@prepros-append theme/floating-basket.js

//@prepros-append theme/image-box.js

;(function () {
  function Plugin(sfw, $) {

    var $floating_basket = false;

    function render() {

      // bail
      if (sfw.get('disable-floating-basket')) return;

      // don't add basket if another basket is found on the page
      if ($('[data-basket]').length) return;

      $floating_basket = $('<div id="sfw-floating-basket" class="sfw"></div>');

      var $summary = $('<div class="--quick-summary grid-x"></div>');
      var $label = $('<div class="cell auto"></div>');
      $('<span class="--label"></span>').append('<i class="ac ac-cart"></i> ' + sfw._x('Cart', 'floating basket', 'apparelcuts-spreadshirt')).append('<span class="show-for-basket --item-count"><span data-basket-quantity-label></span></span>').appendTo($label);

      $summary.append($label).append('<span data-checkout class="cell shrink show-for-basket">' + sfw._x('Checkout', 'floating basket checkout action', 'apparelcuts-spreadshirt') + '</span>').appendTo($floating_basket);

      $('<div class="--basket-wrapper"></div>').append('<div data-basket></div>').append('<div class="--closer">x</div>').prependTo($floating_basket);

      var $body = $('body');

      $summary.on('click', '.--label', function () {
        return $floating_basket.toggleClass('--open');
      });
      $floating_basket.on('click', '.--closer', function () {
        return $floating_basket.removeClass('--open');
      });

      $body.append($floating_basket);

      wp.hooks.addAction('sfw.basket.item.added.now', 'sfw-floating-basket', function () {
        $floating_basket.addClass('--open');
      });
    }

    wp.hooks.addAction('sfw.ready', 'sfw.floating-basket', render, 10);
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.plugins', 'sfw.plugin.basket', Plugin);
})(); // -- end anon


;(function () {
  function Plugin(sfw, $) {

    function replace_appearance(str, appearance_id) {

      str = str.replace(/appearanceId=([0-9]+)/gmi, 'appearanceId=' + appearance_id);

      return str;
    }

    function render() {

      var promise = $.Deferred(),
          $box = $(this),
          $main = $box.find('.--main-image'),
          $list = $box.find('.--image-list'),
          $article = $box.parents('article');

      $list.on('click', 'img', function () {
        var $_img = $(this),
            $img = $_img.clone().attr('sizes', $main.find('img').attr('sizes'));

        if ($img[0].complete) {

          $main.find('img').replaceWith($img);
        } else $img[0].onload = function () {
          $main.find('img').replaceWith($img);
        };
      });

      // appearance change is only possible when inside article
      if ($article.length && wp.hooks.applyFilters('sfw.image-box.sync-appearance', true, $box, $article)) {
        var replace_appearance_all = function replace_appearance_all(appearance_id) {
          $box.find('img').each(function () {
            var $_img = $(this),
                $img = $_img.clone();

            $img.attr('src', replace_appearance($img.attr('src'), appearance_id));
            $img.attr('srcset', replace_appearance($img.attr('srcset'), appearance_id));

            if ($img[0].complete) $_img.replaceWith($img);else $img[0].onload = function () {
              $_img.replaceWith($img);
            };
          });
        };

        var appearance_id = $article.find('[name="appearance"]').val();

        if (appearance_id) replace_appearance_all(appearance_id);

        wp.hooks.addAction('sfw.forms.order.appearance.change', 'sfw.image-box', function ($form, appearance_id, $$producttype) {

          var $_article = $form.parents('article');

          // bail for other articles
          if (!$_article.is($article)) return;

          replace_appearance_all(appearance_id);
        });
      }

      return promise.resolve();
    }

    function renderAll() {

      var renders = [];

      // orderforms
      $('.sfw-image-box').not('[data-sfw-rendered]').each(function () {
        var _this = this;

        var p = render.call(this);
        renders.push(p);
        p.done(function () {
          return $(_this).attr('data-sfw-rendered', true);
        });
      });

      // when all forms have been rendered
      $.when(renders).always(function () {
        return wp.hooks.doAction('sfw.image-box.renderedAll');
      });
    }

    wp.hooks.addAction('sfw.refresh', 'sfw.image-box', renderAll);
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.plugins', 'sfw.plugin.basket', Plugin);
})(); // -- end anon