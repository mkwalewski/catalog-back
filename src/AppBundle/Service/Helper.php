<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class Helper
{
    public static function getDataFromRequest (Request $request, $keys)
    {
        $data = [];
        if ($request->getContent())
        {
            $content = $request->getContent();
            $content = json_decode($content, true);
            foreach ($keys as $key)
            {
                $data[$key] = isset($content[$key]) ? $content[$key] : '';
            }
        }
        else
        {
            if ($request->isMethod('post'))
            {
                foreach ($keys as $key)
                {
                    $data[$key] = $request->request->get($key);
                }
            }
            else
            {
                foreach ($keys as $key)
                {
                    $data[$key] = $request->get($key);
                }
            }
        }
        return $data;
    }
}