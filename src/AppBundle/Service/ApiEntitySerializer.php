<?php


namespace AppBundle\Service;


use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiEntitySerializer
{
    private $serializer;
    public static $commentsPerPage = 1000;

    public function __construct(Serializer $serializer, RequestStack $requestStack)
    {
        $this->serializer = $serializer;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function serialize($data, array $groups = null)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        if ($groups) {
            $context->setGroups($groups);
        }

        return $this->serializer->serialize($data, 'json', $context);
    }

    public function serializeToResponse($data, $groups = [])
    {
        $response = new Response();
        $response->setContent($this->serialize($data, $groups));
        $response->headers->set('Content-Type', 'application/json');

        if ($data === null) {
            $response->setStatusCode(404);
        }
        return $response;
    }

    public function rawJsonResponse($json)
    {
        $response = new Response();
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function deserialize($jsonData, $class)
    {
        return $this->serializer->deserialize($jsonData, $class, "json");
    }


    public function makeXlsResponse($fileName, $data)
    {
        return new Response($data, 200, [
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Content-type' => 'application/vnd.ms-office',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '";',
            'Content-length' =>  \strlen($data),
            'Pragma' =>  'no-cache',
        ]);
    }

    public function makePdfResponse($data, $filename)
    {
        $response = new StreamedResponse(function () use ($data) {
            echo $data;
        });

        $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        $response->headers->add([
            'Content-Type' => 'application/pdf',
            'Cache-Control' => '',
            'Content-Length' => \strlen($data),
            'Last-Modified' => gmdate('D, d M Y H:i:s'),
            'Content-Disposition' => $contentDisposition,
        ]);

        return $response->prepare($this->request);
    }

    public function paginate(QueryBuilder $builder)
    {
        $params = $this->getPageLimitAndOffset();

        return $builder->setMaxResults($params['limit'])
            ->setFirstResult($params['offset'])
            ->getQuery()
            ->getResult();
    }

    public function getPageLimitAndOffset()
    {
        $page = abs($this->request->query->getInt('page', 1));
        $limit = abs($this->request->query->getInt('limit', self::$commentsPerPage));

        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $page ? abs($limit * ($page - 1)) : 0
        ];
    }

    public function serializeToResponseAndPaginate(QueryBuilder $builder, $groups = [])
    {
        return $this->serializeToResponse(
            $this->paginate($builder),
            $groups
        );
    }
}