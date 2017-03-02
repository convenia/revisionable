<?php

namespace Convenia\Revisionable;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Revision.
 *
 * Base model to allow for revision history on
 * any model that extends this model
 *
 * (c) Venture Craft <http://www.venturecraft.com.au>
 */
class Revision extends Eloquent
{
    /**
     * @var string
     */
    public $table = 'revisions';

    /**
     * @var array
     */
    protected $revisionFormattedFields = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Revisionable.
     *
     * Grab the revision history for the model that is calling
     *
     * @return array revision history
     */
    public function revisionable()
    {
        return $this->morph1To();
    }

    /**
     * Field Name.
     *
     * Returns the field that was updated, in the case that it's a foreign key
     * denoted by a suffix of "_id", then "_id" is simply stripped
     *
     * @return string field
     */
    public function fieldName()
    {
        if ($formatted = $this->formatFieldName($this->key)) {
            return $formatted;
        }

        if (strpos($this->key, '_id')) {
            return str_replace('_id', '', $this->key);
        }

        return $this->key;
    }

    /**
     * Format field name.
     *
     * Allow overrides for field names.
     *
     * @param $key
     *
     * @return bool
     */
    private function formatFieldName($key)
    {
        $relatedModel = $this->revisionable_type;
        $relatedModel = new $relatedModel;
        $revisionFormattedFieldNames = $relatedModel->getRevisionFormattedFieldNames();

        if (isset($revisionFormattedFieldNames[$key])) {
            return $revisionFormattedFieldNames[$key];
        }

        return false;
    }

    /**
     * Old Value.
     *
     * Grab the old value of the field, if it was a foreign key
     * attempt to get an identifying name for the model.
     *
     * @return string old value
     */
    public function oldValue()
    {
        return $this->getValue('old');
    }

    /**
     * New Value.
     *
     * Grab the new value of the field, if it was a foreign key
     * attempt to get an identifying name for the model.
     *
     * @return string old value
     */
    public function newValue()
    {
        return $this->getValue('new');
    }

    /**
     * Responsible for actually doing the grunt work for getting the
     * old or new value for the revision.
     *
     * @param  string $which old or new
     *
     * @return string value
     */
    private function getValue($which = 'new')
    {
        $whichValue = $which.'_value';

        // First find the main model that was updated
        $main_model = $this->revisionable_type;

        // Load it, WITH the related model
        if (class_exists($main_model)) {
            $main_model = new $main_model;

            try {
                if ($this->isRelated()) {
                    $related_model = $this->getRelatedModel();

                    // Now we can find out the namespace of of related model
                    if (! method_exists($main_model, $related_model)) {
                        $related_model = camel_case($related_model); // for cases like published_status_id
                        if (! method_exists($main_model, $related_model)) {
                            throw new \Exception('Relation '.$related_model.' does not exist for '.$main_model);
                        }
                    }
                    $related_class = $main_model->$related_model()->getRelated();

                    // Finally, now that we know the namespace of the related model
                    // we can load it, to find the information we so desire
                    $item = $related_class::find($this->$whichValue);

                    if (is_null($this->$whichValue) || $this->$whichValue == '') {
                        $item = new $related_class;

                        return $item->getRevisionNullString();
                    }

                    if (! $item) {
                        $item = new $related_class;

                        return $this->format($this->key, $item->getRevisionUnknownString());
                    }

                    // see if there's an available mutator
                    $mutator = 'get'.studly_case($this->key).'Attribute';

                    if (method_exists($item, $mutator)) {
                        return $this->format($item->$mutator($this->key), $item->identifiableName());
                    }

                    return $this->format($this->key, $item->identifiableName());
                }
            } catch (\Exception $e) {
                // Just a fail-safe, in the case the data setup isn't as expected
                // Nothing to do here.
                Log::info('Revisionable: '.$e);
            }

            // if there was an issue
            // or, if it's a normal value

            $mutator = 'get'.studly_case($this->key).'Attribute';
            if (method_exists($main_model, $mutator)) {
                return $this->format($this->key, $main_model->$mutator($this->$whichValue));
            }
        }

        return $this->format($this->key, $this->$whichValue);
    }

    /**
     * Return true if the key is for a related model.
     *
     * @return bool
     */
    private function isRelated()
    {
        $isRelated = false;
        $idSuffix = '_id';
        $pos = strrpos($this->key, $idSuffix);

        if ($pos !== false
            && strlen($this->key) - strlen($idSuffix) === $pos
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
    private function getRelatedModel()
    {
        $idSuffix = '_id';

        return substr($this->key, 0, strlen($this->key) - strlen($idSuffix));
    }

    /**
     * User Responsible.
     *
     * @return User|bool user responsible for the change
     */
    public function userResponsible()
    {
        if (empty($this->user_id)) {
            return false;
        }

        if (class_exists($class = '\Cartalyst\Sentry\Facades\Laravel\Sentry')
            || class_exists($class = '\Cartalyst\Sentinel\Laravel\Facades\Sentinel')
        ) {
            return $class::findUserById($this->user_id);
        }

        $userModel = app('config')->get('auth.model');

        if (empty($userModel)) {
            $userModel = app('config')->get('auth.providers.users.model');
            if (empty($userModel)) {
                return false;
            }
        }

        if (! class_exists($userModel)) {
            return false;
        }

        return $userModel::find($this->user_id);
    }

    /**
     * Returns the object we have the history of.
     *
     * @return object|false
     */
    public function historyOf()
    {
        if (class_exists($class = $this->revisionable_type)) {
            return $class::find($this->revisionable_id);
        }

        return false;
    }

    /*
     * Examples:
    array(
        'public' => 'boolean:Yes|No',
        'minimum'  => 'string:Min: %s'
    )
     */

    /**
     * Format the value according to the $revisionFormattedFields array.
     *
     * @param  $key
     * @param  $value
     *
     * @return string formatted value
     */
    public function format($key, $value)
    {
        $related_model = $this->revisionable_type;
        $related_model = new $related_model;
        $revisionFormattedFields = $related_model->getRevisionFormattedFields();

        if (isset($revisionFormattedFields[$key])) {
            return FieldFormatter::format($key, $value, $revisionFormattedFields);
        }

        return $value;
    }
}
