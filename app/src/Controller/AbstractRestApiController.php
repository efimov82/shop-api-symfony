<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AbstractRestApiController extends AbstractController
{

  public function convertToJson($data): string
  {
    $encoders = [new JsonEncoder()];
    $defaultContext = [
      AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context): string {
        return $object->getId();
      },
    ];
    $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];
    $serializer = new Serializer($normalizers, $encoders);

    $jsonContent = $serializer->serialize($data, 'json', ['groups' => ['main']]);

    return $jsonContent;
  }
}
