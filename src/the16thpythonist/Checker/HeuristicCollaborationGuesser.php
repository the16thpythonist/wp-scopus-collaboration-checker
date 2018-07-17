<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 10:38
 */

namespace the16thpythonist\Checker;

/**
 * Class HeuristicCollaborationGuesser
 *
 * This class is used to make a really simple guess as to which collaboration a publication belongs to, based on the
 * assumption that more often than not the name of the collaboration is mentioned either in the name or the tags of the
 * publication. So this object, if given the title and an array of the tags, will search if either of those contains a
 * name of collaborations (These have to be passed to the object as parameter) and returns a name as guess if it has
 * been found somewhere.
 *
 * Example:
 * // This is the set of possible collaborations to look for
 * $collaborations = array("CMS", "AUGER");
 * $guesser = new HeuristicCollaborationGuesser($collaborations);
 *
 * // Setting the new state, to base the computation on
 * $guesser->set("The new large Hydron Collider at the CMS experiment", array());
 * $guesser->hasGuess(); // True
 * $guesser->guess;      // "CMS"
 *
 * CHANGELOG
 *
 * Added 17.07.2018
 *
 * @since 0.0.0.1
 *
 * @package the16thpythonist\Checker
 */
class HeuristicCollaborationGuesser
{

    /**
     * @var array $collaborations   The array of all the collaborations to check for. All the names in this array will
     *                              be searched for in the title and tags. These names will also be returned as the
     *                              guess, if there is one.
     */
    public $collaborations;

    /**
     * @var array $collaborations_upper_map
     *                              The check if the strings contain a publication name or not is not case sensitive,
     *                              this is achieved by only comparing the upper string versions. This is an assoc
     *                              array, whose keys are the actual collaboration names passed to the object and which
     *                              will also be returned as guess. The values are the upper string versions of these
     *                              names which are used for the actual string comparison.
     */
    public $collaborations_upper_map;

    /**
     * @var string $guess           The collaboration, which the guesser has computed for the currently set title and
     *                              tags. If there is no solid guess possible, this will be an empty string.
     */
    public $guess = '';

    /**
     * @var string $title           This object works state based, which means a new state, in this case the title and
     *                              tags can be set as new attributes and based in this new state of the attributes
     *                              the computation is performed.
     *                              The string title of the currently set publication.
     */
    public $title;

    /**
     * @var array $tags             The array of string tags for the currently set publication
     */
    public $tags;

    /**
     * HeuristicCollaborationGuesser constructor.
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.1
     *
     * @param array $collaborations
     */
    public function __construct(array $collaborations)
    {
        $this->collaborations = $collaborations;
        $this->collaborations_upper_map = array();
        foreach ($this->collaborations as $collaboration) {
            $collaboration_upper = strtoupper($collaboration);
            $this->collaborations_upper_map[$collaboration] = $collaboration_upper;
        }
    }

    /**
     * Sets the title string and the tags array for the new current publication to be judged.
     *
     * This method actually also performs the computations necessary, which means after calling this function, the
     * guess, if there is one, can be gotten from the 'guess' attribute of the object.
     *
     * CHANGELOG
     *
     * Added 17.07.2018 - 0.0.0.1
     *
     * Changed 17.07.2018 - 0.0.0.2
     * Fixed the bug with a previous guess not being cleared and thus also being used for all following guesses even
     * though the computation didnt bring anything up
     *
     * @since 0.0.0.1
     *
     * @param string $title     the string title of the publication
     * @param array $tags       the array of string tags for the publication
     */
    public function set($title, $tags) {
        /*
         * For a new set of title and tags the guess has to be cleared first, because if the previous publication
         * actually had a guess and the current one doesnt without clearing it the previous one would be taken for
         * the current one as well.
         */
        $this->guess = '';

        $this->title = $title;
        $this->tags = $tags;

        $this->makeGuess();
    }

    /**
     * Calls the sub functions to make a guess based on title and tags, and sets the guess to the 'guess' attribute
     *
     * This function does not have to be called. It is being called from within the "set" method.
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.1
     */
    public function makeGuess() {
        $title_guess = $this->titleGuess();
        $tags_guess = $this->tagsGuess();
        if ($title_guess !== '') {
            $this->guess = $title_guess;
        } elseif ($tags_guess !== '') {
            $this->guess = $tags_guess;
        }
    }

    /**
     * Returns whether or not the guesser has a guess as to which collaboration it could be or not.
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.1
     *
     * @return bool
     */
    public function hasGuess() {
        return $this->guess !== '';
    }

    /**
     * Checks if the current title contains a collaboration name and if that is the case returns that name as its guess.
     *
     * If the title does not contain any name however returns an empty string
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.1
     *
     * @return string
     */
    private function titleGuess(): string {
        $collaboration = $this->containsCollaboration($this->title);
        return $collaboration;
    }

    /**
     * Checks if one of the tags contains a collaboration name and if that is the case returns the name as its guess.
     *
     * If the tags do not contain any collaboration names though, an empty string will be returned.
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.1
     *
     * @return string
     */
    private function tagsGuess(): string {
        foreach ($this->tags as $tag) {
            $collaboration = $this->containsCollaboration($tag);
            if ($collaboration !== '') {
                return $collaboration;
            }
        }
        return '';
    }

    /**
     * Checks if the given string contains one of the collaborations, if it does returns the collaboration name.
     *
     * If the string does not contain a collaboration name returns an empty string instead
     *
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @since 0.0.0.1
     *
     * @param string $string    the string to be checked for a occurrance of a collaboration.
     * @return string
     */
    private function containsCollaboration(string $string): string {
        $string_upper = strtoupper($string);
        foreach ($this->collaborations_upper_map as $collaboration => $collaboration_upper) {
            if (strpos($string_upper, $collaboration_upper) !== false) {
                return $collaboration;
            }
        }
        return '';
    }
}