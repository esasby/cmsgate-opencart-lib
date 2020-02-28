<?php
namespace esas\cmsgate\opencart;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\OpencartVersion;
use Model;

class ModelExtensionPayment extends Model
{
    public function saveBillId($orderId, $billId)
    {
//        switch (OpencartVersion::getVersion()) {
//            case OpencartVersion::v3_x:
//                $sql = 'UPDATE
//                        `' . DB_PREFIX . 'order`
//                    SET
//                   	    payment_custom_field = "' . $billId . '"
//                    WHERE
//                        order_id = \'' . (int)$orderId . '\'';
//                break;
//            case OpencartVersion::v2_3_x:
//                $sql = 'UPDATE
//                        ' . DB_PREFIX . 'order
//                    SET
//                   	    payment_custom_field = "' . $this->db->escape(isset($billId) ? json_encode(array("extOrderId" => $billId)) : '') . '"
//                    WHERE
//                        order_id = \'' . (int)$orderId . '\'';
//                break;
//        }
        $sql = 'UPDATE
                        ' . DB_PREFIX . 'order     
                    SET
                   	    payment_custom_field = "' . $this->db->escape(isset($billId) ? json_encode(array("extOrderId" => $billId)) : '') . '"
                    WHERE
                        order_id = \'' . (int)$orderId . '\'';

        $this->db->query($sql);
    }

    public function getMethod($address, $total)
    {
        $moduleName = Registry::getRegistry()->getPaySystemName();
        $this->language->load('extension/payment/' . $moduleName);

        $status = true;

        if ($status) {
            return array(
                'code' => $moduleName,
                'title' => Registry::getRegistry()->getConfigWrapper()->getPaymentMethodName(),
                'terms' => '',
                'sort_order' => $this->config->get($moduleName . "_sort_order")
            );
        } else {
            return array();
        }
    }
}
