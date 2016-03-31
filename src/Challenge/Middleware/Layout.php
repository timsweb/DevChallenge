<?php
namespace Challenge\Middleware;

class Layout
{

    protected $renderer;

    protected $header;

    protected $footer;

    protected $enabled = true;

    protected $attributes = [];

    public function __construct($renderer, $config)
    {
        $this->renderer = $renderer;
        if (is_array($config)) {
            if (isset($config['header'])) {
                $this->header = $config['header'];
            }
            if (isset($config['footer'])) {
                $this->footer = $config['footer'];
            }
        }
    }

    /**
     * Layout middleware
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if ($this->enabled) {
            $response = $this->renderer->render($response, $this->header, $this->attributes);
        }
        $response = $next($request, $response);
        if ($this->enabled) {
            $response = $this->renderer->render($response, $this->footer, $this->attributes);
        }
        return $response;
    }


    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param  boolean $enabled
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param  array $attributes
     * @return self
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
}