<?php
namespace esas\cmsgate\opencart;

use esas\cmsgate\ConfigFieldsOpencart;
use esas\cmsgate\utils\OpencartVersion;
use esas\cmsgate\Registry;
use \Opencart\System\Engine\Model as Model;

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
        $sql = 'UPDATE `' . DB_PREFIX . 'order` SET payment_custom_field = "' . $this->db->escape(isset($extOrderId) ? json_encode(array("extOrderId" => $extOrderId)) : '')
            . '" WHERE order_id = \'' . (int)$orderId . '\'';

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
        $status = true;
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
            case OpencartVersion::v3_x:
                $this->language->load('extension/payment/' . $moduleName);
                return array(
                    'code' => $moduleName,
                    'title' => Registry::getRegistry()->getConfigWrapper()->getPaymentMethodName(),
                    'terms' => Registry::getRegistry()->getConfigWrapper()->getPaymentMethodDetails(),
                    'sort_order' => $this->config->get(ConfigFieldsOpencart::sortOrder())
                );
            case OpencartVersion::v4_x:
                $this->language->load('extension/cmsgate_opencart_hutkigrosh/payment/' . $moduleName);
                $method_data = $option_data = [];
                $option_data[$moduleName] = [
                    'code' => $moduleName . '.' . $moduleName,
                    'name' => Registry::getRegistry()->getConfigWrapper()->getPaymentMethodDetails()
                ];
                $method_data = [
                    'code' => $moduleName,
                    'name' => Registry::getRegistry()->getConfigWrapper()->getPaymentMethodName(),
                    'option' => $option_data,
                    'sort_order' => $this->config->get(ConfigFieldsOpencart::sortOrder())
                ];
                return $method_data;

        }
        return array();
    }
}
