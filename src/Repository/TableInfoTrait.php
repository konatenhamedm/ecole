<?php

namespace App\Repository;


trait TableInfoTrait
{
    private function getTableName($entity, $em)
    {
        return $em->getClassMetadata($entity)->getTableName();
    }

    private function getSearchColumns($searchValue, &$params = [], $cols = [])
    {
        $sql = "";
        if ($searchValue) {
            $sql .= " AND (";
            $countColumns = count($cols);
            foreach ($cols as $index => $col) {
                $colKey = str_replace('.', '_', $col);
                $params[$colKey] = "%".addcslashes($searchValue, "_%")."%";
                if ($index != ($countColumns - 1)) {
                    $sql .= "{$col} LIKE :{$colKey} OR ";
                } else {
                    $sql .=  "{$col} LIKE :{$colKey} ";
                }
            }

            $sql .= ") ";
        }
        return $sql;
    }
}