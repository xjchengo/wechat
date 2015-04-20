<?php namespace Xjchen\Wechat\Test\Service;

use Xjchen\Wechat\Service\Menu as MenuService;

class MenuTest extends AbstractServiceTest
{
    private $menuService;
    private $menu;

    public function setUp()
    {
        parent::setUp();
        $this->menuService = new MenuService($this->config, $this->httpClient);
        $this->menu = [
            'button' => [
                [
                    'name' => 'test1',
                    'type' => 'click',
                    'key' => 'test1click'
                ],
                [
                    'name' => 'test2',
                    'type' => 'view',
                    'url' => 'http://www.echo58.com'
                ],
                [
                    'name' => 'test3',
                    'sub_button' => [
                        [
                            'name' => 'test4',
                            'type' => 'click',
                            'key' => 'test4click'
                        ],
                        [
                            'name' => 'test5',
                            'type' => 'view',
                            'url' => 'http://www.echo58.com'
                        ],
                    ]
                ]
            ]
        ];
    }

    public function testCreate()
    {
        $result = $this->menuService->create($this->menu);
        $this->assertTrue($result);
    }

    /**
     * @depends testCreate
     */
    public function testGet()
    {
        $result = $this->menuService->get();
        $this->assertArrayHasKey('menu', $result);
    }

    public function testDelete()
    {
        $result = $this->menuService->delete();
        $this->assertTrue($result);
    }

    public function testGetCurrentInfo()
    {
        $result = $this->menuService->getCurrentInfo();
        $this->assertArrayHasKey('is_menu_open', $result);
    }
}
