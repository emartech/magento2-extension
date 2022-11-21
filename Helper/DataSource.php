<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Helper;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;

class DataSource
{
    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var array
     */
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
     * GetAllOptions
     *
     * @param AbstractSource[] $sourceModels
     * @param int[]            $storeIds
     *
     * @return array
     */
    public function getAllOptions(array $sourceModels, array $storeIds): array
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
     * GetAllOptionsByStore
     *
     * @param AbstractSource[] $sourceModels
     * @param int              $storeId
     *
     * @return void
     */
    private function getAllOptionsByStore(array $sourceModels, int $storeId): void
    {
        foreach ($sourceModels as $attributeCode => $sourceModel) {
            foreach ($sourceModel->getAllOptions() as $option) {
                if (is_string($option['value'])) {
                    $this->optionValues[$storeId][$attributeCode][$option['value']] = $option['label'] . '';
                }
            }
        }
    }

    /**
     * StartEmulation
     *
     * @param int $storeId
     *
     * @return DataSource
     */
    private function startEmulation(int $storeId): DataSource
    {
        $area = Area::AREA_FRONTEND;
        if ($storeId == 0) {
            $area = Area::AREA_ADMINHTML;
        }
        $this->emulation->startEnvironmentEmulation($storeId, $area, true);

        return $this;
    }

    /**
     * StopEmulation
     *
     * @return DataSource
     */
    private function stopEmulation(): DataSource
    {
        $this->emulation->stopEnvironmentEmulation();

        return $this;
    }
}
