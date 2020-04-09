<?php
namespace esas\cmsgate\opencart;

use esas\cmsgate\Registry;
use Model;

class ModelExtensionPayment extends Model
{
    public function saveBillId($orderId, $extOrderId)
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
                   	    payment_custom_field = "' . $this->db->escape(isset($extOrderId) ? json_encode(array("extOrderId" => $extOrderId)) : '') . '"
                    WHERE
                        order_id = \'' . (int)$orderId . '\'';

        $this->db->query($sql);
    }

    public function getOrderIdByExtId($extOrderId)
    {
        $sql = 'SELECT order_id FROM ' . DB_PREFIX . 'order             
                    WHERE
                        payment_custom_field LIKE "' . $this->db->escape(isset($extOrderId) ? json_encode(array("extOrderId" => $extOrderId)) : '') . '"';;

        $result = $this->db->query($sql);
        if ($result->num_rows) {
            return $result->row['order_id'];
        } else
            return null;
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
