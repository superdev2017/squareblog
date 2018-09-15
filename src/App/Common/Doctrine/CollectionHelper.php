<?php

namespace App\Common\Doctrine;

use Doctrine\Common\Collections\Collection;

class CollectionHelper
{
    public static function getByIdOrFirst(Collection $collection, $id)
    {
        if ($id) {
            foreach ($collection as $obj) {
                if ($id == $obj->getId()) {
                    $ret = $obj;
                }
            }
        }
        if (!$id || !isset($ret)) {
            $ret = $collection->first();
        }

        return $ret;

    }
}