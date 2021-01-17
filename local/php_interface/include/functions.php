<?php
use Bitrix\Main;
class CustomEventsClass
{
    /**
     * Функция заполняет свойство с кодом UTM_SOURCE в сохраняемом заказе
     */

    function addCustomOrderProps(Main\Event $event)
    {
        $utm_cookie = $_COOKIE["utm_source"];
        $order = $event->getParameter("ENTITY");
        $propertyCollection = $order->getPropertyCollection();
        foreach ($propertyCollection as $propertyItem) {
            if($propertyItem->getField("CODE") == 'UTM_SOURCE') {
                if(!$propertyItem->getField("VALUE") && $utm_cookie){
                    $propertyItem->setField("VALUE", $utm_cookie);
                }
                break;
            }
        }
    }
}
/**
 * п1.1
 * Перехват GET запроса и сохранение в куки на 3 дня
 */
if($_GET['utm_source']){
    setcookie("utm_source", $_GET['utm_source'], time()+60*60*24*3, "/", $_SERVER['HTTP_HOST'], 1);
}
/**
 * п1.2
 * Перехват сохранения заказа и добавление поля UTM_SOURCE
 */
Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderBeforeSaved',
    array('CustomEventsClass', 'addCustomOrderProps')
);