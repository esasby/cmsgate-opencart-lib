<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.09.2018
 * Time: 13:08
 */

namespace esas\cmsgate\wrappers;

use Cart\Cart;
use Cart\Currency;
use esas\cmsgate\opencart\ModelExtensionPayment;
use esas\cmsgate\OrderStatus;
use esas\cmsgate\utils\OpencartVersion;
use ModelCheckoutOrder;
use ModelExtensionTotalShipping;
use Registry;
use Throwable;

class OrderWrapperOpencart extends OrderSafeWrapper
{
    private $localOrderInfo;

    /**
     * @var ModelCheckoutOrder
     */
    private $model_checkout_order;

    /**
     * @var ModelExtensionPayment
     */
    private $model_extension_payment;

    /**
     * @var ModelExtensionTotalShipping
     */
    private $model_extension_total_shipping;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var Cart
     */
    private $cart;

    private $session;

    /**
     * OrderWrapperOpencart constructor.
     */
    public function __construct($orderId, Registry $registry)
    {
        parent::__construct();
        $loader = $registry->get("load");
        $loader->model('checkout/order');
        $this->model_checkout_order = $registry->get('model_checkout_order');
//        $loader->model('extension/payment/' . CmsgateRegistry::getRegistry()->getPaySystemName());
//        $this->model_extension_payment = $registry->get('model_extension_payment_'. CmsgateRegistry::getRegistry()->getPaySystemName());
        $this->model_extension_payment = new ModelExtensionPayment($registry);
        $this->localOrderInfo = $this->model_checkout_order->getOrder($orderId);
        $this->currency = $registry->get("currency");
        $this->cart = $registry->get("cart");
        $this->session = $registry->get("session");
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
                $loader->model('total/shipping');
                $this->model_extension_total_shipping = $registry->get('model_total_shipping');
                break;
            default:
                $loader->model('extension/total/shipping');
                $this->model_extension_total_shipping = $registry->get('model_extension_total_shipping');
                break;
        }
    }


    /**
     * Уникальный номер заказ в рамках CMS
     * @return string
     * @throws Throwable
     */
    public function getOrderIdUnsafe()
    {
        return $this->localOrderInfo["order_id"];
    }

    /**
     * Полное имя покупателя
     * @return string
     * @throws Throwable
     */
    public function getFullNameUnsafe()
    {
        return $this->localOrderInfo['firstname'] . ' ' . $this->localOrderInfo['lastname'];
    }

    /**
     * Мобильный номер покупателя для sms-оповещения
     * (если включено администратором)
     * @return string
     * @throws Throwable
     */
    public function getMobilePhoneUnsafe()
    {
        return preg_replace("/[^0-9]/", '', $this->localOrderInfo['telephone']);
    }

    /**
     * Email покупателя для email-оповещения
     * (если включено администратором)
     * @return string
     * @throws Throwable
     */
    public function getEmailUnsafe()
    {
        return $this->localOrderInfo['email'];
    }

    /**
     * Физический адрес покупателя
     * @return string
     * @throws Throwable
     */
    public function getAddressUnsafe()
    {
        return $this->localOrderInfo['payment_address_1'] . ' ' . $this->localOrderInfo['payment_address_2'] . ' ' . $this->localOrderInfo['payment_zone'];
    }

    /**
     * Общая сумма товаров в заказе
     * @return string
     * @throws Throwable
     */
    public function getAmountUnsafe()
    {
//        return $this->localOrderInfo['total']; // check
        return $this->formatAmount($this->localOrderInfo['total']);
    }

    /**
     * Валюта заказа (буквенный код)
     * @return string
     * @throws Throwable
     */
    public function getCurrencyUnsafe()
    {
        return $this->localOrderInfo['currency_code'];
    }

    /**
     * Массив товаров в заказе
     * @return OrderProductWrapper[]
     * @throws Throwable
     */
    public function getProductsUnsafe()
    {
        $products = $this->cart->getProducts();
        foreach ($products as $product)
            $productsWrappers[] = new OrderProductWrapperOpencart($product, $this->formatAmount($product['price']));
        if ($this->session != null && is_array($this->session->data) && array_key_exists('vouchers', $this->session->data)) {
            $products = $this->session->data['vouchers'];
            foreach ($products as $product)
                $productsWrappers[] = new OrderProductVoucherWrapperOpencart($product, $this->formatAmount($product['amount']));
        }
        return $productsWrappers;
    }

    private function formatAmount($amount)
    {
        return $this->currency->format($amount, $this->localOrderInfo['currency_code'], $this->localOrderInfo['currency_value'], false);
    }

    /**
     * BillId (идентификатор хуткигрош) успешно выставленного счета
     * @return mixed
     * @throws Throwable
     */
    public function getExtIdUnsafe()
    {
        if (array_key_exists('payment_custom_field', $this->localOrderInfo) && array_key_exists('extOrderId', $this->localOrderInfo['payment_custom_field']))
            return $this->localOrderInfo['payment_custom_field']['extOrderId'];
        return '';
//        switch (OpencartVersion::getVersion()) {
//            case OpencartVersion::v2_3_x:
//                return $this->localOrderInfo['payment_custom_field']['extOrderId'];
//            case OpencartVersion::v3_x:
//                return $this->localOrderInfo['payment_custom_field'];
//        }
    }

    /**
     * Текущий статус заказа в CMS
     * @return OrderStatus
     * @throws Throwable
     */
    public function getStatusUnsafe()
    {
        return new OrderStatus(
            $this->localOrderInfo['order_status_id'],
            $this->localOrderInfo['order_status_id']);
    }

    /**
     * Обновляет статус заказа в БД
     * @param OrderStatus $newStatus
     * @return mixed
     * @throws Throwable
     */
    public function updateStatus($newStatus)
    {
        $this->model_checkout_order->addOrderHistory($this->getOrderId(), $newStatus->getOrderStatus());
    }

    /**
     * Сохраняет привязку billid к заказу
     * @param $billId
     * @return mixed
     * @throws Throwable
     */
    public function saveExtId($billId)
    {
        $this->model_extension_payment->saveBillId($this->getOrderId(), $billId);
        $this->localOrderInfo['payment_custom_field'] = $billId;
    }

    /**
     * Идентификатор клиента
     * @return string
     * @throws Throwable
     */
    public function getClientIdUnsafe()
    {
        return $this->localOrderInfo['customer_id'];
    }

    public function getShippingAmountUnsafe()
    {
        $total = 0;
        $total_data = array(
            'totals' => array(),
            'taxes' => array(),
            'total' => &$total // только при переде по ссылке получается вернуть значение
        );
        $this->model_extension_total_shipping->getTotal($total_data);
        return $this->formatAmount($total);
    }
}