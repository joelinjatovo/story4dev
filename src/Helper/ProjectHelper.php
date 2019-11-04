<?php

namespace App\Helper;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use App\Entity\Project;
use App\Form\QueryType;

class ProjectHelper {
    
    public static function getMetaFields(Project $project){
        $metas = [
            'query' => [
                'form_type' => QueryType::class,
                'activity' => [
                    'order_by' => $project->getMeta('activity_order_by', 'createdAt'),
                    'order'    => $project->getMeta('activity_order', 'ASC'),
                    'count'    => $project->getMeta('activity_count', 10),
                ],
                'report' => [
                    'order_by' => $project->getMeta('report_order_by', 'createdAt'),
                    'order'    => $project->getMeta('report_order', 'ASC'),
                    'count'    => $project->getMeta('report_count', 10),
                ],
                'contribution' => [
                    'order_by' => $project->getMeta('contribution_order_by', 'createdAt'),
                    'order'    => $project->getMeta('contribution_order', 'ASC'),
                    'count'    => $project->getMeta('contribution_count', 10),
                ],
            ]
        ];

        return $metas;
    }
    
}