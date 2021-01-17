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

    /**
     * Функция не дает обновить поля Имя и Код для раздела при импорте из 1С
     */
    function disabledSectionChange(&$arFields)
    {
        if (@$_REQUEST['mode'] == 'import') {
            unset($arFields['NAME']);
            unset($arFields['CODE']);
        }
    }

    /**
     * Функция отправляет уведомление об успешном окончании выгрузки
     * !Важно: для корректной работы функции необходим активный почтовый шаблон с типом события "COMPLETE_1C_IMPORT"
     * Примечание: реализовано через почтовыве шаблоны для удобства пользователей админки и возможности изменения адресов получателей уведомления
     */

    function sendCompleteImportNotification()
    {
        CEvent::Send('COMPLETE_1C_IMPORT', 's1', array());
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
/**
 * п2.1
 * Перехват служебного события о завершении выгрузки
 */
Main\EventManager::getInstance()->addEventHandler(
    "catalog",
    "OnCompleteCatalogImport1C",
    array("CustomEventsClass", "sendCompleteImportNotification")
);
/**
 * п2.2
 * Перехват изменения названия раздела при выгрузке из 1С
 */
Main\EventManager::getInstance()->addEventHandler(
    "iblock",
    "OnBeforeIBlockSectionUpdate",
    array("CustomEventsClass", "disabledSectionChange")
);


