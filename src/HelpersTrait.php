<?php

namespace Convenia\Revisionable;

/**
 * Class HelpersTrait.
 */
trait HelpersTrait
{
    /**
     * Return true if the key is for a related model.
     *
     * @return bool
     */
    protected function isRelated($key)
    {
        $isRelated = false;
        $idSuffix = '_id';
        $pos = strrpos($key, $idSuffix);

        if ($pos !== false
            && strlen($key) - strlen($idSuffix) === $pos
        ) {
            $isRelated = true;
        }

        return $isRelated;
    }

    /**
     * Return the name of the related model.
     *
     * @return string
     */
    protected function getRelatedModel($key, $divergentRelations)
    {
        if (array_key_exists($key, $divergentRelations)) {
            return $divergentRelations[$key];
        }
        $idSuffix = '_id';

        return substr($key, 0, strlen($key) - strlen($idSuffix));
    }
}
