<?php

declare(strict_types=1);

namespace App\Tests\Component\Validator\Constraints;

use App\Component\Validator\Constraints\FileExists;
use App\Component\Validator\Constraints\FileExistsValidator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class FileExistsValidatorTest extends ConstraintValidatorTestCase
{
    private vfsStreamDirectory $vfsRoot;

    protected function createValidator(): FileExistsValidator
    {
        return new FileExistsValidator();
    }

    public function testValidFile()
    {
        $this->createVirtualFile();

        $constraint = new FileExists();

        $this->validator->validate($this->vfsRoot->url() . '/logs/temp.log', $constraint);

        $this->assertNoViolation();
    }

    public function testInvalidFile()
    {
        $constraint = new FileExists();

        $this->validator->validate('/path/to/non-existing/file.txt', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ file }}', '/path/to/non-existing/file.txt')
            ->assertRaised();
    }

    private function createVirtualFile(): void
    {
        $structure = [
            'logs' => [
                'temp.log' => ''
            ]
        ];

        $this->vfsRoot = vfsStream::setup(sys_get_temp_dir(), null, $structure);
    }
}
