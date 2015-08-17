<?php
class SoapClientWrapper extends SoapClient {
    public function __doRequest($request, $location, $action, $version, $one_way = NULL) {
        try {
            $request = preg_replace('|<env:|', '<soap:', $request);

            $request = preg_replace('|</env:|', '</soap:', $request);

            $request = preg_replace('|xmlns:env=|', 'xmlns:soap=', $request);

            $request = str_replace(' xmlns:ns1="http://www.plexus-online.com/DataSource"', '', $request);

            file_put_contents(__DIR__ . '/soap_log.txt', $request);

            return parent::__doRequest($request, $location, $action, $version);
        } catch (Exception $e) {
            echo($e);
        }
    }
}