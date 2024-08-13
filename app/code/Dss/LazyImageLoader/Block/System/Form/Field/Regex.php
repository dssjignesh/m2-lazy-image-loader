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
namespace Dss\LazyImageLoader\Block\System\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Regex extends AbstractFieldArray
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Child Constructor
     */
    protected function _construct()
    {
        $this->addColumn('lazyimage', ['label' => __('Matched Expression')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Match');
        parent::_construct();
    }
}
