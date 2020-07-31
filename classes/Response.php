<?php
class Response {
    private $_ERROR_CONST   = 'error';
    private $_MESSAGE_CONST = 'message';
    private $_BODY_CONST    = 'body';

    public function showError($message) {
        echo $this->jsonResponse(
            array(
                $this->_ERROR_CONST   => true,
                $this->_MESSAGE_CONST => $message
            )
        );
    }

    public function showSuccess($message, $body) {
        echo $this->jsonResponse(
            array(
                $this->_ERROR_CONST   => false,
                $this->_MESSAGE_CONST => $message,
                $this->_BODY_CONST    => $body,
            )
        );
    }

    private function jsonResponse($arrVar) {
        $error       = $arrVar["error"] ?? "true";
        $message     = $arrVar["message"] ?? "An internal error occurred. Try again later!";
        $body        = $arrVar["body"] ?? [];
        $encodedBody = $this->encodeItems($body);

        echo json_encode(
            array(
                $this->_ERROR_CONST   => $error,
                $this->_MESSAGE_CONST => $message,
                $this->_BODY_CONST    => $encodedBody,
            )
        );
    }

    private function encodeItems($array) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $array[$key] = $this->encodeItems($value);
            } else {
                $array[$key] = utf8_encode($value);
            }
        }

        return $array;
    }
}