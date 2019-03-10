<?php

namespace AppBundle\Controller;

use AppBundle\Service\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/get_list_of_partitions", name="get_list_of_partitions")
     */
    public function getListOfPartitionsAction (Request $request)
    {
        $response = $this->container->get('app.catlog')->getPartitions();
        return new JsonResponse($response);
    }

    /**
     * @Route("/get_list_of_folders", name="get_list_of_folders")
     */
    public function getListOfFoldersAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['path']);
        //$data = $this->getDataFromRequest($request, ['path']);
        $response = $this->container->get('app.catlog')->getFolders($data['path']);
        //$response = $this->container->get('app.catlog')->getFolders($request->request->get('path'));
        return new JsonResponse($response);
    }

    /**
     * @Route("/add_catalog", name="add_catalog")
     */
    public function addCatalogAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['name','path']);
        $response = $this->container->get('app.catlog')->addCatalog($data['name'],$data['path']);
        return new JsonResponse($response);
    }

    /**
     * @Route("/add_catalog_file", name="add_catalog_file")
     */
    public function addCatalogFileAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['catalog_disk_id','path']);
        $response = $this->container->get('app.catlog')->addCatalogFile($data['catalog_disk_id'],$data['path']);
        return new JsonResponse($response);
    }
}
