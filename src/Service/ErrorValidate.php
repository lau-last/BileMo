<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ErrorValidate
{

    private ValidatorInterface $validator;


    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }


    public function check($object): void
    {
        $errors = $this->validator->validate($object);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException($errors[0]->getMessage());
        }
    }


}