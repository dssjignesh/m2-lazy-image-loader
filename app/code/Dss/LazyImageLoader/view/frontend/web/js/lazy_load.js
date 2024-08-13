/**
* Digit Software Solutions.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
*
* @category  Dss
* @package   Dss_LazyImageLoader
* @author    Extension Team
* @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
*/
define([
    'jquery',
    'dss/unveil'
], function ($) {
    'use strict';
    $.widget('dss.dss_config', {
        _create: function () {
            var options = this.options;
            var threshold = parseInt(options.threshold);
            var lazyImageUrl = options.lazyImageUrl;
            var loadingWidth = options.loadingWidth;

            $(document).ready(function() {
                var style = `
                img.lazy {
                     background: url(${lazyImageUrl}) no-repeat;
                     background-size: ${loadingWidth}px;
                     background-position: center;
                     width: 100%;
                 }`;
                $('<style>').prop('type', 'text/css').html(style).appendTo('head');
                $("img.lazy").unveil(threshold);
            });

        }
    });
    return $.dss.dss_config;
});
