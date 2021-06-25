<?php

namespace Application
{
    /**
     * @core
     */
    class Core
    {
        /**
         * @test test1, test2
         */
        protected $_test = array();

        /**
         * @readwrite
         */
        public function __construct()
        {
            echo "Construct of Core class is executed";
        }
    }

}