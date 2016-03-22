<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Message\Trace;

/**
 * Context of a message trace.
 *
 * Debug information about a trace: was it initiated with CLI or Web request, cli arguments, web URI ...
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 *
 * TODO: separate CLI and HTTP contexts in separate classes.
 */
class Context
{
    /**
     * @var string
     */
    private $phpInterface;

    /**
     * @var bool
     */
    private $cli;

    /**
     * @var string
     */
    private $arguments;

    /**
     * @var string
     */
    private $requestMethod;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $host;

    public function __construct()
    {
        $this->phpInterface = php_sapi_name();
        $this->cli = $this->inCliContext();
        if ($this->cli) {
            $this->getCliInfo();
        } else {
            $this->getHttpInfo();
        }
    }

    /**
     * Context in CLI.
     *
     * @return bool
     */
    public function isCli(): bool
    {
        return true === $this->cli;
    }

    /**
     * Getter de phpInterface.
     *
     * @return string
     */
    public function getPhpInterface()
    {
        return $this->phpInterface;
    }

    /**
     * Arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->argumentsStringToArray($this->arguments);
    }

    /**
     * RequestMethod.
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    private function inCliContext(): bool
    {
        return 'cli' === $this->phpInterface || defined('STDIN');
    }

    private function getCliInfo()
    {
        $this->arguments = $this->argumentsToString($_SERVER['argv']);
    }

    private function argumentsToString(array $args): string
    {
        $tokens = array_map(
            function ($token) {
                if (preg_match('{^(-[^=]+=)(.+)}', $token, $match)) {
                    return $match[1].$this->escapeToken($match[2]);
                }

                if ($token && $token[0] !== '-') {
                    return $this->escapeToken($token);
                }

                return $token;
            },
            $args
        );

        return implode(' ', $tokens);
    }

    private function escapeToken(string $token): string
    {
        return preg_match('{^[\w-]+$}', $token) ? $token : escapeshellarg($token);
    }

    private function argumentsStringToArray(string $argsString): array
    {
        return explode(' ', $argsString);
    }

    private function getHttpInfo()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->host = $_SERVER['HTTP_HOST'];
        $this->path = $_SERVER['SCRIPT_URL'];
    }
}
