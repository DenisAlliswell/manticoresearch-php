<?php


namespace Manticoresearch\Endpoints\Indices;


use Manticoresearch\Endpoints\Sql;
use Manticoresearch\Exceptions\RuntimeException;
use Manticoresearch\Utils;

class Alter extends Sql
{
    use Utils;
    /**
     * @var string
     */
    protected $_index;

    public function setBody($params)
    {
        if (isset($this->_index)) {
            if(isset($params['operation'])) {
                if($params['operation']=='add') {
                    if(isset($params['column'])) {
                        return parent::setBody(['query' => "ALTER TABLE ".$this->_index." ADD COLUMN ". $params['column']['name']." ".strtoupper($params['column']['type'])]);
                        return $this;
                    }
                }
                if($params['operation']=='drop') {
                    return parent::setBody(['query' => "ALTER TABLE ".$this->_index." DROP COLUMN ". $params['column']['name']]);
                }
            }

        }
        throw new RuntimeException('Index name is missing.');
    }
    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->_index = $index;
    }
}