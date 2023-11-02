<?php

namespace Core\Joi\System\Contracts;
interface IMenuItem
{


    /**
     *
     * @param string $id
     * @param string $name
     * @param string $icon
     * @param string $link
     * @param int $order
     * @return mixed
     */
    public function addItem(string $id, string $name, string $icon, string $link, int $order): self;

    /**
     * @param string $id
     * @param string $name
     * @param string $icon
     * @param string $imageIcon
     * @param string $link
     * @param string $type
     * @param array $subItems
     * @return mixed
     */
    public function setSubItem(string $id, string $name, string $link,);


    /**
     * @return array
     */
    public function getMenu(): array;


    public function isIni(): bool;


}