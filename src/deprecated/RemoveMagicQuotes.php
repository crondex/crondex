<?php namespace Crondex\Helpers;

class RemoveMagicQuotes implements RemoveMagicQuotesInterface
{
    /*
     * The injected model
     *
     * @Inject
     * @var object
     */
    protected $recursiveArrayWalk;

    /*
     * Constructor
     *
     * @Inject
     * @param object RecursiveArrayWalkInterface $RecursiveArrayWalkObj
     */
    public function __construct(RecursiveArrayWalkInterface $RecursiveArrayWalkObj)
    {
        $this->recursiveArrayWalk = $RecursiveArrayWalkObj;
    }

    /*
     * Check for and remove magic quotes.
     *
     * @param array $array
     * @return array 
     */
    public function removeQuotes($array)
    {
        if (get_magic_quotes_gpc()) {
            $this->recursiveArrayWalk->setValue($array, 'stripslashes');
            return $this->recursiveArrayWalk->getValue();
        }
        return $array;
    }
}

