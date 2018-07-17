<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 11:01
 */


use PHPUnit\Framework\TestCase;

use the16thpythonist\Checker\HeuristicCollaborationGuesser;


$collaborations = array(
    'CMS',
    'Auger',
    'KATRIN'
);


class TestHeuristicCollaborationGuesser extends TestCase
{
    public function testDoesBasicallyWork() {
        global $collaborations;
        $guesser = new HeuristicCollaborationGuesser($collaborations);
        $guesser->set('Title', array());
        $this->assertEquals($guesser->title, 'Title');
    }

    public function testTagGuessWorks() {
        $tags = array(
            'experiment',
            'important paper',
            'buzz buzz',
            'Katrin collab.',
            'something with electronics'
        );
        $title = "I do not contain a guess";

        global $collaborations;
        $guesser = new HeuristicCollaborationGuesser($collaborations);
        $guesser->set($title, $tags);
        $this->assertTrue($guesser->hasGuess());
        $this->assertEquals($guesser->guess, 'KATRIN');
    }

    public function testTitleGuessWorks() {
        $tags = array(
            'experiment',
            'important paper',
            'buzz buzz',
            'I dont contain the collab.',
            'something with electronics'
        );
        $title = 'Looking intensely at stars, during the auger experiment';

        global $collaborations;
        $guesser = new HeuristicCollaborationGuesser($collaborations);
        $guesser->set($title, $tags);
        $this->assertTrue($guesser->hasGuess());
        $this->assertEquals($guesser->guess, 'Auger');
    }
}