
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

    var _batchSelector = 'input[data-lug-grid-batch]';
    var _batchToggleSelector = 'input[data-lug-grid-batch-toggle]';

    $.fn.lugGridBatch = function () {
        this.each(function () {
            var _that = $(this);
            var _batch = $('#' + _that.attr('data-lug-grid-batch'));

            _that.trigger('lug.grid.batch.pre_init', [_that]);

            _that.on('change', function () {
                _that.trigger('lug.grid.batch.pre_update', [_that]);
                _batch.prop('checked', _that.is(':checked')).trigger('change');
                _that.trigger('lug.grid.batch.post_update', [_that]);
            });

            _that.trigger('lug.grid.batch.post_init', [_that]);
        });
    };

    $.fn.lugGridBatchToggle = function () {
        this.each(function () {
            var _that = $(this);
            var _batches = _that.closest('table').find(_batchSelector);

            _that.trigger('lug.grid.batch.toggle.pre_init', [_that]);

            _that.on('change', function () {
                _that.trigger('lug.grid.batch.toggle.pre_update', [_that]);
                _batches.prop('checked', _that.is(':checked')).trigger('change');
                _that.trigger('lug.grid.batch.toggle.post_update', [_that]);
            });

            _that.trigger('lug.grid.batch.toggle.post_init', [_that]);
        });
    };

    $(_batchSelector).lugGridBatch();
    $(_batchToggleSelector).lugGridBatchToggle();

}(jQuery));
