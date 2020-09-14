<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Gudoguy\Greeting\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class PrintDealHelper extends AbstractHelper {

    public function getStoreConfig() {
        return true;
    }

    /*private function request($url, $options = []) {
        $url = "https://api.printdeal.com/api/" . $url;
        $curl = curl_init($url);

        curl_setopt_array($curl, array_merge($options, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "User-ID: 8f5d3ccf-daab-44d9-9b41-3c2f13772a3a",
                "API-Secret: eo38LCDmMKW1KaBWNDfV6o1nHKJljrsxnAMMsT3Iet/yZGRw",
            ),
        ]));

        //session_write_close();
        $response = curl_exec($curl);
        //session_start();

        curl_close($curl);
        return $response;
    }*/

    public function request($url, $options = []) {
        $url = "https://api.printdeal.com/api/" . $url;
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "User-ID: 8f5d3ccf-daab-44d9-9b41-3c2f13772a3a",
            "API-Secret: eo38LCDmMKW1KaBWNDfV6o1nHKJljrsxnAMMsT3Iet/yZGRw",
        ]);

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt_array($curl, $options);

        return json_decode(curl_exec($curl), true);
    }

    private function get($url) {
        return $this->request($url, [
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);
    }

    private function post($url, $body) {
        return $this->request($url, [
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
        ]);
    }

    public function getCategories() {
        return $this->get("products/categories");
    }

    public function getProductAttributes($sku) {
        return $this->get("products/{$sku}/attributes");
    }

    public function getProductCombinations($sku) {
        return $this->get("products/{$sku}/combinations");
    }

    public function postProduct($sku, $product) {
        return $this->post("products/{$sku}", $product);
    }

}
