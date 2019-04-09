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
        $response = $this->container->get('app.catlog')->getFolders($data['path']);
        return new JsonResponse($response);
    }

    /**
     * @Route("/add_catalog", name="add_catalog")
     */
    public function addCatalogAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['group_id','name','path','recursively']);
        $response = $this->container->get('app.catlog')->addCatalog($data['group_id'],$data['name'],$data['path'],$data['recursively']);
        return new JsonResponse($response);
    }

    /**
     * @Route("/get_catalog_disks", name="get_catalog_disks")
     */
    public function getCatalogDisksAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['group_id']);
        $response = $this->container->get('app.catlog')->getCatalogDisksByGroupId($data['group_id']);
        return new JsonResponse($response);
    }

    /**
     * @Route("/add_catalog_file", name="add_catalog_file")
     */
    public function addCatalogFileAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['catalog_disk_id','path']);
        $this->container->get('app.catlog')->addCatalogFile($data['catalog_disk_id'],$data['path']);
        $response = [
            'alerts' => $request->getSession()->getFlashBag()->all()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/add_group", name="add_group")
     */
    public function addGroupAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['name']);
        $this->container->get('app.catlog')->addGroup($data['name']);
        $response = [
            'alerts' => $request->getSession()->getFlashBag()->all()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/get_list_of_groups", name="get_list_of_groups")
     */
    public function getListOfGroupsAction (Request $request)
    {
        $response = $this->container->get('app.catlog')->getGroups();
        return new JsonResponse($response);
    }

    /**
     * @Route("/cancel_add_catalog_file", name="cancel_add_catalog_file")
     */
    public function cancelAddCatalogFileAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['catalog_disk_id']);
        $this->container->get('app.catlog')->deleteCatalogDisk($data['catalog_disk_id']);
        $response = [
            'alerts' => $request->getSession()->getFlashBag()->all()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/get_catalog_folders", name="get_catalog_folders")
     */
    public function getCatalogFoldersAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['disk_id']);
        $response = $this->container->get('app.catlog')->getTreeFolders($data['disk_id']);
        return new JsonResponse($response);
    }

    /**
     * @Route("/get_catalog_files", name="get_catalog_files")
     */
    public function getCatalogFilesAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['disk_id']);
        $response = $this->container->get('app.catlog')->getTreeFilesByDiskId($data['disk_id']);
        return new JsonResponse($response);
    }

    /**
     * @Route("/edit_group_name", name="edit_group_name")
     */
    public function editGroupNameAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['id','name']);
        $this->container->get('app.catlog')->editGroupName($data['id'],$data['name']);
        $response = [
            'alerts' => $request->getSession()->getFlashBag()->all()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/edit_disk_name", name="edit_disk_name")
     */
    public function editDiskNameAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['id','name']);
        $this->container->get('app.catlog')->editDiskName($data['id'],$data['name']);
        $response = [
            'alerts' => $request->getSession()->getFlashBag()->all()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/toggle_favorite", name="toggle_favorite")
     */
    public function toggleFavoriteAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['id']);
        $status = $this->container->get('app.catlog')->toggleFavorite($data['id']);
        $response = [
            'status' => $status,
            'alerts' => $request->getSession()->getFlashBag()->all()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/get_frames", name="get_frames")
     */
    public function getFramesAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['id']);
        $frames = $this->container->get('app.catlog')->getFramesByFileId($data['id']);
        $response = [
            'frames' => $frames
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/generate_frames", name="generate_frames")
     */
    public function generateFramesAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['id']);
        $frames = $this->container->get('app.catlog')->generateFrames($data['id']);
        $response = [
            'frames' => $frames,
            'alerts' => $request->getSession()->getFlashBag()->all()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/open_file", name="open_file")
     */
    public function openFileAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['id']);
        $this->container->get('app.catlog')->openFile($data['id']);
        return new JsonResponse([]);
    }

    /**
     * @Route("/open_folder", name="open_folder")
     */
    public function openFolderAction (Request $request)
    {
        $data = Helper::getDataFromRequest($request, ['id']);
        $this->container->get('app.catlog')->openFolder($data['id']);
        return new JsonResponse([]);
    }

    /**
     * @Route("/update", name="update")
     */
    public function updateAction (Request $request)
    {
        $this->container->get('app.catlog')->helperUpdate();
    }
}
