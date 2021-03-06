<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Globals: $CONFIG_APP
 *          $CONFIG_DB
 *          $CONFIG_SECCIONES
 *
 *          $seccCtrl
 *          $objectsStatus
 *          Event
 *          $LOCAL
 *
 */

namespace angelrove\membrillo;

use angelrove\membrillo\WApp\Config_Secciones;
use angelrove\membrillo\WApp\SeccCtrl;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WObjectsStatus\ObjectsStatus;


class AppApi extends Application
{
    public static $lang = array();

    //-----------------------------------------------------------------
    public function __construct($document_root)
    {
        parent::__construct($document_root);

        //----------------------------------------------------
        /* Globals */
        global $CONFIG_APP,
               $CONFIG_DB,
               $CONFIG_SECCIONES,
               $seccCtrl,
               $objectsStatus,
               $LOCAL;

        //----------------------------------------------------
        /* System objects */
        //----------------------------------------------------
        // >> $CONFIG_SECCIONES -----
        $CONFIG_SECCIONES = new Config_Secciones();
        require PATH_SRC . '/CONFIG_SECC.inc';

        // >> $seccCtrl -------------
        $seccCtrl = new SeccCtrl($_REQUEST['secc']);
        $seccCtrl->initSecc();

        // >> $objectsStatus --------
        $objectsStatus = new ObjectsStatus();
        $objectsStatus->initPage();

        //----------------------------------------------------
        /* Config front */
        include_once 'lang/es.inc';

        //----------------------------------------------------
        /* Load on init */
        require PATH_SRC . '/onInitPage.inc';

        //----------------------------------------------------
        /* Parse event */
        header('Content-Type: application/json');

        Event::initPage_api();

        //---------------------------------
        // Object status
        $wObjectStatus = $objectsStatus->setNewObject(Event::$CONTROL); // if no exist
        $wObjectStatus->updateDatos();

        // Main controller
        EventController::parseApiEvent();
    }
    //-----------------------------------------------------------------
}
