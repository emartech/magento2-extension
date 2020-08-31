<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Helper;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Area;

class DataSource
{
    /**
     * @var Emulation
     */
    private $emulation;

    private $optionValues = [];

    /**
     * DataSource constructor.
     *
     * @param Emulation $emulation
     */
    public function __construct(
        Emulation $emulation
    ) {
        $this->emulation = $emulation;
    }

    /**
     * @param AbstractSource[] $sourceModels
     * @param int[]            $storeIds
     *
     * @return array
     */
    public function getAllOptions($sourceModels, $storeIds)
    {
        foreach ($storeIds as $storeId) {
            $this->optionValues[$storeId] = [];
            $this->startEmulation($storeId)
                 ->getAllOptionsByStore($sourceModels, $storeId);
        }
        $this->stopEmulation();

        return $this->optionValues;
    }

    /**
     * @param AbstractSource[] $sourceModels
     * @param int              $storeId
     */
    private function getAllOptionsByStore($sourceModels, $storeId)
    {
        foreach ($sourceModels as $attributeCode => $sourceModel) {
            foreach ($sourceModel->getAllOptions() as $option) {
                if (is_string($option['value'])) {
                    $this->optionValues[$storeId][$attributeCode][$option['value']] =
                        $option['label'].'';
                }
            }
        }
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    private function startEmulation($storeId)
    {
        $area = Area::AREA_FRONTEND;
        if ($storeId == 0) {
            $area = Area::AREA_ADMINHTML;
        }
        $this->emulation->startEnvironmentEmulation($storeId, $area, true);

        return $this;
    }

    /**
     * @return $this
     */
    private function stopEmulation()
    {
        $this->emulation->stopEnvironmentEmulation();

        return $this;
    }
}
