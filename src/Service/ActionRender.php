<?php

/**
 * Génération de fil d'ariane
 */
namespace App\Service;

use Symfony\Component\Workflow\WorkflowInterface;

class ActionRender
{
    private $closure;

    private $workflowInterface;

    public function __construct(\Closure $closure, WorkflowInterface $workflowInterface = null)
    {
        $this->closure = $closure;   
        $this->workflowInterface = $workflowInterface; 
    }


    public function execute($args = null)
    {
        return $this->closure->call($this, $args);
    }
}