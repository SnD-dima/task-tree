<?php

namespace App\Console\Commands;

use App\Models\Tree;
use Illuminate\Console\Command;

class GenerateTree extends Command
{

    const  MAXELEMENTS = 500;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'treeElement:generate {maxRandom=25}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generation tree elements from lorem text';

    /**
     * @var int maxRandom
     */
    protected $maxRandom;

    /**
     * @var int
     */


    /**
     * @var int
     */
    private $currentElements = 0;

    /**
     * @var string
     */
    protected $text;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Tree::truncate();


        preg_match_all('/[a-z0-9\']{3,}/i', $this->getDefaultText(), $stingTree);
        //get maxRandom number or set it
        $this->maxRandom = $this->argument('maxRandom');

        $this->text = $stingTree[0];

        $rootElement = Tree::create(['name' => $this->getRandomWord()]);
        $this->currentElements++;

        $this->makeTree($rootElement);
        dump('Successful');
        return true;
    }

    /**
     * @return string
     */
    private function getDefaultText()
    {
        return strip_tags(file_get_contents('http://loripsum.net/api'));
    }

    /**
     * @return int
     */
    private function getRandTreeElement()
    {
        return mt_rand(10, $this->maxRandom);
    }

    /**
     * @param $element
     * @param int $deep
     */
    private function createBranch($element, $deep = 0)
    {
        if($deep === 1 || $this->currentElements === self::MAXELEMENTS) return;

        if($deep === 0) {
            $deep = $this->getRandTreeElement();
        }

        $child = $element->children()->create(['name' => $this->getRandomWord()]);
        $this->currentElements++;


        return $this->createBranch($child, $deep -1);

    }

    /**
     * @param $rootElement
     */
    private function makeTree($rootElement)
    {
        do  {
            $this->createBranch($rootElement);
        }
        while($this->currentElements < 500);
    }


    /**
     * @return string
     */
    private function getRandomWord()
    {
        return $this->text[array_rand($this->text)];
    }

}
