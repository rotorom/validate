<?php
/**
 * Copyright 2015 Spafaridis Xenofon
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Phramework\Validate;

use \Phramework\Validate\ValidateResult;
use \Phramework\Exceptions\IncorrectParametersException;
use \Phramework\Models\Filter;

/**
 * Array validator
 * @property object|array $items If it is an object,
 * this object MUST be a valid JSON Schema. If it is an array, items of this
 * array MUST be objects, and each of these objects MUST be a valid JSON Schema.
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Spafaridis Xenophon <nohponex@gmail.com>
 * @see http://json-schema.org/latest/json-schema-validation.html#anchor36 Validation keywords for arrays
 * @since 1.0.0
 * @todo Cannot be named Array
 */
class ArrayValidator extends \Phramework\Validate\BaseValidator
{
    /**
     * Overwrite base class type
     * @var string
     */
    protected static $type = 'array';

    protected static $typeAttributes = [
        'minItems',
        'maxItems',
        'additionalItems',
        'items',
        'uniqueItems'
    ];

    public function __construct(
        $minItems = 0,
        $maxItems = null,
        $items = null,
        $uniqueItems = false,
        $additionalItems = null
    ) {
        parent::__construct();

        $this->minItems = $minItems;
        $this->maxItems = $maxItems;
        $this->items = $items;
        $this->uniqueItems = $uniqueItems;
        $this->additionalItems = $additionalItems;
        
    }

    /**
     * Validate value
     * @see \Phramework\Validate\ValidateResult for ValidateResult object
     * @param  mixed $value Value to validate
     * @return ValidateResult
     * @todo incomplete
     */
    public function validate($value)
    {
        $return = new ValidateResult($value, false);

        if (!is_array($value)) {
            $return->errorObject = 'properties validation';
            //error
            $return->errorObject = new IncorrectParametersException([
                [
                    'type' => static::getType(),
                    'failure' => 'type'
                ]
            ]);
            return $return;
        } else {
            $propertiesCount = count($value);

            if ($propertiesCount < $this->minItems) {
                //error
                $return->errorObject = new IncorrectParametersException(
                    [
                        'type' => static::getType(),
                        'failure' => 'minItems'
                    ]
                );
                return $return;
            } elseif ($this->maxItems !== null
                && $propertiesCount > $this->maxItems
            ) {
                $return->errorObject = new IncorrectParametersException(
                    [
                        'type' => static::getType(),
                        'failure' => 'maxItems'
                    ]
                );
                //error
                return $return;
            }
        }
        
        if ($this->items !== null) {
            $errorItems = [];
            //Currently we support only a signle type
            foreach ($value as $k => $v) {
                $validateItems = $this->items->validate($v);
                
                if (!$validateItems->status) {
                    $errorItems[$k] = $validateItems->errorObject->getParameters()[0];
                } else {
                    $value[$k] = $validateItems->value;
                }
            }
            
            if (!empty($errorItems)) {
                $return->errorObject = new IncorrectParametersException(
                    [
                        'type' => static::getType(),
                        'failure' => 'items',
                        'items' => [
                            $errorItems
                        ]
                    ]
                );
                return $return;
            }
        }
        
        //Check if contains duplicate items
        if ($this->uniqueItems && count($value) !== count(array_unique($value))) {
            $return->errorObject = new IncorrectParametersException(
                [
                    'type' => static::getType(),
                    'failure' => 'uniqueItems'
                ]
            );
            return $return;
        }

        //Success
        $return->errorObject = null;
        $return->status = true;
        //typecasted
        $return->value = $value;
        
        return $return;
    }
}