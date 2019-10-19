<?php
/**
 * WForm
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\membrillo\Login\Login;
use angelrove\FormInputs\FormInputs;
use angelrove\FormInputs\InputContainer;

trait InputsTrait
{
    private $datos = [];

    //--------------------------------------------------------------
    /**
     * Data array or Eloquent object
     */
    public function setData($data)
    {
        // Values with errors (comes from a redirect): keep the previous values ---
        if (self::$errors) {
            $this->datos = array_merge($this->datos, $_POST);
            return;
        }

        // Set inputs data ----
        if (is_array($data)) {
            $this->datos = $data;
        } else {
            // $this->datos = $data->toArray();
            $this->datos = $data;
        }
    }
    //--------------------------------------------------------------
    public function getData()
    {
        return $this->datos;
    }
    //------------------------------------------------------------------
    public function fInput(string $type, string $name = '', string $title = '')
    {
        // Value ---
        $value = ($this->datos[$name])?? '';

        // Compatibilidad (!!)
        $typePrev = '';
        if ($type == 'text_read') {
            $typePrev = $type;
            $type = 'text';
        } else if ($type == 'checkbox') {
            $type = 'check';
        }

        // Input ---
        $input = FormInputs::{$type}($name, $value)->title($title);

        // Compatibilidad (!!)
        if ($typePrev == 'text_read') {
            $input->readonly();
        }

        // Set default timezone to user browser
        if ($type == 'datetime') {
            $input->timezone(Login::$timezone);
        }

        // Set container ---
        return $input->container('horizontal');
    }

    public function inputContainer_start(string $title, string $name = '')
    {
        return InputContainer::start($title, $name);
    }

    public function inputContainer_end()
    {
        return InputContainer::end();
    }
    //------------------------------------------------------------------
    // DEPRECATED !!
    //------------------------------------------------------------------
    public function getField($title, $htmInput, $name = '')
    {
        return InputContainer::horizontal($htmInput, $title, $name);
    }

    public function input($name, $type = 'text', $title = '', $required = false, array $params = [])
    {
        return $this->getInput($name, $title, $required, $type, $params);
    }

    public function getInput($name, $title = '', $required = false, $type = 'text', array $params = [])
    {
        $value = ($this->datos[$name])?? '';

        // Compatibilidad (!!)
        $typePrev = '';
        if ($type == 'text_read' || $type == 'readonly') {
            $typePrev = $type;
            $type = 'text';
        }

        $formInput = $this->fInput($type, $name, $title)->required($required);

        // Input "select" ---
        if ($type == 'select') {
            // Data
            if (isset($params[0])) {
                $formInput->data($params[0]);
            } else {
                $formInput->data($params['query']);
            }

            // Placeholder
            if (isset($params[1]) && $params[1]) {
                $formInput->placeholder($params[1]);
            } else if (isset($params['emptyOption'])) {
                $formInput->placeholder($params['emptyOption']);
            }

        } else if ($typePrev == 'text_read' || $typePrev == 'readonly') {
            $formInput->readonly();
        }

        return $formInput->get();
    }
    //------------------------------------------------------------------
}
