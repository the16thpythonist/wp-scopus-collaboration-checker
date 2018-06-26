<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 25.06.18
 * Time: 14:21
 */

namespace the16thpythonist\Checker;

use Scopus\Response\Abstracts;
use OutOfRangeException;

class CollaborationChecker
{
    public $AUTHOR_LIMIT = 25;

    public $abstract;
    public $data;
    public $collaborations;
    public $guess;
    public $collaboration;

    public function __construct(array $collaborations)
    {
        $this->collaborations = $collaborations;
    }

    /**
     * CHANGELOG
     *
     * Added 25.06.2018
     *
     * @param Abstracts $abstract
     */
    public function set(Abstracts $abstract) {
        /* @var $abstract Abstracts */
        $this->abstract = $abstract;
        $this->data = $this->getData();
    }


    public function isCollaboration() {
        if( !isset($this->guess) ) {
            $this->guess = $this->containsCollaborationEntry() && $this->exceedsAuthorLimit() && $this->matchesCollaboration();
        }
        return $this->guess;
    }

    public function knowsCollaboration() {
        return isset($this->collaboration);
    }

    public function getCollaboration() {
        if (!isset($this->guess)) {
            $guess = $this->isCollaboration();
            if ($guess == true && $this->knowsCollaboration()) {
                return $this->collaboration;
            }
        }
        return false;
    }

    /**
     * CHANGELOG
     *
     * Added 26.06.2018
     *
     * @return bool
     */
    private function matchesCollaboration() {
        // Searching in the title for a match of collaboration and in the tags
        $title = $this->abstract->getCoredata()->getTitle();

        foreach ($this->collaborations as $search => $term) {
            if ($this->stringMatch($search, $title)) {
                $this->collaboration = $term;
                return true;
            }
        }
        return false;
    }

    /**
     * CHANGELOG
     *
     * Added 26.06.2018
     *
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    private function stringMatch(string $needle, string $haystack) {
        // This search is supposed to be character insensitive
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);

        $contains_characters = strpos($haystack, $needle) !== false;

        return $contains_characters;
    }

    /**
     * CHANGELOG
     *
     * Added 25.06.2018
     *
     * @return bool
     */
    private function exceedsAuthorLimit() {
        $authors = $this->abstract->getAuthors();
        $exceeds = count($authors) > $this->AUTHOR_LIMIT;
        return $exceeds;
    }

    /**
     * CHANGELOG
     *
     * Added 25.06.2018
     *
     * @return bool
     */
    private function containsCollaborationEntry() {
        $author_group = $this->getAuthorGroup();
        foreach ($author_group as $entry) {
            if (array_key_exists('collaboration', $entry)) {
                $name = $entry['collaboration']['ce:indexed-name'];
                foreach ($this->collaborations as $search => $term) {
                    if ($this->stringMatch($search, $name)) {
                        $this->collaboration = $term;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     *
     * CHANGELOG
     *
     * Added 25.06.2018
     *
     * @author Jonas Teufel <jonseb1998@gmail.com>
     * @since 0.0.0.0
     *
     * @throws OutOfRangeException author-group is not part of the data array
     *
     * @return mixed
     */
    private function getAuthorGroup() {
        $query = 'item/bibrecord/head/author-group';
        $author_group = $this->queryElement('');
        return $author_group;
    }

    /**
     * gets the sub element of the abstract data array described by the given key sequence query. Delimiter "/"
     *
     * CHANGELOG
     *
     * Added 25.06.2018
     *
     * @author Jonas Teufel <jonseb1998@gmail.com>
     * @since 0.0.0.0
     *
     * @param string $query
     *
     * @throws OutOfRangeException if the given sequence of keys does not lead to an element in the nested data array
     *
     * @return mixed
     */
    private function queryElement(string $query){
        $keys = explode('/', $query);
        $element = $this->getElement($keys);
        return $element;
    }

    /**
     * gets the sub element of the abstract data array described by the given key sequence
     *
     * CHANGELOG
     *
     * Added 25.06.2018
     *
     * @author Jonas Teufel <jonseb1998@gmail.com>
     * @since 0.0.0.0
     *
     * @param array $keys the sequence of the keys, to go down the nested array structure of the abstract data array
     *
     * @throws OutOfRangeException if the given sequence of keys does not lead to an element in the nested data array
     *
     * @return mixed the actual element within the data array
     */
    private function getElement(array $keys) {
        // Copying the whole data array into a new variable
        $current_array = $this->data;
        foreach ($keys as $key) {
            if (array_key_exists($key, $current_array )) {
                $current_array = $current_array[$key];
            } else {
                throw new OutOfRangeException("The array did not contain the key " . $key);
            }
        }
        return $current_array;
    }

    /**
     * CHANGELOG
     *
     * Added 25.06.2018
     */
    private function getData() {
        $closure = function () { return $this->data; };
        $getData = Closure::bind($closure, $this->abstract, Abstracts::class);
        $data = $getData();
        return $data;
    }
}