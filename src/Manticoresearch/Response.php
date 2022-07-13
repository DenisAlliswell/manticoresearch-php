<?php


namespace Manticoresearch;

/**
 * Manticore response object
 *  Stores result array, time and errors
 * @category ManticoreSearch
 * @package ManticoreSearch
 * @author Adrian Nuta <adrian.nuta@manticoresearch.com>
 * @link https://manticoresearch.com
 */
use Manticoresearch\Exceptions\RuntimeException;

/**
 * Class Response
 * @package Manticoresearch
 */
class Response
{
    /**
     * execution time to get the response
     * @var integer|float
     */
    protected $time;

    /**
     * raw response as string
     * @var string
     */
    protected $string;

    /**
     * information about request
     * @var array
     */
    protected $transportInfo;

    protected $status;
    /**
     * response as array
     * @var array
     */
    protected $response;

    /**
     * additional params as array
     * @var array
     */
    protected $params;
    
    public function __construct($responseString, $status = null, $params = [])
    {
        if (is_array($responseString)) {
            $this->response = $responseString;
        } else {
            $this->string = $this->normalizeString($responseString);
        }
        $this->status = $status;
        $this->params = $params;
    }

    /**
     * Remove bad characters from response json string
     * @param string $string
     * @return string
     */
    private function normalizeString(string $string): string
    {
        $cases = [
            '/\x03/' => ' ',
            '/[[:cntrl:]]/' => ''
        ];

        foreach ($cases as $problem => $solution) {
            $string = preg_replace($problem, $solution, $string);
        }

        return $string;
    }

    /*
     * Return response
     * @return array
     */
    public function getResponse()
    {
        if (null === $this->response) {
            $this->response = json_decode($this->string, true, 512, JSON_THROW_ON_ERROR);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('fatal error while trying to decode JSON response');
            }

            if (empty($this->response)) {
                $this->response = [];
            }
        }
        return $this->response;
    }

    /*
     * Check whenever response has error
     * @return bool
     */
    public function hasError()
    {
        $response = $this->getResponse();
        return (isset($response['error']) && $response['error'] !== '') ||
            (isset($response['errors']) && $response['errors'] !== false);
    }

    /*
     * Return error
     * @return false|string
     */
    public function getError()
    {
        $response = $this->getResponse();
        if (isset($response['error'])) {
            return json_encode($response['error'], true);
        } elseif (isset($response['errors'])) {
            return json_encode($response['errors'], true);
        } else {
            return '';
        }
    }

    /*
     * set execution time
     * @param int|float $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /*
     * returns execution time
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     *  set request info
     * @param array $info
     * @return $this
     */
    public function setTransportInfo($info)
    {
        $this->transportInfo = $info;
        return $this;
    }

    /**
     * get request info
     * @return array
     */
    public function getTransportInfo()
    {
        return $this->transportInfo;
    }
}
