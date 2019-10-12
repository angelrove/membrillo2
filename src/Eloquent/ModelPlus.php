<?php

namespace angelrove\membrillo\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager as DB;
use angelrove\membrillo\Database\GenQuery;
use angelrove\membrillo\WObjects\WForm\WForm;

class ModelPlus extends Model
{
    //--------------------------------------------------
    public static function emptyRow(): array
    {
        $emptyRow = [];

        $columns = self::getTableColumns();
        foreach ($columns as $column) {
            $emptyRow[$column] = '';
        }

        return $emptyRow;
    }
    //--------------------------------------------------
    public static function createForm(array $listValues = [])
    {
        $DB_TABLE = self::getTableName();

        // Get Form values ---
        $formValues = GenQuery::getFormValues($DB_TABLE, $listValues);

        // WForm show errors ---
        if (isset($formValues['errors'])) {
            WForm::update_setErrors($formValues['errors']);
            return false;
        }

        // Create row ---
        $row = self::create($formValues);

        // Update "Event::$row_id" ---
        \Event::setRowId($row->id);

        // Message ok ---
        \Messages::set(\Local::$t['Saved']);
    }
    //--------------------------------------------------
    public function updateForm(array $listValues = [])
    {
        $DB_TABLE = $this->table;
        $id       = $this->id;

        // Get Form values ---
        $formValues = GenQuery::getFormValues($DB_TABLE, $listValues, $id);

        // WForm show errors ---
        if (isset($formValues['errors'])) {
            WForm::update_setErrors($formValues['errors'], $id);
            return false;
        }

        // Update row ---
        $this->update($formValues);

        // Update "Event::ROW_ID" ---
        \Event::setRowId($id);

        // Message ok ---
        \Messages::set(\Local::$t['Saved']);
    }
    //--------------------------------------------------
    // PRIVATE
    //--------------------------------------------------
    private static function getTableColumns()
    {
        $tableName = self::getTableName();
        return DB::schema()->getColumnListing($tableName);
    }

    private static function getTableName()
    {
        return (new static)->getTable();
    }
    //--------------------------------------------------
}
