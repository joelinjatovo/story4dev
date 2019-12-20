<?php

namespace App\Helper;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

use App\Entity\Project;
use App\Form\QueryType;

class ProjectHelper {
    
    public static function getMetaFields(Project $project){
        $metas = [
            'colors' => [
                'admin_only' => false,
                'form_type'  => ColorType::class,
                'data' => [
                    'header_bg' => $project->getMeta('colors_header_bg', "#ffffff"),
                ]
            ],
            'queries' => [
                'admin_only' => false,
                'form_type' => QueryType::class,
                'data' => [
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
                    'file' => [
                        'order_by' => $project->getMeta('file_order_by', 'createdAt'),
                        'order'    => $project->getMeta('file_order', 'ASC'),
                        'count'    => $project->getMeta('file_count', 10),
                    ],
                    'indicator' => [
                        'order_by' => $project->getMeta('indicator_order_by', 'createdAt'),
                        'order'    => $project->getMeta('indicator_order', 'ASC'),
                        'count'    => $project->getMeta('indicator_count', 10),
                    ],
                ]
            ],
            'quotas' => [
                'admin_only' => true,
                'form_type'  => NumberType::class,
                'data' => [
                    'indicator' => $project->getMeta('quotas_indicator', 100),
                ]
            ]
        ];

        return $metas;
    }
    
}