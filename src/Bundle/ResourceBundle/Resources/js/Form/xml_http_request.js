
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

    var _selector = 'form[data-xml-http-request]';

    $.fn.lugFormXmlHttpRequest = function () {
        this.each(function () {
            var _form = $(this);
            var _formName = _form.attr('name');

            _form.trigger('lug-form-xml-http-request-pre-init', [_form]);

            _form.find('[data-xml-http-request-trigger]').each(function () {
                var _element = $(this);

                _form.trigger('lug-form-xml-http-request-element-pre-init', [_form, _element]);

                _element.on('change', function () {
                    _form.trigger('lug-form-xml-http-request-element-pre-send', [_form, _element]);

                    var _data = _form.serializeArray();
                    _data.push({'name': _formName + '[_xml_http_request]', 'value': 'true'});

                    $.ajax({
                        url: _form.attr('action'),
                        method: _form.attr('method'),
                        data: _data
                    })
                    .done(function(data) {
                        _form.trigger('lug-form-xml-http-request-post-send', [_form, _element, data]);

                        var formSelector = 'form[name="' + _formName + '"]';
                        var form = $(data).find(formSelector);

                        _form.trigger('lug-form-xml-http-request-pre-replace', [_form, _element, data, form]);
                        $(formSelector).replaceWith(form);
                        _form.trigger('lug-form-xml-http-request-post-replace', [_form, _element, data, form]);

                        form.find('#' + _element.attr('id')).focus();
                        form.lugFormXmlHttpRequest();
                    });
                });

                _form.trigger('lug-form-xml-http-request-element-post-init', [_form, _element]);
            });

            _form.trigger('lug-form-xml-http-request-post-init', [_form]);
        });
    };

    $(_selector).lugFormXmlHttpRequest();

}(jQuery));
