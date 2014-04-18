<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Page\Component\SiteMap;

/**
 * Class SiteMapItem
 * @package Module\Page\Component\SiteMap
 * @link http://www.sitemaps.org/ru/protocol.html
 */
class SiteMapItem
{
    private $loc;

    /** @var \DateTime */
    private $lastmod;

    const LastmodFormatShort = 'Y-m-d';

    const LastmodFormatLong = 'c';

    private $changefreqVariants = array(
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never',
    );

    private $changefreq;

    private $priority = '0.5';

    function __construct($loc)
    {
        $this->setLoc($loc);
        $this->setPriority(0.5);
        $this->setChangefreq('weekly');
    }

    /**
     * @param string $changefreq
     *
     * @throws \UnexpectedValueException
     */
    public function setChangefreq($changefreq)
    {
        if (!in_array($changefreq, $this->changefreqVariants)) {
            throw new \UnexpectedValueException(
                sprintf('Available variants for changefreq [%s]', join(', ', $this->changefreqVariants))
            );
        }

        $this->changefreq = $changefreq;
    }

    /**
     * @return string
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * @param \DateTime|int $lastmod
     *
     * @throws \InvalidArgumentException
     */
    public function setLastmod($lastmod)
    {
        if (is_numeric($lastmod) && preg_match('/\d+/', $lastmod)) {
            $lastmod = (new \DateTime())->setTimestamp($lastmod);
        }
        if (!$lastmod instanceof \DateTime) {
            throw new \InvalidArgumentException('Lastmod can be a timestamp or DateTime');
        }
        $this->lastmod = $lastmod;
    }

    /**
     * @return \DateTime
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * @return string
     */
    public function getLastmodShort()
    {
        return $this->getLastmod()->format(self::LastmodFormatShort);
    }

    /**
     * @return string
     */
    public function getLastmodLong()
    {
        return $this->getLastmod()->format(self::LastmodFormatLong);
    }

    /**
     * @param string $loc
     *
     * @throws \UnexpectedValueException
     */
    public function setLoc($loc)
    {
        if (!filter_var($loc, FILTER_VALIDATE_URL)) {
            throw new \UnexpectedValueException('Loc is not valid url');
        }

        $this->loc = $loc;
    }

    /**
     * @return string
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param string $priority
     *
     * @throws \OutOfBoundsException
     */
    public function setPriority($priority)
    {
        if (floatval($priority) < 0 || floatval($priority) > 1) {
            throw new \OutOfBoundsException('Priority can have values ​​from 0 to 1');
        }
        $this->priority = number_format($priority, 1);
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
