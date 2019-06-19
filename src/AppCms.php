<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Globals: $CONFIG_APP
 *          $CONFIG_DB
 *          $CONFIG_SECCIONES
 *
 *          Session
 *          $seccCtrl
 *          $objectsStatus
 *          Event
 *          Local
 *
 */

namespace angelrove\membrillo;

use angelrove\membrillo\Login\LoginCtrl;
use angelrove\membrillo\WApp\Config_Secciones;
use angelrove\membrillo\WApp\SeccCtrl;
use angelrove\membrillo\WApp\Session;
use angelrove\membrillo\WApp\Local;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WObjectsStatus\ObjectsStatus;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;

class AppCms extends Application
{
    public static $t = array();

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
               $objectsStatus;

        //----------------------------------------------------
        /* Login */
        LoginCtrl::init();

        //----------------------------------------------------
        /* System Events */
        $this->system_services();

        //----------------------------------------------------
        /* System objects */
        //----------------------------------------------------
        // >> $CONFIG_SECCIONES -----
        if (!Session::get('CONFIG_SECCIONES')) {
            require DOCUMENT_ROOT . '/app/CONFIG_SECC.inc';

            // Usuario: cuando se ha cargado el último objeto de sesión
            require DOCUMENT_ROOT . '/app/onInitSession.inc';
        }
        $CONFIG_SECCIONES = Session::get('CONFIG_SECCIONES');

        // Sección por defecto
        if (!isset($_REQUEST['secc']) || !$_REQUEST['secc']) {
            header('Location: /' . $CONFIG_SECCIONES->getDefault() . '/');exit();
        }

        // >> $seccCtrl -------------
        $seccCtrl = Session::get('seccCtrl');

        // Inicio de la app o cambio de seccion: reiniciar
        if (!$seccCtrl || ($_REQUEST['secc'] != $seccCtrl->secc)) {
            $seccCtrl = Session::set('seccCtrl', new SeccCtrl($_REQUEST['secc']));
            $seccCtrl->initSecc();
        } else {
            $seccCtrl->initPage();
        }

        // >> $objectsStatus --------
        $objectsStatus = Session::get('objectsStatus');
        if (!$objectsStatus) {
            $objectsStatus = Session::set('objectsStatus', new ObjectsStatus());
        }
        $objectsStatus->initPage();

        //----------------------------------------------------
        /* Config front */
        //----------------------------------------------------

        // Local ---------------------
        Local::_init();

        // CssJsLoad -----------------
        CssJsLoad::__init(CACHE_PATH, CACHE_URL);
        CssJsLoad::set_minify(CSSJSLOAD_MINIFY);
        CssJsLoad::set_version(CACHE_VERSION);
        CssJsLoad::set_cache_disabled(CACHE_CSSJS_DISABLED);

        //----------------------------------------------------
        /* Load on init */
        //----------------------------------------------------
        require __DIR__ . '/_vendor_cssjs.inc';
        CssJsLoad::set(__DIR__ . '/_themes/_basics.css');

        require DOCUMENT_ROOT . '/_vendor_cssjs.inc';
        require DOCUMENT_ROOT . '/app/onInitPage.inc';

        // Basics vendor css/js -----
        Vendor::usef('jquery');
        Vendor::usef('bootstrap');
        Vendor::usef('font-awesome');
        Vendor::usef('material-icons');
        Vendor::usef('lightbox');
        Vendor::usef('datatables');

        //----------------------------------------------------
        /* Parse event */
        //----------------------------------------------------
        Event::initPage();

        $path_secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);
        $objectsStatus->parseEvent($path_secc);
    }
    //-----------------------------------------------------------------
    private function system_services()
    {
        if (!isset($_REQUEST['APP_EVENT'])) {
            return true;
        }

        switch ($_REQUEST['APP_EVENT']) {
            case 'close':
                Session::session_destroy();
                break;

            case 'local':
                Local::onChangeLang();
                break;

            case 'download':
                $file     = $_REQUEST['f'];
                $fileUser = $_REQUEST['fu'];
                $mime     = $_REQUEST['mime'];

                header("Content-type: $mime;");
                header('Content-Disposition: attachment; filename="' . $fileUser . '";');
                readfile($file);

                exit();
                break;

            case 'timezone':
                if (isset($_GET['tofm'])) {
                   \angelrove\membrillo\Login\Login::set_timezone(timezone_name_from_abbr("", $_GET['tofm']*60, false));
                }
                else {
                   echo
  "<script>
  var timezone_offset_minutes = new Date().getTimezoneOffset();
  timezone_offset_minutes = timezone_offset_minutes == 0 ? 0 : -timezone_offset_minutes;
  location.href='/?APP_EVENT=timezone&tofm='+timezone_offset_minutes;
  </script> "; exit();
                }

                break;
        }
    }
    //-----------------------------------------------------------------
}
