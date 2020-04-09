<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 08.04.2020
 * Time: 14:19
 */

namespace esas\cmsgate\wrappers;


use esas\cmsgate\opencart\ModelExtensionPayment;

class OrderWrapperFactoryOpencart extends OrderWrapperFactory
{
    private $opencartRegistry;

    /**
     * OrderWrapperFactoryOpencart constructor.
     * @param $opencartRegistry
     */
    public function __construct($opencartRegistry)
    {
        $this->opencartRegistry = $opencartRegistry;
    }


    /**
     * По локальному id заказа возвращает wrapper
     * @param $orderId
     * @return OrderWrapper
     */
    public function getOrderWrapperByOrderId($orderId)
    {
        return new OrderWrapperOpencart($orderId, $this->opencartRegistry);
    }

    /**
     * Возвращает OrderWrapper для текущего заказа текущего пользователя
     * @return OrderWrapper
     */
    public function getOrderWrapperForCurrentUser()
    {
        $orderId = $this->session->data['order_id']; //todo check
        return $this->getOrderWrapperByOrderId($orderId);
    }

    /**
     * По номеру транзакции внешней система возвращает wrapper
     * @param $extId
     * @return OrderWrapper
     */
    public function getOrderWrapperByExtId($extId)
    {
        $opencartCmsgateModel = new ModelExtensionPayment($this->opencartRegistry);
        $orderId = $opencartCmsgateModel->getOrderIdByExtId($extId);
        if ($orderId == null || $orderId == '0')
            return null;
        return $this->getOrderWrapperByOrderId($orderId);
    }
}