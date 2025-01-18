<?php

namespace Strekoza\SkipFormKey\Plugin;

use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class FormKeyValidatorSkip
{
    public const ACCEPT_MODULE = ['checkout', 'liqpay'];

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param AppState $appState
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     */
    public function __construct(
        AppState $appState,
        LoggerInterface $logger,
        RequestInterface $request
    )
    {
        $this->appState = $appState;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * @param $subject
     * @param $result
     * @return bool
     */
    public function afterValidate($subject, $result)
    {
        try {
            $areaCode = $this->appState->getAreaCode();
        } catch (LocalizedException $exception) {
            $areaCode = null;
        }

        if (in_array(
            $areaCode,
            [Area::AREA_ADMINHTML],
            true
        )
        ) {
            return $result;
        }

        $moduleName = $this->request->getModuleName();
        if ($moduleName && in_array(
                $moduleName,
                self::ACCEPT_MODULE,
                true
            )) {
            return true;
        }

        return $result;
    }
}
