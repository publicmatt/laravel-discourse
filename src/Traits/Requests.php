<?php
/**
*
* Discourse Groups
*
* @link https://docs.discourse.org/#tag/Groups
*
**/

namespace MatthewJensen\LaravelDiscourse\Traits;

trait Requests {


    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
        * @param string $reqString
        * @param array  $paramArray
        * @param string $apiUser
        * @param string $HTTPMETHOD
        * @return \stdClass
        *
        **/
    private function _getRequest(string $reqString, array $paramArray = [], string $apiUser = 'system', $HTTPMETHOD = 'GET'): \stdClass
    {
        $paramArray['api_key']      = $this->_apiKey;
        $paramArray['api_username'] = $apiUser;
        $paramArray['show_emails']  = 'true';
        $ch                         = curl_init();
        $url                        = sprintf('%s://%s%s?%s', $this->_protocol, $this->_dcHostname, $reqString, http_build_query($paramArray));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $HTTPMETHOD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $body = curl_exec($ch);
        $rc   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resObj            = new \stdClass();
        $resObj->http_code = $rc;
        // Only return valid json
        $json              = json_decode($body);
        $resObj->apiresult = $body;
        if (json_last_error() === JSON_ERROR_NONE) {
            $resObj->apiresult = $json;
        }

        return $resObj;
    }

    /** @noinspection MoreThanThreeArgumentsInspection * */
    /**
        * @param string $reqString
        * @param array  $paramArray
        * @param string $apiUser
        * @param string $HTTPMETHOD
        * @return \stdClass
        **/
    private function _putpostRequest(string $reqString, array $paramArray, string $apiUser = 'system', $HTTPMETHOD = 'POST'): \stdClass
    {
        $ch  = curl_init();
        $url = sprintf('%s://%s%s?api_key=%s&api_username=%s', $this->_protocol, $this->_dcHostname, $reqString, $this->_apiKey, $apiUser);
        curl_setopt($ch, CURLOPT_URL, $url);
        $query = '';
        if (isset($paramArray['group']) && is_array($paramArray['group'])) {
            $query = http_build_query($paramArray);
        } else {
            if (is_array($paramArray[0] ?? null)) {
                foreach ($paramArray[0] as $param => $value) {
                    $query .= $param . '=' . urlencode($value) . '&';
                }
            }
        }
        $query = trim($query, '&');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $HTTPMETHOD);
        $body = curl_exec($ch);
        $rc   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resObj            = new \stdClass();
        $json              = json_decode($body);
        $resObj->apiresult = $body;
        if (json_last_error() === JSON_ERROR_NONE) {
            $resObj->apiresult = $json;
        }

        $resObj->http_code = $rc;

        return $resObj;
    }

    /**
        * @param string $reqString
        * @param array  $paramArray
        * @param string $apiUser
        * @return \stdClass
        */
    private function _deleteRequest(string $reqString, array $paramArray, string $apiUser = 'system'): \stdClass
    {
        return $this->_putpostRequest($reqString, $paramArray, $apiUser, 'DELETE');
    }

    /**
        * @param string $reqString
        * @param array  $paramArray
        * @param string $apiUser
        * @return \stdClass
        */
    private function _putRequest(string $reqString, array $paramArray, string $apiUser = 'system'): \stdClass
    {
        return $this->_putpostRequest($reqString, $paramArray, $apiUser, 'PUT');
    }

    /**
        * @param string $reqString
        * @param array  $paramArray
        * @param string $apiUser
        * @return \stdClass
        */
    private function _postRequest(string $reqString, array $paramArray, string $apiUser = 'system'): \stdClass
    {
        /** @noinspection ArgumentEqualsDefaultValueInspection * */
        return $this->_putpostRequest($reqString, $paramArray, $apiUser, 'POST');
    }
}
