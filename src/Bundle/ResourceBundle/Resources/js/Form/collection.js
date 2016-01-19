
/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

(function ($) {
    'use strict';

    var _selector = 'div[data-lug-form-type="collection"]';

    $.fn.lugFormCollection = function () {
        this.each(function () {
            var _that = $(this);

            _that.trigger('lug-form-collection-pre-init', [_that]);

            var _list = _that.find('div[data-lug-form-collection="list"]');
            var _prototype = _that.attr('data-lug-form-prototype');

            var _initDelete = function (item) {
                item.find('a[data-lug-form-collection="delete"]').on('click', function (event) {
                    event.preventDefault();

                    _that.trigger('lug-form-collection-pre-remove', [_that, _list, item]);
                    item.remove();
                    _that.trigger('lug-form-collection-post-remove', [_that, _list, item]);
                });
            };

            _that.find('div[data-lug-form-collection="item"]').each(function () {
                _initDelete($(this));
            });

            _that.find('a[data-lug-form-collection="add"]').on('click', function (event) {
                event.preventDefault();

                var indexes = $.map(_list.find('div[data-lug-form-collection-key]'), function (item) {
                    return $(item).attr('data-lug-form-collection-key');
                });

                var index = 0;
                while (indexes.indexOf(index.toString()) !== -1) {
                    index++;
                }

                var prototype = $(_prototype.replace(/__name__/g, index));

                _that.trigger('lug-form-collection-pre-add', [_that, _list, prototype]);
                _list.append(prototype);
                _initDelete(prototype);
                _that.trigger('lug-form-collection-post-add', [_that, _list, prototype]);
            });

            _that.trigger('lug-form-collection-post-init', [_that]);
        });
    };

    $(_selector).lugFormCollection();

    $(document).on('lug-form-xml-http-request-pre-replace', function (event, root, trigger, data ,form) {
        form.find(_selector).lugFormCollection();
    })

}(jQuery));
