<?php
// ============================================================================
// File:    ValidatorRequestSchema.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\RequestSchema;


use Seymenkonuk\Validator\Validator\Validator;
use Seymenkonuk\Validator\Validator\ObjectValidator;


abstract class ValidatorRequestSchema implements IRequestSchema
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
        $result = $this->rules()->validate($data, $exists);
        return new ValidationResult(
            isValid: $result->passed(),
            errors: $result->errors(),
            validated: $result->validated(),
        );
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "seymenkonuk/validator";
    }
}
