<?php
/**
 *
 * Оставляем возможность попасть в админку через GET параметр, если в init допущена ошибка
 * Для отключения init - disabled_init=Y
 * Для включения   - disabled_init=N
 *
 */
if (isset($_GET['disabled_init']) && !empty($_GET['disabled_init']))
{
    $strdisabled_init = strval($_GET['disabled_init']);
    if ($strdisabled_init == 'N')
    {
        if (isset($_SESSION['NO_INIT']))
            unset($_SESSION['NO_INIT']);
    }
    elseif ($strdisabled_init == 'Y')
    {
        $_SESSION['NO_INIT'] = 'Y';
    }
}

if (!(isset($_SESSION['NO_INIT']) && $_SESSION['NO_INIT'] == 'Y'))
{
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/functions.php"))
        require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/functions.php");
}
?>