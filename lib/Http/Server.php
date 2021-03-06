<?php

declare(strict_types=1);

/** 
 *        _      _            _
 *  _ __ (_)_ _ (_)_ __ _____| |__
 * | '  \| | ' \| \ V  V / -_) '_ \
 * |_|_|_|_|_||_|_|\_/\_/\___|_.__/
 *
 * This file is part of Kristuff\MiniWeb.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @version    0.9.2
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Http;

/**
 * Class Server
 *
 * Abstracts $_SERVER superglobal access.
 *
 */
class Server
{
    /**
     * Gets/returns the content of the $_SERVER super global, or the the fallback value 
     * if the key value is null.
     *
     * @access private
     * @static
     * @param string        $key                The key
     * @param mixed         $fallbackValue      (optional) The fallback value. Default is null.
     *
     * @return mixed        The value of the key in $_SERVER if exists, otherwhise the fallback value
     */
    private static function getServerValue(string $key, $fallbackValue = null)
    {
         return isset($_SERVER[$key]) ? $_SERVER[$key] : $fallbackValue;
    }

    /**
     * Gets/returns the host name
     *
     * @access public
     * @static
     *
     * @return string       The value of HTTP_HOST in $_SERVER if exists, otherwhise 'localhost'
     */
    public static function httpHost(): string
    {
        return self::getServerValue('HTTP_HOST', 'localhost');
    }

    /**
     * Indicates whether the script was queried through the HTTPS protocol. 
     *
     * @access public
     * @static
     *
     * @return bool        true if the script was queried through the HTTPS protocol, otherwhise false. 
     */
    public static function isHttps(): bool
    {
        $https = self::getServerValue('HTTPS');
        return !empty($https) && $https != 'off';
    }

    /**
     * Gets/returns the request method
     *
     * @access public
     * @static
     *
     * @return string       The value of REQUEST_METHOD in $_SERVER if exists, otherwhise 'GET'.
     */
    public static function requestMethod(): string
    {
        return self::getServerValue('REQUEST_METHOD', 'GET');
    }
      
    /**
     * Gets/returns the request uri.
     *
     * @access public
     * @static
     *
     * @return string|null  The value of REQUEST_URI in $_SERVER if exists, otherwhise null.
     */
    public static function requestUri(): ?string
    {
        return self::getServerValue('REQUEST_URI');
    }

    /**
     * Gets/returns the server protocol. 
     *
     * @access public
     * @static
     *
     * @return string       The value of SERVER_PROTOCOL in $_SERVER if exists, otherwhise 'HTTP/1.0'.
     */
    public static function serverProtocol(): string
    {
        return self::getServerValue('SERVER_PROTOCOL', 'HTTP/1.0') ;
    }

    /**
     * Gets/returns the script name. 
     *
     * @access public
     * @static
     *
     * @return string       The value of SCRIPT_NAME in $_SERVER.
     */
    public static function scriptName(): ?string
    {
        return self::getServerValue('SCRIPT_NAME');
    }

     /**
     * Gets/returns the server identification string, given in the headers when responding to requests.
     *
     * @access public
     * @static
     *
     * @return string       The server identification string.
     */
    public static function serverSofware(): ?string
    {
        return self::getServerValue('SERVER_SOFTWARE');
    }
}