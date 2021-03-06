<?php
namespace App\Models;

use angelrove\membrillo\Database\Model;
use angelrove\membrillo\Database\GenQuery;
use angelrove\membrillo\Login\Login;

class [name_model] extends Model
{
    protected const CONF = array(
        'table' => '[name_table]',
        'soft_delete' => true,
    );

    //-------------------------------------------------------------------
    public static function list(array $filter_data)
    {
        $conditions = [
        ];

        // [parent_id]
        if (isset($filter_data['f_parent'])) {
            $conditions['f_parent'] = "A.[parent_id] = '[VALUE]'";
        }

        // Search ---
        $conditions['f_text'] = "(A.name LIKE '%[VALUE]%')";
        if ($filter_data['f_text']) {
            $conditions['f_parent'] = false;
        }

        $conditions['f_deleted'] = [
           'default' => "A.deleted_at IS NULL",
                   1 => "A.deleted_at IS NOT NULL",
        ];

        // Query ---
        $sqlFilters = GenQuery::getSqlFilters($conditions, $filter_data);
        // print_r2($sqlFilters);

        $dbTable = self::CONF['table'];

        $sqlQ = <<<EOD
            SELECT A.*,
                B.name
            FROM $dbTable AS A
            LEFT JOIN [table_parent] B ON(A.[parent_id]=B.id)
            $sqlFilters
        EOD;

        return $sqlQ;
    }
    //-------------------------------------------------------------------
}
