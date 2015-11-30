<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Content\Repository\Mapping;

/**
 * Interface for mapping definition of content-repository.
 */
interface MappingInterface
{
    /**
     * Indicates if content-repository should hydrate shadow pages.
     *
     * @return bool
     */
    public function shouldHydrateShadow();

    /**
     * Indicates if content-repository should follow internal links.
     *
     * @return bool
     */
    public function followInternalLink();

    /**
     * Indicates if content-repository should hydrate ghost pages.
     *
     * @return bool
     */
    public function shouldHydrateGhost();

    /**
     * Returns list of properties.
     *
     * @return string[]
     */
    public function getProperties();
}