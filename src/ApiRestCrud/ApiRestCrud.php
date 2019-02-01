<?php
namespace angelrove\membrillo\ApiRestCrud;

use angelrove\membrillo\ApiRestCrud\ApiRestCrudInterface;
use angelrove\membrillo\ApiRestCrud\ApiRestCrudHelper;

class ApiRestCrud implements ApiRestCrudInterface
{
    public static function read($asJson=false, array $params=array()) {
        return ApiRestCrudHelper::read(static::CONF, $asJson, $params);
    }

    public static function readById($id) {
        $data = ApiRestCrudHelper::readById(static::CONF, $id);
        return (array) $data['body'];
    }

    public static function readEmpty() {
        return ApiRestCrudHelper::readEmpty(static::$columns);
    }

    public static function create($data) {
        return ApiRestCrudHelper::create(static::CONF, static::$columns, $data);
    }

    public static function update($id, $data) {
        return ApiRestCrudHelper::update(static::CONF, static::$columns, $id, $data);
    }

    public static function delete($id) {
        return ApiRestCrudHelper::delete(static::CONF, $id);
    }
}