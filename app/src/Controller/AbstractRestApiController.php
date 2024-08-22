<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

use App\Enums\SerializeGroup;

class AbstractRestApiController extends AbstractController
{

  /**
   * Convert array to JSON string
   * 
   * @param mixed $data
   * @param SerializerInterface $serializer
   * @param array[string] $groups
   * @return string
   */
  public function convertToJson(
    mixed $data,
    SerializerInterface $serializer,
    array $groups = [SerializeGroup::MAIN->value]
  ): string {
    $encoders = [new JsonEncoder()];
    $defaultContext = [
      AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context): string {
        return $object->getId();
      },
    ];

    $context = (new ObjectNormalizerContextBuilder())->withGroups($groups)->toArray();
    $result = $serializer->normalize($data, null, $context);

    return json_encode($result);
  }

  /**
   * 
   * @param mixed $data
   * @param SerializerInterface $serializer
   * @param array[string] $groups
   * @param int $responseStatus
   * @param array $additionalHeaders
   * @return Response
   */
  public function convertToJsonResponse(
    mixed $data,
    SerializerInterface $serializer,
    array $groups = [SerializeGroup::MAIN->value],
    int $responseStatus = Response::HTTP_OK,
    array $additionalHeaders = []
  ): Response {
    $content = $this->convertToJson($data, $serializer, $groups);

    $defaultHeaders = ['Content-Type' => 'application/json'];
    $headers = array_merge($defaultHeaders, $additionalHeaders);

    return new Response($content, $responseStatus, $headers);
  }
}
