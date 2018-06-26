<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 26.06.18
 * Time: 15:40
 */

namespace the16thpythonist\Checker;

use the16thpythonist\Checker\CollaborationGuesserInterface;

class SimpleCollaborationGuesser implements CollaborationGuesserInterface
{
    public $AUTHOR_LIMIT;

    public $collaborations;
    public $title;
    public $tags;
    public $authors;

    public function __construct($collaborations=array(), int $author_limit)
    {
        $this->collaborations = $collaborations;
        $this->AUTHOR_LIMIT = $author_limit;
    }

    public function set($publication)
    {
        $this->title = $publication['title'];
        $this->authors = $publication['authors'];
        $this->tags = $publication['tags'];
    }

    public function suspectsCollaboration()
    {
        return $this->publicationContainsCollaborationName() || $this->exceedsAuthorLimit();
    }

    public function publicationContainsCollaborationName() {
        return $this->titleContainsCollaborationName() || $this->tagsContainCollaborationName();
    }

    private function titleContainsCollaborationName() {
        return $this->containsCollaborationName($this->title);
    }

    private function tagsContainCollaborationName() {
        foreach ($this->tags as $tag) {
            $contains = $this->containsCollaborationName($tag);
            if ($contains) {
                return true;
            }
        }
        return false;
    }

    private function containsCollaborationName(string $haystack) {
        foreach ($this->collaborations as $search => $term) {
            if ($this->stringContains($haystack, $search)) {
                return true;
            }
        }
        return false;
    }

    private function exceedsAuthorLimit() {
        return count($this->authors) > $this->AUTHOR_LIMIT;
    }

    private function stringContains(string $haystack, string $needle) {
        $haystack_lower = strtolower($haystack);
        $needle_lower = strtolower($needle);
        return strpos($haystack_lower, $needle_lower) !== false;
    }
}