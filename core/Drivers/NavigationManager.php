<?php

namespace Core\Drivers;

use Core\Joi\System\Contracts\IMenuItem;
use Nette\Utils\Arrays;

class NavigationManager
{

    /**
     * @var array
     */
    protected array $menu = [];

    /** @var bool */
    protected $init = false;


    public $set_icon = '';
    public $set_name = '';
    public $set_link = '';
    public $set_image_icon = '';
    public $set_id = '';
    public $set_type = '';
    public int $set_order = 1;
    protected array $set_subitem = [];


    public $top_html = '';
    public $bottom_html = '';

    public function __construct()
    {

    }


    public function removeItem($key, $itemKey = null){

        if($itemKey === null){
            // Check if the key exists in the array before removing it
            if (array_key_exists($key, $this->menu)) {
                unset($this->menu[$key]);
            }
        }else{
            // Check if the key exists in the array before removing it
            if (array_key_exists($key, $this->menu) && array_key_exists($itemKey, $this->menu[$key])) {
                unset($this->menu[$key][$itemKey]);
            }
        }
    }

    public function getItem($key){
        return Arrays::pick($this->menu, $key);
    }

    public function save(): void
    {
        $this->menu[$this->set_id] = [
            'name' => $this->set_name,
            'link' => $this->set_link,
            'image_icon' => $this->set_image_icon,
            'type' => $this->set_type,
            'icon' => $this->set_icon,
            'id' => $this->set_id,
            'sub_menu' => $this->set_subitem,
            'order' => $this->set_order,
            'top_html' =>  $this->top_html,
            'bottom_html' => $this->bottom_html
        ];
        $this->clearValues();
    }


    /**
     * @return void
     */
    private function clearValues()
    {
        $this->set_id = '';
        $this->set_link = '';
        $this->set_image_icon = '';
        $this->set_type = '';
        $this->set_icon = '';
        $this->set_subitem = [];
    }

    public function setSubItem($id, $name, $icon, $imageIcon, $link, $type, $subItems = [])
    {
        $this->set_subitem[] = [
            'id' => $id,
            'name' => $name,
            'icon' => $icon,
            'image_icon' => $imageIcon,
            'link' => $link,
            'type' => $type,
            'items' => $subItems,
        ];
    }

    /**
     * @return array
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    public function loadSubMenus(array $menuItem)
    {
        foreach ($menuItem as $plugin_key => $plugin_val) {
            if ($plugin_val instanceof IMenuItem) {
                $submenu = $this->getImentOBJ($plugin_val);
                if ($submenu->isIni()) {
                    $this->set_subitem[] = $submenu->getMenu();
                }
            }
        }
    }

    private function getImentOBJ(IMenuItem $menuItem)
    {
        return $menuItem;
    }

    /**
     * @param array $menu
     */
    public function setMenu(array $menu): void
    {
        $this->menu = $menu;
    }



}