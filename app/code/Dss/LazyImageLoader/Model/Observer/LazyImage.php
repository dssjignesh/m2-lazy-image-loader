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

namespace Dss\LazyImageLoader\Model\Observer;

use Dss\LazyImageLoader\Helper\Data;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class LazyImage implements ObserverInterface
{

    /**
     * Lazy Image constructor
     *
     * @param \Dss\LazyImageLoader\Helper\Data $helper
     * @return void
     */
    public function __construct(
        protected Data $helper
    ) {
    }

    /**
     * Observer controller_front_send_response_before
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $request = $observer->getEvent()->getRequest();

        if ($request->isAjax()) {
            return;
        }
        
        $response = $observer->getEvent()->getResponse();
        if (!$response) {
            return;
        }

        $html = $response->getBody();
        if ($html == '') {
            return;
        }

        if (!$this->helper->isEnabled($html)) {
            return;
        }
        $html = $this->helper->lazyLoad($html);
        $response->setBody($html);
    }
}
