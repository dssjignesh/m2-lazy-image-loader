<?php

declare(strict_types=1);

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
namespace Dss\LazyImageLoader\Block;

use Dss\LazyImageLoader\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

class LazyLoad extends Template
{
    /**
     * LazyLoad constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Dss\LazyImageLoader\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        protected Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get Helper
     *
     * @return \Dss\LazyImageLoader\Helper\Data
     */
    public function getHelper(): Data
    {
        return $this->helper;
    }
}
