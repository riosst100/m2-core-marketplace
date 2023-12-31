/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
define([
    'Magento_Ui/js/form/element/file-uploader'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            fileInputName: ''
        },

        /**
         * Adds provided file to the files list.
         *
         * @param {Object} file
         * @returns {FileUploder} Chainable.
         */
        addFile: function (file) {
            var processedFile = this.processFile(file),
                tmpFile = [],
                resultFile = {
                'file': processedFile.file,
                'name': processedFile.name,
                'size': processedFile.size,
                'status': processedFile.status ? processedFile.status : 'new'
            };

            tmpFile[0] = resultFile;

            this.isMultipleFiles ?
                this.value.push(tmpFile) :
                this.value(tmpFile);

            return this;
        }
    });
});
