<?php
namespace Mixten\Controller;

use Canister\CanisterInterface;

/**
 * Class LazyController
 *
 * @package App\Core\Controller
 */
class LazyController
{
    /**
     * @var CanisterInterface
     */
    private $container;

    /**
     * LazyController constructor.
     *
     * @param CanisterInterface $container
     */
    public function __construct(CanisterInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $mixed
     *
     * @return mixed
     */
    public function get(string $mixed)
    {
        return $this->container->get($mixed);
    }

    /**
     * @param string $mixed
     *
     * @return bool
     */
    public function has(string $mixed)
    {
        return $this->container->has($mixed);
    }
}