<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\PageBundle\Tests\Unit\Admin;

use PHPUnit\Framework\TestCase;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactory;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\PageBundle\Admin\PageAdmin;
use Sulu\Bundle\PageBundle\Teaser\Provider\TeaserProviderPoolInterface;
use Sulu\Component\Localization\Localization;
use Sulu\Component\PHPCR\SessionManager\SessionManagerInterface;
use Sulu\Component\Security\Authorization\SecurityChecker;
use Sulu\Component\Webspace\Manager\WebspaceCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\Webspace;

class PageAdminTest extends TestCase
{
    /**
     * @var ViewBuilderFactory
     */
    private $viewBuilderFactory;

    /**
     * @var SecurityChecker
     */
    private $securityChecker;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var TeaserProviderPoolInterface
     */
    private $teaserProviderPool;

    public function setUp(): void
    {
        $this->viewBuilderFactory = new ViewBuilderFactory();
        $this->securityChecker = $this->prophesize(SecurityChecker::class);
        $this->webspaceManager = $this->prophesize(WebspaceManagerInterface::class);
        $this->sessionManager = $this->prophesize(SessionManagerInterface::class);
        $this->teaserProviderPool = $this->prophesize(TeaserProviderPoolInterface::class);
    }

    public function testGetViews()
    {
        $this->securityChecker->hasPermission('sulu.webspaces.test-1', 'edit')->willReturn(true);

        $localization1 = new Localization('de');

        $webspace1 = new Webspace();
        $webspace1->setKey('test-1');
        $webspace1->setLocalizations([$localization1]);
        $webspace1->setDefaultLocalization($localization1);

        $localization2 = new Localization('en');

        $webspace2 = new Webspace();
        $webspace2->setKey('test-2');
        $webspace2->setLocalizations([$localization2]);
        $webspace2->setDefaultLocalization($localization2);

        $webspaceCollection = new WebspaceCollection();
        $webspaceCollection->setWebspaces([$webspace1, $webspace2]);

        $this->webspaceManager->getWebspaceCollection()->willReturn($webspaceCollection);

        $admin = new PageAdmin(
            $this->viewBuilderFactory,
            $this->webspaceManager->reveal(),
            $this->securityChecker->reveal(),
            $this->sessionManager->reveal(),
            $this->teaserProviderPool->reveal(),
            false
        );

        $viewCollection = new ViewCollection();
        $admin->configureViews($viewCollection);

        $webspaceView = $viewCollection->get('sulu_page.webspaces')->getView();
        $pageListView = $viewCollection->get('sulu_page.pages_list')->getView();

        $this->assertSame('sulu_page.webspaces', $webspaceView->getName());
        $this->assertSame('test-1', $webspaceView->getAttributeDefault('webspace'));

        $this->assertSame('sulu_page.pages_list', $pageListView->getName());
        $this->assertSame('de', $pageListView->getAttributeDefault('locale'));
    }

    public function testGetConfigWithVersioning()
    {
        $admin = new PageAdmin(
            $this->viewBuilderFactory,
            $this->webspaceManager->reveal(),
            $this->securityChecker->reveal(),
            $this->sessionManager->reveal(),
            $this->teaserProviderPool->reveal(),
            true
        );

        $config = $admin->getConfig();

        $this->assertEquals(true, $config['versioning']);
    }

    public function testGetConfigWithoutVersioning()
    {
        $admin = new PageAdmin(
            $this->viewBuilderFactory,
            $this->webspaceManager->reveal(),
            $this->securityChecker->reveal(),
            $this->sessionManager->reveal(),
            $this->teaserProviderPool->reveal(),
            false
        );

        $config = $admin->getConfig();

        $this->assertEquals(false, $config['versioning']);
    }
}
