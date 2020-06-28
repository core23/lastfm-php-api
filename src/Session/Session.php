<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\LastFm\Session;

final class Session implements SessionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $subscriber;

    public function __construct(string $name, string $key, int $subscriber = 0)
    {
        $this->name       = $name;
        $this->key        = $key;
        $this->subscriber = $subscriber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSubscriber(): int
    {
        return $this->subscriber;
    }
}
