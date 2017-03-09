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
    use HelpersTrait;

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
        return $this->morphTo();
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
    protected function formatFieldName($key)
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
    protected function getValue($which = 'new')
    {
        $whichValue = $which.'_value';

        // First find the main model that was updated
        $mainModel = $this->revisionable_type;

        // Load it, WITH the related model
        if (class_exists($mainModel)) {
            $mainModel = new $mainModel;

            try {
                if ($this->isRelated($this->key)) {
                    $relatedModel = $this->getRelatedModel($this->key);

                    // Now we can find out the namespace of the related model
                    if (! method_exists($mainModel, $relatedModel)) {
                        $relatedModel = camel_case($relatedModel); // for cases like published_status_id
                        if (! method_exists($mainModel, $relatedModel)) {
                            throw new \Exception('Relation '.$relatedModel.' does not exist for '.$mainModel);
                        }
                    }

                    $relatedClass = $mainModel->$relatedModel()->getRelated();

                    // Finally, now that we know the namespace of the related model
                    // we can load it, to find the information we so desire
                    $item = $relatedClass::find($this->$whichValue);

                    if (is_null($this->$whichValue) || $this->$whichValue == '') {
                        $item = new $relatedClass;

                        return $item->getRevisionNullString();
                    }

                    if (! $item) {
                        $item = new $relatedClass;

                        return $this->format($this->key, $item->getRevisionUnknownString());
                    }

                    // see if there's an available mutator
                    $mutator = 'get'.studly_case($this->key).'Attribute';

                    if (method_exists($item, $mutator)) {
                        return $this->format($item->$mutator($this->key), $this->getModelidentifiableName($item));
                    }

                    return $this->format($this->key, $this->getModelidentifiableName($item));
                }
            } catch (\Exception $e) {
                // Just a fail-safe, in the case the data setup isn't as expected
                // Nothing to do here.
                Log::info('Revisionable: '.$e);
            }

            // if there was an issue
            // or, if it's a normal value

            $mutator = 'get'.studly_case($this->key).'Attribute';
            if (method_exists($mainModel, $mutator)) {
                return $this->format($this->key, $mainModel->$mutator($this->$whichValue));
            }
        }

        return $this->format($this->key, $this->$whichValue);
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

        if ($userModel === null) {
            $userModel = app('config')->get('auth.providers.users.model');
            if ($userModel === null) {
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
        $relatedModel = $this->revisionable_type;
        $relatedModel = new $relatedModel;
        $revisionFormattedFields = $relatedModel->getRevisionFormattedFields();

        if (isset($revisionFormattedFields[$key])) {
            return FieldFormatter::format($key, $value, $revisionFormattedFields);
        }

        return $value;
    }

        /**
         * Identifiable Name
         * When displaying revision history, when a foreign key is updated
         * instead of displaying the ID, you can choose to display a string
         * of your choice, just override this method in your model
         * By default, it will fall back to the models 'name' or 'title' fields, otherwise the ID.
         *
         * @return string an identifying name for the model
         */
        protected function getModelidentifiableName($model)
        {
            if (method_exists($model, 'identifiableName')) {
                return $model->identifiableName();
            }

            $displayFields = [
                    'name',
                    'title',
            ];

            foreach ($displayFields as $displayField) {
                if ($model->getAttribute($displayField) !== null) {
                    return $model->getAttribute($displayField);
                }
            }

            return $model->getKey();
        }
}
