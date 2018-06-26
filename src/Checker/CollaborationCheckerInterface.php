<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 26.06.18
 * Time: 14:52
 */

namespace the16thpythonist\Checker;

use Scopus\Response\Abstracts;

interface CollaborationGuesserInterface
{
    public function set($publication);
    public function suspectsCollaboration();
}