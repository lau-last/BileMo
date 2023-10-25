<?php

namespace App\Service;

use App\Entity\Device;
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

    public function checkVersionAndBuildNumber(Device $device): void
    {
        if ($device->getVersion() === 0){
            throw new BadRequestHttpException('The expected version value is of type integer and greater than 0.');
        }
        if ($device->getBuildNumber() === 0){
            throw new BadRequestHttpException('The expected build number value is of type integer and greater than 0.');
        }
    }


}
