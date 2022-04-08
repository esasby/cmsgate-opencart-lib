<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.09.2018
 * Time: 14:01
 */

namespace esas\cmsgate\wrappers;

use Throwable;

class OrderProductVoucherWrapperOpencart extends OrderProductSafeWrapper
{
    private $voucherData;
    private $unitPrice;

    /**
     * OrderProductWrapperOpencart constructor.
     * @param $voucherData
     */
    public function __construct($voucherData, $unitPrice)
    {
        parent::__construct();
        $this->voucherData = $voucherData;
        $this->unitPrice = $unitPrice;
    }


    /**
     * Артикул товара
     * @throws Throwable
     * @return string
     */
    public function getInvIdUnsafe()
    {
        return 'VOUCHER';
    }

    /**
     * Название или краткое описание товара
     * @throws Throwable
     * @return string
     */
    public function getNameUnsafe()
    {
        return $this->voucherData['description'];
    }

    /**
     * Количество товароа в корзине
     * @throws Throwable
     * @return mixed
     */
    public function getCountUnsafe()
    {
        return 1;
    }

    /**
     * Цена за единицу товара
     * @throws Throwable
     * @return mixed
     */
    public function getUnitPriceUnsafe()
    {
        return $this->unitPrice;
    }
}