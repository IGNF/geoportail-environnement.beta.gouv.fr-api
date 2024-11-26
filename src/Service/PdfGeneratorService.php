<?php

namespace App\Service;

use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;

class PdfGeneratorService{
    
    public function __construct(
        private DompdfFactoryInterface $factory,
        private DompdfWrapperInterface $wrapper,
    ) {
    }

    public function getStreamResponse($html, $filename){
        return $this->wrapper->getStreamResponse($html, $filename);
    }
}
