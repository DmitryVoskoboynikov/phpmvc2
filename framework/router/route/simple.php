<?php

namespace Framework\Router\Route
{
    use Framework\Router as Router;
    use Framework\ArrayMethods as ArrayMethods;

    class Simple extends Router\Route
    {
        public function matches($url)
        {
            $pattern = $this->pattern;

            preg_match_all("#:([a-zA-Z0-9]+)#", $pattern, $keys);

            if (sizeof($keys) && sizeof($keys[0]) && sizeof($keys[1]))
            {
                $keys = $keys[1];
            }
            else
            {
                return preg_match("#^{$pattern}$#", $url);
            }

            $pattern = preg_replace("#(:[a-zA-Z0-9]+)#", "([a-zA-Z0-9-_]+)", $pattern);

            preg_match_all("#^{$pattern}$#", $url, $values);

            if (sizeof($values) && sizeof($values[0]) && sizeof($values[1]))
            {
                unset($values[0]);

                $derived = array_combine($keys, ArrayMethods::flatten($values));
                $this->parameters = array_merge($this->parameters, $derived);

                return true;
            }

            return false;
        }
    }

}
