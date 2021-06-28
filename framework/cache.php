<?php

namespace Framework
{
    //use Framework\Base as Base;
    //use Framework\Cache as Cache;
    use Framework\Cache\Exception as Exception;

    class Cache extends Base
    {
        /**
         * @readwrite
         */
        protected $_type;

        /**
         * @readwrite
         */
        protected $_options;

        protected function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }

        public function initialize()
        {
            if (!$this->type)
            {
                throw new Exception\Argument("Invalid type");
            }

            switch ($this->type)
            {
                case "memcached":
                {
                    return new Cache\Driver\Memcached($this->options);
                    break;
                }
                default:
                {
                    throw new Exception\Argument("Invalid type");
                    break;
                }
            }

        }

    }

}
