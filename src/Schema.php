<?php
// ============================================================================
// File:    Schema.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use Seymenkonuk\Validator\Validator\Validator;
use Seymenkonuk\Validator\Validator\ObjectValidator;
use Seymenkonuk\Validator\Validator\ValidationResult;


abstract class Schema
{
    // --------------------------------------------------------------------------
    //  DEPENDENCIES
    // --------------------------------------------------------------------------

    public function __construct(
        protected Validator $validator,
    ) {}

    // --------------------------------------------------------------------------
    //  RULES
    // --------------------------------------------------------------------------

    public function body(): ObjectValidator
    {
        return $this->validator->object()->schema([]);
    }

    public function query(): ObjectValidator
    {
        return $this->validator->object()->schema([]);
    }

    public function params(): ObjectValidator
    {
        return $this->validator->object()->schema([]);
    }

    public function files(): ObjectValidator
    {
        return $this->validator->object()->schema([]);
    }

    public function rules(): ObjectValidator
    {
        return $this->validator->object()->schema([
            "body" => $this->body(),
            "query"  => $this->query(),
            "params" => $this->params(),
            "files"  => $this->files(),
        ]);
    }

    // --------------------------------------------------------------------------
    //  VALIDATE
    // --------------------------------------------------------------------------

    final public function validate(mixed $data, bool $exists = true): ValidationResult
    {
        return $this->rules()->validate($data, $exists);
    }

    // --------------------------------------------------------------------------
    //  AUTHORIZE
    // --------------------------------------------------------------------------

    // /** @return array{
    //  *      title: string,
    //  *      description: string
    //  * }|null */
    // abstract public function authorize(Request $request, ...$args): array|null;
}
