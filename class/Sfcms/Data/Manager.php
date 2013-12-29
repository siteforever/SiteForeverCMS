<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Data;

use Sfcms\Model;
use Symfony\Component\DependencyInjection\Container;

class Manager
{
    /** @var Container */
    private $container;

    function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Вернет нужную модель
     *
     * @param string $model
     *
     * @return Model
     */
    public function getModel($model)
    {
        return $this->container->get(sprintf('mapper.%s', trim(strtolower($model), ' .\\')));
    }
}
